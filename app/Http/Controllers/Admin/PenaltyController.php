<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberPenalty;
use App\Models\Penalty;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenaltyController extends Controller
{
    public function __construct()
    {
        abort_unless(
            Auth::check() && Auth::user()->role === 'super_admin',
            403,
            'Only a Super Admin can access penalties.'
        );
    }

    /* =========================================================
       CATALOG
    ========================================================= */

    public function index()
    {
        $penalties = Penalty::with('paymentItem')->orderByDesc('id')->get();
        return view('admin.penalties.index', compact('penalties'));
    }

    public function create()
    {
        return view('admin.penalties.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'amount'           => ['required', 'numeric', 'min:0'],
            'action'           => ['required', 'string', 'max:255'],
            'status'           => ['nullable', 'boolean'],
            'is_custom_amount' => ['nullable', 'boolean'],
        ]);

        $data['status']           = $request->boolean('status', true);
        $data['is_custom_amount'] = $request->boolean('is_custom_amount', false);

        Penalty::create($data);

        return redirect()->route('admin.penalties.index')
            ->with('success', 'Penalty created successfully.');
    }

    public function edit(Penalty $penalty)
    {
        return view('admin.penalties.edit', compact('penalty'));
    }

    public function update(Request $request, Penalty $penalty)
    {
        $data = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'amount'           => ['required', 'numeric', 'min:0'],
            'action'           => ['required', 'string', 'max:255'],
            'status'           => ['nullable', 'boolean'],
            'is_custom_amount' => ['nullable', 'boolean'],
        ]);

        $data['status']           = $request->boolean('status', true);
        $data['is_custom_amount'] = $request->boolean('is_custom_amount', false);

        $penalty->update($data);

        return redirect()->route('admin.penalties.index')
            ->with('success', 'Penalty updated successfully.');
    }

    public function destroy(Penalty $penalty)
    {
        $penalty->delete();
        return back()->with('success', 'Penalty deleted.');
    }

    /* =========================================================
       ASSIGNMENTS
    ========================================================= */

    public function assignments(Request $request)
    {
        $query = MemberPenalty::with([
            'penalty',
            'user',
            'assignedBy',
            'generatedDocument',
        ])->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        $assignments = $query->paginate(20)->withQueryString();

        return view('admin.penalties.assignments', compact('assignments'));
    }

    public function assignForm(Request $request)
    {
        $member = $request->filled('user_id')
            ? User::find($request->user_id)
            : null;

        $catalog = Penalty::where('status', 1)->orderBy('name')->get();

        $members = User::where('role', 'member')
            ->where('status', 1)
            ->whereHas('profile', fn($q) => $q->where('status', 'approved'))
            ->orderBy('username')
            ->get();

        return view('admin.penalties.assign', compact('catalog', 'member', 'members'));
    }

    public function assignStore(Request $request, TransactionService $transactionService)
    {
        $request->validate([
            'penalty_id'           => ['required', 'exists:penalties,id'],
            'user_id'              => ['nullable', 'exists:users,id'],
            'non_member_name'      => ['nullable', 'string', 'max:255'],
            'non_member_phone'     => ['nullable', 'string', 'max:30'],
            'amount'               => ['nullable', 'numeric', 'min:0'],
            'notes'                => ['nullable', 'string'],
            'include_admin_charge' => ['nullable', 'boolean'],
        ]);

        if (!$request->filled('user_id') && !$request->filled('non_member_name')) {
            return back()
                ->withErrors(['non_member_name' => 'Provide either a member or a non-member name.'])
                ->withInput();
        }

        $catalog = Penalty::findOrFail($request->penalty_id);

        DB::transaction(function () use ($request, $catalog, $transactionService) {

            $amount = $request->filled('amount')
                ? (float) $request->amount
                : (float) $catalog->amount;

            $assignment = MemberPenalty::create([
                'penalty_id'       => $catalog->id,
                'user_id'          => $request->user_id ?: null,
                'non_member_name'  => $request->non_member_name,
                'non_member_phone' => $request->non_member_phone,
                'name'             => $catalog->name,
                'amount'           => $amount,
                'action'           => $catalog->action,
                'payment_status'   => 'unpaid',
                'notes'            => $request->notes,
                'assigned_by'      => Auth::id(),
            ]);

            if ($assignment->user_id) {
                $debit = $transactionService->createPenaltyDebit($assignment);
                $assignment->update(['debit_transaction_id' => $debit->id]);
            }

            // Optional §6a — additional administrative charge
            if ($request->boolean('include_admin_charge') && $assignment->user_id) {

                $adminCharge = Penalty::where('name', 'like', 'Additional Administrative Charge%')
                    ->where('status', 1)
                    ->first();

                if ($adminCharge) {
                    $charge = MemberPenalty::create([
                        'penalty_id'       => $adminCharge->id,
                        'user_id'          => $assignment->user_id,
                        'name'             => $adminCharge->name,
                        'amount'           => $adminCharge->amount,
                        'action'           => $adminCharge->action,
                        'payment_status'   => 'unpaid',
                        'assigned_by'      => Auth::id(),
                    ]);

                    $debit = $transactionService->createPenaltyDebit($charge);
                    $charge->update(['debit_transaction_id' => $debit->id]);
                }
            }
        });

        return redirect()->route('admin.penalties.assignments')
            ->with('success', 'Penalty assigned successfully.');
    }

    public function markPaid(MemberPenalty $memberPenalty)
    {
        $memberPenalty->update([
            'payment_status' => 'paid',
            'paid_at'        => now(),
        ]);

        return back()->with('success', 'Penalty marked as paid.');
    }
}
