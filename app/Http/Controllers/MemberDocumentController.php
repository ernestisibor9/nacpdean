<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\GeneratedDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class MemberDocumentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | VIEW DOCUMENT
    |--------------------------------------------------------------------------
    |
    | A member can ONLY view their own GeneratedDocument.
    |
    */

    public function show($id)
    {
        $generatedDocument = GeneratedDocument::with([
            'document',
            'user',
        ])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->firstOrFail();

        return view('member.documents.show', [
            'generatedDocument' => $generatedDocument,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | PRINT DOCUMENT
    |--------------------------------------------------------------------------
    |
    | Printing is intentionally rendered through the browser.
    | The same authorization rule applies.
    |
    */

    public function print($id)
    {
        $generatedDocument = GeneratedDocument::with([
            'document',
            'user',
        ])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->firstOrFail();

        return view('member.documents.print', [
            'generatedDocument' => $generatedDocument,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD DOCUMENT
    |--------------------------------------------------------------------------
    |
    | This generates the PDF from the document's Blade template.
    |
    */

    public function download($id)
    {
        $generatedDocument = GeneratedDocument::with([
            'document',
            'user',
        ])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | PDF generation
        |--------------------------------------------------------------------------
        |
        | Requires barryvdh/laravel-dompdf.
        |
        */

        if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            abort(
                Response::HTTP_SERVICE_UNAVAILABLE,
                'PDF generation service is not available.'
            );
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'member.documents.pdf',
            [
                'generatedDocument' => $generatedDocument,
            ]
        );

        $documentCode = $generatedDocument->document->code
            ?? 'NACPDEAN-DOCUMENT';

        $documentNumber = $generatedDocument->document_number
            ?? $generatedDocument->id;

        $filename = $documentCode . '-' . $documentNumber . '.pdf';

        /*
        |--------------------------------------------------------------------------
        | Prevent unsafe filename characters
        |--------------------------------------------------------------------------
        */

        $filename = preg_replace(
            '/[^A-Za-z0-9._-]/',
            '-',
            $filename
        );

        return $pdf->download($filename);
    }


    /*
    |--------------------------------------------------------------------------
    | SCAN / VERIFY DOCUMENT
    |--------------------------------------------------------------------------
    |
    | This endpoint is intentionally NOT restricted to Auth::id().
    |
    | The QR code on a document can be scanned by an external person,
    | so verification is based on the document tracking code.
    |
    */

    public function verify($trackingCode)
    {
        $generatedDocument = GeneratedDocument::with([
            'document',
            'user',
        ])
            ->where('tracking_code', $trackingCode)
            ->where('status', 'active')
            ->first();

        if (! $generatedDocument) {
            abort(
                Response::HTTP_NOT_FOUND,
                'Document not found or no longer valid.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Check expiry
        |--------------------------------------------------------------------------
        */

        $isExpired = false;

        if (
            ! empty($generatedDocument->expires_at) &&
            now()->startOfDay()->greaterThan(
                \Carbon\Carbon::parse($generatedDocument->expires_at)
            )
        ) {
            $isExpired = true;
        }

        return view('documents.verify', [
            'generatedDocument' => $generatedDocument,
            'isExpired' => $isExpired,
        ]);
    }
}
