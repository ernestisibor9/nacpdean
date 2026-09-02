<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\GeneratedDocument;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | VIEW DOCUMENT
    |--------------------------------------------------------------------------
    |
    | This method is responsible for displaying the document definition
    | / document access page.
    |
    | Authorization chain:
    |
    | USER
    |   ↓
    | PAID DEBIT TRANSACTION
    |   ↓
    | PAYMENT ITEM
    |   ↓
    | DOCUMENT
    |
    |--------------------------------------------------------------------------
    */

    public function show(Request $request, $documentId)
    {
        /*
        |--------------------------------------------------------------------------
        | AUTHENTICATED USER
        |--------------------------------------------------------------------------
        */

        $user = Auth::user();

        if (!$user) {
            return redirect()
                ->route('login')
                ->with('error', 'Please login to continue.');
        }


        /*
        |--------------------------------------------------------------------------
        | DOCUMENT
        |--------------------------------------------------------------------------
        */

        $document = Document::query()
            ->where('id', $documentId)
            ->where('is_active', true)
            ->first();

        if (!$document) {
            abort(404, 'Document not found.');
        }


        /*
        |--------------------------------------------------------------------------
        | FIND PAID DEBIT TRANSACTION
        |--------------------------------------------------------------------------
        |
        | The DEBIT transaction represents the user's obligation/payment
        | item that has been successfully paid.
        |
        |--------------------------------------------------------------------------
        */

        $paidDebit = Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', 'debit')
            ->where('status', 'paid')
            ->whereHas('paymentItem', function ($query) use ($documentId) {

                $query
                    ->where('is_active', true)
                    ->where('document_id', $documentId);

            })
            ->with([
                'paymentItem.document',
                'creditTransaction',
            ])
            ->latest('id')
            ->first();


        /*
        |--------------------------------------------------------------------------
        | NO PAID DEBIT
        |--------------------------------------------------------------------------
        */

        if (!$paidDebit) {

            abort(
                403,
                'Payment required before this document can be accessed.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PAYMENT ITEM
        |--------------------------------------------------------------------------
        */

        $paymentItem = $paidDebit->paymentItem;

        if (!$paymentItem) {

            Log::critical(
                'PAID DEBIT HAS NO PAYMENT ITEM',
                [
                    'user_id' =>
                        $user->id,

                    'debit_transaction_id' =>
                        $paidDebit->id,
                ]
            );

            abort(
                403,
                'The payment item associated with this payment could not be found.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY PAYMENT ITEM → DOCUMENT
        |--------------------------------------------------------------------------
        */

        if (
            !$paymentItem->document_id ||
            (int) $paymentItem->document_id !==
            (int) $document->id
        ) {

            Log::critical(
                'PAYMENT ITEM DOCUMENT MISMATCH',
                [
                    'user_id' =>
                        $user->id,

                    'debit_transaction_id' =>
                        $paidDebit->id,

                    'payment_item_id' =>
                        $paymentItem->id,

                    'payment_item_document_id' =>
                        $paymentItem->document_id,

                    'document_id' =>
                        $document->id,
                ]
            );

            abort(
                403,
                'This payment does not unlock the requested document.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FIND CREDIT TRANSACTION
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | The CREDIT transaction is linked to the original DEBIT through:
        |
        | transactions.debit_transaction_id
        |
        | Therefore:
        |
        | CREDIT.debit_transaction_id = DEBIT.id
        |
        |--------------------------------------------------------------------------
        */

        $creditTransaction = Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', 'credit')
            ->where('status', 'paid')
            ->where('payment_item_id', $paymentItem->id)
            ->where(
                'debit_transaction_id',
                $paidDebit->id
            )
            ->latest('id')
            ->first();


        /*
        |--------------------------------------------------------------------------
        | CREDIT NOT FOUND
        |--------------------------------------------------------------------------
        */

        if (!$creditTransaction) {

            Log::warning(
                'PAID DEBIT HAS NO CORRESPONDING CREDIT',
                [
                    'user_id' =>
                        $user->id,

                    'debit_transaction_id' =>
                        $paidDebit->id,

                    'payment_item_id' =>
                        $paymentItem->id,
                ]
            );

            abort(
                403,
                'Your payment has not been fully processed yet. Please try again shortly.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FIND GENERATED DOCUMENT
        |--------------------------------------------------------------------------
        |
        | generated_documents.transaction_id MUST contain the CREDIT ID.
        |
        |--------------------------------------------------------------------------
        */

        $generatedDocument = GeneratedDocument::query()
            ->where('user_id', $user->id)
            ->where('document_id', $document->id)
            ->where(
                'transaction_id',
                $creditTransaction->id
            )
            ->latest('id')
            ->first();


        /*
        |--------------------------------------------------------------------------
        | DOCUMENT NOT GENERATED
        |--------------------------------------------------------------------------
        */

        if (!$generatedDocument) {

            /*
            |--------------------------------------------------------------------------
            | If the document requires a form, send the user to create it.
            |--------------------------------------------------------------------------
            */

            if ($document->requires_form) {

                return redirect()->route(
                    'documents.create',
                    $document->id
                );
            }


            /*
            |--------------------------------------------------------------------------
            | If the document does not require a form, generate it.
            |--------------------------------------------------------------------------
            */

            return $this->generateDocument(
                $document,
                $paymentItem,
                $paidDebit,
                $user,
                []
            );
        }


        /*
        |--------------------------------------------------------------------------
        | REVOKED DOCUMENT
        |--------------------------------------------------------------------------
        */

        if ($generatedDocument->status === 'revoked') {

            return view(
                'documents.revoked',
                compact('generatedDocument')
            );
        }


        /*
        |--------------------------------------------------------------------------
        | EXPIRATION
        |--------------------------------------------------------------------------
        */

        if (
            $generatedDocument->expires_at &&
            now()->startOfDay()->greaterThan(
                $generatedDocument->expires_at
            )
        ) {

            if ($generatedDocument->status !== 'expired') {

                $generatedDocument->update([
                    'status' => 'expired',
                ]);
            }

            return view(
                'documents.expired',
                compact('generatedDocument')
            );
        }


        /*
        |--------------------------------------------------------------------------
        | INVALID STATUS
        |--------------------------------------------------------------------------
        */

        if ($generatedDocument->status !== 'active') {

            abort(
                403,
                'This document is not currently available.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | RETURN DOCUMENT VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'member.documents.show',
            [
                'generatedDocument' =>
                    $generatedDocument,

                'document' =>
                    $document,

                'transaction' =>
                    $creditTransaction,

                'paymentItem' =>
                    $paymentItem,

                'user' =>
                    $user,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VIEW GENERATED DOCUMENT
    |--------------------------------------------------------------------------
    |
    | This method receives generated_documents.id.
    |
    | URL:
    |
    | /documents/generated/{generatedDocument}
    |
    |--------------------------------------------------------------------------
    */

    public function viewDocument($generatedDocumentId)
    {
        /*
        |--------------------------------------------------------------------------
        | AUTHENTICATED USER
        |--------------------------------------------------------------------------
        */

        $user = Auth::user();

        if (!$user) {

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Please login to continue.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | GENERATED DOCUMENT
        |--------------------------------------------------------------------------
        |
        | Immediately restrict by authenticated user.
        |
        |--------------------------------------------------------------------------
        */

        $generatedDocument = GeneratedDocument::query()
            ->with([
                'document',
                'transaction.paymentItem',
                'transaction.debitTransaction',
            ])
            ->where('id', $generatedDocumentId)
            ->where('user_id', $user->id)
            ->first();


        /*
        |--------------------------------------------------------------------------
        | NOT FOUND
        |--------------------------------------------------------------------------
        */

        if (!$generatedDocument) {

            Log::warning(
                'GENERATED DOCUMENT NOT FOUND OR UNAUTHORIZED ACCESS',
                [
                    'user_id' =>
                        $user->id,

                    'generated_document_id' =>
                        $generatedDocumentId,
                ]
            );

            abort(
                404,
                'Document not found.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DOCUMENT DEFINITION
        |--------------------------------------------------------------------------
        */

        $document =
            $generatedDocument->document;

        if (!$document) {

            Log::critical(
                'GENERATED DOCUMENT HAS NO DOCUMENT DEFINITION',
                [
                    'user_id' =>
                        $user->id,

                    'generated_document_id' =>
                        $generatedDocument->id,

                    'document_id' =>
                        $generatedDocument->document_id,
                ]
            );

            abort(
                404,
                'The document definition could not be found.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        |
        | generated_documents.transaction_id
        |             ↓
        | CREDIT TRANSACTION
        |
        |--------------------------------------------------------------------------
        */

        $creditTransaction =
            $generatedDocument->transaction;

        if (!$creditTransaction) {

            Log::critical(
                'GENERATED DOCUMENT HAS NO CREDIT TRANSACTION',
                [
                    'user_id' =>
                        $user->id,

                    'generated_document_id' =>
                        $generatedDocument->id,

                    'transaction_id' =>
                        $generatedDocument->transaction_id,
                ]
            );

            abort(
                404,
                'The payment transaction for this document could not be found.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY CREDIT
        |--------------------------------------------------------------------------
        */

        if ($creditTransaction->type !== 'credit') {

            Log::critical(
                'GENERATED DOCUMENT LINKED TO NON-CREDIT TRANSACTION',
                [
                    'user_id' =>
                        $user->id,

                    'generated_document_id' =>
                        $generatedDocument->id,

                    'transaction_id' =>
                        $creditTransaction->id,

                    'transaction_type' =>
                        $creditTransaction->type,
                ]
            );

            abort(
                403,
                'This document is not linked to a valid payment.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY CREDIT STATUS
        |--------------------------------------------------------------------------
        */

        if ($creditTransaction->status !== 'paid') {

            Log::warning(
                'GENERATED DOCUMENT LINKED TO UNPAID CREDIT',
                [
                    'user_id' =>
                        $user->id,

                    'generated_document_id' =>
                        $generatedDocument->id,

                    'credit_transaction_id' =>
                        $creditTransaction->id,

                    'status' =>
                        $creditTransaction->status,
                ]
            );

            abort(
                403,
                'This document is not currently available.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY CREDIT OWNERSHIP
        |--------------------------------------------------------------------------
        */

        if (
            (int) $creditTransaction->user_id !==
            (int) $user->id
        ) {

            Log::critical(
                'CREDIT TRANSACTION USER MISMATCH',
                [
                    'user_id' =>
                        $user->id,

                    'credit_transaction_id' =>
                        $creditTransaction->id,

                    'credit_user_id' =>
                        $creditTransaction->user_id,
                ]
            );

            abort(
                403,
                'Unauthorized document access.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PAYMENT ITEM
        |--------------------------------------------------------------------------
        */

        $paymentItem =
            $creditTransaction->paymentItem;

        if (!$paymentItem) {

            Log::critical(
                'CREDIT TRANSACTION HAS NO PAYMENT ITEM',
                [
                    'user_id' =>
                        $user->id,

                    'generated_document_id' =>
                        $generatedDocument->id,

                    'credit_transaction_id' =>
                        $creditTransaction->id,

                    'payment_item_id' =>
                        $creditTransaction->payment_item_id,
                ]
            );

            abort(
                404,
                'The payment item for this document could not be found.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY CREDIT → PAYMENT ITEM
        |--------------------------------------------------------------------------
        */

        if (
            !$creditTransaction->payment_item_id ||
            (int) $creditTransaction->payment_item_id !==
            (int) $paymentItem->id
        ) {

            abort(
                403,
                'The payment authorization could not be verified.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY PAYMENT ITEM → DOCUMENT
        |--------------------------------------------------------------------------
        */

        if (
            !$paymentItem->document_id ||
            (int) $paymentItem->document_id !==
            (int) $document->id
        ) {

            Log::critical(
                'PAYMENT ITEM DOCUMENT MISMATCH',
                [
                    'user_id' =>
                        $user->id,

                    'generated_document_id' =>
                        $generatedDocument->id,

                    'credit_transaction_id' =>
                        $creditTransaction->id,

                    'payment_item_id' =>
                        $paymentItem->id,

                    'payment_item_document_id' =>
                        $paymentItem->document_id,

                    'document_id' =>
                        $document->id,
                ]
            );

            abort(
                403,
                'The document authorization could not be verified.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY GENERATED DOCUMENT → DOCUMENT
        |--------------------------------------------------------------------------
        */

        if (
            (int) $generatedDocument->document_id !==
            (int) $document->id
        ) {

            Log::critical(
                'GENERATED DOCUMENT DOCUMENT MISMATCH',
                [
                    'user_id' =>
                        $user->id,

                    'generated_document_id' =>
                        $generatedDocument->id,

                    'generated_document_document_id' =>
                        $generatedDocument->document_id,

                    'document_id' =>
                        $document->id,
                ]
            );

            abort(
                403,
                'The document authorization could not be verified.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY CREDIT → ORIGINAL DEBIT
        |--------------------------------------------------------------------------
        */

        $paidDebit =
            $creditTransaction->debitTransaction;

        if (!$paidDebit) {

            Log::critical(
                'CREDIT TRANSACTION HAS NO ORIGINAL DEBIT',
                [
                    'user_id' =>
                        $user->id,

                    'credit_transaction_id' =>
                        $creditTransaction->id,

                    'debit_transaction_id' =>
                        $creditTransaction->debit_transaction_id,
                ]
            );

            abort(
                403,
                'The original payment transaction could not be verified.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY DEBIT OWNERSHIP
        |--------------------------------------------------------------------------
        */

        if (
            (int) $paidDebit->user_id !==
            (int) $user->id
        ) {

            Log::critical(
                'DEBIT TRANSACTION USER MISMATCH',
                [
                    'user_id' =>
                        $user->id,

                    'credit_transaction_id' =>
                        $creditTransaction->id,

                    'debit_transaction_id' =>
                        $paidDebit->id,

                    'debit_user_id' =>
                        $paidDebit->user_id,
                ]
            );

            abort(
                403,
                'The payment authorization could not be verified.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY DEBIT
        |--------------------------------------------------------------------------
        */

        if (
            $paidDebit->type !== 'debit' ||
            $paidDebit->status !== 'paid'
        ) {

            abort(
                403,
                'The original payment is not valid.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY DEBIT → PAYMENT ITEM
        |--------------------------------------------------------------------------
        */

        if (
            (int) $paidDebit->payment_item_id !==
            (int) $paymentItem->id
        ) {

            Log::critical(
                'DEBIT PAYMENT ITEM MISMATCH',
                [
                    'user_id' =>
                        $user->id,

                    'debit_transaction_id' =>
                        $paidDebit->id,

                    'debit_payment_item_id' =>
                        $paidDebit->payment_item_id,

                    'expected_payment_item_id' =>
                        $paymentItem->id,
                ]
            );

            abort(
                403,
                'The payment authorization could not be verified.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DOCUMENT ACTIVE
        |--------------------------------------------------------------------------
        */

        if (!$document->is_active) {

            abort(
                403,
                'This document is no longer available.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | REVOKED DOCUMENT
        |--------------------------------------------------------------------------
        */

        if ($generatedDocument->status === 'revoked') {

            return view(
                'documents.revoked',
                compact('generatedDocument')
            );
        }


        /*
        |--------------------------------------------------------------------------
        | EXPIRATION
        |--------------------------------------------------------------------------
        */

        if (
            $generatedDocument->expires_at &&
            now()->startOfDay()->greaterThan(
                $generatedDocument->expires_at
            )
        ) {

            if ($generatedDocument->status !== 'expired') {

                $generatedDocument->update([
                    'status' => 'expired',
                ]);
            }

            return view(
                'documents.expired',
                compact('generatedDocument')
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FINAL STATUS
        |--------------------------------------------------------------------------
        */

        if ($generatedDocument->status !== 'active') {

            abort(
                403,
                'This document is not currently available.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FINAL VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'member.documents.show',
            [
                'generatedDocument' =>
                    $generatedDocument,

                'document' =>
                    $document,

                'transaction' =>
                    $creditTransaction,

                'paymentItem' =>
                    $paymentItem,

                'debitTransaction' =>
                    $paidDebit,

                'user' =>
                    $user,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE DOCUMENT
    |--------------------------------------------------------------------------
    |
    | Displays the dynamic document form.
    |
    |--------------------------------------------------------------------------
    */

    public function create(Request $request, $documentId)
    {
        /*
        |--------------------------------------------------------------------------
        | AUTHENTICATED USER
        |--------------------------------------------------------------------------
        */

        $user = Auth::user();

        if (!$user) {

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Please login to continue.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | DOCUMENT
        |--------------------------------------------------------------------------
        */

        $document = Document::query()
            ->where('id', $documentId)
            ->where('is_active', true)
            ->with([
                'fields' => function ($query) {
                    $query->orderBy('sort_order');
                },
            ])
            ->first();

        if (!$document) {
            abort(404, 'Document not found.');
        }


        /*
        |--------------------------------------------------------------------------
        | FIND PAID DEBIT
        |--------------------------------------------------------------------------
        */

        $paidDebit = Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', 'debit')
            ->where('status', 'paid')
            ->whereHas('paymentItem', function ($query) use ($documentId) {

                $query
                    ->where('is_active', true)
                    ->where('document_id', $documentId);

            })
            ->with([
                'paymentItem.document',
                'creditTransaction',
            ])
            ->latest('id')
            ->first();


        /*
        |--------------------------------------------------------------------------
        | NO PAYMENT
        |--------------------------------------------------------------------------
        */

        if (!$paidDebit) {

            abort(
                403,
                'Payment required before this document can be accessed.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PAYMENT ITEM
        |--------------------------------------------------------------------------
        */

        $paymentItem =
            $paidDebit->paymentItem;

        if (!$paymentItem) {

            abort(
                403,
                'The payment item associated with this transaction could not be found.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PAYMENT ITEM → DOCUMENT
        |--------------------------------------------------------------------------
        */

        if (
            !$paymentItem->document_id ||
            (int) $paymentItem->document_id !==
            (int) $document->id
        ) {

            abort(
                403,
                'This payment does not unlock the requested document.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FIND CREDIT
        |--------------------------------------------------------------------------
        */

        $creditTransaction = Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', 'credit')
            ->where('status', 'paid')
            ->where('payment_item_id', $paymentItem->id)
            ->where(
                'debit_transaction_id',
                $paidDebit->id
            )
            ->latest('id')
            ->first();


        /*
        |--------------------------------------------------------------------------
        | CREDIT NOT FOUND
        |--------------------------------------------------------------------------
        */

        if (!$creditTransaction) {

            abort(
                403,
                'Your payment has not been fully processed yet. Please try again shortly.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ALREADY GENERATED
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | generated_documents.transaction_id = CREDIT ID
        |
        |--------------------------------------------------------------------------
        */

        $generatedDocument = GeneratedDocument::query()
            ->where('user_id', $user->id)
            ->where('document_id', $document->id)
            ->where(
                'transaction_id',
                $creditTransaction->id
            )
            ->latest('id')
            ->first();


        if ($generatedDocument) {

            return redirect()->route(
                'documents.generated',
                $generatedDocument->id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DOCUMENT DOES NOT REQUIRE FORM
        |--------------------------------------------------------------------------
        */

        if (!$document->requires_form) {

            return $this->generateDocument(
                $document,
                $paymentItem,
                $paidDebit,
                $user,
                []
            );
        }


        /*
        |--------------------------------------------------------------------------
        | RETURN FORM
        |--------------------------------------------------------------------------
        */

        return view(
            'documents.create',
            [
                'document' =>
                    $document,

                'fields' =>
                    $document->fields,

                'paymentItem' =>
                    $paymentItem,

                'paidTransaction' =>
                    $paidDebit,

                'creditTransaction' =>
                    $creditTransaction,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STORE DOCUMENT FORM
    |--------------------------------------------------------------------------
    |
    | Validates and stores the dynamic document fields.
    |
    |--------------------------------------------------------------------------
    */

    public function store(Request $request, $documentId)
    {
        /*
        |--------------------------------------------------------------------------
        | AUTHENTICATED USER
        |--------------------------------------------------------------------------
        */

        $user = Auth::user();

        if (!$user) {

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Please login to continue.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | DOCUMENT
        |--------------------------------------------------------------------------
        */

        $document = Document::query()
            ->where('id', $documentId)
            ->where('is_active', true)
            ->with([
                'fields' => function ($query) {
                    $query->orderBy('sort_order');
                },
            ])
            ->first();

        if (!$document) {
            abort(404, 'Document not found.');
        }


        /*
        |--------------------------------------------------------------------------
        | FIND PAID DEBIT
        |--------------------------------------------------------------------------
        */

        $paidDebit = Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', 'debit')
            ->where('status', 'paid')
            ->whereHas('paymentItem', function ($query) use ($documentId) {

                $query
                    ->where('is_active', true)
                    ->where('document_id', $documentId);

            })
            ->with([
                'paymentItem.document',
            ])
            ->latest('id')
            ->first();


        if (!$paidDebit) {

            abort(
                403,
                'Payment required before this document can be accessed.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PAYMENT ITEM
        |--------------------------------------------------------------------------
        */

        $paymentItem =
            $paidDebit->paymentItem;

        if (!$paymentItem) {

            abort(
                403,
                'The payment item could not be found.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PAYMENT ITEM → DOCUMENT
        |--------------------------------------------------------------------------
        */

        if (
            !$paymentItem->document_id ||
            (int) $paymentItem->document_id !==
            (int) $document->id
        ) {

            abort(
                403,
                'This payment does not unlock the requested document.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FIND CREDIT
        |--------------------------------------------------------------------------
        */

        $creditTransaction = Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', 'credit')
            ->where('status', 'paid')
            ->where('payment_item_id', $paymentItem->id)
            ->where(
                'debit_transaction_id',
                $paidDebit->id
            )
            ->latest('id')
            ->first();


        if (!$creditTransaction) {

            abort(
                403,
                'Your payment has not been fully processed yet. Please try again shortly.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PREVENT DUPLICATE GENERATION
        |--------------------------------------------------------------------------
        */

        $existingDocument = GeneratedDocument::query()
            ->where('user_id', $user->id)
            ->where('document_id', $document->id)
            ->where(
                'transaction_id',
                $creditTransaction->id
            )
            ->first();


        if ($existingDocument) {

            return redirect()->route(
                'documents.generated',
                $existingDocument->id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BUILD VALIDATION RULES
        |--------------------------------------------------------------------------
        */

        $rules = [];


        foreach ($document->fields as $field) {

            $fieldRules = [];


            /*
            |--------------------------------------------------------------------------
            | REQUIRED / OPTIONAL
            |--------------------------------------------------------------------------
            */

            $fieldRules[] =
                $field->is_required
                    ? 'required'
                    : 'nullable';


            /*
            |--------------------------------------------------------------------------
            | FIELD TYPE
            |--------------------------------------------------------------------------
            */

            switch ($field->field_type) {

                case 'text':
                case 'email':
                case 'tel':
                case 'url':

                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:255';

                    if ($field->field_type === 'email') {
                        $fieldRules[] = 'email';
                    }

                    if ($field->field_type === 'url') {
                        $fieldRules[] = 'url';
                    }

                    break;


                case 'textarea':

                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:5000';

                    break;


                case 'date':

                    $fieldRules[] = 'date';

                    break;


                case 'number':

                    $fieldRules[] = 'numeric';

                    break;


                case 'select':

                    /*
                    |--------------------------------------------------------------------------
                    | Validate configured select options.
                    |--------------------------------------------------------------------------
                    */

                    if (
                        is_array($field->options) &&
                        count($field->options) > 0
                    ) {

                        $options =
                            array_keys(
                                $field->options
                            );

                        $fieldRules[] =
                            'in:' .
                            implode(
                                ',',
                                $options
                            );
                    }

                    break;


                case 'checkbox':

                    $fieldRules[] = 'boolean';

                    break;


                default:

                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:5000';

                    break;
            }


            $rules[
                $field->field_key
            ] = $fieldRules;
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATE
        |--------------------------------------------------------------------------
        */

        $validated =
            $request->validate($rules);


        /*
        |--------------------------------------------------------------------------
        | ONLY STORE DOCUMENT FIELDS
        |--------------------------------------------------------------------------
        */

        $fieldValues = [];


        foreach ($document->fields as $field) {

            if (
                array_key_exists(
                    $field->field_key,
                    $validated
                )
            ) {

                $fieldValues[
                    $field->field_key
                ] =
                    $validated[
                        $field->field_key
                    ];
            }
        }


        /*
        |--------------------------------------------------------------------------
        | GENERATE
        |--------------------------------------------------------------------------
        */

        return $this->generateDocument(
            $document,
            $paymentItem,
            $paidDebit,
            $user,
            $fieldValues
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE DOCUMENT
    |--------------------------------------------------------------------------
    |
    | This is the central document-generation method.
    |
    | IMPORTANT FINANCIAL CHAIN:
    |
    | PAID DEBIT
    |     ↓
    | CREDIT
    |     ↓
    | GENERATED DOCUMENT
    |
    | generated_documents.transaction_id
    | MUST contain the CREDIT transaction ID.
    |
    |--------------------------------------------------------------------------
    */

    protected function generateDocument(
        Document $document,
        $paymentItem,
        Transaction $paidDebit,
        $user,
        array $fieldValues = []
    ) {

        /*
        |--------------------------------------------------------------------------
        | VERIFY DEBIT
        |--------------------------------------------------------------------------
        */

        if (
            $paidDebit->type !== 'debit' ||
            $paidDebit->status !== 'paid'
        ) {

            Log::critical(
                'INVALID DEBIT PASSED TO DOCUMENT GENERATION',
                [
                    'user_id' =>
                        $user->id,

                    'document_id' =>
                        $document->id,

                    'transaction_id' =>
                        $paidDebit->id,

                    'type' =>
                        $paidDebit->type,

                    'status' =>
                        $paidDebit->status,
                ]
            );

            abort(
                403,
                'This payment cannot be used to generate the document.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY USER
        |--------------------------------------------------------------------------
        */

        if (
            (int) $paidDebit->user_id !==
            (int) $user->id
        ) {

            abort(
                403,
                'The payment authorization could not be verified.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY PAYMENT ITEM
        |--------------------------------------------------------------------------
        */

        if (!$paymentItem) {

            abort(
                403,
                'The payment item could not be found.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY DEBIT → PAYMENT ITEM
        |--------------------------------------------------------------------------
        */

        if (
            !$paidDebit->payment_item_id ||
            (int) $paidDebit->payment_item_id !==
            (int) $paymentItem->id
        ) {

            Log::critical(
                'DEBIT PAYMENT ITEM MISMATCH DURING DOCUMENT GENERATION',
                [
                    'user_id' =>
                        $user->id,

                    'debit_transaction_id' =>
                        $paidDebit->id,

                    'debit_payment_item_id' =>
                        $paidDebit->payment_item_id,

                    'payment_item_id' =>
                        $paymentItem->id,
                ]
            );

            abort(
                403,
                'The payment authorization could not be verified.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY PAYMENT ITEM → DOCUMENT
        |--------------------------------------------------------------------------
        */

        if (
            !$paymentItem->document_id ||
            (int) $paymentItem->document_id !==
            (int) $document->id
        ) {

            Log::critical(
                'PAYMENT ITEM DOCUMENT MISMATCH DURING GENERATION',
                [
                    'user_id' =>
                        $user->id,

                    'document_id' =>
                        $document->id,

                    'payment_item_id' =>
                        $paymentItem->id,

                    'payment_item_document_id' =>
                        $paymentItem->document_id,
                ]
            );

            abort(
                403,
                'This payment item does not unlock this document.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FIND CREDIT TRANSACTION
        |--------------------------------------------------------------------------
        |
        | CREDIT.debit_transaction_id = DEBIT.id
        |
        |--------------------------------------------------------------------------
        */

        $creditTransaction = Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', 'credit')
            ->where('status', 'paid')
            ->where(
                'payment_item_id',
                $paymentItem->id
            )
            ->where(
                'debit_transaction_id',
                $paidDebit->id
            )
            ->latest('id')
            ->first();


        /*
        |--------------------------------------------------------------------------
        | CREDIT NOT FOUND
        |--------------------------------------------------------------------------
        */

        if (!$creditTransaction) {

            Log::critical(
                'DOCUMENT GENERATION CREDIT TRANSACTION NOT FOUND',
                [
                    'user_id' =>
                        $user->id,

                    'document_id' =>
                        $document->id,

                    'payment_item_id' =>
                        $paymentItem->id,

                    'debit_transaction_id' =>
                        $paidDebit->id,
                ]
            );

            abort(
                403,
                'The payment could not be fully verified. Please contact support.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY CREDIT
        |--------------------------------------------------------------------------
        */

        if (
            $creditTransaction->type !== 'credit' ||
            $creditTransaction->status !== 'paid'
        ) {

            abort(
                403,
                'The payment credit is not valid.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY CREDIT OWNERSHIP
        |--------------------------------------------------------------------------
        */

        if (
            (int) $creditTransaction->user_id !==
            (int) $user->id
        ) {

            Log::critical(
                'CREDIT USER MISMATCH DURING DOCUMENT GENERATION',
                [
                    'user_id' =>
                        $user->id,

                    'credit_transaction_id' =>
                        $creditTransaction->id,

                    'credit_user_id' =>
                        $creditTransaction->user_id,
                ]
            );

            abort(
                403,
                'The payment authorization could not be verified.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY CREDIT → DEBIT
        |--------------------------------------------------------------------------
        */

        if (
            (int) $creditTransaction->debit_transaction_id !==
            (int) $paidDebit->id
        ) {

            Log::critical(
                'CREDIT → DEBIT RELATIONSHIP MISMATCH',
                [
                    'user_id' =>
                        $user->id,

                    'credit_transaction_id' =>
                        $creditTransaction->id,

                    'credit_debit_transaction_id' =>
                        $creditTransaction->debit_transaction_id,

                    'expected_debit_transaction_id' =>
                        $paidDebit->id,
                ]
            );

            abort(
                403,
                'The payment authorization could not be verified.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY CREDIT → PAYMENT ITEM
        |--------------------------------------------------------------------------
        */

        if (
            (int) $creditTransaction->payment_item_id !==
            (int) $paymentItem->id
        ) {

            abort(
                403,
                'The payment authorization could not be verified.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CHECK FOR EXISTING GENERATED DOCUMENT
        |--------------------------------------------------------------------------
        */

        $existingDocument = GeneratedDocument::query()
            ->where('user_id', $user->id)
            ->where('document_id', $document->id)
            ->where(
                'transaction_id',
                $creditTransaction->id
            )
            ->latest('id')
            ->first();


        if ($existingDocument) {

            return redirect()->route(
                'documents.generated',
                $existingDocument->id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DOCUMENT NUMBER
        |--------------------------------------------------------------------------
        */

        do {

            $documentNumber =
                strtoupper(
                    $document->code ?: 'DOC'
                )
                . '-'
                . now()->format('Y')
                . '-'
                . str_pad(
                    (string) random_int(
                        1,
                        999999
                    ),
                    6,
                    '0',
                    STR_PAD_LEFT
                );

        } while (
            GeneratedDocument::where(
                'document_number',
                $documentNumber
            )->exists()
        );


        /*
        |--------------------------------------------------------------------------
        | TRACKING CODE
        |--------------------------------------------------------------------------
        |
        | Used later for QR-code verification.
        |
        |--------------------------------------------------------------------------
        */

        do {

            $trackingCode =
                'NACP-'
                . strtoupper(
                    bin2hex(
                        random_bytes(8)
                    )
                );

        } while (
            GeneratedDocument::where(
                'tracking_code',
                $trackingCode
            )->exists()
        );


        /*
        |--------------------------------------------------------------------------
        | ISSUE DATE
        |--------------------------------------------------------------------------
        */

        $issuedAt =
            now()->toDateString();


        /*
        |--------------------------------------------------------------------------
        | EXPIRATION
        |--------------------------------------------------------------------------
        */

        $expiresAt = null;


        switch ($document->validity_type) {

            case 'days':

                if ($document->validity_value) {

                    $expiresAt =
                        now()
                            ->addDays(
                                $document->validity_value
                            )
                            ->toDateString();
                }

                break;


            case 'months':

                if ($document->validity_value) {

                    $expiresAt =
                        now()
                            ->addMonths(
                                $document->validity_value
                            )
                            ->toDateString();
                }

                break;


            case 'years':

                if ($document->validity_value) {

                    $expiresAt =
                        now()
                            ->addYears(
                                $document->validity_value
                            )
                            ->toDateString();
                }

                break;


            case 'fixed_date':

                if ($document->validity_date) {

                    $expiresAt =
                        $document->validity_date
                            ->toDateString();
                }

                break;


            case 'none':
            default:

                $expiresAt = null;

                break;
        }


        /*
        |--------------------------------------------------------------------------
        | CREATE GENERATED DOCUMENT
        |--------------------------------------------------------------------------
        |
        | VERY IMPORTANT:
        |
        | transaction_id = CREDIT TRANSACTION ID
        |
        |--------------------------------------------------------------------------
        */

        $generatedDocument = DB::transaction(
            function () use (
                $user,
                $document,
                $creditTransaction,
                $documentNumber,
                $trackingCode,
                $issuedAt,
                $expiresAt,
                $fieldValues
            ) {

                return GeneratedDocument::create([
                    'user_id' =>
                        $user->id,

                    'document_id' =>
                        $document->id,

                    'transaction_id' =>
                        $creditTransaction->id,

                    'document_number' =>
                        $documentNumber,

                    'tracking_code' =>
                        $trackingCode,

                    'issued_at' =>
                        $issuedAt,

                    'expires_at' =>
                        $expiresAt,

                    'status' =>
                        'active',

                    'field_values' =>
                        $fieldValues,
                ]);
            }
        );


        /*
        |--------------------------------------------------------------------------
        | LOG
        |--------------------------------------------------------------------------
        */

        Log::info(
            'DOCUMENT GENERATED SUCCESSFULLY',
            [
                'user_id' =>
                    $user->id,

                'generated_document_id' =>
                    $generatedDocument->id,

                'document_id' =>
                    $document->id,

                'document_number' =>
                    $documentNumber,

                'tracking_code' =>
                    $trackingCode,

                'payment_item_id' =>
                    $paymentItem->id,

                'debit_transaction_id' =>
                    $paidDebit->id,

                'credit_transaction_id' =>
                    $creditTransaction->id,

                'expires_at' =>
                    $expiresAt,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect()->route(
            'documents.generated',
            $generatedDocument->id
        );
    }
}
