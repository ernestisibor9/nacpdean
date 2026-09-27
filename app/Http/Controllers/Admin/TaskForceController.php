<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\MembershipOfficerAppointment;
use App\Models\NationalExecutivePosition;
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
        // Which type are we creating? (task_force | national_executive)
        $type = $request->input('type', 'task_force');

        abort_unless(
            in_array($type, ['task_force', 'national_executive'], true),
            404
        );

        // Members eligible to be appointed (only approved ones)
        $members = Membership::with('user')
            ->whereHas('user', function ($q) {
                $q->where('role', 'member');
            })
            ->orderByDesc('id')
            ->get();

        // For national executive: list the positions catalog
        $positions = $type === 'national_executive'
            ? NationalExecutivePosition::orderBy('position')->get()
            : collect();

        // For task force: predefined position titles
        $taskForcePositions = $type === 'task_force'
            ? $this->taskForcePositionTitles()
            : collect();

        $states = $this->nigerianStates();

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
            'type'             => ['required', 'in:task_force,national_executive'],
            'membership_id'    => ['required', 'exists:memberships,id'],
            'executive_id'     => ['nullable', 'string', 'exists:national_executive_positions,code'],
            'taskforce_id'     => ['nullable', 'string', 'max:50'],
            'position'         => ['required', 'string', 'max:255'],
            'level'            => ['nullable', 'in:national,state'],
            'state'            => ['nullable', 'string', 'max:100'],
        ]);

        if ($data['type'] === 'national_executive') {
            $request->validate([
                'executive_id' => ['required', 'exists:national_executive_positions,code'],
            ]);
        }

        if ($data['type'] === 'task_force') {
            $request->validate([
                'level' => ['required', 'in:national,state'],
            ]);

            if ($data['level'] === 'state') {
                $request->validate([
                    'state' => ['required', 'string', 'max:100'],
                ]);
            }
        }

        // Prevent duplicate appointment of the same member for the same role
        $duplicate = MembershipOfficerAppointment::where('membership_id', $data['membership_id'])
            ->where('appointment_type', $data['type'] === 'task_force' ? 'task_force' : 'national_executive')
            ->where('position', $data['position'])
            ->where('status', '!=', 'rejected')
            ->exists();

        if ($duplicate) {
            return back()->withInput()->withErrors([
                'membership_id' => 'This member already holds this appointment.',
            ]);
        }

        MembershipOfficerAppointment::create([
            'membership_id'    => $data['membership_id'],
            'appointment_type' => $data['type'] === 'task_force' ? 'task_force' : 'national_executive',
            'executive_id'     => $data['type'] === 'national_executive' ? $data['executive_id'] : null,
            'taskforce_id'     => $data['type'] === 'task_force' ? ($data['taskforce_id'] ?? $this->generateTaskForceId()) : null,
            'position' => $data['type'] === 'national_executive'
                ? (NationalExecutivePosition::where('code', $data['executive_id'])->value('position') ?? '—')
                : $data['position'],
            'level'            => $data['type'] === 'task_force' ? $data['level'] : null,
            'state'            => $data['type'] === 'task_force' && $data['level'] === 'state' ? $data['state'] : null,
            'status'           => 'approved', // super admin creates → auto-approved
            'appointed_at'     => now(),
            'approved_at'      => now(),
            'approved_by'      => Auth::id(),
        ]);

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
