<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Penalty;
use App\Models\MemberPenalty;
use App\Models\PaymentItem;
use App\Models\Payment;
use App\Models\Transaction;
use App\Services\DocumentGenerationService;
use Illuminate\Support\Str;

echo "=== PENALTY RECEIPT PIPELINE TEST ===\n\n";

// ============================================================
// 1. FIND A USABLE MEMBER
// ============================================================
// Must have:
//   role = member
//   membership_category_id NOT NULL (required by payments table)
// Optionally has:
//   an approved profile

echo "Searching for a usable member...\n\n";

$user = User::where('role', 'member')
    ->whereNotNull('membership_category_id')
    ->whereHas('profile', fn ($q) => $q->where('status', 'approved'))
    ->first();

// Fallback 1: any member with a category (approved profile not required for the test)
if (!$user) {
    echo "No member with an approved profile found — trying any member with a category...\n";
    $user = User::where('role', 'member')
        ->whereNotNull('membership_category_id')
        ->first();
}

// Fallback 2: no usable member — print everything so we can diagnose
if (!$user) {
    echo "=== NO USABLE MEMBER FOUND ===\n\n";
    echo "All 'member' users in the DB:\n\n";

    $allMembers = User::where('role', 'member')->get();

    if ($allMembers->isEmpty()) {
        echo "  (none)\n";
    } else {
        printf("  %-5s %-25s %-30s %-10s %s\n", 'ID', 'USERNAME', 'EMAIL', 'STATUS', 'CATEGORY_ID');
        echo str_repeat('-', 90) . "\n";

        foreach ($allMembers as $u) {
            printf(
                "  %-5d %-25s %-30s %-10s %s\n",
                $u->id,
                $u->username ?? '(none)',
                $u->email    ?? '(none)',
                $u->status   ?? '-',
                $u->membership_category_id ?? 'NULL'
            );
        }
    }

    die("\nFix one of these members (set membership_category_id) or create a new one.\n");
}

echo "Member picked:\n";
echo "  id                     : {$user->id}\n";
echo "  username               : " . ($user->username ?? '(empty)') . "\n";
echo "  email                  : " . ($user->email ?? '(empty)') . "\n";
echo "  membership_category_id : {$user->membership_category_id}\n";
echo "\n";

// ============================================================
// 2. PENALTY PAYMENT ITEM
// ============================================================

$penaltyItem = PaymentItem::where('code', 'PENALTY')->first();
echo "Payment Item: {$penaltyItem?->id}\n";

if (!$penaltyItem) {
    die("PENALTY payment item not found. Run: php artisan db:seed --class=PenaltySeeder\n");
}

// ============================================================
// 3. MEMBER PENALTY
// ============================================================

$penalty = Penalty::where('status', 1)->first();

if (!$penalty) {
    die("No active penalty in catalog. Run: php artisan db:seed --class=PenaltySeeder\n");
}

$assignment = MemberPenalty::create([
    'penalty_id'     => $penalty->id,
    'user_id'        => $user->id,
    'name'           => 'Test — Operating Without Operational Right',
    'amount'         => 100000,
    'action'         => 'Pay penalty + renew operational membership',
    'payment_status' => 'unpaid',
    'assigned_by'    => $user->id,
]);

echo "Assignment: {$assignment->id}\n";

// ============================================================
// 4. DEBIT
// ============================================================

$debit = Transaction::create([
    'user_id'         => $user->id,
    'payment_item_id' => $penaltyItem->id,
    'narration'       => 'Penalty - ' . $assignment->name,
    'type'            => 'debit',
    'status'          => 'not paid',
    'amount'          => 100000,
]);

$assignment->update(['debit_transaction_id' => $debit->id]);
echo "Debit: {$debit->id}\n";

// ============================================================
// 5. SIMULATE PAYMENT — DEBIT PAID + CREDIT CREATED
// ============================================================

$debit->update(['status' => 'paid']);

$credit = Transaction::create([
    'user_id'              => $user->id,
    'payment_item_id'      => $penaltyItem->id,
    'debit_transaction_id' => $debit->id,
    'narration'            => 'Payment received - ' . $assignment->name,
    'type'                 => 'credit',
    'status'               => 'paid',
    'amount'               => 100000,
]);

echo "Credit: {$credit->id}\n";

// ============================================================
// 6. PAYMENT ROW
// ============================================================
// payments table has several NOT NULL columns without defaults:
//   user_id, payment_item_id, membership_category_id,
//   payment_type, fee_type, amount, description, reference, status

$payment = Payment::create([
    'user_id'                => $user->id,
    'payment_item_id'        => $penaltyItem->id,
    'membership_category_id' => $user->membership_category_id,
    'payment_type'           => 'penalty',
    'fee_type'               => 'penalty',
    'amount'                 => 100000,
    'description'            => $assignment->name,
    'reference'              => 'NACP-TEST-' . Str::random(12),
    'status'                 => 'paid',
    'paid_at'                => now(),
]);

echo "Payment: {$payment->id}\n";

// ============================================================
// 7. LINK ASSIGNMENT TO CREDIT
// ============================================================

$assignment->update([
    'credit_transaction_id' => $credit->id,
    'payment_status'        => 'paid',
    'paid_at'               => now(),
]);

// ============================================================
// 8. GENERATE THE RECEIPT
// ============================================================

$service = app(DocumentGenerationService::class);

try {
    $generated = $service->generate($payment, $credit);

    echo "\n=== SUCCESS ===\n";
    echo "Generated Document ID : {$generated->id}\n";
    echo "Document Number       : {$generated->document_number}\n";
    echo "Tracking Code         : {$generated->tracking_code}\n";
    echo "Issued At             : {$generated->issued_at}\n";
    echo "Status                : {$generated->status}\n";

    echo "\nField Values:\n";
    foreach (($generated->field_values ?? []) as $key => $value) {
        if (is_array($value)) {
            echo "  {$key} => [" . implode(', ', $value) . "]\n";
        } else {
            echo "  {$key} => " . var_export($value, true) . "\n";
        }
    }

    echo "\nView receipt at:\n";
    echo "  http://localhost/member/documents/{$generated->id}\n";

} catch (\Throwable $e) {
    echo "\n=== ERROR ===\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File:    " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nTrace:\n" . $e->getTraceAsString() . "\n";
}
