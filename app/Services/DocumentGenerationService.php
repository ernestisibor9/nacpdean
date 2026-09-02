<?php

namespace App\Services;

use App\Models\GeneratedDocument;
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
     * Generate a document from a successful payment.
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
        |
        | Your current callback also stores the debit ID inside
        | transaction_id.
        |
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
        |
        | The exact CREDIT transaction identifies this successful payment.
        |
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
        | 13. Build document field values
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
                        $debit
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
        | 14. Generate document number
        |--------------------------------------------------------------------------
        */

        $documentNumber =
            $this->generateDocumentNumber($document);


        /*
        |--------------------------------------------------------------------------
        | 15. Generate verification tracking code
        |--------------------------------------------------------------------------
        */

        $trackingCode =
            $this->generateTrackingCode();


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
        |
        | IMPORTANT:
        |
        | generated_documents.transaction_id
        | points to the CREDIT transaction.
        |
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
        Transaction $debit
    ) {

        $profile =
            $user->profile;

        $membership =
            $user->membership;


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
                $this->getMemberFullName($profile, $user),

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
                $profile?->phone,

            'photo' =>
                $profile?->photo,

            'date_of_birth' =>
                $profile?->date_of_birth?->format('Y-m-d'),

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
                $membership?->issued_at?->format('Y-m-d'),

            'membership_expires_at' =>
                $membership?->expires_at?->format('Y-m-d'),


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

            'payment_reference' =>
                $payment->payment_reference
                ?? $payment->reference,

            'paystack_reference' =>
                $payment->paystack_reference
                ?? $payment->reference,

            'payment_date' =>
                $payment->paid_at
                    ? Carbon::parse(
                        $payment->paid_at
                    )->format('Y-m-d')
                    : now()->format('Y-m-d'),


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
            $document->validity_type;


        if (
            !$validityType ||
            $validityType === 'none'
        ) {
            return null;
        }


        switch ($validityType) {

            case 'days':

                if (!$document->validity_value) {
                    return null;
                }

                return $issuedAt->copy()
                    ->addDays(
                        (int) $document->validity_value
                    );


            case 'months':

                if (!$document->validity_value) {
                    return null;
                }

                return $issuedAt->copy()
                    ->addMonths(
                        (int) $document->validity_value
                    );


            case 'years':

                if (!$document->validity_value) {
                    return null;
                }

                return $issuedAt->copy()
                    ->addYears(
                        (int) $document->validity_value
                    );


            case 'date':

                if (!$document->validity_date) {
                    return null;
                }

                return Carbon::parse(
                    $document->validity_date
                );


            default:

                throw new RuntimeException(
                    "Unsupported document validity type [{$validityType}]."
                );
        }
    }
}
