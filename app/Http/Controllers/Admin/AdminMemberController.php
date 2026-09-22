<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipCategory;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Throwable;

class AdminMemberController extends Controller
{
    /**
     * List the member onboarding pipeline.
     *
     * Four distinct stages:
     *   1. Unpaid members (no profile, unpaid debit)
     *   2. Paid members without profile
     *   3. Paid members with submitted profile
     *   4. Paid members with draft profile (started, not submitted)
     */
    public function index(): View
    {
        /*
        |--------------------------------------------------------------------------
        | LIST 1 — UNPAID MEMBERS
        |--------------------------------------------------------------------------
        |
        | No profile row yet, AND still have an unpaid membership debit.
        | payment_item_id = NULL identifies the membership debit.
        |
        */

        $newMembers = User::query()
            ->where('role', 'member')
            ->doesntHave('profile')
            ->whereHas('transactions', function ($query) {
                $query->where('type', 'debit')
                    ->where('status', 'not paid')
                    ->whereNull('payment_item_id');
            })
            ->with('membershipCategory')
            ->orderByDesc('created_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | LIST 2 — PAID MEMBERS WITHOUT PROFILE
        |--------------------------------------------------------------------------
        |
        | Membership debit paid, but no member_profiles row yet.
        | Admin can click [Complete Profile].
        |
        */

        $paidMembers = User::query()
            ->where('role', 'member')
            ->doesntHave('profile')
            ->whereHas('transactions', function ($query) {
                $query->where('type', 'debit')
                    ->where('status', 'paid')
                    ->whereNull('payment_item_id');
            })
            ->with('membershipCategory')
            ->orderByDesc('created_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | LIST 3 — PAID MEMBERS WITH SUBMITTED PROFILE
        |--------------------------------------------------------------------------
        |
        | Membership debit paid, AND a member_profiles row exists
        | with status = 'submitted' (waiting for admin review).
        |
        */

        $submittedMembers = User::query()
            ->where('role', 'member')
            ->whereHas('profile', function ($query) {
                $query->where('status', 'submitted');
            })
            ->with(['membershipCategory', 'profile'])
            ->orderByDesc('updated_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | LIST 4 — PAID MEMBERS WITH DRAFT PROFILE (NOT SUBMITTED YET)
        |--------------------------------------------------------------------------
        |
        | Membership debit paid, a member_profiles row exists, but the
        | member (or admin) has NOT submitted it yet → status = 'draft'.
        |
        | The admin can resume editing by clicking [Continue Profile].
        |
        */

        $draftMembers = User::query()
            ->where('role', 'member')
            ->whereHas('profile', function ($query) {
                $query->where('status', 'draft');
            })
            ->whereHas('transactions', function ($query) {
                $query->where('type', 'debit')
                    ->where('status', 'paid')
                    ->whereNull('payment_item_id');
            })
            ->with(['membershipCategory', 'profile'])
            ->orderByDesc('updated_at')
            ->get();

        return view(
            'admin.member.index',
            compact(
                'newMembers',
                'paidMembers',
                'submittedMembers',
                'draftMembers'
            )
        );
    }

    /**
     * Show the admin "create member" form.
     * Uses the exact same category query as the frontend register page.
     */
    public function create(): View
    {
        $categories = MembershipCategory::query()
            ->where('status', true)
            ->with(['fees' => fn ($q) => $q->where('status', true)])
            ->orderBy('name')
            ->get();

        return view('admin.member.create', compact('categories'));
    }

    /**
     * Store a newly created member account.
     *
     * Mirrors RegisteredUserController@store EXACTLY:
     *   - creates user
     *   - creates membership debit (unpaid)
     *   - does NOT create a member_profiles row
     *   - NO email verification / OTP
     */
    public function store(
        Request $request,
        TransactionService $transactionService
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | VALIDATE — identical to frontend
        |--------------------------------------------------------------------------
        */
        $validated = $request->validate([
            'member_type'            => ['required', 'string', 'in:regular,affiliate'],
            'membership_category_id' => ['required', 'integer', 'exists:membership_categories,id'],

            'username' => ['nullable', 'string', 'min:3', 'max:50', 'unique:users,username'],
            'phone'    => ['nullable', 'string', 'max:30', 'unique:users,phone'],
            'email'    => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],

            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | NORMALIZE
        |--------------------------------------------------------------------------
        */
        $username = !empty($validated['username']) ? strtolower(trim($validated['username'])) : null;
        $email    = !empty($validated['email'])    ? strtolower(trim($validated['email']))    : null;
        $phone    = !empty($validated['phone'])    ? trim($validated['phone'])                : null;

        /*
        |--------------------------------------------------------------------------
        | AT LEAST ONE LOGIN IDENTIFIER
        |--------------------------------------------------------------------------
        */
        if (empty($username) && empty($email) && empty($phone)) {
            return back()->withInput()->withErrors([
                'email' => 'Please provide at least an email address, username, or phone number.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | RE-CHECK UNIQUENESS (post-normalization)
        |--------------------------------------------------------------------------
        */
        if ($username && User::where('username', $username)->exists()) {
            return back()->withInput()->withErrors(['username' => 'This username is already taken.']);
        }
        if ($email && User::where('email', $email)->exists()) {
            return back()->withInput()->withErrors(['email' => 'This email address is already registered.']);
        }
        if ($phone && User::where('phone', $phone)->exists()) {
            return back()->withInput()->withErrors(['phone' => 'This phone number is already registered.']);
        }

        /*
        |--------------------------------------------------------------------------
        | FETCH CATEGORY
        |--------------------------------------------------------------------------
        */
        $category = MembershipCategory::query()
            ->where('id', $validated['membership_category_id'])
            ->where('status', true)
            ->with(['fees' => fn ($q) => $q->where('status', true)])
            ->first();

        if (!$category) {
            return back()->withInput()->withErrors([
                'membership_category_id' => 'The selected membership category is not available.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CATEGORY <-> MEMBER TYPE MATCH
        |--------------------------------------------------------------------------
        */
        if (!empty($category->member_type) && $category->member_type !== $validated['member_type']) {
            return back()->withInput()->withErrors([
                'membership_category_id' => 'The selected membership category is not available for the selected member type.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | AFFILIATE RESTRICTION (RCG only)
        |--------------------------------------------------------------------------
        */
        if (
            $validated['member_type'] === 'affiliate' &&
            strtoupper($category->code ?? '') !== 'RCG'
        ) {
            return back()->withInput()->withErrors([
                'membership_category_id' => 'Affiliate members can only register under the RCG membership category.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE USER + MEMBERSHIP DEBIT (identical to frontend)
        |--------------------------------------------------------------------------
        */
        try {
            DB::transaction(function () use (
                $validated,
                $username,
                $email,
                $phone,
                $category,
                $transactionService
            ) {
                $user = User::create([
                    'name'                   => null,
                    'username'               => $username,
                    'email'                  => $email,
                    'phone'                  => $phone,
                    'password'               => Hash::make($validated['password']),
                    'member_type'            => $validated['member_type'],
                    'membership_category_id' => $category->id,
                    'role'                   => 'member',
                    'user_type'              => 'member',
                    'status'                 => 1,
                ]);

                // false = NOT PAID
                $transactionService->createMembershipDebit(
                    $user->id,
                    $category->id,
                    false
                );
            });
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->withErrors([
                'email' => 'Member account could not be created. Please try again.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | REDIRECT — back to the pipeline listing
        |--------------------------------------------------------------------------
        */
        return redirect()
            ->route('admin.member.index')
            ->with('success', 'Member account created successfully. They can now log in to complete payment and profile.');
    }

    /**
     * Prepare an on-behalf membership payment for a member who
     * has no profile yet.
     *
     * Flow:
     *   1. Ensure an unpaid membership debit exists for the member.
     *   2. Set a session flag telling PaymentController who we're
     *      paying for.
     *   3. Redirect to the standard member payment page.
     */
    public function pay(User $member, TransactionService $transactionService)
    {
        // ---------------------------------------------
        // SAFETY: only members
        // ---------------------------------------------
        if ($member->role !== 'member') {
            abort(404);
        }

        // ---------------------------------------------
        // SAFETY: only members WITHOUT a profile
        // ---------------------------------------------
        if ($member->profile()->exists()) {
            return redirect()
                ->route('admin.member.index')
                ->with('error', 'This member has already completed their profile.');
        }

        // ---------------------------------------------
        // SAFETY: the member must have a membership category
        // ---------------------------------------------
        if (!$member->membership_category_id) {
            return redirect()
                ->route('admin.member.index')
                ->with('error', 'This member has no membership category assigned.');
        }

        // ---------------------------------------------
        // Ensure an unpaid membership debit exists.
        // ---------------------------------------------
        $debit = Transaction::where('user_id', $member->id)
            ->where('type', 'debit')
            ->where('status', 'not paid')
            ->whereNull('payment_item_id')
            ->latest()
            ->first();

        if (!$debit) {
            // Create it exactly like the frontend registration does.
            $transactionService->createMembershipDebit(
                $member->id,
                $member->membership_category_id,
                false   // false = new membership (not renewal)
            );
        }

        // ---------------------------------------------
        // Set a session flag so PaymentController knows
        // the admin is paying on behalf of this member.
        // ---------------------------------------------
        session([
            'admin_paying_for_member_id' => $member->id,
            'admin_paying_return_url'    => route('admin.member.index'),
        ]);

        // ---------------------------------------------
        // Send the admin to the same payment page a
        // member would see after login.
        // ---------------------------------------------
        return redirect()
            ->route('payment.index')
            ->with('info', "You are paying the annual membership fee on behalf of {$member->username}.");
    }

    /**
     * Prepare an on-behalf profile completion OR resumption for a member
     * who has paid the membership fee.
     *
     * Behaviour:
     *   - No profile row yet         → start fresh
     *   - Profile exists (draft)     → resume editing
     *   - Profile submitted/approved → reject
     */
    public function completeProfile(User $member)
    {
        /*
        |--------------------------------------------------------------------------
        | SAFETY: only members
        |--------------------------------------------------------------------------
        */

        if ($member->role !== 'member') {
            abort(404);
        }

        /*
        |--------------------------------------------------------------------------
        | SAFETY: must have paid the membership debit
        |--------------------------------------------------------------------------
        */

        $paidDebit = Transaction::where('user_id', $member->id)
            ->where('type', 'debit')
            ->where('status', 'paid')
            ->whereNull('payment_item_id')
            ->latest()
            ->first();

        if (!$paidDebit) {
            return redirect()
                ->route('admin.member.index')
                ->with('error', 'This member has not paid their membership fee yet.');
        }

        /*
        |--------------------------------------------------------------------------
        | SAFETY: if a profile exists, only allow resuming drafts
        |--------------------------------------------------------------------------
        */

        $existingProfile = $member->profile;

        if (
            $existingProfile &&
            in_array($existingProfile->status, ['submitted', 'approved', 'rejected'], true)
        ) {
            return redirect()
                ->route('admin.member.index')
                ->with('error', 'This profile has already been submitted and cannot be edited from here.');
        }

        /*
        |--------------------------------------------------------------------------
        | Set a session flag so MemberProfileController knows
        | the admin is filling the profile on behalf of this member.
        |--------------------------------------------------------------------------
        */

        session([
            'admin_filling_profile_for_member_id' => $member->id,
            'admin_filling_profile_return_url'    => route('admin.member.index'),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Redirect to the shared /profile route.
        | The controller will detect the session flag and render the
        | admin-layout Blade.
        |--------------------------------------------------------------------------
        */

        $message = $existingProfile
            ? "Resuming the draft profile on behalf of {$member->username}."
            : "You are completing the profile on behalf of {$member->username}.";

        return redirect()
            ->route('member.profile')
            ->with('info', $message);
    }
}
