<?php

namespace App\Http\Controllers;

use App\Models\OperationalRightsDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class OperationalRightsDocumentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | MEMBER DOCUMENTS
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $documents = OperationalRightsDocument::with([
            'membership',
            'category',
            'profile',
            'payment.paymentItem',
        ])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view(
            'member.documents.operational-rights.index',
            compact('documents')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VIEW DOCUMENT
    |--------------------------------------------------------------------------
    */

    public function show(
        OperationalRightsDocument $document
    ) {
        abort_unless(
            $document->user_id === Auth::id(),
            403
        );

        $document->load([
            'user',
            'membership',
            'category',
            'profile',
            'payment.paymentItem',
        ]);

        /*
        |--------------------------------------------------------------------------
        | VERIFICATION URL
        |--------------------------------------------------------------------------
        */

        $verificationUrl = route(
            'documents.operational-rights.verify',
            $document->qr_token
        );

        /*
        |--------------------------------------------------------------------------
        | GENERATE QR
        |--------------------------------------------------------------------------
        */

        $qrCode = $this->generateQrCode(
            $document,
            $verificationUrl
        );

        return view(
            'member.documents.operational-rights.show',
            compact(
                'document',
                'verificationUrl',
                'qrCode'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VIEW PDF
    |--------------------------------------------------------------------------
    */

    public function pdf(
        OperationalRightsDocument $document
    ) {
        abort_unless(
            $document->user_id === Auth::id(),
            403
        );

        $document->load([
            'user',
            'membership',
            'category',
            'profile',
            'payment.paymentItem',
        ]);

        /*
        |--------------------------------------------------------------------------
        | VERIFICATION URL
        |--------------------------------------------------------------------------
        */

        $verificationUrl = route(
            'documents.verify',
            $document->qr_token
        );

        /*
        |--------------------------------------------------------------------------
        | GENERATE QR FILE
        |--------------------------------------------------------------------------
        */

        $qrCode = $this->generateQrCode(
            $document,
            $verificationUrl
        );

        /*
        |--------------------------------------------------------------------------
        | GENERATE PDF
        |--------------------------------------------------------------------------
        */

        $pdf = Pdf::loadView(
            'member.documents.operational-rights.pdf',
            compact(
                'document',
                'verificationUrl',
                'qrCode'
            )
        );

        $pdf->setPaper(
            'A4',
            'portrait'
        );

        return $pdf->stream(
            $document->document_number . '.pdf'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD PDF
    |--------------------------------------------------------------------------
    */

    public function download(
        OperationalRightsDocument $document
    ) {
        abort_unless(
            $document->user_id === Auth::id(),
            403
        );

        $pdfPath = $this->getPdfPath(
            $document
        );

        return response()->download(
            $pdfPath,
            $this->getPdfFilename($document),
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PRINT
    |--------------------------------------------------------------------------
    */

    public function print(
        OperationalRightsDocument $document
    ) {
        abort_unless(
            $document->user_id === Auth::id(),
            403
        );

        $document->load([
            'user',
            'membership',
            'category',
            'profile',
            'payment.paymentItem',
        ]);

        $verificationUrl = route(
            'documents.verify',
            $document->qr_token
        );

        $qrCode = $this->generateQrCode(
            $document,
            $verificationUrl
        );

        return view(
            'member.documents.operational-rights.print',
            compact(
                'document',
                'verificationUrl',
                'qrCode'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PUBLIC VERIFICATION
    |--------------------------------------------------------------------------
    */

public function verify(string $token)
{
    $document = OperationalRightsDocument::with([
        'membership',
        'category',
        'profile',
        'payment',
        'user',
    ])
        ->where('qr_token', $token)
        ->first();

    /*
    |--------------------------------------------------------------------------
    | DOCUMENT NOT FOUND
    |--------------------------------------------------------------------------
    */

    if (!$document) {

        return view(
            'member.documents.verify',
            [
                'document' => null,
                'verified' => false,
                'message' =>
                    'This document could not be found. The verification token is invalid or the document does not exist.',
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CHECK DOCUMENT STATUS
    |--------------------------------------------------------------------------
    */

    $verified = false;

    $message = '';


    if ($document->status !== 'active') {

        $message =
            'This document exists, but it is no longer active.';

    } elseif (
        $document->expires_at &&
        now()->greaterThan(
            $document->expires_at
        )
    ) {

        $message =
            'This document has expired and is no longer valid.';

    } else {

        $verified = true;

        $message =
            'This document is authentic and currently active.';
    }


    /*
    |--------------------------------------------------------------------------
    | RETURN PUBLIC VERIFICATION PAGE
    |--------------------------------------------------------------------------
    */

    return view(
        'member.documents.verify',
        compact(
            'document',
            'verified',
            'message'
        )
    );
}


    /*
    |--------------------------------------------------------------------------
    | GENERATE QR CODE
    |--------------------------------------------------------------------------
    |
    | The QR is saved directly inside:
    |
    | public/documents/qr-codes/
    |
    | No storage:link required.
    |
    */

    protected function generateQrCode(
        OperationalRightsDocument $document,
        string $verificationUrl
    ): array {

        /*
        |--------------------------------------------------------------------------
        | DIRECTORY
        |--------------------------------------------------------------------------
        */

        $directory = public_path(
            'documents/qr-codes'
        );

        if (!File::exists($directory)) {

            File::makeDirectory(
                $directory,
                0755,
                true
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FILE NAME
        |--------------------------------------------------------------------------
        */

        $filename =
            $document->qr_token . '.png';

        $fullPath =
            $directory .
            DIRECTORY_SEPARATOR .
            $filename;

        /*
        |--------------------------------------------------------------------------
        | GENERATE ONLY IF IT DOES NOT EXIST
        |--------------------------------------------------------------------------
        */

        if (!File::exists($fullPath)) {

            $result = Builder::create()
                ->writer(
                    new PngWriter()
                )
                ->data(
                    $verificationUrl
                )
                ->size(300)
                ->margin(15)
                ->build();

            /*
            |--------------------------------------------------------------------------
            | SAVE PNG
            |--------------------------------------------------------------------------
            */

            $result->saveToFile(
                $fullPath
            );
        }

        /*
        |--------------------------------------------------------------------------
        | RETURN INFORMATION
        |--------------------------------------------------------------------------
        */

        return [

            /*
            | Browser URL
            */

            'url' =>
                asset(
                    'documents/qr-codes/' .
                    $filename
                ),

            /*
            | Absolute server path
            | Used by DomPDF.
            */

            'path' =>
                $fullPath,

            /*
            | Verification URL
            */

            'verification_url' =>
                $verificationUrl,

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE PDF
    |--------------------------------------------------------------------------
    */

    protected function generatePdf(
        OperationalRightsDocument $document
    ): string {

        $document->load([
            'user',
            'membership',
            'category',
            'profile',
            'payment.paymentItem',
        ]);

        /*
        |--------------------------------------------------------------------------
        | PDF DIRECTORY
        |--------------------------------------------------------------------------
        */

        $directory =
            public_path(
                'documents/operational-rights'
            );

        if (!File::exists($directory)) {

            File::makeDirectory(
                $directory,
                0755,
                true
            );
        }

        /*
        |--------------------------------------------------------------------------
        | PDF FILENAME
        |--------------------------------------------------------------------------
        */

        $filename =
            $this->getPdfFilename(
                $document
            );

        $fullPath =
            $directory .
            DIRECTORY_SEPARATOR .
            $filename;

        /*
        |--------------------------------------------------------------------------
        | VERIFICATION URL
        |--------------------------------------------------------------------------
        */

        $verificationUrl = route(
            'documents.operational-rights.verify',
            $document->qr_token
        );

        /*
        |--------------------------------------------------------------------------
        | QR CODE
        |--------------------------------------------------------------------------
        */

        $qrCode = $this->generateQrCode(
            $document,
            $verificationUrl
        );

        /*
        |--------------------------------------------------------------------------
        | PDF
        |--------------------------------------------------------------------------
        */

        $pdf = Pdf::loadView(
            'member.documents.operational-rights.pdf',
            compact(
                'document',
                'verificationUrl',
                'qrCode'
            )
        );

        $pdf->setPaper(
            'A4',
            'portrait'
        );

        /*
        |--------------------------------------------------------------------------
        | SAVE
        |--------------------------------------------------------------------------
        */

        $pdf->save(
            $fullPath
        );

        /*
        |--------------------------------------------------------------------------
        | DATABASE
        |--------------------------------------------------------------------------
        */

        $document->update([
            'pdf_path' =>
                'documents/operational-rights/' .
                $filename,

            'generated_at' =>
                $document->generated_at
                    ?? now(),
        ]);

        return $fullPath;
    }


    /*
    |--------------------------------------------------------------------------
    | GET PDF PATH
    |--------------------------------------------------------------------------
    */

    protected function getPdfPath(
        OperationalRightsDocument $document
    ): string {

        if ($document->pdf_path) {

            $path = public_path(
                $document->pdf_path
            );

            if (File::exists($path)) {

                return $path;
            }
        }

        return $this->generatePdf(
            $document
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PDF FILE NAME
    |--------------------------------------------------------------------------
    */

    protected function getPdfFilename(
        OperationalRightsDocument $document
    ): string {

        return
            $document->document_number .
            '.pdf';
    }
}
