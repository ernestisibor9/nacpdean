<?php

namespace App\Http\Controllers;

use App\Models\GeneratedDocument;
use App\Models\Membership;
use App\Services\QrCodeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class GeneratedDocumentController extends Controller
{
/**
 * Display the authenticated member's current documents.
 *
 * IMPORTANT:
 * A member must have an active, non-expired membership
 * before they can access their generated documents.
 *
 * Active documents are displayed.
 *
 * Expired documents are displayed only when they have
 * NOT already been replaced by a successful renewal.
 *
 * Old replaced documents remain in the database for
 * historical and audit purposes.
 */
public function index(Request $request)
{
    $user = $request->user();

    if (!$user) {
        abort(403);
    }

    /*
    |--------------------------------------------------------------------------
    | Membership Access Check
    |--------------------------------------------------------------------------
    |
    | This is the centralized membership-expiry rule used by:
    |
    | - index()
    | - show()
    | - print()
    | - download()
    |
    | If the membership has expired, the member cannot access
    | the document listing.
    |
    */

    $membership = $this->getActiveMembership($user->id);

    /*
    |--------------------------------------------------------------------------
    | Get Member Generated Documents
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    | The Blade view expects the variable:
    |
    |     $generatedDocuments
    |
    | Therefore, we use that exact variable name here.
    |
    */

    $generatedDocuments = GeneratedDocument::query()
        ->where('user_id', $user->id)

        /*
        |--------------------------------------------------------------------------
        | Only show:
        |
        | 1. Active documents
        |
        | OR
        |
        | 2. Expired documents that have not been replaced
        |
        |--------------------------------------------------------------------------
        */

        ->where(function ($query) {

            /*
            |--------------------------------------------------------------------------
            | Active documents
            |--------------------------------------------------------------------------
            */

            $query->where('status', 'active')

                /*
                |--------------------------------------------------------------------------
                | Expired documents that have NOT been renewed/replaced
                |--------------------------------------------------------------------------
                */

                ->orWhere(function ($query) {

                    $query
                        ->where('status', 'expired')
                        ->whereNull('replaced_by_document_id');

                });
        })

        /*
        |--------------------------------------------------------------------------
        | Load Required Relationships
        |--------------------------------------------------------------------------
        */

        ->with([
            'document',
            'user.profile',
            'transaction.paymentItem.renewalPaymentItem',
            'replacedBy',
        ])

        /*
        |--------------------------------------------------------------------------
        | Newest Documents First
        |--------------------------------------------------------------------------
        */

        ->latest('id')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | Return Member Documents View
    |--------------------------------------------------------------------------
    */

    return view(
        'member.documents.index',
        compact(
            'generatedDocuments',
            'membership'
        )
    );
}

    /**
     * Display a generated document.
     *
     * IMPORTANT:
     * The document is explicitly scoped to the authenticated member.
     *
     * Membership expiry is checked before the document is displayed.
     */
    public function show(
        Request $request,
        GeneratedDocument $generatedDocument,
        QrCodeService $qrCodeService
    ) {
        $generatedDocument = $this->getMemberDocument(
            $request,
            $generatedDocument
        );

        /*
        |--------------------------------------------------------------------------
        | Check Generated Document Status
        |--------------------------------------------------------------------------
        */

        $this->checkDocumentStatus($generatedDocument);

        /*
        |--------------------------------------------------------------------------
        | Load Required Relationships
        |--------------------------------------------------------------------------
        */

        $generatedDocument->load([
            'document',
            'user.profile',
        ]);

        $document = $generatedDocument->document;

        if (!$document) {
            abort(
                404,
                'The document definition could not be found.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Resolve Configured Template
        |--------------------------------------------------------------------------
        */

        $template = $this->resolveTemplate($document);

        /*
        |--------------------------------------------------------------------------
        | Generate QR Code
        |--------------------------------------------------------------------------
        */

        $qrCode = $qrCodeService->generate(
            $generatedDocument->tracking_code
        );

        return view('member.documents.show', [
            'generatedDocument' => $generatedDocument,
            'document'         => $document,
            'template'         => $template,
            'qrCode'            => $qrCode,
            'printMode'        => false,
            'downloadMode'     => false,
        ]);
    }

    /**
     * Display the generated document in print mode.
     *
     * Membership expiry is checked through getMemberDocument().
     */
    public function print(
        Request $request,
        GeneratedDocument $generatedDocument,
        QrCodeService $qrCodeService
    ) {
        $generatedDocument = $this->getMemberDocument(
            $request,
            $generatedDocument
        );

        /*
        |--------------------------------------------------------------------------
        | Check Generated Document Status
        |--------------------------------------------------------------------------
        */

        $this->checkDocumentStatus($generatedDocument);

        /*
        |--------------------------------------------------------------------------
        | Load Required Relationships
        |--------------------------------------------------------------------------
        */

        $generatedDocument->load([
            'document',
            'user.profile',
        ]);

        $document = $generatedDocument->document;

        if (!$document) {
            abort(
                404,
                'The document definition could not be found.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Resolve Configured Document Template
        |--------------------------------------------------------------------------
        */

        $template = $this->resolveTemplate($document);

        /*
        |--------------------------------------------------------------------------
        | Generate QR Code
        |--------------------------------------------------------------------------
        */

        $qrCode = $qrCodeService->generate(
            $generatedDocument->tracking_code
        );

        return view($template, [
            'generatedDocument' => $generatedDocument,
            'document'         => $document,
            'qrCode'            => $qrCode,
            'printMode'        => true,
            'downloadMode'     => false,
        ]);
    }

/**
 * Download the generated document as a PDF.
 *
 * Membership expiry is checked through getMemberDocument().
 */
public function download(
    Request $request,
    GeneratedDocument $generatedDocument,
    QrCodeService $qrCodeService
) {
    $generatedDocument = $this->getMemberDocument(
        $request,
        $generatedDocument
    );

    /*
    |--------------------------------------------------------------------------
    | Check Generated Document Status
    |--------------------------------------------------------------------------
    */

    $this->checkDocumentStatus($generatedDocument);

    /*
    |--------------------------------------------------------------------------
    | Load Required Relationships
    |--------------------------------------------------------------------------
    */

    $generatedDocument->load([
        'document',
        'user.profile',
    ]);

    $document = $generatedDocument->document;

    if (!$document) {
        abort(
            404,
            'The document definition could not be found.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Resolve Configured Document Template
    |--------------------------------------------------------------------------
    */

    $template = $this->resolveTemplate($document);

    /*
    |--------------------------------------------------------------------------
    | Generate QR Code
    |--------------------------------------------------------------------------
    */

    $qrCode = $qrCodeService->generate(
        $generatedDocument->tracking_code
    );

    /*
    |--------------------------------------------------------------------------
    | LOAD CERTIFICATE BACKGROUND FOR DOMPDF
    |--------------------------------------------------------------------------
    |
    | Dompdf may not be able to load the certificate JPG through
    | asset() when generating the PDF.
    |
    | Therefore, we read the actual JPG file from the public
    | directory and convert it to a Base64 image.
    |
    */

    $certificateBackground = null;

    $certificatePath = public_path(
        'images/certificates/regular-exporter-template.jpg'
    );

    if (file_exists($certificatePath)) {

        $certificateBackground =
            'data:image/jpeg;base64,' .
            base64_encode(
                file_get_contents($certificatePath)
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Render Blade Template
    |--------------------------------------------------------------------------
    */

    $html = view($template, [
        'generatedDocument'      => $generatedDocument,
        'document'              => $document,
        'qrCode'                => $qrCode,
        'certificateBackground' => $certificateBackground,
        'printMode'             => true,
        'downloadMode'          => true,
    ])->render();

    /*
    |--------------------------------------------------------------------------
    | Resolve PDF Orientation
    |--------------------------------------------------------------------------
    */

    $orientation = $this->resolvePdfOrientation(
        $document
    );

    /*
    |--------------------------------------------------------------------------
    | Generate PDF
    |--------------------------------------------------------------------------
    */

    $pdf = Pdf::loadHTML($html)
        ->setPaper('a4', $orientation);

    /*
    |--------------------------------------------------------------------------
    | PDF Filename
    |--------------------------------------------------------------------------
    */

    $filename =
        $generatedDocument->document_number . '.pdf';

    return $pdf->download($filename);
}

    /**
     * Public document verification.
     *
     * IMPORTANT:
     * This method MUST NOT require authentication.
     *
     * QR codes on documents must be scannable by anyone.
     *
     * Membership expiry does NOT block public verification.
     */
    public function verify(string $trackingCode)
    {
        $generatedDocument = GeneratedDocument::query()
            ->with([
                'document',
                'user.profile',
            ])
            ->where(
                'tracking_code',
                $trackingCode
            )
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Tracking Code Does Not Exist
        |--------------------------------------------------------------------------
        */

        if (!$generatedDocument) {
            return view('documents.verify', [
                'valid'             => false,
                'generatedDocument' => null,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Revoked Documents Are Never Valid
        |--------------------------------------------------------------------------
        */

        if ($generatedDocument->status === 'revoked') {
            return view('documents.verify', [
                'valid'             => false,
                'generatedDocument' => $generatedDocument,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Automatically Expire Documents Whose Expiry Date Has Passed
        |--------------------------------------------------------------------------
        */

        if (
            $generatedDocument->expires_at &&
            $generatedDocument->expires_at->isPast()
        ) {
            if ($generatedDocument->status === 'active') {
                $generatedDocument->update([
                    'status' => 'expired',
                ]);

                /*
                |--------------------------------------------------------------------------
                | Refresh Model Status
                |--------------------------------------------------------------------------
                */

                $generatedDocument->status = 'expired';
            }

            return view('documents.verify', [
                'valid'             => false,
                'generatedDocument' => $generatedDocument,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Only Active Documents Are Valid
        |--------------------------------------------------------------------------
        */

        $valid =
            $generatedDocument->status === 'active';

        return view('documents.verify', [
            'valid'             => $valid,
            'generatedDocument' => $generatedDocument,
        ]);
    }

    /**
     * Retrieve a generated document belonging to
     * the authenticated member.
     *
     * This is the main ownership/security boundary
     * for member-facing generated documents.
     *
     * A member cannot access another member's document
     * even if they manually change the document ID in
     * the URL.
     *
     * IMPORTANT:
     * This method also enforces the member's active
     * membership requirement.
     *
     * The only exception is renewal, because an expired
     * member must still be able to initiate renewal.
     */
    protected function getMemberDocument(
        Request $request,
        GeneratedDocument $generatedDocument,
        bool $requireActiveMembership = true
    ): GeneratedDocument {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | Membership Access Check
        |--------------------------------------------------------------------------
        |
        | show(), print(), and download() use the default:
        |
        |     $requireActiveMembership = true
        |
        | renew() passes false because an expired member
        | must still be able to renew.
        |
        */

        if ($requireActiveMembership) {
            $this->getActiveMembership($user->id);
        }

        /*
        |--------------------------------------------------------------------------
        | Verify Document Ownership
        |--------------------------------------------------------------------------
        */

        return GeneratedDocument::query()
            ->whereKey($generatedDocument->id)
            ->where('user_id', $user->id)
            ->firstOrFail();
    }

    /**
     * Get the authenticated member's active and
     * non-expired membership.
     *
     * THIS IS THE CENTRAL MEMBERSHIP EXPIRY RULE.
     *
     * All member-facing document access must use this
     * method rather than implementing its own expiry logic.
     */
    protected function getActiveMembership(
        int $userId
    ): Membership {
        $membership = Membership::query()
            ->with([
                'profile',
                'membershipCategory',
            ])
            ->where('user_id', $userId)
            ->latest('id')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | No Membership
        |--------------------------------------------------------------------------
        */

        if (!$membership) {
            abort(
                403,
                'You do not have a membership.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Membership Status
        |--------------------------------------------------------------------------
        */

        if ($membership->status !== 'active') {
            abort(
                403,
                'Your membership is no longer active.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Membership Expiry
        |--------------------------------------------------------------------------
        |
        | The Membership model casts expires_at as a date.
        |
        | Using endOfDay() means:
        |
        | expires_at = 31 December 2026
        |
        | remains valid throughout 31 December 2026.
        |
        | Access is blocked from 1 January 2027.
        |
        */

        if (
            $membership->expires_at &&
            now()->greaterThan(
                $membership->expires_at
                    ->copy()
                    ->endOfDay()
            )
        ) {
            abort(
                403,
                'Your membership has expired. Please renew your membership to access your documents.'
            );
        }

        return $membership;
    }

    /**
     * Check whether the generated document can still
     * be accessed by the member.
     *
     * NOTE:
     * Membership expiry is NOT checked here.
     *
     * Membership expiry is centralized inside
     * getActiveMembership().
     *
     * This method deals only with the document's own
     * status and expiry.
     */
    protected function checkDocumentStatus(
        GeneratedDocument $generatedDocument
    ): void {
        /*
        |--------------------------------------------------------------------------
        | Revoked Documents Can Never Be Accessed
        |--------------------------------------------------------------------------
        */

        if ($generatedDocument->status === 'revoked') {
            abort(
                403,
                'This document has been revoked.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Automatically Mark Expired Documents
        |--------------------------------------------------------------------------
        */

        if (
            $generatedDocument->expires_at &&
            $generatedDocument->expires_at->isPast()
        ) {
            if ($generatedDocument->status === 'active') {
                $generatedDocument->update([
                    'status' => 'expired',
                ]);
            }

            abort(
                403,
                'This document has expired.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Only Active Documents Are Accessible
        |--------------------------------------------------------------------------
        */

        if ($generatedDocument->status !== 'active') {
            abort(
                403,
                'This document is not currently available.'
            );
        }
    }

    /**
     * Resolve the Blade template configured by the administrator.
     *
     * The database may contain either:
     *
     *     afforestation_payment_receipt
     *
     * or:
     *
     *     documents.afforestation_payment_receipt
     *
     * If only the template name is stored, the default
     * documents/ directory is automatically added.
     */
    protected function resolveTemplate($document): string
    {
        $template = trim(
            (string) $document->template
        );

        if ($template === '') {
            abort(
                404,
                "No template has been configured for document [{$document->code}]."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Complete Laravel View Name
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | documents.afforestation_payment_receipt
        |
        */

        if (str_contains($template, '.')) {
            return $template;
        }

        /*
        |--------------------------------------------------------------------------
        | Template Stored As A Simple Filename
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | afforestation_payment_receipt
        |
        | becomes:
        |
        | documents.afforestation_payment_receipt
        |
        */

        return 'documents.' . $template;
    }

    /**
     * Resolve PDF orientation for the document.
     *
     * Default:
     * A4 portrait.
     */
    protected function resolvePdfOrientation($document): string
    {
        $template = strtolower(
            trim((string) $document->template)
        );

        /*
        |--------------------------------------------------------------------------
        | Landscape Documents
        |--------------------------------------------------------------------------
        */

        $landscapeTemplates = [
            'membership_certificate',
            'membership_certificate_regular_exporter',
            'membership_certificate_exporter',
            'documents.membership_certificate',
            'documents.membership_certificate_regular_exporter',
            'documents.membership_certificate_exporter',
        ];

        if (
            in_array(
                $template,
                $landscapeTemplates,
                true
            )
        ) {
            return 'landscape';
        }

        /*
        |--------------------------------------------------------------------------
        | Default: Portrait
        |--------------------------------------------------------------------------
        */

        return 'portrait';
    }

    /**
     * Prepare an expired generated document for renewal.
     *
     * IMPORTANT:
     * Renewal is intentionally allowed even when the
     * membership has expired.
     *
     * This is necessary so the member has a route to
     * renew the document/membership.
     *
     * The document remains member-owned and therefore
     * cannot operate on another member's document.
     */
    public function renew(
        Request $request,
        GeneratedDocument $generatedDocument
    ) {
        /*
        |--------------------------------------------------------------------------
        | Ownership Check Only
        |--------------------------------------------------------------------------
        |
        | We deliberately pass false here so that an expired
        | membership can still initiate renewal.
        |
        */

        $generatedDocument = $this->getMemberDocument(
            $request,
            $generatedDocument,
            false
        );

        /*
        |--------------------------------------------------------------------------
        | Document Must Be Expired
        |--------------------------------------------------------------------------
        */

        if (
            !$generatedDocument->expires_at ||
            !$generatedDocument->expires_at->isPast()
        ) {
            return redirect()
                ->route(
                    'member.documents.show',
                    $generatedDocument
                )
                ->with(
                    'info',
                    'This document does not need to be renewed yet.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Revoked Documents Cannot Be Renewed
        |--------------------------------------------------------------------------
        */

        if ($generatedDocument->status === 'revoked') {
            return redirect()
                ->route('member.documents.index')
                ->with(
                    'error',
                    'A revoked document cannot be renewed.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Find Original Transaction
        |--------------------------------------------------------------------------
        */

        $transaction = $generatedDocument
            ->transaction()
            ->with([
                'paymentItem.renewalPaymentItem',
            ])
            ->first();

        if (
            !$transaction ||
            !$transaction->paymentItem
        ) {
            return redirect()
                ->route('member.documents.index')
                ->with(
                    'error',
                    'The renewal payment configuration for this document could not be found.'
                );
        }

        $originalPaymentItem = $transaction->paymentItem;

        /*
        |--------------------------------------------------------------------------
        | Check Whether Original Payment Item Is Renewable
        |--------------------------------------------------------------------------
        */

        if (!$originalPaymentItem->is_renewable) {
            return redirect()
                ->route('member.documents.index')
                ->with(
                    'error',
                    'This document cannot be renewed online.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Find Configured Renewal Payment Item
        |--------------------------------------------------------------------------
        */

        $renewalPaymentItem =
            $originalPaymentItem->renewalPaymentItem;

        if (!$renewalPaymentItem) {
            return redirect()
                ->route('member.documents.index')
                ->with(
                    'error',
                    'No renewal payment item has been configured for this document.'
                );
        }

        if (!$renewalPaymentItem->is_active) {
            return redirect()
                ->route('member.documents.index')
                ->with(
                    'error',
                    'The renewal payment item is currently unavailable.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Load Renewal Document and Manual Fields
        |--------------------------------------------------------------------------
        |
        | The renewal document comes from the RENEWAL payment item.
        | This keeps the renewal system dynamic.
        |
        */

        $renewalPaymentItem->load([
            'document' => function ($query) {
                $query
                    ->where('is_active', true)
                    ->with([
                        'fields' => function ($query) {
                            $query
                                ->where('is_system', false)
                                ->orderBy('sort_order')
                                ->orderBy('id');
                        },
                    ]);
            },
        ]);

        /*
        |--------------------------------------------------------------------------
        | Renewal Payment Item Must Have an Active Document
        |--------------------------------------------------------------------------
        */

        if (!$renewalPaymentItem->document) {
            return redirect()
                ->route('member.documents.index')
                ->with(
                    'error',
                    'The document configuration for this renewal payment item could not be found.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Dynamic Manual Fields
        |--------------------------------------------------------------------------
        */

        $fields = $renewalPaymentItem
            ->document
            ->fields;

        /*
        |--------------------------------------------------------------------------
        | Previous Values
        |--------------------------------------------------------------------------
        |
        | These values came from the old generated document.
        |
        */

        $previousValues = collect(
            is_array($generatedDocument->field_values)
                ? $generatedDocument->field_values
                : []
        );

        /*
        |--------------------------------------------------------------------------
        | Return Renewal Page
        |--------------------------------------------------------------------------
        */

        return view(
            'member.documents.renew',
            [
                'generatedDocument' => $generatedDocument,
                'paymentItem'       => $renewalPaymentItem,
                'fields'            => $fields,
                'previousValues'    => $previousValues,
            ]
        );
    }
}
