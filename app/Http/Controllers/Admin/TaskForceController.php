<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\MembershipOfficerAppointment;
use App\Models\NationalExecutivePosition;
use App\Models\StateExecutivePosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TaskForceController extends Controller
{
    /**
     * Only super admins can touch anything in this controller.
     */
    public function __construct()
    {
        abort_unless(
            Auth::check() && Auth::user()->role === 'super_admin',
            403,
            'Only Super Admins can manage appointments.'
        );
    }

    /* =========================================================
       LIST — all appointments (filterable)
    ========================================================= */

    public function index(Request $request)
    {
        $query = MembershipOfficerAppointment::with(['membership.user', 'approvedBy'])
            ->orderByDesc('id');

        if ($request->filled('appointment_type')) {
            $query->where('appointment_type', $request->appointment_type);
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('state')) {
            $query->where('state', $request->state);
        }

        $appointments = $query->paginate(20)->withQueryString();

        // Data for filter dropdowns
        $states = $this->nigerianStates();

        return view('admin.taskforce.index', compact('appointments', 'states'));
    }

    /* =========================================================
       CREATE — form
    ========================================================= */

    public function create(Request $request)
    {
        $type = $request->input('type', 'task_force');

        abort_unless(
            in_array($type, ['task_force', 'national_executive', 'state_executive'], true),
            404
        );

        /*
    |--------------------------------------------------------------------------
    | ELIGIBLE MEMBERS
    |--------------------------------------------------------------------------
    */

        $members = Membership::with('user')
            ->whereHas('user', function ($q) {
                $q->where('role', 'member');
            })
            ->orderByDesc('id')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | POSITION CATALOGS
    |--------------------------------------------------------------------------
    */

        $positions = collect();
        $taskForcePositions = collect();
        $states = $this->nigerianStates();

        if ($type === 'national_executive') {

            $positions = NationalExecutivePosition::orderBy('position')->get();
        } elseif ($type === 'state_executive') {

            $positions = StateExecutivePosition::orderBy('state')
                ->orderBy('position')
                ->get();
        } elseif ($type === 'task_force') {

            $taskForcePositions = $this->taskForcePositionTitles();
        }

        return view('admin.taskforce.create', compact(
            'type',
            'members',
            'positions',
            'taskForcePositions',
            'states'
        ));
    }

    /* =========================================================
       STORE — save a new appointment (auto-approved since super admin)
    ========================================================= */

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'          => ['required', 'in:task_force,national_executive,state_executive'],
            'membership_id' => ['required', 'exists:memberships,id'],
            'executive_id'  => ['nullable', 'string', 'max:50'],
            'taskforce_id'  => ['nullable', 'string', 'max:50'],
            'position'      => ['nullable', 'string', 'max:255'],
            'level'         => ['nullable', 'in:national,state'],
            'state'         => ['nullable', 'string', 'max:100'],
        ]);

        /*
    |--------------------------------------------------------------------------
    | TYPE-SPECIFIC VALIDATION
    |--------------------------------------------------------------------------
    */

        $positionName = null;

        if ($data['type'] === 'national_executive') {

            $request->validate([
                'executive_id' => ['required', 'exists:national_executive_positions,code'],
            ]);

            $positionName = NationalExecutivePosition::where('code', $data['executive_id'])
                ->value('position') ?? '—';
        } elseif ($data['type'] === 'state_executive') {

            $request->validate([
                'executive_id' => ['required', 'exists:state_executive_positions,code'],
            ]);

            $statePosition = StateExecutivePosition::where('code', $data['executive_id'])->first();

            $positionName = $statePosition?->position ?? '—';

            // Keep the state on the appointment row for filtering
            $data['state'] = $statePosition?->state;
        } elseif ($data['type'] === 'task_force') {

            $request->validate([
                'level' => ['required', 'in:national,state'],
            ]);

            if ($data['level'] === 'state') {
                $request->validate([
                    'state' => ['required', 'string', 'max:100'],
                ]);
            }

            $request->validate([
                'position' => ['required', 'string', 'max:255'],
            ]);

            $positionName = $data['position'];
        }

        /*
    |--------------------------------------------------------------------------
    | DUPLICATE CHECK
    |--------------------------------------------------------------------------
    */

        $duplicate = MembershipOfficerAppointment::where('membership_id', $data['membership_id'])
            ->where('appointment_type', $data['type'])
            ->where('position', $positionName)
            ->where('status', '!=', 'rejected')
            ->exists();

        if ($duplicate) {
            return back()->withInput()->withErrors([
                'membership_id' => 'This member already holds this appointment.',
            ]);
        }

        /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

        MembershipOfficerAppointment::create([
            'membership_id'    => $data['membership_id'],
            'appointment_type' => $data['type'],
            'executive_id'     => in_array($data['type'], ['national_executive', 'state_executive'], true)
                ? $data['executive_id']
                : null,
            'taskforce_id'     => $data['type'] === 'task_force'
                ? ($data['taskforce_id'] ?? $this->generateTaskForceId())
                : null,
            'position'         => $positionName,
            'level'            => $data['type'] === 'task_force' ? $data['level'] : null,
            'state'            => ($data['type'] === 'state_executive')
                ? $data['state']
                : (($data['type'] === 'task_force' && $data['level'] === 'state') ? $data['state'] : null),
            'status'           => 'approved',
            'appointed_at'     => now(),
            'approved_at'      => now(),
            'approved_by'      => Auth::id(),
        ]);

        /*
    |--------------------------------------------------------------------------
    | MARK POSITION AS OCCUPIED
    |--------------------------------------------------------------------------
    */

        if ($data['type'] === 'national_executive') {
            NationalExecutivePosition::where('code', $data['executive_id'])
                ->update(['status' => 'occupied']);
        }

        if ($data['type'] === 'state_executive') {
            StateExecutivePosition::where('code', $data['executive_id'])
                ->update(['status' => 'occupied']);
        }

        return redirect()
            ->route('admin.taskforce.index')
            ->with('success', 'Appointment created successfully.');
    }

    /* =========================================================
       APPROVE / REJECT — for later workflows
    ========================================================= */

    public function approve(MembershipOfficerAppointment $appointment)
    {
        $appointment->update([
            'status'       => 'approved',
            'approved_at'  => now(),
            'approved_by'  => Auth::id(),
        ]);

        return back()->with('success', 'Appointment approved.');
    }

    public function reject(Request $request, MembershipOfficerAppointment $appointment)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $appointment->update([
            'status'           => 'rejected',
            'rejected_at'      => now(),
            'rejected_by'      => Auth::id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Appointment rejected.');
    }

    public function destroy(MembershipOfficerAppointment $appointment)
    {
        $appointment->delete();

        return back()->with('success', 'Appointment removed.');
    }

    /* =========================================================
       HELPERS
    ========================================================= */

    private function taskForcePositionTitles(): array
    {
        return [
            'Chairman Task Force',
            'Vice Chairman Task Force',
            'Secretary Task Force',
            'Public Relations Officer Task Force',
            'Member Task Force',
        ];
    }


    /* =========================================================
   STATE EXECUTIVE POSITIONS — CATALOG CRUD
========================================================= */

    public function statePositionsIndex()
    {
        $positions = StateExecutivePosition::orderBy('state')
            ->orderBy('position')
            ->paginate(30);

        $states = $this->nigerianStates();

        return view('admin.taskforce.state-positions.index', compact('positions', 'states'));
    }

    public function statePositionsStore(Request $request)
    {
        $data = $request->validate([
            'code'     => ['required', 'string', 'max:40', 'unique:state_executive_positions,code'],
            'position' => ['required', 'string', 'max:255'],
            'state'    => ['required', 'string', 'max:100'],
        ]);

        StateExecutivePosition::create([
            'code'     => strtoupper($data['code']),
            'position' => $data['position'],
            'state'    => $data['state'],
            'status'   => 'vacant',
        ]);

        return back()->with('success', 'State executive position added.');
    }

    public function statePositionsUpdate(Request $request, StateExecutivePosition $position)
    {
        $data = $request->validate([
            'position' => ['required', 'string', 'max:255'],
            'state'    => ['required', 'string', 'max:100'],
            'status'   => ['required', 'in:vacant,occupied'],
        ]);

        $position->update($data);

        return back()->with('success', 'State executive position updated.');
    }

    public function statePositionsDestroy(StateExecutivePosition $position)
    {
        $position->delete();

        return back()->with('success', 'State executive position removed.');
    }


    private function generateTaskForceId(): string
    {
        $last = MembershipOfficerAppointment::whereNotNull('taskforce_id')
            ->orderByDesc('id')
            ->value('taskforce_id');

        $next = 1;
        if ($last && preg_match('/(\d+)$/', $last, $m)) {
            $next = (int) $m[1] + 1;
        }

        return 'TF-' . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Nigerian states + FCT. Trimmed for brevity — add the full list if needed.
     */
    private function nigerianStates(): array
    {
        return [
            'Abia',
            'Adamawa',
            'Akwa Ibom',
            'Anambra',
            'Bauchi',
            'Bayelsa',
            'Benue',
            'Borno',
            'Cross River',
            'Delta',
            'Ebonyi',
            'Edo',
            'Ekiti',
            'Enugu',
            'FCT - Abuja',
            'Gombe',
            'Imo',
            'Jigawa',
            'Kaduna',
            'Kano',
            'Katsina',
            'Kebbi',
            'Kogi',
            'Kwara',
            'Lagos',
            'Nasarawa',
            'Niger',
            'Ogun',
            'Ondo',
            'Osun',
            'Oyo',
            'Plateau',
            'Rivers',
            'Sokoto',
            'Taraba',
            'Yobe',
            'Zamfara',
        ];
    }
}
