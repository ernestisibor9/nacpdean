<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\MemberPenalty;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Transaction;
use App\Models\User;
use App\Services\DocumentGenerationService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MemberPenaltyController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LIST UNPAID + PAID PENALTIES
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $user = Auth::user();

        $penalties = MemberPenalty::with(['penalty', 'generatedDocument'])
            ->where('user_id', $user->id)
            ->orderByRaw("FIELD(payment_status, 'unpaid', 'paid')")
            ->orderByDesc('id')
            ->get();

        $unpaidTotal = $penalties
            ->where('payment_status', 'unpaid')
            ->sum('amount');

        return view('member.penalties.index', compact('penalties', 'unpaidTotal'));
    }

    /*
    |--------------------------------------------------------------------------
    | INITIALIZE PAYMENT (Paystack)
    |--------------------------------------------------------------------------
    |
    | Member clicks "Pay Now" on ONE OR MANY unpaid penalties.
    |
    | IMPORTANT — DEBIT REUSE:
    |
    | The admin's createPenaltyDebit() already creates an unpaid debit
    | when the penalty is assigned. We MUST reuse that debit rather
    | than create a new one. Otherwise we end up with orphan debits
    | that inflate the member's outstanding balance.
    |
    */

    public function pay(Request $request, TransactionService $transactionService)
    {
        $request->validate([
            'penalty_ids'   => ['required', 'array', 'min:1'],
            'penalty_ids.*' => ['integer', 'exists:member_penalties,id'],
        ]);

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | LOAD + VALIDATE SELECTED PENALTIES
        |--------------------------------------------------------------------------
        */

        $assignments = MemberPenalty::with('penalty')
            ->whereIn('id', $request->penalty_ids)
            ->where('user_id', $user->id)
            ->where('payment_status', 'unpaid')
            ->get();

        if ($assignments->isEmpty()) {
            return back()->withErrors([
                'payment' => 'No valid unpaid penalties were selected.',
            ]);
        }

        if ($assignments->count() !== count($request->penalty_ids)) {
            return back()->withErrors([
                'payment' => 'One or more selected penalties are not available.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | TOTAL AMOUNT
        |--------------------------------------------------------------------------
        */

        $totalAmount = (float) $assignments->sum('amount');

        if ($totalAmount <= 0) {
            return back()->withErrors([
                'payment' => 'The selected penalties have an invalid total amount.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | PENALTY PAYMENT ITEM
        |--------------------------------------------------------------------------
        */

        $penaltyPaymentItem = PaymentItem::where('code', 'PENALTY')->first();

        if (!$penaltyPaymentItem) {
            return back()->withErrors([
                'payment' => 'The penalty payment item is not configured.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | UNIQUE REFERENCE
        |--------------------------------------------------------------------------
        */

        $reference = null;

        for ($i = 0; $i < 5; $i++) {
            $candidate = 'NACP-PNL-' . strtoupper(bin2hex(random_bytes(16)));

            if (!Payment::where('reference', $candidate)->exists()) {
                $reference = $candidate;
                break;
            }
        }

        if (!$reference) {
            return back()->withErrors([
                'payment' => 'Unable to generate a payment reference.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE / REUSE DEBIT + CREATE PAYMENT
        |--------------------------------------------------------------------------
        */

        try {

            $result = DB::transaction(function () use (
                $user,
                $assignments,
                $penaltyPaymentItem,
                $totalAmount,
                $reference
            ) {
                /*
                |--------------------------------------------------------------------------
                | NARRATION
                |--------------------------------------------------------------------------
                */

                $narration = $assignments->count() === 1
                    ? 'Penalty - ' . $assignments->first()->name
                    : 'Penalty Payment - ' . $assignments->count() . ' penalties';

                /*
                |--------------------------------------------------------------------------
                | REUSE EXISTING UNPAID DEBIT
                |--------------------------------------------------------------------------
                |
                | Priority order:
                |   1. A debit already linked to one of the selected MemberPenalty rows
                |   2. A debit matching payment_item_id + amount + narration
                |   3. Create a new one (only if truly absent)
                |
                */

                $linkedDebitIds = $assignments
                    ->pluck('debit_transaction_id')
                    ->filter()
                    ->values()
                    ->all();

                $debit = null;

                if (!empty($linkedDebitIds)) {
                    $debit = Transaction::where('user_id', $user->id)
                        ->where('type', 'debit')
                        ->where('status', 'not paid')
                        ->whereIn('id', $linkedDebitIds)
                        ->latest('id')
                        ->lockForUpdate()
                        ->first();
                }

                if (!$debit) {
                    $debit = Transaction::where('user_id', $user->id)
                        ->where('type', 'debit')
                        ->where('status', 'not paid')
                        ->where('payment_item_id', $penaltyPaymentItem->id)
                        ->where('amount', $totalAmount)
                        ->where('narration', $narration)
                        ->latest('id')
                        ->lockForUpdate()
                        ->first();
                }

                if ($debit) {

                    /*
                    |------------------------------------------------------------------
                    | UPDATE THE EXISTING DEBIT
                    |------------------------------------------------------------------
                    |
                    | Amount might have changed if the admin included §6a
                    | administrative charge. Refresh it here.
                    |
                    */

                    $gateway = is_array($debit->gateway) ? $debit->gateway : [];
                    $gateway['paystack'] = $reference;

                    $debit->update([
                        'amount'  => $totalAmount,
                        'gateway' => $gateway,
                    ]);

                } else {

                    /*
                    |------------------------------------------------------------------
                    | FALLBACK: CREATE A NEW DEBIT
                    |------------------------------------------------------------------
                    |
                    | Only happens if no debit exists at all (edge case —
                    | shouldn't happen because the admin's assignment path
                    | already creates one).
                    |
                    */

                    $debit = Transaction::create([
                        'user_id'              => $user->id,
                        'payment_item_id'      => $penaltyPaymentItem->id,
                        'debit_transaction_id' => null,
                        'narration'            => $narration,
                        'type'                 => 'debit',
                        'status'               => 'not paid',
                        'amount'               => $totalAmount,
                        'transaction_id'       => null,
                        'gateway'              => [
                            'paystack' => $reference,
                        ],
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | LINK EVERY ASSIGNMENT TO THE DEBIT
                |--------------------------------------------------------------------------
                */

                MemberPenalty::whereIn('id', $assignments->pluck('id'))
                    ->update([
                        'debit_transaction_id' => $debit->id,
                    ]);

                /*
                |--------------------------------------------------------------------------
                | LOCAL PAYMENT
                |--------------------------------------------------------------------------
                */

                $payment = Payment::create([
                    'user_id'                => $user->id,
                    'payment_item_id'        => $penaltyPaymentItem->id,
                    'membership_category_id' => $user->membership_category_id,
                    'payment_type'           => 'penalty',
                    'fee_type'               => 'penalty',
                    'amount'                 => $totalAmount,
                    'description'            => $narration,
                    'payment_reference'      => $reference,
                    'reference'              => $reference,
                    'gateway'                => 'paystack',
                    'gateway_status'         => 'pending',
                    'status'                 => 'pending',
                ]);

                return [
                    'debit'   => $debit,
                    'payment' => $payment,
                ];
            });

        } catch (\Throwable $e) {

            Log::error('PENALTY PAYMENT PREPARATION FAILED', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return back()->withErrors([
                'payment' => 'Unable to prepare your penalty payment. Please try again.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | PAYSTACK INITIALIZE
        |--------------------------------------------------------------------------
        */

        try {

            $paymentEmail = $user->email
                ?: 'user' . $user->id . '@nacpdean.org';

            $response = Http::withToken(config('services.paystack.secret_key'))
                ->acceptJson()
                ->post(
                    config('services.paystack.url') . '/transaction/initialize',
                    [
                        'email'        => $paymentEmail,
                        'amount'       => (int) round($totalAmount * 100),
                        'reference'    => $reference,
                        'currency'     => 'NGN',
                        'callback_url' => route('member.penalties.callback'),
                        'metadata'     => [
                            'payment_id'     => $result['payment']->id,
                            'transaction_id' => $result['debit']->id,
                            'payment_type'   => 'penalty',
                            'user_id'        => $user->id,
                            'penalty_ids'    => $assignments->pluck('id')->all(),
                        ],
                    ]
                );

            $data = $response->json();

            if (
                !$response->successful() ||
                ($data['status'] ?? false) !== true ||
                empty($data['data']['authorization_url'])
            ) {
                $result['payment']->update([
                    'status'           => 'failed',
                    'gateway_status'   => 'initialization_failed',
                    'gateway_response' => $data,
                ]);

                return back()->withErrors([
                    'payment' => $data['message'] ?? 'Unable to initialize payment with Paystack.',
                ]);
            }

            $result['payment']->update([
                'paystack_reference'         => $reference,
                'paystack_authorization_url' => $data['data']['authorization_url'],
                'gateway_status'             => 'initialized',
                'gateway_response'           => $data,
            ]);

            return redirect()->away($data['data']['authorization_url']);

        } catch (\Throwable $e) {

            Log::error('PENALTY PAYMENT PAYSTACK INIT FAILED', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            $result['payment']->update([
                'status'         => 'failed',
                'gateway_status' => 'initialization_failed',
            ]);

            return back()->withErrors([
                'payment' => 'Unable to connect to Paystack. Please try again.',
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PAYSTACK CALLBACK
    |--------------------------------------------------------------------------
    |
    | After successful payment:
    |   1. Verify with Paystack
    |   2. Mark debit paid
    |   3. Create credit
    |   4. Mark payment paid
    |   5. Mark MemberPenalty rows paid
    |   6. Generate ONE receipt covering all penalties
    |
    */

    public function callback(
        Request $request,
        DocumentGenerationService $documentService
    ) {
        $reference = $request->query('reference')
            ?? $request->input('reference');

        if (!$reference) {
            return redirect()
                ->route('member.penalties.index')
                ->with('error', 'Payment reference was not provided.');
        }

        $payment = Payment::where(function ($q) use ($reference) {
            $q->where('reference', $reference)
                ->orWhere('payment_reference', $reference)
                ->orWhere('paystack_reference', $reference);
        })
            ->where('payment_type', 'penalty')
            ->first();

        if (!$payment) {
            return redirect()
                ->route('member.penalties.index')
                ->with('error', 'The penalty payment could not be found.');
        }

        $user = Auth::user();

        if (!$user || (int) $payment->user_id !== (int) $user->id) {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | VERIFY WITH PAYSTACK
        |--------------------------------------------------------------------------
        */

        $response = Http::withToken(config('services.paystack.secret_key'))
            ->acceptJson()
            ->get(
                config('services.paystack.url')
                    . '/transaction/verify/'
                    . urlencode($reference)
            );

        if (!$response->successful()) {
            return redirect()
                ->route('member.penalties.index')
                ->with('error', 'Unable to verify the payment with Paystack.');
        }

        $paystackData = $response->json('data');

        if (
            !$response->json('status') ||
            ($paystackData['status'] ?? null) !== 'success'
        ) {
            return redirect()
                ->route('member.penalties.index')
                ->with('error', 'Payment was not successful.');
        }

        $expectedAmount = (int) round((float) $payment->amount * 100);

        if ((int) ($paystackData['amount'] ?? 0) !== $expectedAmount) {
            return redirect()
                ->route('member.penalties.index')
                ->with('error', 'Payment amount does not match.');
        }

        if (strtoupper($paystackData['currency'] ?? '') !== 'NGN') {
            return redirect()
                ->route('member.penalties.index')
                ->with('error', 'Payment currency mismatch.');
        }

        /*
        |--------------------------------------------------------------------------
        | SETTLE LEDGER
        |--------------------------------------------------------------------------
        */

        try {

            $result = DB::transaction(function () use (
                $user,
                $payment,
                $reference,
                $paystackData
            ) {
                $lockedPayment = Payment::where('id', $payment->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                /*
                |------------------------------------------------------------------
                | FIND THE DEBIT
                |------------------------------------------------------------------
                |
                | Priority:
                |   1. Linked from the payment metadata (Paystack stores it)
                |   2. Look up by user + payment_item + reference in gateway JSON
                |   3. Match by amount + status not paid
                |
                */

                $metadataDebitId =
                    $paystackData['metadata']['transaction_id'] ?? null;

                $lockedDebit = null;

                if ($metadataDebitId) {
                    $lockedDebit = Transaction::where('id', $metadataDebitId)
                        ->where('user_id', $user->id)
                        ->where('type', 'debit')
                        ->lockForUpdate()
                        ->first();
                }

                if (!$lockedDebit) {
                    $lockedDebit = Transaction::where('user_id', $user->id)
                        ->where('payment_item_id', $payment->payment_item_id)
                        ->where('type', 'debit')
                        ->whereJsonContains('gateway->paystack', $reference)
                        ->latest('id')
                        ->lockForUpdate()
                        ->first();
                }

                if (!$lockedDebit) {
                    $lockedDebit = Transaction::where('user_id', $user->id)
                        ->where('payment_item_id', $payment->payment_item_id)
                        ->where('type', 'debit')
                        ->where('amount', $payment->amount)
                        ->latest('id')
                        ->lockForUpdate()
                        ->first();
                }

                if (!$lockedDebit) {
                    throw new \RuntimeException('Penalty debit could not be found.');
                }

                /*
                |------------------------------------------------------------------
                | IDEMPOTENCY
                |------------------------------------------------------------------
                */

                if ($lockedPayment->status === 'paid' && $lockedDebit->status === 'paid') {
                    $credit = Transaction::where('debit_transaction_id', $lockedDebit->id)
                        ->where('type', 'credit')
                        ->first();

                    $receipt = \App\Models\GeneratedDocument::where('user_id', $user->id)
                        ->where('transaction_id', $credit?->id)
                        ->latest('id')
                        ->first();

                    return [
                        'payment' => $lockedPayment,
                        'debit'   => $lockedDebit,
                        'credit'  => $credit,
                        'receipt' => $receipt,
                    ];
                }

                /*
                |------------------------------------------------------------------
                | FIND OR CREATE CREDIT
                |------------------------------------------------------------------
                */

                $credit = Transaction::where('debit_transaction_id', $lockedDebit->id)
                    ->where('type', 'credit')
                    ->lockForUpdate()
                    ->first();

                if (!$credit) {
                    $credit = Transaction::create([
                        'user_id'              => $user->id,
                        'payment_item_id'      => $lockedDebit->payment_item_id,
                        'debit_transaction_id' => $lockedDebit->id,
                        'narration'            => 'Payment received - ' . $lockedDebit->narration,
                        'type'                 => 'credit',
                        'status'               => 'paid',
                        'amount'               => $lockedDebit->amount,
                        'transaction_id'       => null,
                        'gateway'              => ['paystack' => $reference],
                    ]);
                }

                /*
                |------------------------------------------------------------------
                | MARK DEBIT + PAYMENT PAID
                |------------------------------------------------------------------
                */

                $lockedDebit->update([
                    'status'  => 'paid',
                    'gateway' => ['paystack' => $reference],
                ]);

                $lockedPayment->update([
                    'status'                 => 'paid',
                    'paid_at'                => now(),
                    'verified_at'            => now(),
                    'gateway_transaction_id' => (string) ($paystackData['id'] ?? ''),
                    'gateway_status'         => 'success',
                    'gateway_response'       => $paystackData,
                ]);

                /*
                |------------------------------------------------------------------
                | MARK MEMBER PENALTIES PAID + LINK CREDIT
                |------------------------------------------------------------------
                */

                MemberPenalty::where('user_id', $user->id)
                    ->where('debit_transaction_id', $lockedDebit->id)
                    ->where('payment_status', 'unpaid')
                    ->update([
                        'payment_status'        => 'paid',
                        'paid_at'               => now(),
                        'credit_transaction_id' => $credit->id,
                    ]);

                return [
                    'payment' => $lockedPayment,
                    'debit'   => $lockedDebit,
                    'credit'  => $credit,
                    'receipt' => null,
                ];

            });

        } catch (\Throwable $e) {

            Log::error('PENALTY CALLBACK SETTLEMENT FAILED', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return redirect()
                ->route('member.penalties.index')
                ->with('error', 'Payment was received but could not be settled. Please contact support.');
        }

        /*
        |--------------------------------------------------------------------------
        | GENERATE RECEIPT (after financial settle commits)
        |--------------------------------------------------------------------------
        */

        if (empty($result['receipt']) && $result['credit']) {

            try {

                $receipt = $documentService->generate(
                    $result['payment'],
                    $result['credit']
                );

                /*
                |------------------------------------------------------------------
                | LINK RECEIPT BACK TO EVERY COVERED PENALTY
                |------------------------------------------------------------------
                */

                MemberPenalty::where('user_id', $user->id)
                    ->where('credit_transaction_id', $result['credit']->id)
                    ->update([
                        'generated_document_id' => $receipt->id,
                    ]);

                $result['receipt'] = $receipt;

            } catch (\Throwable $e) {

                Log::error('PENALTY RECEIPT GENERATION FAILED', [
                    'user_id'    => $user->id,
                    'payment_id' => $result['payment']->id,
                    'credit_id'  => $result['credit']->id,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        if (!empty($result['receipt'])) {
            return redirect()
                ->route('member.documents.show', $result['receipt']->id)
                ->with('success', 'Penalty payment successful. Your receipt is ready.');
        }

        return redirect()
            ->route('member.penalties.index')
            ->with('success', 'Penalty payment successful.');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW SINGLE RECEIPT
    |--------------------------------------------------------------------------
    */

    public function receipt(MemberPenalty $memberPenalty)
    {
        abort_unless($memberPenalty->user_id === Auth::id(), 403);

        if (!$memberPenalty->generated_document_id) {
            return redirect()
                ->route('member.penalties.index')
                ->with('error', 'No receipt is available for this penalty.');
        }

        return redirect()->route(
            'member.documents.show',
            $memberPenalty->generated_document_id
        );
    }
}
