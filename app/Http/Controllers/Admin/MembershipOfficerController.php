<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneratedDocument;
use App\Models\MemberProfile;
use App\Models\Membership;
use App\Models\MembershipCard;
use App\Models\MembershipOfficerAppointment;
use App\Models\NationalExecutivePosition;
use App\Services\DocumentGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class MembershipOfficerController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CREATE OFFICER APPOINTMENT
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        /*
        |--------------------------------------------------------------------------
        | ONLY APPROVED + ACTIVE MEMBERS
        |--------------------------------------------------------------------------
        */

        $members = MemberProfile::with([
            'user',
            'membership',
            'membershipCategory',
        ])
            ->where('status', 'approved')
            ->whereHas('membership', function ($query) {
                $query->where('status', 'active');
            })
            ->orderBy('first_name')
            ->orderBy('middle_name')
            ->orderBy('surname')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | NATIONAL EXECUTIVE POSITIONS
        |--------------------------------------------------------------------------
        |
        | Only VACANT NEM positions can be selected for a new
        | National Executive appointment.
        |
        */

        $executivePositions = NationalExecutivePosition::where(
            'status',
            'vacant'
        )
            ->orderBy('code')
            ->get();

        return view(
            'admin.membership_officers.create',
            compact(
                'members',
                'executivePositions'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STORE OFFICER APPOINTMENT
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([

            'membership_id' => [
                'required',
                'integer',
                'exists:memberships,id',
            ],

            'appointment_type' => [
                'required',
                'in:national_executive,task_force',
            ],

            /*
            |--------------------------------------------------------------------------
            | NATIONAL EXECUTIVE
            |--------------------------------------------------------------------------
            */

            'executive_id' => [
                'nullable',
                'string',
                'max:20',
            ],

            /*
            |--------------------------------------------------------------------------
            | TASK FORCE
            |--------------------------------------------------------------------------
            */

            'taskforce_id' => [
                'nullable',
                'string',
                'max:50',
            ],

            'taskforce_position' => [
                'nullable',
                'string',
                'max:150',
            ],

            'taskforce_level' => [
                'nullable',
                'in:national,state',
            ],

            'taskforce_state' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        try {

            $appointment = DB::transaction(function () use (
                $validated
            ) {

                /*
                |--------------------------------------------------------------------------
                | LOCK MEMBERSHIP
                |--------------------------------------------------------------------------
                */

                $membership = Membership::lockForUpdate()
                    ->findOrFail(
                        $validated['membership_id']
                    );

                if ($membership->status !== 'active') {

                    throw new RuntimeException(
                        'Only active memberships can be appointed as officers.'
                    );
                }

                if (!$membership->membership_number) {

                    throw new RuntimeException(
                        'This membership does not have a valid membership number.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | NATIONAL EXECUTIVE
                |--------------------------------------------------------------------------
                */

                if (
                    $validated['appointment_type'] ===
                    'national_executive'
                ) {

                    if (
                        empty(
                            $validated['executive_id']
                        )
                    ) {

                        throw new RuntimeException(
                            'Please select a National Executive position.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | LOCK NEM POSITION
                    |--------------------------------------------------------------------------
                    */

                    $executivePosition =
                        NationalExecutivePosition::where(
                            'code',
                            strtoupper(
                                trim(
                                    $validated['executive_id']
                                )
                            )
                        )
                            ->lockForUpdate()
                            ->first();

                    if (!$executivePosition) {

                        throw new RuntimeException(
                            'The selected National Executive position could not be found.'
                        );
                    }

                    if (
                        strtolower(
                            trim(
                                $executivePosition->status
                            )
                        ) !== 'vacant'
                    ) {

                        throw new RuntimeException(
                            'The selected National Executive position is no longer vacant.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | CHECK EXISTING APPOINTMENT FOR THIS MEMBER
                    |--------------------------------------------------------------------------
                    */

                    $existingMemberAppointment =
                        MembershipOfficerAppointment::where(
                            'membership_id',
                            $membership->id
                        )
                            ->where(
                                'appointment_type',
                                'national_executive'
                            )
                            ->whereIn(
                                'status',
                                [
                                    'pending',
                                    'approved',
                                ]
                            )
                            ->exists();

                    if ($existingMemberAppointment) {

                        throw new RuntimeException(
                            'This member already has a pending or approved National Executive appointment.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | CHECK POSITION ALREADY REQUESTED
                    |--------------------------------------------------------------------------
                    */

                    $existingPositionAppointment =
                        MembershipOfficerAppointment::where(
                            'executive_id',
                            $executivePosition->code
                        )
                            ->whereIn(
                                'status',
                                [
                                    'pending',
                                    'approved',
                                ]
                            )
                            ->exists();

                    if ($existingPositionAppointment) {

                        throw new RuntimeException(
                            'There is already a pending or approved appointment for this National Executive position.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | CREATE NATIONAL EXECUTIVE APPOINTMENT
                    |--------------------------------------------------------------------------
                    */

                    return MembershipOfficerAppointment::create([

                        'membership_id' =>
                            $membership->id,

                        'appointment_type' =>
                            'national_executive',

                        'executive_id' =>
                            $executivePosition->code,

                        'taskforce_id' =>
                            null,

                        'position' =>
                            $executivePosition->position,

                        'level' =>
                            null,

                        'state' =>
                            null,

                        'status' =>
                            'pending',

                        'appointed_at' =>
                            null,

                        'approved_at' =>
                            null,

                        'approved_by' =>
                            null,

                        'rejected_at' =>
                            null,

                        'rejected_by' =>
                            null,

                        'rejection_reason' =>
                            null,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | TASK FORCE
                |--------------------------------------------------------------------------
                */

                if (
                    empty(
                        $validated['taskforce_id']
                    )
                ) {

                    throw new RuntimeException(
                        'Task Force ID is required.'
                    );
                }

                if (
                    empty(
                        $validated['taskforce_position']
                    )
                ) {

                    throw new RuntimeException(
                        'Task Force position is required.'
                    );
                }

                if (
                    empty(
                        $validated['taskforce_level']
                    )
                ) {

                    throw new RuntimeException(
                        'Please select the Task Force level.'
                    );
                }

                if (
                    $validated['taskforce_level'] ===
                    'state'
                    &&
                    empty(
                        $validated['taskforce_state']
                    )
                ) {

                    throw new RuntimeException(
                        'Task Force state is required for a state-level appointment.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | NATIONAL LEVEL MUST NOT HAVE A STATE
                |--------------------------------------------------------------------------
                */

                $taskForceState =
                    $validated['taskforce_level'] ===
                    'state'
                        ? trim(
                            $validated['taskforce_state']
                        )
                        : null;

                /*
                |--------------------------------------------------------------------------
                | CHECK EXISTING TASK FORCE APPOINTMENT
                |--------------------------------------------------------------------------
                */

                $existingTaskForce =
                    MembershipOfficerAppointment::where(
                        'membership_id',
                        $membership->id
                    )
                        ->where(
                            'appointment_type',
                            'task_force'
                        )
                        ->where(
                            'taskforce_id',
                            trim(
                                $validated['taskforce_id']
                            )
                        )
                        ->whereIn(
                            'status',
                            [
                                'pending',
                                'approved',
                            ]
                        )
                        ->exists();

                if ($existingTaskForce) {

                    throw new RuntimeException(
                        'This member already has a pending or approved appointment for this Task Force.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | CREATE TASK FORCE APPOINTMENT
                |--------------------------------------------------------------------------
                */

                return MembershipOfficerAppointment::create([

                    'membership_id' =>
                        $membership->id,

                    'appointment_type' =>
                        'task_force',

                    'executive_id' =>
                        null,

                    'taskforce_id' =>
                        trim(
                            $validated['taskforce_id']
                        ),

                    'position' =>
                        trim(
                            $validated['taskforce_position']
                        ),

                    'level' =>
                        $validated['taskforce_level'],

                    'state' =>
                        $taskForceState,

                    'status' =>
                        'pending',

                    'appointed_at' =>
                        null,

                    'approved_at' =>
                        null,

                    'approved_by' =>
                        null,

                    'rejected_at' =>
                        null,

                    'rejected_by' =>
                        null,

                    'rejection_reason' =>
                        null,
                ]);
            });

            return redirect()
                ->route(
                    'admin.membership-officers.show',
                    $appointment->id
                )
                ->with(
                    'success',
                    'Officer appointment application created successfully and is awaiting approval.'
                );

        } catch (\Throwable $e) {

            Log::error(
                'OFFICER APPOINTMENT CREATION FAILED',
                [
                    'admin_id' =>
                        Auth::id(),

                    'error' =>
                        $e->getMessage(),

                    'trace' =>
                        $e->getTraceAsString(),
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW OFFICER APPOINTMENT
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $appointment =
            MembershipOfficerAppointment::findOrFail(
                $id
            );

        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP
        |--------------------------------------------------------------------------
        */

        $membership = Membership::with([
            'user',
            'membershipCategory',
        ])->findOrFail(
            $appointment->membership_id
        );

        /*
        |--------------------------------------------------------------------------
        | PROFILE
        |--------------------------------------------------------------------------
        */

        $profile = MemberProfile::find(
            $membership->member_profile_id
        );

        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP CARDS
        |--------------------------------------------------------------------------
        |
        | A member can now have:
        |
        | membership
        | national_executive
        | task_force
        |
        */

        $cards = MembershipCard::where(
            'membership_id',
            $membership->id
        )
            ->where(
                'status',
                'active'
            )
            ->orderBy('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | NATIONAL EXECUTIVE POSITION
        |--------------------------------------------------------------------------
        */

        $executivePosition = null;

        if ($appointment->executive_id) {

            $executivePosition =
                NationalExecutivePosition::where(
                    'code',
                    $appointment->executive_id
                )->first();
        }

        /*
        |--------------------------------------------------------------------------
        | MEMBER DOCUMENTS
        |--------------------------------------------------------------------------
        */

        $documents = GeneratedDocument::where(
            'user_id',
            $membership->user_id
        )
            ->latest('id')
            ->get();

        return view(
            'admin.membership_officers.show',
            compact(
                'appointment',
                'membership',
                'profile',
                'cards',
                'executivePosition',
                'documents'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE OFFICER APPOINTMENT
    |--------------------------------------------------------------------------
    */

    public function approve(
        $id,
        DocumentGenerationService $documentGenerationService
    ) {
        try {

            $result = DB::transaction(function () use (
                $id,
                $documentGenerationService
            ) {

                /*
                |--------------------------------------------------------------------------
                | LOCK APPOINTMENT
                |--------------------------------------------------------------------------
                */

                $appointment =
                    MembershipOfficerAppointment::lockForUpdate()
                        ->findOrFail($id);

                if ($appointment->status !== 'pending') {

                    throw new RuntimeException(
                        'Only pending officer appointments can be approved.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | LOCK MEMBERSHIP
                |--------------------------------------------------------------------------
                */

                $membership =
                    Membership::lockForUpdate()
                        ->findOrFail(
                            $appointment->membership_id
                        );

                if ($membership->status !== 'active') {

                    throw new RuntimeException(
                        'Only active memberships can receive an officer appointment.'
                    );
                }

                if (!$membership->membership_number) {

                    throw new RuntimeException(
                        'Membership number has not been generated for this member.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | LOCK PROFILE
                |--------------------------------------------------------------------------
                */

                $profile =
                    MemberProfile::lockForUpdate()
                        ->find(
                            $membership->member_profile_id
                        );

                if (!$profile) {

                    throw new RuntimeException(
                        'Member profile could not be found.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | NATIONAL EXECUTIVE APPOINTMENT
                |--------------------------------------------------------------------------
                */

                if (
                    $appointment->appointment_type ===
                    'national_executive'
                ) {

                    if (!$appointment->executive_id) {

                        throw new RuntimeException(
                            'National Executive appointment does not have an NEM code.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | LOCK NEM POSITION
                    |--------------------------------------------------------------------------
                    */

                    $executivePosition =
                        NationalExecutivePosition::where(
                            'code',
                            $appointment->executive_id
                        )
                            ->lockForUpdate()
                            ->first();

                    if (!$executivePosition) {

                        throw new RuntimeException(
                            'The National Executive position could not be found.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | POSITION MUST STILL BE VACANT
                    |--------------------------------------------------------------------------
                    */

                    if (
                        strtolower(
                            trim(
                                $executivePosition->status
                            )
                        ) !== 'vacant'
                    ) {

                        throw new RuntimeException(
                            'This National Executive position is no longer vacant.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | MEMBER CANNOT ALREADY HOLD ANOTHER NEM
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $membership->executive_id &&
                        $membership->executive_id !==
                        $appointment->executive_id
                    ) {

                        throw new RuntimeException(
                            'This member already holds another National Executive position: ' .
                                $membership->executive_id .
                                '.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | MEMBER CANNOT ALREADY HOLD SAME NEM
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $membership->executive_id ===
                        $appointment->executive_id
                    ) {

                        throw new RuntimeException(
                            'This member is already assigned to ' .
                                $appointment->executive_id .
                                '.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | CHECK POSITION IS NOT ASSIGNED ELSEWHERE
                    |--------------------------------------------------------------------------
                    */

                    $positionAlreadyAssigned =
                        Membership::where(
                            'executive_id',
                            $appointment->executive_id
                        )
                            ->where(
                                'id',
                                '!=',
                                $membership->id
                            )
                            ->exists();

                    if ($positionAlreadyAssigned) {

                        throw new RuntimeException(
                            'This National Executive position is already assigned to another membership.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | CURRENT MEMBERSHIP NUMBER
                    |--------------------------------------------------------------------------
                    */

                    $currentMembershipNumber =
                        $membership->membership_number;

                    /*
                    |--------------------------------------------------------------------------
                    | REMOVE EXISTING NEM SUFFIX
                    |--------------------------------------------------------------------------
                    */

                    $baseMembershipNumber =
                        preg_replace(
                            '/-NEM-\d{2}$/i',
                            '',
                            $currentMembershipNumber
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | BUILD NEW MEMBERSHIP NUMBER
                    |--------------------------------------------------------------------------
                    |
                    | Example:
                    |
                    | NMN-EXP-LAG-0007
                    |
                    | becomes:
                    |
                    | NMN-EXP-LAG-0007-NEM-11
                    |
                    */

                    $newMembershipNumber =
                        $baseMembershipNumber .
                        '-' .
                        strtoupper(
                            trim(
                                $appointment->executive_id
                            )
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE MEMBERSHIP
                    |--------------------------------------------------------------------------
                    */

                    $membership->update([

                        'membership_number' =>
                            $newMembershipNumber,

                        'membership_position' =>
                            $executivePosition->position,

                        'executive_id' =>
                            $executivePosition->code,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE MEMBER PROFILE
                    |--------------------------------------------------------------------------
                    */

                    $profile->update([

                        'membership_number' =>
                            $newMembershipNumber,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | DO NOT UPDATE EXISTING MEMBERSHIP CARD
                    |--------------------------------------------------------------------------
                    |
                    | IMPORTANT:
                    |
                    | The ordinary membership card remains:
                    |
                    | CARD-000013
                    | NMN-EXP-LAG-0007
                    |
                    | We create a NEW card for the National Executive.
                    |
                    */

                    $existingExecutiveCard =
                        MembershipCard::where(
                            'membership_id',
                            $membership->id
                        )
                            ->where(
                                'card_type',
                                'national_executive'
                            )
                            ->where(
                                'status',
                                'active'
                            )
                            ->lockForUpdate()
                            ->first();

                    if (!$existingExecutiveCard) {

                        /*
                        |--------------------------------------------------------------------------
                        | GENERATE EXECUTIVE CARD NUMBER
                        |--------------------------------------------------------------------------
                        */

                        $cardNumber =
                            $this->generateCardNumber();

                        /*
                        |--------------------------------------------------------------------------
                        | GENERATE EXECUTIVE QR TOKEN
                        |--------------------------------------------------------------------------
                        */

                        $qrToken =
                            (string) Str::uuid();

                        /*
                        |--------------------------------------------------------------------------
                        | CREATE NATIONAL EXECUTIVE CARD
                        |--------------------------------------------------------------------------
                        */

                        MembershipCard::create([

                            'membership_id' =>
                                $membership->id,

                            'card_type' =>
                                'national_executive',

                            'member_profile_id' =>
                                $membership->member_profile_id,

                            'card_number' =>
                                $cardNumber,

                            'membership_number' =>
                                $newMembershipNumber,

                            'membership_category_id' =>
                                $membership->membership_category_id,

                            'issued_at' =>
                                $membership->issued_at,

                            'expires_at' =>
                                $membership->expires_at,

                            'qr_token' =>
                                $qrToken,

                            'status' =>
                                'active',

                            'replaced_card_id' =>
                                null,

                            'generated_at' =>
                                now(),
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | MARK NEM POSITION AS OCCUPIED
                    |--------------------------------------------------------------------------
                    */

                    $executivePosition->update([
                        'status' =>
                            'occupied',
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | APPROVE APPOINTMENT
                    |--------------------------------------------------------------------------
                    */

                    $appointment->update([

                        'status' =>
                            'approved',

                        'appointed_at' =>
                            now(),

                        'approved_at' =>
                            now(),

                        'approved_by' =>
                            Auth::id(),

                        'rejected_at' =>
                            null,

                        'rejected_by' =>
                            null,

                        'rejection_reason' =>
                            null,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | REGENERATE MEMBERSHIP DOCUMENTS
                    |--------------------------------------------------------------------------
                    */

                    $generatedDocuments =
                        $documentGenerationService
                            ->regenerateMembershipDocumentsForOfficerAppointment(
                                $membership->fresh()
                            );

                    return [

                        'appointment' =>
                            $appointment->fresh(),

                        'membership' =>
                            $membership->fresh(),

                        'generated_documents' =>
                            $generatedDocuments,
                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | TASK FORCE APPOINTMENT
                |--------------------------------------------------------------------------
                */

                if (
                    $appointment->appointment_type ===
                    'task_force'
                ) {

                    if (!$appointment->taskforce_id) {

                        throw new RuntimeException(
                            'Task Force appointment does not have a Task Force ID.'
                        );
                    }

                    if (!$appointment->position) {

                        throw new RuntimeException(
                            'Task Force appointment does not have a position.'
                        );
                    }

                    if (!$appointment->level) {

                        throw new RuntimeException(
                            'Task Force appointment does not have a level.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | STATE LEVEL REQUIRES STATE
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $appointment->level === 'state' &&
                        !$appointment->state
                    ) {

                        throw new RuntimeException(
                            'State is required for a state-level Task Force appointment.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | NATIONAL TASK FORCE MUST NOT HAVE STATE
                    |--------------------------------------------------------------------------
                    */

                    $taskForceState =
                        $appointment->level === 'state'
                            ? $appointment->state
                            : null;

                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE TASK FORCE INFORMATION
                    |--------------------------------------------------------------------------
                    |
                    | IMPORTANT:
                    |
                    | We do NOT touch:
                    |
                    | executive_id
                    | membership_position
                    | membership_number
                    |
                    | This allows:
                    |
                    | Membership + NEM + Task Force
                    |
                    */

                    $membership->update([

                        'taskforce_id' =>
                            $appointment->taskforce_id,

                        'taskforce_position' =>
                            $appointment->position,

                        'taskforce_level' =>
                            $appointment->level,

                        'taskforce_state' =>
                            $taskForceState,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | CREATE TASK FORCE CARD
                    |--------------------------------------------------------------------------
                    |
                    | IMPORTANT:
                    |
                    | The Task Force card is separate from:
                    |
                    | 1. Membership card
                    | 2. National Executive card
                    |
                    | If the member has an NEM suffix, the Task Force
                    | card uses the BASE membership number.
                    |
                    | Example:
                    |
                    | Membership:
                    | NMN-EXP-LAG-0007
                    |
                    | Current membership after NEM:
                    | NMN-EXP-LAG-0007-NEM-11
                    |
                    | Task Force card:
                    | NMN-EXP-LAG-0007
                    |
                    */

                    $existingTaskForceCard =
                        MembershipCard::where(
                            'membership_id',
                            $membership->id
                        )
                            ->where(
                                'card_type',
                                'task_force'
                            )
                            ->where(
                                'status',
                                'active'
                            )
                            ->lockForUpdate()
                            ->first();

                    if (!$existingTaskForceCard) {

                        /*
                        |--------------------------------------------------------------------------
                        | GET BASE MEMBERSHIP NUMBER
                        |--------------------------------------------------------------------------
                        */

                        $baseMembershipNumber =
                            preg_replace(
                                '/-NEM-\d{2}$/i',
                                '',
                                $membership->membership_number
                            );

                        /*
                        |--------------------------------------------------------------------------
                        | GENERATE TASK FORCE CARD NUMBER
                        |--------------------------------------------------------------------------
                        */

                        $cardNumber =
                            $this->generateCardNumber();

                        /*
                        |--------------------------------------------------------------------------
                        | GENERATE TASK FORCE QR TOKEN
                        |--------------------------------------------------------------------------
                        */

                        $qrToken =
                            (string) Str::uuid();

                        /*
                        |--------------------------------------------------------------------------
                        | CREATE TASK FORCE CARD
                        |--------------------------------------------------------------------------
                        */

                        MembershipCard::create([

                            'membership_id' =>
                                $membership->id,

                            'card_type' =>
                                'task_force',

                            'member_profile_id' =>
                                $membership->member_profile_id,

                            'card_number' =>
                                $cardNumber,

                            'membership_number' =>
                                $baseMembershipNumber,

                            'membership_category_id' =>
                                $membership->membership_category_id,

                            'issued_at' =>
                                $membership->issued_at,

                            'expires_at' =>
                                $membership->expires_at,

                            'qr_token' =>
                                $qrToken,

                            'status' =>
                                'active',

                            'replaced_card_id' =>
                                null,

                            'generated_at' =>
                                now(),
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | APPROVE TASK FORCE APPOINTMENT
                    |--------------------------------------------------------------------------
                    */

                    $appointment->update([

                        'status' =>
                            'approved',

                        'appointed_at' =>
                            now(),

                        'approved_at' =>
                            now(),

                        'approved_by' =>
                            Auth::id(),

                        'rejected_at' =>
                            null,

                        'rejected_by' =>
                            null,

                        'rejection_reason' =>
                            null,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | REGENERATE MEMBERSHIP DOCUMENTS
                    |--------------------------------------------------------------------------
                    */

                    $generatedDocuments =
                        $documentGenerationService
                            ->regenerateMembershipDocumentsForOfficerAppointment(
                                $membership->fresh()
                            );

                    return [

                        'appointment' =>
                            $appointment->fresh(),

                        'membership' =>
                            $membership->fresh(),

                        'generated_documents' =>
                            $generatedDocuments,
                    ];
                }

                throw new RuntimeException(
                    'Unsupported officer appointment type.'
                );
            });

            /*
            |--------------------------------------------------------------------------
            | SUCCESS MESSAGE
            |--------------------------------------------------------------------------
            */

            $generatedCount =
                count(
                    $result['generated_documents']
                );

            $cardMessage =
                $result['appointment']->appointment_type ===
                'national_executive'
                    ? ' A National Executive ID card was created.'
                    : ' A Task Force ID card was created.';

            return redirect()
                ->route(
                    'admin.membership-officers.show',
                    $result['appointment']->id
                )
                ->with(
                    'success',
                    'Officer appointment approved successfully. ' .
                        'Membership number: ' .
                        $result['membership']->membership_number .
                        '. ' .
                        $generatedCount .
                        ' membership document(s) regenerated successfully.' .
                        $cardMessage
                );

        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | LOG ERROR
            |--------------------------------------------------------------------------
            */

            Log::error(
                'OFFICER APPOINTMENT APPROVAL FAILED',
                [
                    'admin_id' =>
                        Auth::id(),

                    'appointment_id' =>
                        $id,

                    'error' =>
                        $e->getMessage(),

                    'trace' =>
                        $e->getTraceAsString(),
                ]
            );

            return back()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT OFFICER APPOINTMENT
    |--------------------------------------------------------------------------
    */

    public function reject(
        Request $request,
        $id
    ) {
        $request->validate([

            'rejection_reason' => [
                'required',
                'string',
                'max:2000',
            ],

        ]);

        $appointment =
            MembershipOfficerAppointment::findOrFail(
                $id
            );

        /*
        |--------------------------------------------------------------------------
        | ONLY PENDING APPOINTMENTS
        |--------------------------------------------------------------------------
        */

        if ($appointment->status !== 'pending') {

            return back()->with(
                'error',
                'Only pending officer appointments can be rejected.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | REJECT
        |--------------------------------------------------------------------------
        */

        $appointment->update([

            'status' =>
                'rejected',

            'rejected_at' =>
                now(),

            'rejected_by' =>
                Auth::id(),

            'rejection_reason' =>
                $request->rejection_reason,

        ]);

        return redirect()
            ->route(
                'admin.membership-officers.show',
                $appointment->id
            )
            ->with(
                'success',
                'Officer appointment rejected successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE CARD NUMBER
    |--------------------------------------------------------------------------
    */

    private function generateCardNumber(): string
    {
        $lastCard =
            MembershipCard::latest('id')
                ->lockForUpdate()
                ->first();

        $nextNumber = 1;

        if ($lastCard) {

            if (
                preg_match(
                    '/(\d+)$/',
                    $lastCard->card_number,
                    $matches
                )
            ) {

                $nextNumber =
                    ((int) $matches[1]) + 1;
            }
        }

        return 'CARD-' .
            str_pad(
                $nextNumber,
                6,
                '0',
                STR_PAD_LEFT
            );
    }
}
