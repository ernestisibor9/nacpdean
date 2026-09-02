<?php

namespace App\Http\Controllers;

use App\Models\GeneratedDocument;
use Illuminate\Http\Request;

class GeneratedDocumentController extends Controller
{
    /**
     * Display all generated documents belonging to
     * the currently authenticated member.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $documents = GeneratedDocument::query()
            ->where('user_id', $user->id)
            ->with('document')
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
        GeneratedDocument $generatedDocument
    ) {
        $this->authorizeDocument($request, $generatedDocument);

        $this->checkDocumentStatus($generatedDocument);

        $document = $generatedDocument->document;

        if (!$document) {
            abort(
                404,
                'The document definition could not be found.'
            );
        }

        $template = $this->resolveTemplate($document);

        return view($template, [
            'generatedDocument' => $generatedDocument,
            'document' => $document,
            'printMode' => false,
            'downloadMode' => false,
        ]);
    }

    /**
     * Display the generated document in print mode.
     */
    public function print(
        Request $request,
        GeneratedDocument $generatedDocument
    ) {
        $this->authorizeDocument($request, $generatedDocument);

        $this->checkDocumentStatus($generatedDocument);

        $document = $generatedDocument->document;

        if (!$document) {
            abort(
                404,
                'The document definition could not be found.'
            );
        }

        $template = $this->resolveTemplate($document);

        return view($template, [
            'generatedDocument' => $generatedDocument,
            'document' => $document,
            'printMode' => true,
            'downloadMode' => false,
        ]);
    }

    /**
     * Prepare the generated document for download.
     *
     * PDF generation will be added after the document
     * templates and generation service are completed.
     */
    public function download(
        Request $request,
        GeneratedDocument $generatedDocument
    ) {
        $this->authorizeDocument($request, $generatedDocument);

        $this->checkDocumentStatus($generatedDocument);

        $document = $generatedDocument->document;

        if (!$document) {
            abort(
                404,
                'The document definition could not be found.'
            );
        }

        $template = $this->resolveTemplate($document);

        return view($template, [
            'generatedDocument' => $generatedDocument,
            'document' => $document,
            'printMode' => false,
            'downloadMode' => true,
        ]);
    }

    /**
     * Public document verification.
     *
     * This route does not require authentication.
     */
    public function verify(string $trackingCode)
    {
        $generatedDocument = GeneratedDocument::query()
            ->with('document')
            ->where('tracking_code', $trackingCode)
            ->first();

        if (!$generatedDocument) {
            return view('documents.verify', [
                'valid' => false,
                'generatedDocument' => null,
            ]);
        }

        /*
         * A revoked document is never valid.
         */
        if ($generatedDocument->status === 'revoked') {
            return view('documents.verify', [
                'valid' => false,
                'generatedDocument' => $generatedDocument,
            ]);
        }

        /*
         * Automatically expire documents whose expiry
         * date has passed.
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
                'valid' => false,
                'generatedDocument' => $generatedDocument,
            ]);
        }

        $valid = $generatedDocument->status === 'active';

        return view('documents.verify', [
            'valid' => $valid,
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
            $user && $generatedDocument->user_id === $user->id,
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
     * Example:
     *
     * member.documents.templates.afforestation-payment-receipt
     */
    protected function resolveTemplate($document): string
    {
        if (empty($document->template)) {
            abort(
                404,
                "No template has been configured for document [{$document->code}]."
            );
        }

        return $document->template;
    }
}
