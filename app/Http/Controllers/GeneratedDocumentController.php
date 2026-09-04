<?php

namespace App\Http\Controllers;

use App\Models\GeneratedDocument;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class GeneratedDocumentController extends Controller
{
    /**
     * Display the member's current documents.
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

        $documents = GeneratedDocument::query()
            ->where('user_id', $user->id)

            ->where(function ($query) {

                /*
            |--------------------------------------------------------------------------
            | Active documents
            |--------------------------------------------------------------------------
            */

                $query->where('status', 'active')

                    /*
                |--------------------------------------------------------------------------
                | Expired documents that have NOT been renewed
                |--------------------------------------------------------------------------
                */

                    ->orWhere(function ($query) {
                        $query
                            ->where('status', 'expired')
                            ->whereNull('replaced_by_document_id');
                    });
            })

            ->with([
                'document',
                'user.profile',
                'transaction.paymentItem.renewalPaymentItem',
                'replacedBy',
            ])

            ->latest('id')

            ->get();

        return view(
            'member.documents.index',
            compact('documents')
        );
    }

    /**
     * Display a generated document.
     */
    public function show(
        Request $request,
        GeneratedDocument $generatedDocument,
        QrCodeService $qrCodeService
    ) {
        $this->authorizeDocument(
            $request,
            $generatedDocument
        );

        $this->checkDocumentStatus(
            $generatedDocument
        );

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

        $template = $this->resolveTemplate(
            $document
        );

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
     */
    public function print(
        Request $request,
        GeneratedDocument $generatedDocument,
        QrCodeService $qrCodeService
    ) {
        $this->authorizeDocument(
            $request,
            $generatedDocument
        );

        $this->checkDocumentStatus(
            $generatedDocument
        );

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
         * Resolve the configured document template.
         */
        $template = $this->resolveTemplate(
            $document
        );

        /*
         * Generate QR code using the document tracking code.
         */
        $qrCode = $qrCodeService->generate(
            $generatedDocument->tracking_code
        );

        return view($template, [
            'generatedDocument' => $generatedDocument,
            'document'         => $document,
            'qrCode'           => $qrCode,
            'printMode'        => true,
            'downloadMode'     => false,
        ]);
    }

    /**
     * Download the generated document as a PDF.
     */
    public function download(
        Request $request,
        GeneratedDocument $generatedDocument,
        QrCodeService $qrCodeService
    ) {
        $this->authorizeDocument(
            $request,
            $generatedDocument
        );

        $this->checkDocumentStatus(
            $generatedDocument
        );

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
         * Resolve the configured document template.
         */
        $template = $this->resolveTemplate(
            $document
        );

        /*
         * Generate QR code using the document tracking code.
         */
        $qrCode = $qrCodeService->generate(
            $generatedDocument->tracking_code
        );

        /*
         * Render the Blade template into HTML.
         */
        $html = view($template, [
            'generatedDocument' => $generatedDocument,
            'document'         => $document,
            'qrCode'           => $qrCode,
            'printMode'        => true,
            'downloadMode'     => true,
        ])->render();

        /*
         * Determine PDF orientation from the document template.
         *
         * Afforestation Payment Receipt is portrait.
         * Membership certificates are landscape.
         */
        $orientation = $this->resolvePdfOrientation(
            $document
        );

        /*
         * Generate the actual PDF.
         */
        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4', $orientation);

        /*
         * Use the generated document number
         * as the PDF filename.
         */
        $filename =
            $generatedDocument->document_number . '.pdf';

        return $pdf->download(
            $filename
        );
    }

    /**
     * Public document verification.
     *
     * This route does not require authentication.
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
         * Tracking code does not exist.
         */
        if (!$generatedDocument) {
            return view('documents.verify', [
                'valid'             => false,
                'generatedDocument' => null,
            ]);
        }

        /*
         * Revoked documents are never valid.
         */
        if ($generatedDocument->status === 'revoked') {
            return view('documents.verify', [
                'valid'             => false,
                'generatedDocument' => $generatedDocument,
            ]);
        }

        /*
         * Automatically expire documents whose
         * expiry date has passed.
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

            return view('documents.verify', [
                'valid'             => false,
                'generatedDocument' => $generatedDocument,
            ]);
        }

        /*
         * Only active documents are valid.
         */
        $valid =
            $generatedDocument->status === 'active';

        return view('documents.verify', [
            'valid'             => $valid,
            'generatedDocument' => $generatedDocument,
        ]);
    }

    /**
     * Make sure the authenticated member owns
     * the generated document.
     */
    protected function authorizeDocument(
        Request $request,
        GeneratedDocument $generatedDocument
    ): void {
        $user = $request->user();

        abort_unless(
            $user &&
                (int) $generatedDocument->user_id ===
                (int) $user->id,
            403,
            'You are not authorized to access this document.'
        );
    }

    /**
     * Check whether the document can still be accessed.
     */
    protected function checkDocumentStatus(
        GeneratedDocument $generatedDocument
    ): void {
        /*
         * Revoked documents can never be accessed.
         */
        if ($generatedDocument->status === 'revoked') {
            abort(
                403,
                'This document has been revoked.'
            );
        }

        /*
         * Automatically mark expired documents.
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
         * Only active documents are accessible.
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
    protected function resolveTemplate(
        $document
    ): string {
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
         * If the administrator already stored a complete
         * Laravel view name, use it as-is.
         *
         * Example:
         *
         * documents.afforestation_payment_receipt
         */
        if (str_contains($template, '.')) {
            return $template;
        }

        /*
         * Otherwise assume the template is stored inside:
         *
         * resources/views/documents/
         *
         * Example:
         *
         * afforestation_payment_receipt
         *
         * becomes:
         *
         * documents.afforestation_payment_receipt
         */
        return 'documents.' . $template;
    }

    /**
     * Resolve PDF orientation for the document.
     *
     * Default:
     * A4 portrait.
     *
     * Documents that require landscape can be added
     * to the list below.
     */
    protected function resolvePdfOrientation(
        $document
    ): string {
        $template = strtolower(
            trim((string) $document->template)
        );

        /*
         * Landscape documents.
         *
         * Membership certificates currently use
         * landscape orientation.
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
         * All other documents default to portrait.
         */
        return 'portrait';
    }


    public function renew(
        Request $request,
        GeneratedDocument $generatedDocument
    ) {
        $this->authorizeDocument(
            $request,
            $generatedDocument
        );

        /*
    |--------------------------------------------------------------------------
    | Document must be expired
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
    | Revoked documents cannot be renewed
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
    | Find the original payment item
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
    | Check whether the original payment item is renewable
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
    | Find configured renewal payment item
    |--------------------------------------------------------------------------
    */

        $renewalPaymentItem = $originalPaymentItem
            ->renewalPaymentItem;

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
    | Load renewal document and its manual fields
    |--------------------------------------------------------------------------
    |
    | We deliberately load the document attached to the RENEWAL payment
    | item. This keeps the renewal system completely dynamic.
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
    | Make sure renewal payment item has a valid document
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
    | Dynamic manual fields
    |--------------------------------------------------------------------------
    */

        $fields = $renewalPaymentItem
            ->document
            ->fields;

        /*
    |--------------------------------------------------------------------------
    | Previous values
    |--------------------------------------------------------------------------
    |
    | These values came from the OLD generated document.
    |
    | Example:
    |
    | [
    |     'loading_point' => 'Lagos Port',
    |     'buyer_member_name' => 'ABC Exporters',
    |     'vehicle_number' => 'ABC-123-XY',
    | ]
    |
    | The Blade uses these values to pre-fill the renewal form.
    |
    */

        $previousValues = collect(
            is_array($generatedDocument->field_values)
                ? $generatedDocument->field_values
                : []
        );

        /*
    |--------------------------------------------------------------------------
    | Return renewal page
    |--------------------------------------------------------------------------
    */

        return view(
            'member.documents.renew',
            [
                'generatedDocument' => $generatedDocument,

                'paymentItem' => $renewalPaymentItem,

                'fields' => $fields,

                'previousValues' => $previousValues,
            ]
        );
    }
}
