<?php

namespace App\Services;

use App\Models\Document;
use App\Models\GeneratedDocument;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class DocumentGenerationService
{
    /**
     * Generate a document from a successful additional payment.
     *
     * The payment MUST already be marked as paid.
     *
     * The $credit transaction is the exact CREDIT transaction
     * created by the payment callback.
     */
    public function generate(
        User $user,
        Payment $payment,
        Transaction $credit,
        array $documentFieldValues = []
    ): ?GeneratedDocument {

        /*
        |--------------------------------------------------------------------------
        | 1. Payment must be paid
        |--------------------------------------------------------------------------
        */

        if ($payment->status !== 'paid') {
            throw new RuntimeException(
                'Document cannot be generated because the payment is not marked as paid.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Verify payment ownership
        |--------------------------------------------------------------------------
        */

        if ((int) $payment->user_id !== (int) $user->id) {
            throw new RuntimeException(
                'The payment does not belong to this member.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Verify CREDIT transaction
        |--------------------------------------------------------------------------
        */

        if ((int) $credit->user_id !== (int) $user->id) {
            throw new RuntimeException(
                'The credit transaction does not belong to this member.'
            );
        }

        if ($credit->type !== 'credit') {
            throw new RuntimeException(
                'The supplied transaction is not a credit transaction.'
            );
        }

        if ($credit->status !== 'paid') {
            throw new RuntimeException(
                'The credit transaction is not marked as paid.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Load everything required
        |--------------------------------------------------------------------------
        */

        $user->loadMissing([
            'profile',
            'membership',
        ]);

        $payment->loadMissing([
            'paymentItem.document.fields',
        ]);

        $paymentItem = $payment->paymentItem;

        if (!$paymentItem) {
            throw new RuntimeException(
                'The payment item associated with this payment could not be found.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Get attached document
        |--------------------------------------------------------------------------
        */

        $document = $paymentItem->document;

        /*
        |--------------------------------------------------------------------------
        | 6. Payment items are allowed to have no document
        |--------------------------------------------------------------------------
        */

        if (!$document) {
            Log::info(
                'DOCUMENT GENERATION SKIPPED - NO DOCUMENT ATTACHED',
                [
                    'user_id' => $user->id,
                    'payment_id' => $payment->id,
                    'payment_item_id' => $paymentItem->id,
                    'credit_transaction_id' => $credit->id,
                ]
            );

            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | 7. Document must be active
        |--------------------------------------------------------------------------
        */

        if (!$document->is_active) {
            throw new RuntimeException(
                "The document [{$document->code}] is not active."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 8. Verify CREDIT belongs to this payment item
        |--------------------------------------------------------------------------
        */

        if (
            $credit->payment_item_id !== null &&
            (int) $credit->payment_item_id !== (int) $paymentItem->id
        ) {
            throw new RuntimeException(
                'The credit transaction does not belong to this payment item.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 9. Find the exact DEBIT
        |--------------------------------------------------------------------------
        */

        $debitTransactionId = $credit->debit_transaction_id;

        /*
        |--------------------------------------------------------------------------
        | Compatibility with current transaction structure
        |--------------------------------------------------------------------------
        */

        if (!$debitTransactionId && $credit->transaction_id) {
            $debitTransactionId = $credit->transaction_id;
        }

        if (!$debitTransactionId) {
            throw new RuntimeException(
                'The credit transaction is not linked to a debit transaction.'
            );
        }

        $debit = Transaction::query()
            ->where('id', $debitTransactionId)
            ->where('type', 'debit')
            ->first();

        if (!$debit) {
            throw new RuntimeException(
                'The debit transaction associated with this payment could not be found.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 10. Verify DEBIT ownership
        |--------------------------------------------------------------------------
        */

        if ((int) $debit->user_id !== (int) $user->id) {
            throw new RuntimeException(
                'The debit transaction does not belong to this member.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 11. Verify DEBIT payment item
        |--------------------------------------------------------------------------
        */

        if (
            $debit->payment_item_id !== null &&
            (int) $debit->payment_item_id !== (int) $paymentItem->id
        ) {
            throw new RuntimeException(
                'The debit transaction does not belong to this payment item.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 12. Prevent duplicate document generation
        |--------------------------------------------------------------------------
        */

        $existingDocument = GeneratedDocument::query()
            ->where('user_id', $user->id)
            ->where('document_id', $document->id)
            ->where('transaction_id', $credit->id)
            ->first();

        if ($existingDocument) {
            Log::info(
                'DOCUMENT GENERATION SKIPPED - ALREADY EXISTS',
                [
                    'generated_document_id' => $existingDocument->id,
                    'user_id' => $user->id,
                    'document_id' => $document->id,
                    'credit_transaction_id' => $credit->id,
                ]
            );

            return $existingDocument;
        }

        /*
        |--------------------------------------------------------------------------
        | 13. Generate document number
        |--------------------------------------------------------------------------
        */

        $documentNumber = $this->generateDocumentNumber($document);

        /*
        |--------------------------------------------------------------------------
        | 14. Generate verification tracking code
        |--------------------------------------------------------------------------
        */

        $trackingCode = $this->generateTrackingCode();

        /*
        |--------------------------------------------------------------------------
        | 15. Build document field values
        |--------------------------------------------------------------------------
        */

        $fieldValues = [];

        foreach ($document->fields as $field) {

            /*
            |--------------------------------------------------------------------------
            | SYSTEM FIELD
            |--------------------------------------------------------------------------
            */

            if ($field->is_system) {
                $fieldValues[$field->field_key] =
                    $this->resolveSystemField(
                        $field->field_key,
                        $user,
                        $payment,
                        $paymentItem,
                        $document,
                        $credit,
                        $debit,
                        $trackingCode,
                        $documentNumber
                    );

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | MANUAL FIELD
            |--------------------------------------------------------------------------
            |
            | initializeAdditional() has already validated these values.
            |
            */

            if (
                array_key_exists(
                    $field->field_key,
                    $documentFieldValues
                )
            ) {
                $fieldValues[$field->field_key] =
                    $documentFieldValues[$field->field_key];

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | DEFAULT VALUE
            |--------------------------------------------------------------------------
            */

            if ($field->default_value !== null) {
                $fieldValues[$field->field_key] =
                    $field->default_value;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 16. Issue date
        |--------------------------------------------------------------------------
        */

        $issuedAt = $payment->paid_at
            ? Carbon::parse($payment->paid_at)
            : now();

        /*
        |--------------------------------------------------------------------------
        | 17. Expiry date
        |--------------------------------------------------------------------------
        */

        $expiresAt =
            $this->calculateExpiryDate(
                $document,
                $issuedAt
            );

        /*
        |--------------------------------------------------------------------------
        | 18. Create generated document
        |--------------------------------------------------------------------------
        */

        $generatedDocument = GeneratedDocument::create([
            'user_id' => $user->id,

            'document_id' => $document->id,

            'transaction_id' => $credit->id,

            'document_number' => $documentNumber,

            'tracking_code' => $trackingCode,

            'issued_at' => $issuedAt->toDateString(),

            'expires_at' => $expiresAt?->toDateString(),

            'status' => 'active',

            'field_values' => $fieldValues,
        ]);

        /*
|--------------------------------------------------------------------------
| If this is a renewal, mark the old document as replaced.
|--------------------------------------------------------------------------
|
| Payment::renewal_document_id points to the expired document
| that this new document is replacing.
|
*/

        if ($payment->renewal_document_id) {

            $oldGeneratedDocument = GeneratedDocument::query()
                ->where('id', $payment->renewal_document_id)
                ->where('user_id', $user->id)
                ->first();

            if ($oldGeneratedDocument) {

                $oldGeneratedDocument->update([
                    'replaced_by_document_id' => $generatedDocument->id,
                ]);

                Log::info(
                    'OLD DOCUMENT MARKED AS REPLACED',
                    [
                        'old_generated_document_id' =>
                        $oldGeneratedDocument->id,

                        'new_generated_document_id' =>
                        $generatedDocument->id,

                        'user_id' =>
                        $user->id,

                        'payment_id' =>
                        $payment->id,
                    ]
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 19. Log successful generation
        |--------------------------------------------------------------------------
        */

        Log::info(
            'DOCUMENT GENERATED SUCCESSFULLY',
            [
                'generated_document_id' =>
                $generatedDocument->id,

                'document_id' =>
                $document->id,

                'document_code' =>
                $document->code,

                'document_number' =>
                $generatedDocument->document_number,

                'tracking_code' =>
                $generatedDocument->tracking_code,

                'user_id' =>
                $user->id,

                'payment_id' =>
                $payment->id,

                'credit_transaction_id' =>
                $credit->id,

                'debit_transaction_id' =>
                $debit->id,
            ]
        );

        return $generatedDocument;
    }


    /*
    |--------------------------------------------------------------------------
    | SYSTEM FIELD RESOLVER
    |--------------------------------------------------------------------------
    */

    protected function resolveSystemField(
        string $fieldKey,
        User $user,
        Payment $payment,
        $paymentItem,
        $document,
        Transaction $credit,
        Transaction $debit,
        string $trackingCode,
        string $documentNumber
    ) {

        $profile = $user->profile;

        $membership = $user->membership;

        /*
        |--------------------------------------------------------------------------
        | Payment date/time
        |--------------------------------------------------------------------------
        */

        $paymentDateTime = $payment->paid_at
            ? Carbon::parse($payment->paid_at)
            : now();

        return match ($fieldKey) {

            /*
            |--------------------------------------------------------------------------
            | DOCUMENT #35 - AFFORESTATION RECEIPT
            |--------------------------------------------------------------------------
            */

            'receipt_no' =>
            $documentNumber,

            'seller_member_name' =>
            $this->getMemberFullName(
                $profile,
                $user
            ),

            'seller_membership_no' =>
            $membership?->membership_number,

            /*
            |--------------------------------------------------------------------------
            | No dealing_right_number column currently exists
            | in member_profiles.
            |
            | This field is optional on Document #35.
            |--------------------------------------------------------------------------
            */

            'seller_dealing_right_no' =>
            null,

            'seller_phone' =>
            $profile?->phone
                ?? $user->phone,

            /*
            |--------------------------------------------------------------------------
            | USER
            |--------------------------------------------------------------------------
            */

            'user_id' =>
            $user->id,

            'name' =>
            $user->name,

            'member_name' =>
            $this->getMemberFullName(
                $profile,
                $user
            ),

            'email' =>
            $user->email,

            /*
            |--------------------------------------------------------------------------
            | MEMBER PROFILE
            |--------------------------------------------------------------------------
            */

            'surname' =>
            $profile?->surname,

            'first_name' =>
            $profile?->first_name,

            'middle_name' =>
            $profile?->middle_name,

            'phone' =>
            $profile?->phone
                ?? $user->phone,

            'photo' =>
            $profile?->photo,

            'date_of_birth' =>
            $profile?->date_of_birth
                ? Carbon::parse(
                    $profile->date_of_birth
                )->format('Y-m-d')
                : null,

            'gender' =>
            $profile?->gender,

            'nationality' =>
            $profile?->nationality,

            'address' =>
            $profile?->address,

            'city' =>
            $profile?->city,

            'state' =>
            $profile?->state,

            'lga' =>
            $profile?->lga,

            /*
            |--------------------------------------------------------------------------
            | BUSINESS
            |--------------------------------------------------------------------------
            */

            'business_name' =>
            $profile?->business_name,

            'business_registration_number' =>
            $profile?->business_registration_number,

            'business_type' =>
            $profile?->business_type,

            'business_address' =>
            $profile?->business_address,

            /*
            |--------------------------------------------------------------------------
            | MEMBERSHIP
            |--------------------------------------------------------------------------
            */

            'membership_number' =>
            $membership?->membership_number,

            'membership_no' =>
            $membership?->membership_number,

            'membership_status' =>
            $membership?->status,

            'membership_issued_at' =>
            $membership?->issued_at
                ? Carbon::parse(
                    $membership->issued_at
                )->format('Y-m-d')
                : null,

            'membership_expires_at' =>
            $membership?->expires_at
                ? Carbon::parse(
                    $membership->expires_at
                )->format('Y-m-d')
                : null,

            /*
            |--------------------------------------------------------------------------
            | PAYMENT
            |--------------------------------------------------------------------------
            */

            'payment_id' =>
            $payment->id,

            'payment_item_id' =>
            $paymentItem->id,

            'payment_item_code' =>
            $paymentItem->code,

            'payment_item_name' =>
            $paymentItem->name,

            'amount' =>
            $payment->amount,

            'amount_paid' =>
            $payment->amount,

            /*
            |--------------------------------------------------------------------------
            | Amount in words
            |--------------------------------------------------------------------------
            */

            'amount_in_figure' =>
            $this->numberToWords(
                (float) $payment->amount
            ),

            /*
            |--------------------------------------------------------------------------
            | Payment reference
            |--------------------------------------------------------------------------
            */

            'payment_reference' =>
            $payment->payment_reference
                ?? $payment->reference,

            'paystack_reference' =>
            $payment->paystack_reference
                ?? $payment->reference,

            /*
            |--------------------------------------------------------------------------
            | Payment date
            |--------------------------------------------------------------------------
            */

            'payment_date' =>
            $paymentDateTime->format('Y-m-d'),

            /*
            |--------------------------------------------------------------------------
            | Payment time
            |--------------------------------------------------------------------------
            */

            'payment_time' =>
            $paymentDateTime->format('g:ia'),

            /*
            |--------------------------------------------------------------------------
            | DOCUMENT
            |--------------------------------------------------------------------------
            */

            'document_id' =>
            $document->id,

            'document_code' =>
            $document->code,

            'document_name' =>
            $document->name,

            /*
            |--------------------------------------------------------------------------
            | SECURITY / VERIFICATION
            |--------------------------------------------------------------------------
            */

            'tracking_code' =>
            $trackingCode,

            /*
            |--------------------------------------------------------------------------
            | TRANSACTIONS
            |--------------------------------------------------------------------------
            */

            'transaction_id' =>
            $credit->id,

            'credit_transaction_id' =>
            $credit->id,

            'debit_transaction_id' =>
            $debit->id,

            /*
            |--------------------------------------------------------------------------
            | UNKNOWN SYSTEM FIELD
            |--------------------------------------------------------------------------
            */

            default =>
            null,
        };
    }


    /*
    |--------------------------------------------------------------------------
    | NUMBER TO WORDS
    |--------------------------------------------------------------------------
    |
    | Pure PHP implementation.
    |
    | This does NOT require PHP intl / NumberFormatter.
    |
    */

    protected function numberToWords(float $amount): string
    {
        $amount = round($amount, 2);

        $naira = (int) floor($amount);

        $kobo = (int) round(
            ($amount - $naira) * 100
        );

        /*
        |--------------------------------------------------------------------------
        | Handle rounding such as 99.999 -> 100.00
        |--------------------------------------------------------------------------
        */

        if ($kobo >= 100) {
            $naira++;
            $kobo = 0;
        }

        $words = $this->convertNumberToWords($naira);

        $result = $words . ' NAIRA';

        if ($kobo > 0) {
            $result .=
                ' AND ' .
                $this->convertNumberToWords($kobo) .
                ' KOBO';
        }

        return $result . ' ONLY';
    }


    /*
    |--------------------------------------------------------------------------
    | CONVERT INTEGER TO WORDS
    |--------------------------------------------------------------------------
    */

    protected function convertNumberToWords(int $number): string
    {
        if ($number === 0) {
            return 'ZERO';
        }

        if ($number < 0) {
            return 'MINUS ' .
                $this->convertNumberToWords(
                    abs($number)
                );
        }

        $ones = [
            '',
            'ONE',
            'TWO',
            'THREE',
            'FOUR',
            'FIVE',
            'SIX',
            'SEVEN',
            'EIGHT',
            'NINE',
            'TEN',
            'ELEVEN',
            'TWELVE',
            'THIRTEEN',
            'FOURTEEN',
            'FIFTEEN',
            'SIXTEEN',
            'SEVENTEEN',
            'EIGHTEEN',
            'NINETEEN',
        ];

        $tens = [
            '',
            '',
            'TWENTY',
            'THIRTY',
            'FORTY',
            'FIFTY',
            'SIXTY',
            'SEVENTY',
            'EIGHTY',
            'NINETY',
        ];

        /*
        |--------------------------------------------------------------------------
        | 1 - 19
        |--------------------------------------------------------------------------
        */

        if ($number < 20) {
            return $ones[$number];
        }

        /*
        |--------------------------------------------------------------------------
        | 20 - 99
        |--------------------------------------------------------------------------
        */

        if ($number < 100) {

            return $tens[intdiv($number, 10)] .
                (
                    $number % 10
                    ? '-' . $ones[$number % 10]
                    : ''
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 100 - 999
        |--------------------------------------------------------------------------
        */

        if ($number < 1000) {

            return $ones[intdiv($number, 100)] .
                ' HUNDRED' .
                (
                    $number % 100
                    ? ' AND ' .
                    $this->convertNumberToWords(
                        $number % 100
                    )
                    : ''
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 1,000 - 999,999
        |--------------------------------------------------------------------------
        */

        if ($number < 1_000_000) {

            return $this->convertNumberToWords(
                intdiv($number, 1000)
            ) .
                ' THOUSAND' .
                (
                    $number % 1000
                    ? ' ' .
                    $this->convertNumberToWords(
                        $number % 1000
                    )
                    : ''
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 1,000,000 - 999,999,999
        |--------------------------------------------------------------------------
        */

        if ($number < 1_000_000_000) {

            return $this->convertNumberToWords(
                intdiv($number, 1_000_000)
            ) .
                ' MILLION' .
                (
                    $number % 1_000_000
                    ? ' ' .
                    $this->convertNumberToWords(
                        $number % 1_000_000
                    )
                    : ''
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 1,000,000,000 - 999,999,999,999
        |--------------------------------------------------------------------------
        */

        if ($number < 1_000_000_000_000) {

            return $this->convertNumberToWords(
                intdiv($number, 1_000_000_000)
            ) .
                ' BILLION' .
                (
                    $number % 1_000_000_000
                    ? ' ' .
                    $this->convertNumberToWords(
                        $number % 1_000_000_000
                    )
                    : ''
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 1,000,000,000,000+
        |--------------------------------------------------------------------------
        */

        return $this->convertNumberToWords(
            intdiv($number, 1_000_000_000_000)
        ) .
            ' TRILLION' .
            (
                $number % 1_000_000_000_000
                ? ' ' .
                $this->convertNumberToWords(
                    $number % 1_000_000_000_000
                )
                : ''
            );
    }


    /*
    |--------------------------------------------------------------------------
    | MEMBER FULL NAME
    |--------------------------------------------------------------------------
    */

    protected function getMemberFullName(
        $profile,
        User $user
    ): ?string {

        if (!$profile) {
            return $user->name;
        }

        $parts = array_filter([
            $profile->surname,
            $profile->first_name,
            $profile->middle_name,
        ]);

        if (empty($parts)) {
            return $user->name;
        }

        return implode(' ', $parts);
    }


    /*
    |--------------------------------------------------------------------------
    | DOCUMENT NUMBER
    |--------------------------------------------------------------------------
    */

    protected function generateDocumentNumber($document): string
    {
        do {
            $number =
                strtoupper($document->code)
                . '-'
                . now()->year
                . '-'
                . strtoupper(
                    Str::random(8)
                );
        } while (
            GeneratedDocument::query()
            ->where(
                'document_number',
                $number
            )
            ->exists()
        );

        return $number;
    }


    /*
    |--------------------------------------------------------------------------
    | TRACKING CODE
    |--------------------------------------------------------------------------
    */

    protected function generateTrackingCode(): string
    {
        do {
            $trackingCode =
                'NACP-'
                . strtoupper(
                    Str::random(20)
                );
        } while (
            GeneratedDocument::query()
            ->where(
                'tracking_code',
                $trackingCode
            )
            ->exists()
        );

        return $trackingCode;
    }


    /*
    |--------------------------------------------------------------------------
    | EXPIRY DATE
    |--------------------------------------------------------------------------
    */

    protected function calculateExpiryDate(
        $document,
        Carbon $issuedAt
    ): ?Carbon {

        $validityType =
            strtolower(
                trim(
                    (string) $document->validity_type
                )
            );

        if (
            !$validityType ||
            $validityType === 'none'
        ) {
            return null;
        }

        switch ($validityType) {

            /*
            |--------------------------------------------------------------------------
            | DAYS
            |--------------------------------------------------------------------------
            */

            case 'days':

                if (!$document->validity_value) {
                    return null;
                }

                return $issuedAt->copy()
                    ->addDays(
                        (int) $document->validity_value
                    );

                /*
            |--------------------------------------------------------------------------
            | MONTHS
            |--------------------------------------------------------------------------
            */

            case 'months':

                if (!$document->validity_value) {
                    return null;
                }

                return $issuedAt->copy()
                    ->addMonths(
                        (int) $document->validity_value
                    );

                /*
            |--------------------------------------------------------------------------
            | YEARS
            |--------------------------------------------------------------------------
            */

            case 'years':

                if (!$document->validity_value) {
                    return null;
                }

                return $issuedAt->copy()
                    ->addYears(
                        (int) $document->validity_value
                    );

                /*
            |--------------------------------------------------------------------------
            | SPECIFIC DATE
            |--------------------------------------------------------------------------
            */

            case 'fixed_date':

                if (!$document->validity_date) {
                    return null;
                }

                return Carbon::parse(
                    $document->validity_date
                );

                /*
            |--------------------------------------------------------------------------
            | END OF CURRENT YEAR
            |--------------------------------------------------------------------------
            */

            case 'year_end':

                return $issuedAt->copy()->endOfYear();

                /*
            |--------------------------------------------------------------------------
            | UNKNOWN TYPE
            |--------------------------------------------------------------------------
            */

            default:

                throw new RuntimeException(
                    "Unsupported document validity type [{$validityType}]."
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP CERTIFICATE GENERATION
    |--------------------------------------------------------------------------
    |
    | Membership certificates are different from additional-payment
    | documents.
    |
    | Membership payments do NOT depend on payment_items.
    |
    | Paid Membership Payment
    |          ↓
    | Admin Approval
    |          ↓
    | Membership Created
    |          ↓
    | Membership Certificate Generated
    |
    | The certificate document is dynamically selected through:
    |
    | membership_categories.document_id
    |--------------------------------------------------------------------------
    */

    /**
     * Generate membership certificate for an active membership.
     *
     * Membership certificates are generated automatically after:
     * - Initial membership payment
     * - Successful membership renewal payment
     *
     * Membership renewal does NOT use a PaymentItem.
     * Its price comes from membership_category_fees.
     */
public function generateMembershipCertificate(
    User $user,
    Membership $membership
): ?GeneratedDocument {
    /*
    |--------------------------------------------------------------------------
    | 1. Verify membership ownership
    |--------------------------------------------------------------------------
    */
    if ((int) $membership->user_id !== (int) $user->id) {
        throw new RuntimeException(
            'You are not authorized to generate this membership certificate.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Membership must be active
    |--------------------------------------------------------------------------
    */
    if ($membership->status !== 'active') {
        throw new RuntimeException(
            'Membership must be active before a certificate can be generated.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Membership number is required
    |--------------------------------------------------------------------------
    */
    if (empty($membership->membership_number)) {
        throw new RuntimeException(
            'Membership number is missing. The certificate cannot be generated.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Load required relationships
    |--------------------------------------------------------------------------
    */
    $user->loadMissing([
        'profile',
    ]);

    $membership->loadMissing([
        'profile',
        'category.document.fields',
    ]);

    $profile = $membership->profile ?? $user->profile;

    $category = $membership->category;

    if (!$category) {
        throw new RuntimeException(
            'Membership category could not be found.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Get membership certificate document
    |--------------------------------------------------------------------------
    |
    | A membership certificate is OPTIONAL.
    |
    | If the membership category does not have a document_id configured,
    | membership approval should continue without generating a certificate.
    |
    */
    $document = $category->document;

    if (!$document) {
        Log::info(
            'MEMBERSHIP CERTIFICATE GENERATION SKIPPED - NO DOCUMENT ATTACHED',
            [
                'user_id' => $user->id,
                'membership_id' => $membership->id,
                'membership_category_id' =>
                    $membership->membership_category_id,
                'membership_category' => $category->name,
            ]
        );

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | 6. Document must be active
    |--------------------------------------------------------------------------
    */
    if (!$document->is_active) {
        throw new RuntimeException(
            "The membership certificate document [{$document->code}] is not active."
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 7. Find the successful membership payment
    |--------------------------------------------------------------------------
    |
    | Covers:
    |
    | - Initial membership payment
    | - Membership renewal payment
    |
    | Membership renewal does NOT use a PaymentItem.
    |--------------------------------------------------------------------------
    */
    $payment = Payment::query()
        ->where('user_id', $user->id)
        ->where(
            'membership_category_id',
            $membership->membership_category_id
        )
        ->whereIn('payment_type', [
            'membership',
            'membership_renewal',
        ])
        ->where('status', 'paid')
        ->whereNotNull('paid_at')
        ->latest('id')
        ->first();

    if (!$payment) {
        throw new RuntimeException(
            'No successful membership payment was found for this membership.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 8. Get Paystack reference
    |--------------------------------------------------------------------------
    */
    $paystackReference =
        $payment->paystack_reference
        ?? $payment->payment_reference
        ?? $payment->reference;

    if (empty($paystackReference)) {
        throw new RuntimeException(
            'The membership payment does not have a valid payment reference.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 9. Find exact paid membership DEBIT transaction
    |--------------------------------------------------------------------------
    |
    | Both:
    |
    |     membership
    |
    | and:
    |
    |     membership_renewal
    |
    | have:
    |
    |     payment_item_id = NULL
    |
    | Membership renewal is NOT connected to a PaymentItem.
    |
    | The Paystack reference is therefore the primary identifier.
    |--------------------------------------------------------------------------
    */
    $debit = Transaction::query()
        ->where('user_id', $user->id)
        ->where('type', 'debit')
        ->where('status', 'paid')
        ->whereNull('payment_item_id')
        ->whereJsonContains(
            'gateway->paystack',
            $paystackReference
        )
        ->where('amount', $payment->amount)
        ->latest('id')
        ->first();

    /*
    |--------------------------------------------------------------------------
    | 10. Fallback debit lookup
    |--------------------------------------------------------------------------
    |
    | This protects against older transactions where the Paystack
    | reference may not have been stored correctly in the gateway JSON.
    |--------------------------------------------------------------------------
    */
    if (!$debit) {
        $debit = Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', 'debit')
            ->where('status', 'paid')
            ->whereNull('payment_item_id')
            ->where('amount', $payment->amount)
            ->where(function ($query) {
                $query
                    ->where('narration', 'like', '%membership%')
                    ->orWhere('narration', 'like', '%renewal%');
            })
            ->latest('id')
            ->first();
    }

    if (!$debit) {
        throw new RuntimeException(
            'The paid membership debit transaction could not be found.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 11. Verify debit ownership
    |--------------------------------------------------------------------------
    */
    if ((int) $debit->user_id !== (int) $user->id) {
        throw new RuntimeException(
            'The membership payment transaction does not belong to this member.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 12. Verify debit amount
    |--------------------------------------------------------------------------
    */
    if ((float) $debit->amount !== (float) $payment->amount) {
        throw new RuntimeException(
            'The membership debit transaction amount does not match the payment amount.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 13. Find corresponding CREDIT transaction
    |--------------------------------------------------------------------------
    |
    | The credit created after successful payment is linked to the
    | debit using debit_transaction_id.
    |
    | transaction_id is also supported for older records.
    |--------------------------------------------------------------------------
    */
    $credit = Transaction::query()
        ->where('user_id', $user->id)
        ->where('type', 'credit')
        ->where('status', 'paid')
        ->where('amount', $payment->amount)
        ->where(function ($query) use ($debit, $paystackReference) {
            $query
                ->where('debit_transaction_id', $debit->id)
                ->orWhere('transaction_id', $debit->id)
                ->orWhereJsonContains(
                    'gateway->paystack',
                    $paystackReference
                );
        })
        ->latest('id')
        ->first();

    if (!$credit) {
        throw new RuntimeException(
            'The corresponding membership credit transaction could not be found.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 14. Verify credit ownership
    |--------------------------------------------------------------------------
    */
    if ((int) $credit->user_id !== (int) $user->id) {
        throw new RuntimeException(
            'The membership credit transaction does not belong to this member.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 15. Verify credit amount
    |--------------------------------------------------------------------------
    */
    if ((float) $credit->amount !== (float) $payment->amount) {
        throw new RuntimeException(
            'The membership credit transaction amount does not match the payment amount.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 16. Prevent duplicate certificate for this payment
    |--------------------------------------------------------------------------
    |
    | Every successful renewal has a different credit transaction.
    |
    | Therefore:
    |
    | Initial payment  -> Certificate A
    | Renewal payment  -> Certificate B
    |
    | The old certificate is not overwritten.
    |--------------------------------------------------------------------------
    */
    $existingCertificate = GeneratedDocument::query()
        ->where('user_id', $user->id)
        ->where('document_id', $document->id)
        ->where('transaction_id', $credit->id)
        ->first();

    if ($existingCertificate) {
        return $existingCertificate;
    }

    /*
    |--------------------------------------------------------------------------
    | 17. Generate document number
    |--------------------------------------------------------------------------
    */
    $documentNumber = $this->generateDocumentNumber(
        $document
    );

    /*
    |--------------------------------------------------------------------------
    | 18. Generate tracking code
    |--------------------------------------------------------------------------
    */
    $trackingCode = $this->generateTrackingCode();

    /*
    |--------------------------------------------------------------------------
    | 19. Build document field values
    |--------------------------------------------------------------------------
    |
    | System fields are resolved by the system.
    |
    | Manual fields are taken from the member profile where possible,
    | otherwise the configured document-field default is used.
    |
    | Browser input is NOT used here for system fields.
    |--------------------------------------------------------------------------
    */
    $fieldValues = [];

    foreach ($document->fields as $field) {

        /*
        |--------------------------------------------------------------------------
        | SYSTEM FIELD
        |--------------------------------------------------------------------------
        */
        if ($field->is_system) {
            $fieldValues[$field->field_key] =
                $this->resolveMembershipSystemField(
                    $field->field_key,
                    $user,
                    $membership,
                    $payment,
                    $debit,
                    $credit,
                    $document,
                    $documentNumber,
                    $trackingCode
                );

            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | MANUAL FIELD
        |--------------------------------------------------------------------------
        */
        $value = null;

        if ($profile) {

            /*
            |--------------------------------------------------------------------------
            | First try exact field key from member profile
            |--------------------------------------------------------------------------
            */
            if (
                isset($profile->{$field->field_key}) &&
                $profile->{$field->field_key} !== null
            ) {
                $value = $profile->{$field->field_key};
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Fall back to configured default value
        |--------------------------------------------------------------------------
        */
        if (
            ($value === null || $value === '') &&
            $field->default_value !== null
        ) {
            $value = $field->default_value;
        }

        $fieldValues[$field->field_key] = $value;
    }

    /*
    |--------------------------------------------------------------------------
    | 20. Determine issued date
    |--------------------------------------------------------------------------
    */
    $issuedAt = $membership->issued_at
        ?? $payment->paid_at
        ?? now();

    /*
    |--------------------------------------------------------------------------
    | 21. Determine expiry date
    |--------------------------------------------------------------------------
    |
    | For renewal, membershipRenewalCallback() updates the membership's
    | issued_at and expires_at before calling this method.
    |--------------------------------------------------------------------------
    */
    $expiresAt = $membership->expires_at;

    /*
    |--------------------------------------------------------------------------
    | 22. Create generated certificate
    |--------------------------------------------------------------------------
    */
    $generatedDocument = GeneratedDocument::create([
        'user_id' => $user->id,

        'document_id' => $document->id,

        /*
        |--------------------------------------------------------------------------
        | Link certificate to CREDIT transaction
        |--------------------------------------------------------------------------
        */
        'transaction_id' => $credit->id,

        'document_number' => $documentNumber,

        'tracking_code' => $trackingCode,

        'issued_at' => $issuedAt,

        'expires_at' => $expiresAt,

        'status' => 'active',

        'field_values' => $fieldValues,
    ]);

    /*
    |--------------------------------------------------------------------------
    | 23. Log successful generation
    |--------------------------------------------------------------------------
    */
    Log::info(
        'Membership certificate generated successfully.',
        [
            'generated_document_id' => $generatedDocument->id,

            'user_id' => $user->id,

            'membership_id' => $membership->id,

            'membership_number' =>
                $membership->membership_number,

            'membership_category_id' =>
                $membership->membership_category_id,

            'document_id' => $document->id,

            'document_number' =>
                $documentNumber,

            'tracking_code' =>
                $trackingCode,

            'payment_id' =>
                $payment->id,

            'payment_type' =>
                $payment->payment_type,

            'credit_transaction_id' =>
                $credit->id,

            'debit_transaction_id' =>
                $debit->id,
        ]
    );

    /*
    |--------------------------------------------------------------------------
    | 24. Return generated certificate
    |--------------------------------------------------------------------------
    */
    return $generatedDocument;
}


    /**
     * Resolve system fields for a membership certificate.
     *
     * System fields are generated by the backend and must never be
     * trusted from browser-submitted data.
     */
    protected function resolveMembershipSystemField(
        string $fieldKey,
        User $user,
        Membership $membership,
        Payment $payment,
        Transaction $debit,
        Transaction $credit,
        Document $document,
        string $documentNumber,
        string $trackingCode
    ) {
        /*
    |--------------------------------------------------------------------------
    | Load required relationships
    |--------------------------------------------------------------------------
    */
        $user->loadMissing([
            'profile',
        ]);

        $membership->loadMissing([
            'profile',
            'category',
        ]);

        $profile = $membership->profile ?? $user->profile;

        $category = $membership->category;

        /*
    |--------------------------------------------------------------------------
    | Resolve system field
    |--------------------------------------------------------------------------
    */
        return match ($fieldKey) {

            /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        */

            'user_id' =>
            $user->id,

            'name' =>
            $user->name,

            'member_name' =>
            $this->getMemberFullName(
                $profile,
                $user
            ),

            'email' =>
            $user->email,

            /*
        |--------------------------------------------------------------------------
        | MEMBER PROFILE
        |--------------------------------------------------------------------------
        */

            'surname' =>
            $profile?->surname,

            'first_name' =>
            $profile?->first_name,

            'middle_name' =>
            $profile?->middle_name,

            'phone' =>
            $profile?->phone
                ?? $user->phone,

            'photo' =>
            $profile?->photo,

            'date_of_birth' =>
            $profile?->date_of_birth
                ? Carbon::parse(
                    $profile->date_of_birth
                )->format('Y-m-d')
                : null,

            'gender' =>
            $profile?->gender,

            'nationality' =>
            $profile?->nationality,

            'address' =>
            $profile?->address,

            'city' =>
            $profile?->city,

            'state' =>
            $profile?->state,

            'lga' =>
            $profile?->lga,

            /*
        |--------------------------------------------------------------------------
        | BUSINESS INFORMATION
        |--------------------------------------------------------------------------
        */

            'business_name' =>
            $profile?->business_name,

            'business_registration_number' =>
            $profile?->business_registration_number,

            'business_type' =>
            $profile?->business_type,

            'business_address' =>
            $profile?->business_address,

            /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP
        |--------------------------------------------------------------------------
        */

            'membership_number',
            'membership_no' =>
            $membership->membership_number,

            'membership_status' =>
            $membership->status,

            'membership_issued_at' =>
            $membership->issued_at
                ? Carbon::parse(
                    $membership->issued_at
                )->format('Y-m-d')
                : null,

            'membership_expires_at' =>
            $membership->expires_at
                ? Carbon::parse(
                    $membership->expires_at
                )->format('Y-m-d')
                : null,

            'membership_category_id' =>
            $membership->membership_category_id,

            'membership_category' =>
            $category?->name
                ?? $category?->code,

            'membership_category_name' =>
            $category?->name,

            'membership_category_code' =>
            $category?->code,

            /*
        |--------------------------------------------------------------------------
        | PAYMENT
        |--------------------------------------------------------------------------
        */

            'payment_id' =>
            $payment->id,

            'payment_type' =>
            $payment->payment_type,

            'amount' =>
            $payment->amount,

            'amount_paid' =>
            $payment->amount,

            'payment_reference' =>
            $payment->payment_reference
                ?? $payment->reference,

            'paystack_reference' =>
            $payment->paystack_reference
                ?? $payment->payment_reference
                ?? $payment->reference,

            'payment_date' =>
            $payment->paid_at
                ? Carbon::parse(
                    $payment->paid_at
                )->format('Y-m-d')
                : null,

            /*
        |--------------------------------------------------------------------------
        | DOCUMENT
        |--------------------------------------------------------------------------
        */

            'document_id' =>
            $document->id,

            'document_code' =>
            $document->code,

            'document_name' =>
            $document->name,

            'document_number' =>
            $documentNumber,

            /*
        |--------------------------------------------------------------------------
        | VERIFICATION
        |--------------------------------------------------------------------------
        */

            'tracking_code' =>
            $trackingCode,

            /*
        |--------------------------------------------------------------------------
        | TRANSACTIONS
        |--------------------------------------------------------------------------
        */

            'transaction_id' =>
            $credit->id,

            'credit_transaction_id' =>
            $credit->id,

            'debit_transaction_id' =>
            $debit->id,

            /*
        |--------------------------------------------------------------------------
        | UNKNOWN SYSTEM FIELD
        |--------------------------------------------------------------------------
        |
        | If an administrator creates a system field that this resolver
        | does not yet understand, return null rather than accepting
        | untrusted browser data.
        |--------------------------------------------------------------------------
        */
            default =>
            null,
        };
    }
}
