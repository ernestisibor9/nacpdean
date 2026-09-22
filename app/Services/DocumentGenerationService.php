<?php

namespace App\Services;

use App\Models\Document;
use App\Models\GeneratedDocument;
use App\Models\MemberProfile;
use App\Models\Membership;
use App\Models\MembershipCategory;
use App\Models\OperationalRightsDocument;
use App\Models\Payment;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class DocumentGenerationService
{
    /*
    |--------------------------------------------------------------------------
    | GENERATE DOCUMENT FROM PAYMENT
    |--------------------------------------------------------------------------
    */

    public function generate(
        Payment $payment,
        Transaction $creditTransaction,
        array $documentFieldValues = []
    ): GeneratedDocument {
        return DB::transaction(function () use (
            $payment,
            $creditTransaction,
            $documentFieldValues
        ) {
            if (
                (int) $payment->user_id !==
                (int) $creditTransaction->user_id
            ) {
                throw new RuntimeException(
                    'Payment and credit transaction do not belong to the same user.'
                );
            }

            if ($payment->status !== 'paid') {
                throw new RuntimeException(
                    'Document cannot be generated until payment is successful.'
                );
            }

            if ($creditTransaction->type !== 'credit') {
                throw new RuntimeException(
                    'The supplied transaction is not a credit transaction.'
                );
            }

            if ($creditTransaction->status !== 'paid') {
                throw new RuntimeException(
                    'Credit transaction is not marked as paid.'
                );
            }

            if (!$creditTransaction->debit_transaction_id) {
                throw new RuntimeException(
                    'Credit transaction is not linked to a debit transaction.'
                );
            }

            $debitTransaction = Transaction::find(
                $creditTransaction->debit_transaction_id
            );

            if (!$debitTransaction) {
                throw new RuntimeException(
                    'The debit transaction linked to this credit could not be found.'
                );
            }

            if (
                (int) $debitTransaction->user_id !==
                (int) $payment->user_id
            ) {
                throw new RuntimeException(
                    'Debit transaction does not belong to this payment user.'
                );
            }

            if ($debitTransaction->type !== 'debit') {
                throw new RuntimeException(
                    'The transaction linked to this credit is not a debit transaction.'
                );
            }

            if ($debitTransaction->status !== 'paid') {
                throw new RuntimeException(
                    'The debit transaction linked to this credit is not marked as paid.'
                );
            }

            if (
                $payment->payment_item_id !== null &&
                (int) $debitTransaction->payment_item_id !==
                (int) $payment->payment_item_id
            ) {
                throw new RuntimeException(
                    'Debit transaction does not belong to this payment item.'
                );
            }

            $payment->loadMissing([
                'user',
                'paymentItem',
                'membershipCategory',
                'membershipCategoryFee',
                'memberFee',
            ]);

            $user = $payment->user;

            if (!$user) {
                throw new RuntimeException(
                    'Payment user could not be found.'
                );
            }

            $paymentItem = $payment->paymentItem;

            if (!$paymentItem) {
                throw new RuntimeException(
                    'Payment item could not be found.'
                );
            }

            if (
                $creditTransaction->payment_item_id &&
                (int) $creditTransaction->payment_item_id !==
                (int) $paymentItem->id
            ) {
                throw new RuntimeException(
                    'Credit transaction does not belong to this payment item.'
                );
            }

            $document = $paymentItem->documents()
                ->where(
                    'documents.is_active',
                    true
                )
                ->where(
                    'payment_item_documents.generate_after_payment',
                    true
                )
                ->orderBy(
                    'payment_item_documents.is_primary',
                    'desc'
                )
                ->orderBy(
                    'documents.id'
                )
                ->first();

            if (!$document) {
                throw new RuntimeException(
                    'No active document is configured for this payment item.'
                );
            }

            $existing = GeneratedDocument::where(
                'user_id',
                $user->id
            )
                ->where(
                    'document_id',
                    $document->id
                )
                ->where(
                    'transaction_id',
                    $creditTransaction->id
                )
                ->first();

            if ($existing) {
                return $existing;
            }

            $documentNumber = $this->generateDocumentNumber(
                $document,
                $payment
            );

            $trackingCode = $this->generateTrackingCode();

            $issuedAt = $payment->paid_at
                ? Carbon::parse($payment->paid_at)
                : now();

            $expiresAt = $this->calculateDocumentExpiry(
                $document,
                $issuedAt
            );

            $membership = Membership::where(
                'user_id',
                $user->id
            )
                ->latest('id')
                ->first();

            $profile = MemberProfile::where(
                'user_id',
                $user->id
            )->first();

            $fieldValues = $this->buildGenericFieldValues(
                $document,
                $user,
                $profile,
                $membership,
                $payment,
                $creditTransaction,
                $documentNumber,
                $trackingCode,
                $issuedAt,
                $expiresAt,
                $documentFieldValues
            );

            /*
                |--------------------------------------------------------------------------
                | DEDICATED TRANSIT NUMBER
                |--------------------------------------------------------------------------
                |
                | Transit passes get their own number, independent of the internal
                | document_number. Format: TRP-<YEAR>-<STATE>-<SERIAL>
                |
            */

            $transitNo = $this->generateTransitNumber(
                $document,
                $documentFieldValues
            );

            if ($transitNo !== null) {
                $fieldValues['transit_no'] = $transitNo;
            }

            $generatedDocument = GeneratedDocument::create([
                'user_id' => $user->id,
                'document_id' => $document->id,
                'transaction_id' => $creditTransaction->id,
                'document_number' => $documentNumber,
                'tracking_code' => $trackingCode,
                'issued_at' => $issuedAt->toDateString(),
                'expires_at' => $expiresAt
                    ? $expiresAt->toDateString()
                    : null,
                'status' => 'active',
                'field_values' => $fieldValues,
            ]);

            $this->handleRenewalReplacement(
                $generatedDocument,
                $user->id,
                $document->id
            );

            return $generatedDocument;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE MULTIPLE DOCUMENTS FROM ONE PAYMENT
    |--------------------------------------------------------------------------
    */

    public function generateMultiple(
        Payment $payment,
        Transaction $creditTransaction,
        array $documentFieldValuesByCode = []
    ): array {
        return DB::transaction(function () use (
            $payment,
            $creditTransaction,
            $documentFieldValuesByCode
        ) {
            if (
                (int) $payment->user_id !==
                (int) $creditTransaction->user_id
            ) {
                throw new RuntimeException(
                    'Payment and credit transaction do not belong to the same user.'
                );
            }

            if ($payment->status !== 'paid') {
                throw new RuntimeException(
                    'Documents cannot be generated until payment is successful.'
                );
            }

            if ($creditTransaction->type !== 'credit') {
                throw new RuntimeException(
                    'The supplied transaction is not a credit transaction.'
                );
            }

            if ($creditTransaction->status !== 'paid') {
                throw new RuntimeException(
                    'Credit transaction is not marked as paid.'
                );
            }

            if (!$creditTransaction->debit_transaction_id) {
                throw new RuntimeException(
                    'Credit transaction is not linked to a debit transaction.'
                );
            }

            $debitTransaction = Transaction::find(
                $creditTransaction->debit_transaction_id
            );

            if (!$debitTransaction) {
                throw new RuntimeException(
                    'The debit transaction linked to this credit could not be found.'
                );
            }

            if (
                (int) $debitTransaction->user_id !==
                (int) $payment->user_id
            ) {
                throw new RuntimeException(
                    'Debit transaction does not belong to this payment user.'
                );
            }

            if ($debitTransaction->type !== 'debit') {
                throw new RuntimeException(
                    'The transaction linked to this credit is not a debit transaction.'
                );
            }

            if ($debitTransaction->status !== 'paid') {
                throw new RuntimeException(
                    'The debit transaction linked to this credit is not marked as paid.'
                );
            }

            if (!$payment->payment_item_id) {
                throw new RuntimeException(
                    'This payment does not have a payment item.'
                );
            }

            if (
                $debitTransaction->payment_item_id &&
                (int) $debitTransaction->payment_item_id !==
                (int) $payment->payment_item_id
            ) {
                throw new RuntimeException(
                    'Debit transaction does not belong to this payment item.'
                );
            }

            if (
                $creditTransaction->payment_item_id &&
                (int) $creditTransaction->payment_item_id !==
                (int) $payment->payment_item_id
            ) {
                throw new RuntimeException(
                    'Credit transaction does not belong to this payment item.'
                );
            }

            $payment->loadMissing([
                'user',
                'paymentItem.documents.fields',
                'membershipCategory',
                'membershipCategoryFee',
                'memberFee',
            ]);

            $user = $payment->user;

            if (!$user) {
                throw new RuntimeException(
                    'Payment user could not be found.'
                );
            }

            $paymentItem = $payment->paymentItem;

            if (!$paymentItem) {
                throw new RuntimeException(
                    'Payment item could not be found.'
                );
            }

            $documents = $paymentItem->documents()
                ->where(
                    'documents.is_active',
                    true
                )
                ->where(
                    'payment_item_documents.generate_after_payment',
                    true
                )
                ->orderBy(
                    'payment_item_documents.is_primary',
                    'desc'
                )
                ->orderBy(
                    'documents.id'
                )
                ->get();

            if ($documents->isEmpty()) {
                throw new RuntimeException(
                    'No active documents are configured for this payment item.'
                );
            }

            $profile = MemberProfile::where(
                'user_id',
                $user->id
            )->first();

            $membership = Membership::where(
                'user_id',
                $user->id
            )
                ->latest('id')
                ->first();

            $generatedDocuments = [];

            foreach ($documents as $document) {
                $existing = GeneratedDocument::where(
                    'user_id',
                    $user->id
                )
                    ->where(
                        'document_id',
                        $document->id
                    )
                    ->where(
                        'transaction_id',
                        $creditTransaction->id
                    )
                    ->first();

                if ($existing) {
                    $generatedDocuments[] = $existing;

                    continue;
                }

                $documentNumber = $this->generateDocumentNumber(
                    $document,
                    $payment
                );

                $trackingCode = $this->generateTrackingCode();

                $issuedAt = $payment->paid_at
                    ? Carbon::parse($payment->paid_at)
                    : now();

                $expiresAt = $this->calculateDocumentExpiry(
                    $document,
                    $issuedAt
                );

                $documentCode = strtoupper(
                    trim(
                        (string) $document->code
                    )
                );

                $documentFieldValues = [];

                if (
                    isset(
                        $documentFieldValuesByCode[$documentCode]
                    ) &&
                    is_array(
                        $documentFieldValuesByCode[$documentCode]
                    )
                ) {
                    $documentFieldValues =
                        $documentFieldValuesByCode[$documentCode];
                }

                if (
                    empty($documentFieldValues) &&
                    isset(
                        $documentFieldValuesByCode[strtolower(
                            (string) $document->code
                        )]
                    ) &&
                    is_array(
                        $documentFieldValuesByCode[strtolower(
                            (string) $document->code
                        )]
                    )
                ) {
                    $documentFieldValues =
                        $documentFieldValuesByCode[strtolower(
                            (string) $document->code
                        )];
                }

                $fieldValues = $this->buildGenericFieldValues(
                    $document,
                    $user,
                    $profile,
                    $membership,
                    $payment,
                    $creditTransaction,
                    $documentNumber,
                    $trackingCode,
                    $issuedAt,
                    $expiresAt,
                    $documentFieldValues
                );

                /*
                |--------------------------------------------------------------------------
                | DEDICATED TRANSIT NUMBER
                |--------------------------------------------------------------------------
                */

                $transitNo = $this->generateTransitNumber(
                    $document,
                    $documentFieldValues
                );

                if ($transitNo !== null) {
                    $fieldValues['transit_no'] = $transitNo;
                }

                $generatedDocument = GeneratedDocument::create([
                    'user_id' => $user->id,
                    'document_id' => $document->id,
                    'transaction_id' => $creditTransaction->id,
                    'document_number' => $documentNumber,
                    'tracking_code' => $trackingCode,
                    'issued_at' => $issuedAt->toDateString(),
                    'expires_at' => $expiresAt
                        ? $expiresAt->toDateString()
                        : null,
                    'status' => 'active',
                    'field_values' => $fieldValues,
                ]);

                $this->handleRenewalReplacement(
                    $generatedDocument,
                    $user->id,
                    $document->id
                );

                $generatedDocuments[] = $generatedDocument;
            }

            return $generatedDocuments;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE ALL MEMBERSHIP DOCUMENTS
    |--------------------------------------------------------------------------
    */

    public function generateMembershipDocuments(
        Membership $membership
    ): array {
        $membership->loadMissing([
            'user',
            'membershipCategory',
        ]);

        $user = $membership->user;

        if (!$user) {
            throw new RuntimeException(
                'Membership user could not be found.'
            );
        }

        $category = $membership->membershipCategory;

        if (!$category) {
            throw new RuntimeException(
                'Membership category could not be determined.'
            );
        }

        if (!$category->status) {
            throw new RuntimeException(
                'The membership category is inactive.'
            );
        }

        if (!$membership->membership_category_id) {
            throw new RuntimeException(
                'Membership does not have a membership category.'
            );
        }

        $documents = $category->documents()
            ->where(
                'documents.is_active',
                true
            )
            ->orderBy(
                'membership_category_documents.sort_order'
            )
            ->orderBy(
                'documents.id'
            )
            ->get();

        if (
            $documents->isEmpty() &&
            $category->document_id
        ) {
            $legacyDocument = Document::with('fields')
                ->where(
                    'id',
                    $category->document_id
                )
                ->where(
                    'is_active',
                    true
                )
                ->first();

            if ($legacyDocument) {
                $documents = collect([
                    $legacyDocument,
                ]);
            }
        }

        if ($documents->isEmpty()) {
            throw new RuntimeException(
                'No active documents are configured for membership category: ' .
                    $category->name
            );
        }

        [
            $payment,
            $debitTransaction,
            $creditTransaction
        ] = $this->findMembershipPaymentTransactions(
            $membership
        );

        if (!$payment) {
            throw new RuntimeException(
                'A successful membership payment could not be found for this membership category.'
            );
        }

        if (!$creditTransaction) {
            throw new RuntimeException(
                'A successful membership credit transaction could not be found for this membership payment.'
            );
        }

        $generatedDocuments = [];

        foreach ($documents as $document) {
            $generatedDocuments[] =
                $this->generateMembershipDocument(
                    $membership,
                    $document,
                    $payment,
                    $debitTransaction,
                    $creditTransaction
                );
        }

        return $generatedDocuments;
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE MEMBERSHIP DOCUMENTS MANUALLY
    |--------------------------------------------------------------------------
    */

    public function generateMembershipDocumentsManually(
        Membership $membership
    ): array {
        return DB::transaction(function () use ($membership) {
            $membership->loadMissing([
                'user',
                'membershipCategory',
            ]);

            $user = $membership->user;

            if (!$user) {
                throw new RuntimeException(
                    'Membership user could not be found.'
                );
            }

            $category = $membership->membershipCategory;

            if (!$category) {
                throw new RuntimeException(
                    'Membership category could not be determined.'
                );
            }

            if (!$membership->membership_category_id) {
                throw new RuntimeException(
                    'Membership does not have a membership category.'
                );
            }

            if (!$category->status) {
                throw new RuntimeException(
                    'The membership category is inactive.'
                );
            }

            $documents = $category->documents()
                ->where(
                    'documents.is_active',
                    true
                )
                ->where(
                    'membership_category_documents.status',
                    1
                )
                ->orderBy(
                    'membership_category_documents.sort_order'
                )
                ->orderBy(
                    'documents.id'
                )
                ->get();

            if (
                $documents->isEmpty() &&
                $category->document_id
            ) {
                $legacyDocument = Document::with('fields')
                    ->where(
                        'id',
                        $category->document_id
                    )
                    ->where(
                        'is_active',
                        true
                    )
                    ->first();

                if ($legacyDocument) {
                    $documents = collect([
                        $legacyDocument,
                    ]);
                }
            }

            if ($documents->isEmpty()) {
                throw new RuntimeException(
                    'No active documents are configured for membership category: ' .
                        $category->name
                );
            }

            $profile = MemberProfile::where(
                'user_id',
                $membership->user_id
            )->first();

            $manualPayment = new Payment();

            $manualPayment->user_id =
                $membership->user_id;

            $manualPayment->membership_category_id =
                $membership->membership_category_id;

            $manualPayment->payment_type =
                'membership';

            $manualPayment->status =
                'manual';

            $issuedAt = $membership->issued_at
                ? Carbon::parse(
                    $membership->issued_at
                )
                : now();

            $generatedDocuments = [];

            foreach ($documents as $document) {
                $expiresAt = $membership->expires_at
                    ? Carbon::parse(
                        $membership->expires_at
                    )
                    : $this->calculateDocumentExpiry(
                        $document,
                        $issuedAt
                    );

                $existing = GeneratedDocument::where(
                    'user_id',
                    $membership->user_id
                )
                    ->where(
                        'document_id',
                        $document->id
                    )
                    ->where(
                        'status',
                        'active'
                    )
                    ->first();

                if ($existing) {
                    $generatedDocuments[] = $existing;

                    continue;
                }

                $documentNumber = $this->generateDocumentNumber(
                    $document,
                    $manualPayment
                );

                $trackingCode = $this->generateTrackingCode();

                $fieldValues = $this->buildMembershipFieldValues(
                    $document,
                    $user,
                    $profile,
                    $membership,
                    $category,
                    $manualPayment,
                    null,
                    null,
                    $documentNumber,
                    $trackingCode,
                    $issuedAt,
                    $expiresAt
                );


                /*
                |--------------------------------------------------------------------------
                | DEDICATED TRANSIT NUMBER
                |--------------------------------------------------------------------------
                |
                | Manual membership documents do not have submitted field values,
                | but transit passes still need their own transit number.
                |
                */

                $transitNo = $this->generateTransitNumber(
                    $document,
                    []
                );

                if ($transitNo !== null) {
                    $fieldValues['transit_no'] = $transitNo;
                }


                $generatedDocument = GeneratedDocument::create([
                    'user_id' => $membership->user_id,
                    'document_id' => $document->id,
                    'transaction_id' => null,
                    'document_number' => $documentNumber,
                    'tracking_code' => $trackingCode,
                    'issued_at' => $issuedAt->toDateString(),
                    'expires_at' => $expiresAt
                        ? $expiresAt->toDateString()
                        : null,
                    'status' => 'active',
                    'field_values' => $fieldValues,
                ]);

                $generatedDocuments[] =
                    $generatedDocument;
            }

            return $generatedDocuments;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE SINGLE MEMBERSHIP DOCUMENT
    |--------------------------------------------------------------------------
    */

    protected function generateMembershipDocument(
        Membership $membership,
        Document $document,
        Payment $payment,
        ?Transaction $debitTransaction,
        Transaction $creditTransaction
    ): GeneratedDocument {
        $user = $membership->user;

        if (!$user) {
            throw new RuntimeException(
                'Membership user could not be found.'
            );
        }

        $profile = MemberProfile::where(
            'user_id',
            $user->id
        )->first();

        $category = $membership->membershipCategory;

        if (!$category) {
            throw new RuntimeException(
                'Membership category could not be found.'
            );
        }

        if (!$creditTransaction->debit_transaction_id) {
            throw new RuntimeException(
                'Membership credit transaction is not linked to a debit transaction.'
            );
        }

        if (
            (int) $creditTransaction->user_id !==
            (int) $membership->user_id
        ) {
            throw new RuntimeException(
                'Credit transaction does not belong to this membership.'
            );
        }

        if (!$debitTransaction) {
            throw new RuntimeException(
                'The membership payment debit transaction could not be found.'
            );
        }

        if (
            (int) $creditTransaction->debit_transaction_id !==
            (int) $debitTransaction->id
        ) {
            throw new RuntimeException(
                'Credit transaction is not linked to the membership payment debit.'
            );
        }

        if ($debitTransaction->type !== 'debit') {
            throw new RuntimeException(
                'The membership payment transaction is not a debit transaction.'
            );
        }

        if ($debitTransaction->status !== 'paid') {
            throw new RuntimeException(
                'The membership payment debit transaction is not marked as paid.'
            );
        }

        if (
            $payment->payment_item_id !== null &&
            (int) $debitTransaction->payment_item_id !==
            (int) $payment->payment_item_id
        ) {
            throw new RuntimeException(
                'Membership payment debit does not belong to the payment item.'
            );
        }

        $belongsToCategory = $category->documents()
            ->where(
                'documents.id',
                $document->id
            )
            ->exists();

        if (
            !$belongsToCategory &&
            $category->documents()->count() === 0 &&
            (int) $category->document_id ===
            (int) $document->id
        ) {
            $belongsToCategory = true;
        }

        if (!$belongsToCategory) {
            throw new RuntimeException(
                'Document "' .
                    $document->name .
                    '" is not configured for membership category "' .
                    $category->name .
                    '".'
            );
        }

        if (!$document->is_active) {
            throw new RuntimeException(
                'The document is inactive.'
            );
        }

        $existing = GeneratedDocument::where(
            'user_id',
            $user->id
        )
            ->where(
                'document_id',
                $document->id
            )
            ->where(
                'transaction_id',
                $creditTransaction->id
            )
            ->first();

        if ($existing) {
            return $existing;
        }

        $documentNumber = $this->generateDocumentNumber(
            $document,
            $payment
        );

        $trackingCode = $this->generateTrackingCode();

        $issuedAt = $membership->issued_at
            ? Carbon::parse($membership->issued_at)
            : (
                $payment->paid_at
                ? Carbon::parse($payment->paid_at)
                : now()
            );

        $expiresAt = $membership->expires_at
            ? Carbon::parse($membership->expires_at)
            : $this->calculateDocumentExpiry(
                $document,
                $issuedAt
            );

        $fieldValues = $this->buildMembershipFieldValues(
            $document,
            $user,
            $profile,
            $membership,
            $category,
            $payment,
            $debitTransaction,
            $creditTransaction,
            $documentNumber,
            $trackingCode,
            $issuedAt,
            $expiresAt
        );

        $transitNo = $this->generateTransitNumber($document, []);

        if ($transitNo !== null) {
            $fieldValues['transit_no'] = $transitNo;
        }

        $generatedDocument = GeneratedDocument::create([
            'user_id' => $user->id,
            'document_id' => $document->id,
            'transaction_id' => $creditTransaction->id,
            'document_number' => $documentNumber,
            'tracking_code' => $trackingCode,
            'issued_at' => $issuedAt->toDateString(),
            'expires_at' => $expiresAt
                ? $expiresAt->toDateString()
                : null,
            'status' => 'active',
            'field_values' => $fieldValues,
        ]);

        $this->handleRenewalReplacement(
            $generatedDocument,
            $user->id,
            $document->id
        );

        return $generatedDocument;
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE MEMBERSHIP CERTIFICATE
    |--------------------------------------------------------------------------
    */

    public function generateMembershipCertificate(
        $userOrMembership,
        ?Membership $membership = null
    ): ?GeneratedDocument {
        if (
            $userOrMembership instanceof Membership &&
            $membership === null
        ) {
            $membership = $userOrMembership;
        }

        if (!$membership) {
            throw new RuntimeException(
                'Membership is required for certificate generation.'
            );
        }

        if (!$membership->user_id) {
            throw new RuntimeException(
                'Membership does not belong to a user.'
            );
        }

        if ($membership->status !== 'active') {
            throw new RuntimeException(
                'Only active memberships can generate documents.'
            );
        }

        if (!$membership->membership_number) {
            throw new RuntimeException(
                'Membership number has not been generated yet.'
            );
        }

        $membership->loadMissing([
            'user',
            'membershipCategory',
        ]);

        $category = $membership->membershipCategory;

        if (!$category) {
            throw new RuntimeException(
                'Membership category could not be found.'
            );
        }

        $documents = $category->documents()
            ->where(
                'documents.is_active',
                true
            )
            ->orderBy(
                'membership_category_documents.sort_order'
            )
            ->get();

        $document = $documents->first(
            function ($document) {
                $code = strtoupper(
                    trim(
                        $document->code ?? ''
                    )
                );

                $name = strtolower(
                    trim(
                        $document->name ?? ''
                    )
                );

                return
                    str_contains(
                        $name,
                        'certificate'
                    )
                    ||
                    str_contains(
                        $code,
                        'CERT'
                    );
            }
        );

        if (
            !$document &&
            $category->document
        ) {
            $legacyDocument = $category->document;

            $name = strtolower(
                trim(
                    $legacyDocument->name ?? ''
                )
            );

            $code = strtoupper(
                trim(
                    $legacyDocument->code ?? ''
                )
            );

            if (
                str_contains(
                    $name,
                    'certificate'
                )
                ||
                str_contains(
                    $code,
                    'CERT'
                )
            ) {
                $document = $legacyDocument;
            }
        }

        if (!$document) {
            throw new RuntimeException(
                'No active membership certificate is configured for this membership category.'
            );
        }

        [
            $payment,
            $debitTransaction,
            $creditTransaction
        ] = $this->findMembershipPaymentTransactions(
            $membership
        );

        if (!$payment) {
            throw new RuntimeException(
                'Successful membership payment could not be found.'
            );
        }

        if (!$creditTransaction) {
            throw new RuntimeException(
                'Successful membership credit transaction could not be found.'
            );
        }

        return $this->generateMembershipDocument(
            $membership,
            $document,
            $payment,
            $debitTransaction,
            $creditTransaction
        );
    }

    /*
    |--------------------------------------------------------------------------
    | BUILD GENERIC FIELD VALUES
    |--------------------------------------------------------------------------
    */

    protected function buildGenericFieldValues(
        Document $document,
        $user,
        ?MemberProfile $profile,
        ?Membership $membership,
        Payment $payment,
        Transaction $creditTransaction,
        string $documentNumber,
        string $trackingCode,
        Carbon $issuedAt,
        ?Carbon $expiresAt,
        array $documentFieldValues = []
    ): array {
        $fields = $document->fields()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $fieldValues = [];

        foreach ($fields as $field) {
            $key = strtolower(
                trim(
                    $field->field_key
                )
            );

            if ($field->is_system) {
                $value = $this->resolveSystemField(
                    $key,
                    $user,
                    $profile,
                    $membership,
                    $payment,
                    $creditTransaction,
                    $document,
                    $documentNumber,
                    $trackingCode,
                    $issuedAt,
                    $expiresAt
                );
            } else {
                $value =
                    $documentFieldValues[$key]
                    ?? $field->default_value
                    ?? null;
            }

            $fieldValues[$key] = $value;
        }

        return $fieldValues;
    }

    /*
    |--------------------------------------------------------------------------
    | GENERIC SYSTEM FIELD RESOLVER
    |--------------------------------------------------------------------------
    */

    protected function resolveSystemField(
        string $fieldKey,
        $user,
        ?MemberProfile $profile,
        ?Membership $membership,
        Payment $payment,
        Transaction $creditTransaction,
        Document $document,
        string $documentNumber,
        string $trackingCode,
        Carbon $issuedAt,
        ?Carbon $expiresAt
    ) {
        $fieldKey = strtolower(
            trim($fieldKey)
        );

        $payment->loadMissing([
            'paymentItem',
            'membershipCategory',
            'membershipCategoryFee',
            'memberFee',
        ]);

        $paymentItem = $payment->paymentItem;

        $paystackReference =
            $payment->paystack_reference
            ?? $payment->payment_reference
            ?? $payment->reference;

        $debitTransaction = null;

        if ($paystackReference) {
            $debitTransaction = Transaction::where(
                'user_id',
                $user->id
            )
                ->where(
                    'type',
                    'debit'
                )
                ->where(
                    'status',
                    'paid'
                )
                ->whereJsonContains(
                    'gateway->paystack',
                    $paystackReference
                )
                ->latest('id')
                ->first();
        }

        if (
            !$debitTransaction &&
            $creditTransaction->debit_transaction_id
        ) {
            $linkedDebit = Transaction::where(
                'id',
                $creditTransaction->debit_transaction_id
            )
                ->where(
                    'user_id',
                    $user->id
                )
                ->where(
                    'type',
                    'debit'
                )
                ->where(
                    'status',
                    'paid'
                )
                ->first();

            if ($linkedDebit) {
                $debitTransaction = $linkedDebit;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | DOCUMENT
        |--------------------------------------------------------------------------
        */

        switch ($fieldKey) {
            case 'receipt_no':
            case 'receipt_number':
            case 'certificate_number':
            case 'document_number':
            case 'ref_no':
                return $documentNumber;

            case 'tracking_code':
            case 'authentication_code':
            case 'verification_code':
                return $trackingCode;

            case 'verification_url':
                return $this->generateVerificationUrl(
                    $trackingCode
                );

            case 'document_id':
                return $document->id;

            case 'document_code':
                return $document->code;

            case 'document_name':
                return $document->name;

            case 'transit_no':
                return $documentNumber; // fallback; the service will override with the real transit_no
        }

        /*
        |--------------------------------------------------------------------------
        | AFFORESTATION SELLER
        |--------------------------------------------------------------------------
        */

        switch ($fieldKey) {
            case 'seller_member_name':
                return $this->getMemberFullName(
                    $profile,
                    $user
                );

            case 'seller_name':
                return $this->getMemberFullName(
                    $profile,
                    $user
                );

            case 'seller_membership_no':
                return $membership?->membership_number;

            case 'seller_dealing_right_no':
                return $this->getActiveOperationalRightNumber(
                    $user,
                    $membership,
                    [
                        'charcoal_dealing_supplier',
                        'charcoal_dealing_dealer',
                    ]
                );

            case 'seller_phone':
                return $profile?->phone
                    ?? $user->phone
                    ?? null;

            case 'lifting_right_no':
                return $this->getActiveOperationalRightNumber(
                    $user,
                    $membership,
                    [
                        'charcoal_lifting',
                        'charcoal_lifting_rcg',
                    ]
                );

            case 'payment_time':
                return $payment->paid_at
                    ? Carbon::parse(
                        $payment->paid_at
                    )->format('H:i:s')
                    : null;
        }

        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        */

        switch ($fieldKey) {
            case 'user_id':
                return $user->id;

            case 'username':
                return $user->username ?? null;

            case 'name':
            case 'member_name':
            case 'member_full_name':
            case 'full_name':
                return $this->getMemberFullName(
                    $profile,
                    $user
                );

            case 'surname':
            case 'last_name':
                return $profile?->surname;

            case 'first_name':
                return $profile?->first_name;

            case 'middle_name':
                return $profile?->middle_name;

            case 'email':
            case 'member_email':
                return $user->email ?? null;

            case 'phone':
            case 'phone_number':
            case 'member_phone':
                return $profile?->phone
                    ?? $user->phone
                    ?? null;

            case 'representative_name':
            case 'contact_name':
                return $profile?->contact_name
                    ?? $this->getMemberFullName(
                        $profile,
                        $user
                    );
        }

        /*
        |--------------------------------------------------------------------------
        | PROFILE
        |--------------------------------------------------------------------------
        */

        switch ($fieldKey) {
            case 'photo':
            case 'profile_photo':
                return $profile?->photo;

            case 'date_of_birth':
            case 'dob':
                return $profile?->date_of_birth;

            case 'gender':
                return $profile?->gender;

            case 'nationality':
                return $profile?->nationality;

            case 'address':
            case 'residential_address':
                return $profile?->address;

            case 'city':
                return $profile?->city;

            case 'state':
                return $profile?->state;

            case 'lga':
                return $profile?->lga;
        }

        /*
        |--------------------------------------------------------------------------
        | BUSINESS
        |--------------------------------------------------------------------------
        */

        switch ($fieldKey) {
            case 'business_name':
            case 'company_name':
                return $profile?->business_name;

            case 'business_registration_number':
            case 'registration_number':
            case 'cac_number':
                return $profile?->business_registration_number;

            case 'business_type':
                return $profile?->business_type;

            case 'business_address':
                return $profile?->business_address
                    ?? $profile?->address;
        }

        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP
        |--------------------------------------------------------------------------
        */

        switch ($fieldKey) {
            case 'membership_number':
            case 'membership_no':
                return $membership?->membership_number;

            case 'membership_status':
                return $membership?->status;

            case 'membership_issued_at':
            case 'membership_issued_date':
            case 'issued_at':
            case 'issued_date':
                return $membership?->issued_at
                    ?? $issuedAt;

            case 'membership_expires_at':
            case 'membership_expiry_date':
            case 'expiry_date':
            case 'expires_at':
                return $membership?->expires_at
                    ?? $expiresAt;

            case 'membership_category':
            case 'membership_category_name':
                return $membership?->membershipCategory?->name
                    ?? $payment->membershipCategory?->name;

            case 'membership_category_code':
                return $membership?->membershipCategory?->code
                    ?? $payment->membershipCategory?->code;

            case 'category':
                return $membership?->membershipCategory?->name
                    ?? $payment->membershipCategory?->name;

            case 'member_type':
                return $user->member_type;

            case 'membership_year':
                return $issuedAt->format('Y');

            case 'membership_year_range':
            case 'membership_period':
                $year = $issuedAt->format('Y');

                return "01 January {$year} - 31 December {$year}";

            case 'date_joined':
                return $membership?->issued_at
                    ? Carbon::parse(
                        $membership->issued_at
                    )->format('Y-m-d')
                    : $issuedAt->format('Y-m-d');
        }

        /*
        |--------------------------------------------------------------------------
        | PAYMENT
        |--------------------------------------------------------------------------
        */

        switch ($fieldKey) {
            case 'payment_id':
                return $payment->id;

            case 'payment_item_id':
                return $payment->payment_item_id;

            case 'payment_item_code':
                return $paymentItem?->code;

            case 'payment_item_name':
                return $paymentItem?->name;

            case 'payment_for':
                if ($paymentItem?->name) {
                    return $paymentItem->name;
                }

                if (
                    $payment->payment_type ===
                    'membership_renewal'
                ) {
                    return 'Annual Membership Renewal';
                }

                if (
                    $payment->payment_type ===
                    'membership'
                ) {
                    return 'Annual Membership Subscription';
                }

                return $payment->payment_type;

            case 'amount':
            case 'amount_paid':
            case 'payment_amount':
                return $payment->amount;

            case 'amount_in_figure':
                return number_format(
                    (float) $payment->amount,
                    2
                );

            case 'amount_in_words':
            case 'amount_words':
            case 'payment_amount_in_words':
                return $this->numberToWords(
                    $payment->amount
                );

            case 'payment_status':
                return $payment->status;

            case 'payment_reference':
            case 'paystack_reference':
            case 'transaction_reference':
                return $payment->paystack_reference
                    ?? $payment->payment_reference
                    ?? $payment->reference;

            case 'payment_date':
            case 'paid_at':
                return $payment->paid_at;
        }

        /*
        |--------------------------------------------------------------------------
        | TRANSACTIONS
        |--------------------------------------------------------------------------
        */

        switch ($fieldKey) {
            case 'transaction_id':
            case 'credit_transaction_id':
                return $creditTransaction->id;

            case 'debit_transaction_id':
                return $debitTransaction?->id;
        }

        /*
        |--------------------------------------------------------------------------
        | ORGANIZATION
        |--------------------------------------------------------------------------
        */

        switch ($fieldKey) {
            case 'organization_name':
            case 'association_name':
                return config(
                    'nacpdean.organization_name'
                );

            case 'organization_short_name':
                return config(
                    'nacpdean.organization_short_name'
                );

            case 'organization_cac_number':
            case 'association_cac_number':
                return config(
                    'nacpdean.cac_number'
                );

            case 'organization_address':
            case 'association_address':
                return config(
                    'nacpdean.address'
                );

            case 'organization_phone':
            case 'association_phone':
                return config(
                    'nacpdean.phone'
                );

            case 'organization_email':
            case 'association_email':
                return config(
                    'nacpdean.email'
                );

            case 'organization_website':
            case 'association_website':
                return config(
                    'nacpdean.website'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | SIGNATORIES
        |--------------------------------------------------------------------------
        */

        switch ($fieldKey) {
            case 'national_president_name':
            case 'president_name':
                return config(
                    'nacpdean.national_president_name'
                );

            case 'national_president_title':
            case 'president_title':
                return config(
                    'nacpdean.national_president_title'
                );

            case 'secretary_general_name':
            case 'secretary_name':
                return config(
                    'nacpdean.secretary_general_name'
                );

            case 'secretary_general_title':
            case 'secretary_title':
                return config(
                    'nacpdean.secretary_general_title'
                );
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | GET ACTIVE OPERATIONAL RIGHT NUMBER
    |--------------------------------------------------------------------------
    */

    protected function getActiveOperationalRightNumber(
        $user,
        ?Membership $membership,
        array $documentTypes
    ): ?string {
        if (!$membership) {
            return null;
        }

        $operationalRight = OperationalRightsDocument::where(
            'user_id',
            $user->id
        )
            ->where(
                'membership_id',
                $membership->id
            )
            ->where(
                'status',
                'active'
            )
            ->whereIn(
                'document_type',
                $documentTypes
            )
            ->whereDate(
                'expires_at',
                '>=',
                now()->toDateString()
            )
            ->latest('id')
            ->first();

        return $operationalRight?->document_number;
    }

    /*
    |--------------------------------------------------------------------------
    | BUILD MEMBERSHIP FIELD VALUES
    |--------------------------------------------------------------------------
    */

    protected function buildMembershipFieldValues(
        Document $document,
        $user,
        ?MemberProfile $profile,
        Membership $membership,
        MembershipCategory $category,
        Payment $payment,
        ?Transaction $debitTransaction,
        ?Transaction $creditTransaction,
        string $documentNumber,
        string $trackingCode,
        Carbon $issuedAt,
        ?Carbon $expiresAt
    ): array {
        $fields = $document->fields()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $fieldValues = [];

        foreach ($fields as $field) {
            $key = strtolower(
                trim(
                    $field->field_key
                )
            );

            if ($field->is_system) {
                $value = $this->resolveMembershipSystemField(
                    $key,
                    $user,
                    $profile,
                    $membership,
                    $category,
                    $payment,
                    $debitTransaction,
                    $creditTransaction,
                    $document,
                    $documentNumber,
                    $trackingCode,
                    $issuedAt,
                    $expiresAt
                );
            } else {
                $value = null;

                if (
                    $profile &&
                    isset($profile->{$key}) &&
                    $profile->{$key} !== null
                ) {
                    $value = $profile->{$key};
                }

                if (
                    $value === null &&
                    $field->default_value !== null
                ) {
                    $value = $field->default_value;
                }
            }

            $fieldValues[$key] = $value;
        }

        return $fieldValues;
    }

    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP SYSTEM FIELD RESOLVER
    |--------------------------------------------------------------------------
    */

    protected function resolveMembershipSystemField(
        string $fieldKey,
        $user,
        ?MemberProfile $profile,
        Membership $membership,
        MembershipCategory $category,
        Payment $payment,
        ?Transaction $debitTransaction,
        ?Transaction $creditTransaction,
        Document $document,
        string $documentNumber,
        string $trackingCode,
        Carbon $issuedAt,
        ?Carbon $expiresAt
    ) {
        $fieldKey = strtolower(
            trim($fieldKey)
        );

        $payment->loadMissing([
            'paymentItem',
            'membershipCategory',
            'membershipCategoryFee',
            'memberFee',
        ]);

        $paymentItem = $payment->paymentItem;

        $isAnnualMembershipReceipt =
            $this->isAnnualMembershipReceipt(
                $document
            );

        $membershipYear = $this->resolveMembershipYear(
            $membership,
            $payment,
            $paymentItem,
            $issuedAt
        );

        $membershipYearRange =
            "01 January {$membershipYear} - 31 December {$membershipYear}";

        $paymentDate = $payment->paid_at
            ? Carbon::parse($payment->paid_at)
            : null;

        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        */

        switch ($fieldKey) {
            case 'user_id':
                return $user->id;

            case 'username':
                return $user->username ?? null;

            case 'member_name':
            case 'member_full_name':
            case 'full_name':
            case 'name':
                return $this->getMemberFullName(
                    $profile,
                    $user
                );

            case 'surname':
            case 'last_name':
                return $profile?->surname;

            case 'first_name':
                return $profile?->first_name;

            case 'middle_name':
                return $profile?->middle_name;

            case 'phone':
            case 'phone_number':
            case 'member_phone':
                return $profile?->phone
                    ?? $user->phone
                    ?? null;

            case 'email':
            case 'member_email':
                return $user->email ?? null;

            case 'representative_name':
            case 'contact_name':
                return $profile?->contact_name
                    ?? $this->getMemberFullName(
                        $profile,
                        $user
                    );
        }

        /*
        |--------------------------------------------------------------------------
        | PROFILE
        |--------------------------------------------------------------------------
        */

        switch ($fieldKey) {
            case 'photo':
            case 'profile_photo':
                return $profile?->photo;

            case 'date_of_birth':
            case 'dob':
                return $profile?->date_of_birth;

            case 'gender':
                return $profile?->gender;

            case 'nationality':
                return $profile?->nationality;

            case 'address':
            case 'residential_address':
                return $profile?->address;

            case 'city':
                return $profile?->city;

            case 'state':
                return $profile?->state;

            case 'lga':
                return $profile?->lga;
        }

        /*
        |--------------------------------------------------------------------------
        | BUSINESS
        |--------------------------------------------------------------------------
        */

        switch ($fieldKey) {
            case 'business_name':
            case 'company_name':
                return $profile?->business_name;

            case 'business_registration_number':
            case 'registration_number':
            case 'cac_number':
                return $profile?->business_registration_number;

            case 'business_type':
                return $profile?->business_type;

            case 'business_address':
                return $profile?->business_address
                    ?? $profile?->address;
        }

        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP
        |--------------------------------------------------------------------------
        */

        switch ($fieldKey) {
            case 'membership_number':
            case 'membership_no':
                return $membership->membership_number;

            case 'membership_id':
                return $membership->membership_number;

            case 'membership_position':
            case 'position':
            case 'officer_position':
                return $membership->membership_position;

            case 'executive_id':
            case 'national_executive_id':
            case 'nem_code':
            case 'nem_id':
                return $membership->executive_id;

            case 'taskforce_id':
                return $membership->taskforce_id;

            case 'taskforce_position':
                return $membership->taskforce_position;

            case 'taskforce_level':
                return $membership->taskforce_level;

            case 'taskforce_state':
                return $membership->taskforce_state;

            case 'membership_status':
                return $membership->status;

            case 'membership_issued_at':
            case 'membership_issued_date':
            case 'issued_at':
            case 'issued_date':
                return $issuedAt;

            case 'membership_expires_at':
            case 'membership_expiry_date':
            case 'expiry_date':
            case 'expires_at':
                return $expiresAt;

            case 'membership_category':
            case 'membership_category_name':
                return $category->name;

            case 'membership_category_code':
                return $category->code;

            case 'category':
                return $category->name;

            case 'member_type':
                return $user->member_type;

            case 'membership_year':
                if ($isAnnualMembershipReceipt) {
                    return $membershipYearRange;
                }

                return (string) $membershipYear;

            case 'membership_year_number':
                return (string) $membershipYear;

            case 'membership_year_range':
            case 'membership_period':
                return $membershipYearRange;

            case 'date_joined':
                return $membership->issued_at
                    ? Carbon::parse(
                        $membership->issued_at
                    )->format('Y-m-d')
                    : $issuedAt->format('Y-m-d');
        }

        /*
        |--------------------------------------------------------------------------
        | DOCUMENT
        |--------------------------------------------------------------------------
        */

        switch ($fieldKey) {
            case 'document_number':
            case 'certificate_number':
            case 'receipt_number':
            case 'receipt_no':
            case 'ref_no':
                return $documentNumber;

            case 'tracking_code':
            case 'authentication_code':
            case 'verification_code':
                return $trackingCode;

            case 'verification_url':
                return $this->generateVerificationUrl(
                    $trackingCode
                );

            case 'document_id':
                return $document->id;

            case 'document_code':
                return $document->code;

            case 'document_name':
                return $document->name;
        }

        /*
        |--------------------------------------------------------------------------
        | PAYMENT
        |--------------------------------------------------------------------------
        */

        switch ($fieldKey) {
            case 'payment_id':
                return $payment->id;

            case 'payment_item_id':
                return $payment->payment_item_id;

            case 'payment_item_code':
                return $paymentItem?->code;

            case 'payment_item_name':
                return $paymentItem?->name;

            case 'payment_for':
                if ($isAnnualMembershipReceipt) {
                    return
                        'Annual membership subscription ' .
                        $membershipYear;
                }

                if ($paymentItem?->name) {
                    return $paymentItem->name;
                }

                if (
                    $payment->payment_type ===
                    'membership_renewal'
                ) {
                    return 'Annual Membership Renewal';
                }

                if (
                    $payment->payment_type ===
                    'membership'
                ) {
                    return 'Annual Membership Subscription';
                }

                return $payment->payment_type;

            case 'amount':
            case 'amount_paid':
            case 'payment_amount':
                return $payment->amount;

            case 'amount_in_figure':
                return number_format(
                    (float) $payment->amount,
                    2
                );

            case 'amount_in_words':
            case 'amount_words':
            case 'payment_amount_in_words':
                return $this->numberToWords(
                    $payment->amount
                );

            case 'payment_status':
                return $payment->status;

            case 'payment_reference':
            case 'paystack_reference':
            case 'transaction_reference':
                return $payment->paystack_reference
                    ?? $payment->payment_reference
                    ?? $payment->reference;

            case 'payment_date':
            case 'date_of_payment':
            case 'paid_at':
                return $paymentDate;
        }

        /*
        |--------------------------------------------------------------------------
        | TRANSACTIONS
        |--------------------------------------------------------------------------
        */

        switch ($fieldKey) {
            case 'transaction_id':
            case 'credit_transaction_id':
                return $creditTransaction?->id;

            case 'debit_transaction_id':
                return $debitTransaction?->id;
        }

        /*
        |--------------------------------------------------------------------------
        | ORGANIZATION
        |--------------------------------------------------------------------------
        */

        switch ($fieldKey) {
            case 'organization_name':
            case 'association_name':
                return config(
                    'nacpdean.organization_name'
                );

            case 'organization_short_name':
                return config(
                    'nacpdean.organization_short_name'
                );

            case 'organization_cac_number':
            case 'association_cac_number':
                return config(
                    'nacpdean.cac_number'
                );

            case 'organization_address':
            case 'association_address':
                return config(
                    'nacpdean.address'
                );

            case 'organization_phone':
            case 'association_phone':
                return config(
                    'nacpdean.phone'
                );

            case 'organization_email':
            case 'association_email':
                return config(
                    'nacpdean.email'
                );

            case 'organization_website':
            case 'association_website':
                return config(
                    'nacpdean.website'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | SIGNATORIES
        |--------------------------------------------------------------------------
        */

        switch ($fieldKey) {
            case 'national_president_name':
            case 'president_name':
                return config(
                    'nacpdean.national_president_name'
                );

            case 'national_president_title':
            case 'president_title':
                return config(
                    'nacpdean.national_president_title'
                );

            case 'secretary_general_name':
            case 'secretary_name':
                return config(
                    'nacpdean.secretary_general_name'
                );

            case 'secretary_general_title':
            case 'secretary_title':
                return config(
                    'nacpdean.secretary_general_title'
                );
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | DETERMINE ANNUAL MEMBERSHIP RECEIPT
    |--------------------------------------------------------------------------
    */

    protected function isAnnualMembershipReceipt(
        Document $document
    ): bool {
        $code = strtoupper(
            trim(
                (string) ($document->code ?? '')
            )
        );

        $name = strtolower(
            trim(
                (string) ($document->name ?? '')
            )
        );

        return
            $code === 'NACPDEAN-ANNUAL-MEMBERSHIP-RECEIPT'
            ||
            (
                str_contains(
                    $name,
                    'annual membership'
                )
                &&
                str_contains(
                    $name,
                    'receipt'
                )
            );
    }

    /*
    |--------------------------------------------------------------------------
    | RESOLVE MEMBERSHIP YEAR
    |--------------------------------------------------------------------------
    */

    protected function resolveMembershipYear(
        Membership $membership,
        Payment $payment,
        $paymentItem,
        Carbon $issuedAt
    ): int {
        $sources = [
            $paymentItem?->name,
            $paymentItem?->code,
        ];

        foreach ($sources as $source) {
            if (
                $source &&
                preg_match(
                    '/\b(20\d{2})\b/',
                    (string) $source,
                    $matches
                )
            ) {
                return (int) $matches[1];
            }
        }

        if ($membership->expires_at) {
            return Carbon::parse(
                $membership->expires_at
            )->year;
        }

        if ($payment->paid_at) {
            return Carbon::parse(
                $payment->paid_at
            )->year;
        }

        if ($membership->issued_at) {
            return Carbon::parse(
                $membership->issued_at
            )->year;
        }

        return $issuedAt->year;
    }

    /*
    |--------------------------------------------------------------------------
    | FIND MEMBERSHIP PAYMENT + TRANSACTIONS
    |--------------------------------------------------------------------------
    */

    protected function findMembershipPaymentTransactions(
        Membership $membership
    ): array {
        $payment = Payment::where(
            'user_id',
            $membership->user_id
        )
            ->where(
                'status',
                'paid'
            )
            ->where(
                'payment_type',
                'membership'
            )
            ->where(
                'membership_category_id',
                $membership->membership_category_id
            )
            ->latest('id')
            ->first();

        if (!$payment) {
            $payment = Payment::where(
                'user_id',
                $membership->user_id
            )
                ->where(
                    'status',
                    'paid'
                )
                ->where(
                    'payment_type',
                    'membership_renewal'
                )
                ->where(
                    'membership_category_id',
                    $membership->membership_category_id
                )
                ->latest('id')
                ->first();
        }

        if (!$payment) {
            return [
                null,
                null,
                null,
            ];
        }

        $paystackReference =
            $payment->paystack_reference
            ?? $payment->payment_reference
            ?? $payment->reference;

        $debitTransaction = null;

        if ($paystackReference) {
            $debitTransaction = Transaction::where(
                'user_id',
                $membership->user_id
            )
                ->where(
                    'type',
                    'debit'
                )
                ->where(
                    'status',
                    'paid'
                )
                ->whereJsonContains(
                    'gateway->paystack',
                    $paystackReference
                )
                ->latest('id')
                ->first();
        }

        if (
            !$debitTransaction &&
            $payment->payment_item_id
        ) {
            $debitTransaction = Transaction::where(
                'user_id',
                $membership->user_id
            )
                ->where(
                    'type',
                    'debit'
                )
                ->where(
                    'status',
                    'paid'
                )
                ->where(
                    'payment_item_id',
                    $payment->payment_item_id
                )
                ->latest('id')
                ->first();
        }

        if (!$debitTransaction) {
            return [
                $payment,
                null,
                null,
            ];
        }

        $creditTransaction = Transaction::where(
            'user_id',
            $membership->user_id
        )
            ->where(
                'type',
                'credit'
            )
            ->where(
                'status',
                'paid'
            )
            ->where(
                'debit_transaction_id',
                $debitTransaction->id
            )
            ->latest('id')
            ->first();

        return [
            $payment,
            $debitTransaction,
            $creditTransaction,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE DOCUMENT NUMBER
    |--------------------------------------------------------------------------
    */

    protected function generateDocumentNumber(
        Document $document,
        ?Payment $payment = null
    ): string {
        $code = $document->code ?? 'DOC';

        $code = strtoupper(
            preg_replace(
                '/[^A-Za-z0-9\-]/',
                '',
                $code
            )
        );

        return
            $code .
            '-' .
            now()->format('Y') .
            '-' .
            strtoupper(
                Str::random(8)
            );
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE TRACKING CODE
    |--------------------------------------------------------------------------
    */

    protected function generateTrackingCode(): string
    {
        return
            'NACP-' .
            strtoupper(
                Str::random(20)
            );
    }



    /*
    |--------------------------------------------------------------------------
    | GENERATE TRANSIT NUMBER
    |--------------------------------------------------------------------------
    |
    | Transit passes get their own human-readable number, independent
    | of the internal document_number. Format:
    |
    |     TRP-<YEAR>-<STATE_CODE>-<SERIAL>
    |
    | Example: TRP-2026-LAG-000123
    |
    | Only documents whose code is NACPDEAN-CHARCOAL-TRANSIT-PASS
    | receive a value. All other documents return null.
    |
    */

    protected function generateTransitNumber(
        Document $document,
        array $documentFieldValues = []
    ): ?string {
        if (
            strtoupper(trim((string) $document->code))
            !== 'NACPDEAN-CHARCOAL-TRANSIT-PASS'
        ) {
            return null;
        }

        $year = (int) now()->format('Y');

        /*
        |--------------------------------------------------------------------------
        | STATE CODE
        |--------------------------------------------------------------------------
        |
        | Pulled from the submitted document field values first
        | (the member enters their state during the payment flow),
        | then from the profile, then defaulted to "NG".
        |
        */

        $stateRaw = $documentFieldValues['state']
            ?? $documentFieldValues['seller_state']
            ?? 'NG';

        $stateCode = strtoupper(
            preg_replace(
                '/[^A-Za-z]/',
                '',
                (string) $stateRaw
            )
        );

        $stateCode = substr($stateCode, 0, 3);

        if ($stateCode === '') {
            $stateCode = 'NG';
        }

        /*
        |--------------------------------------------------------------------------
        | SERIAL
        |--------------------------------------------------------------------------
        |
        | Count existing transit passes generated this year.
        | Serial is zero-padded to 6 digits and checked for
        | uniqueness across all documents that already store
        | a transit_no in field_values.
        |
        */

        $existingSerialCount = GeneratedDocument::query()
            ->whereYear('created_at', $year)
            ->whereHas('document', function ($query) {
                $query->where(
                    'code',
                    'NACPDEAN-CHARCOAL-TRANSIT-PASS'
                );
            })
            ->count();

        $serial = $existingSerialCount + 1;

        do {
            $transitNo = sprintf(
                'TRP-%d-%s-%06d',
                $year,
                $stateCode,
                $serial
            );

            $exists = GeneratedDocument::query()
                ->whereJsonContains(
                    'field_values->transit_no',
                    $transitNo
                )
                ->exists();

            if ($exists) {
                $serial++;
            }
        } while ($exists);

        return $transitNo;
    }



    /*
    |--------------------------------------------------------------------------
    | VERIFICATION URL
    |--------------------------------------------------------------------------
    */

    protected function generateVerificationUrl(
        string $trackingCode
    ): string {
        return route(
            'documents.verify',
            $trackingCode
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MEMBER FULL NAME
    |--------------------------------------------------------------------------
    */

    protected function getMemberFullName(
        ?MemberProfile $profile,
        $user
    ): string {
        if ($profile) {
            $parts = array_filter([
                $profile->first_name,
                $profile->middle_name,
                $profile->surname,
            ]);

            if (!empty($parts)) {
                return trim(
                    implode(
                        ' ',
                        $parts
                    )
                );
            }
        }

        return trim(
            $user->name
                ?? $user->username
                ?? ''
        );
    }

    /*
    |--------------------------------------------------------------------------
    | NUMBER TO WORDS
    |--------------------------------------------------------------------------
    */

    public function numberToWords(
        float|int|string $number
    ): string {
        $number = (float) $number;

        if ($number === 0.0) {
            return 'Zero Naira Only';
        }

        $formatter = new \NumberFormatter(
            'en',
            \NumberFormatter::SPELLOUT
        );

        $naira = floor($number);

        $kobo = round(
            ($number - $naira) * 100
        );

        $result =
            ucfirst(
                $formatter->format($naira)
            ) .
            ' Naira';

        if ($kobo > 0) {
            $result .=
                ' and ' .
                ucfirst(
                    $formatter->format($kobo)
                ) .
                ' Kobo';
        }

        return $result . ' Only';
    }

    /*
    |--------------------------------------------------------------------------
    | CALCULATE DOCUMENT EXPIRY
    |--------------------------------------------------------------------------
    */

    protected function calculateDocumentExpiry(
        Document $document,
        Carbon $issuedAt
    ): ?Carbon {
        $type = strtolower(
            trim(
                (string) (
                    $document->validity_type
                    ?? ''
                )
            )
        );

        $value = (int) (
            $document->validity_value
            ?? 0
        );

        switch ($type) {
            case 'days':
                return $value > 0
                    ? $issuedAt->copy()->addDays($value)
                    : null;

            case 'months':
                return $value > 0
                    ? $issuedAt->copy()->addMonths($value)
                    : null;

            case 'years':
                return $value > 0
                    ? $issuedAt->copy()->addYears($value)
                    : null;

            case 'fixed_date':
                return $document->validity_date
                    ? Carbon::parse(
                        $document->validity_date
                    )
                    : null;

            case 'year_end':
                return $issuedAt->copy()->endOfYear();

            default:
                return null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RENEWAL / REPLACEMENT
    |--------------------------------------------------------------------------
    */

    protected function handleRenewalReplacement(
        GeneratedDocument $generatedDocument,
        int $userId,
        int $documentId
    ): void {
        GeneratedDocument::where(
            'user_id',
            $userId
        )
            ->where(
                'document_id',
                $documentId
            )
            ->where(
                'id',
                '!=',
                $generatedDocument->id
            )
            ->where(
                'status',
                'active'
            )
            ->update([
                'status' => 'replaced',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | REGENERATE MEMBERSHIP DOCUMENTS AFTER OFFICER APPOINTMENT
    |--------------------------------------------------------------------------
    */

    public function regenerateMembershipDocumentsForOfficerAppointment(
        Membership $membership
    ): array {
        return DB::transaction(function () use ($membership) {
            $membership->loadMissing([
                'user',
                'membershipCategory',
            ]);

            $user = $membership->user;

            if (!$user) {
                throw new RuntimeException(
                    'Membership user could not be found.'
                );
            }

            if ($membership->status !== 'active') {
                throw new RuntimeException(
                    'Only active memberships can regenerate documents.'
                );
            }

            if (!$membership->membership_number) {
                throw new RuntimeException(
                    'Membership number has not been generated.'
                );
            }

            $category = $membership->membershipCategory;

            if (!$category) {
                throw new RuntimeException(
                    'Membership category could not be determined.'
                );
            }

            if (!$category->status) {
                throw new RuntimeException(
                    'The membership category is inactive.'
                );
            }

            $documents = $category->documents()
                ->where(
                    'documents.is_active',
                    true
                )
                ->orderBy(
                    'membership_category_documents.sort_order'
                )
                ->orderBy(
                    'documents.id'
                )
                ->get();

            if (
                $documents->isEmpty() &&
                $category->document_id
            ) {
                $legacyDocument = Document::with('fields')
                    ->where(
                        'id',
                        $category->document_id
                    )
                    ->where(
                        'is_active',
                        true
                    )
                    ->first();

                if ($legacyDocument) {
                    $documents = collect([
                        $legacyDocument,
                    ]);
                }
            }

            if ($documents->isEmpty()) {
                throw new RuntimeException(
                    'No active documents are configured for membership category: ' .
                        $category->name
                );
            }

            [
                $payment,
                $debitTransaction,
                $creditTransaction
            ] = $this->findMembershipPaymentTransactions(
                $membership
            );

            if (!$payment) {
                throw new RuntimeException(
                    'A successful membership payment could not be found for this membership.'
                );
            }

            if (!$creditTransaction) {
                throw new RuntimeException(
                    'A successful membership credit transaction could not be found for this membership payment.'
                );
            }

            $profile = MemberProfile::where(
                'user_id',
                $membership->user_id
            )->first();

            $generatedDocuments = [];

            foreach ($documents as $document) {
                $issuedAt = $membership->issued_at
                    ? Carbon::parse(
                        $membership->issued_at
                    )
                    : now();

                $expiresAt = $membership->expires_at
                    ? Carbon::parse(
                        $membership->expires_at
                    )
                    : $this->calculateDocumentExpiry(
                        $document,
                        $issuedAt
                    );

                $documentNumber =
                    $this->generateDocumentNumber(
                        $document,
                        $payment
                    );

                $trackingCode =
                    $this->generateTrackingCode();

                $fieldValues =
                    $this->buildMembershipFieldValues(
                        $document,
                        $user,
                        $profile,
                        $membership,
                        $category,
                        $payment,
                        $debitTransaction,
                        $creditTransaction,
                        $documentNumber,
                        $trackingCode,
                        $issuedAt,
                        $expiresAt
                    );


                /*
                    |--------------------------------------------------------------------------
                    | DEDICATED TRANSIT NUMBER
                    |--------------------------------------------------------------------------
                    */

                $transitNo = $this->generateTransitNumber(
                    $document,
                    []
                );

                if ($transitNo !== null) {
                    $fieldValues['transit_no'] = $transitNo;
                }


                $generatedDocument =
                    GeneratedDocument::create([
                        'user_id' =>
                        $membership->user_id,

                        'document_id' =>
                        $document->id,

                        'transaction_id' =>
                        $creditTransaction->id,

                        'document_number' =>
                        $documentNumber,

                        'tracking_code' =>
                        $trackingCode,

                        'issued_at' =>
                        $issuedAt->toDateString(),

                        'expires_at' =>
                        $expiresAt
                            ? $expiresAt->toDateString()
                            : null,

                        'status' =>
                        'active',

                        'field_values' =>
                        $fieldValues,
                    ]);

                GeneratedDocument::where(
                    'user_id',
                    $membership->user_id
                )
                    ->where(
                        'document_id',
                        $document->id
                    )
                    ->where(
                        'id',
                        '!=',
                        $generatedDocument->id
                    )
                    ->where(
                        'status',
                        'active'
                    )
                    ->update([
                        'status' =>
                        'replaced',
                    ]);

                $generatedDocuments[] =
                    $generatedDocument;
            }

            return $generatedDocuments;
        });
    }
}
