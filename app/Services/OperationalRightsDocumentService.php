<?php

namespace App\Services;

use App\Models\OperationalRightsDocument;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class OperationalRightsDocumentService
{
    /**
     * Generate an Operational Rights Document
     * from a successful payment.
     */
    public function generate(Payment $payment): OperationalRightsDocument
    {
        /*
        |--------------------------------------------------------------------------
        | Load required relationships
        |--------------------------------------------------------------------------
        */

        $payment->loadMissing([
            'paymentItem',
            'user',
            'user.membership',
            'user.profile',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Payment Item
        |--------------------------------------------------------------------------
        */

        if (!$payment->paymentItem) {
            throw new RuntimeException(
                'This payment does not have a payment item.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Determine document type
        |--------------------------------------------------------------------------
        */

        $documentDetails = $this->getDocumentDetails(
            $payment->paymentItem->code
        );

        /*
        |--------------------------------------------------------------------------
        | This payment does not generate an
        | Operational Rights Document
        |--------------------------------------------------------------------------
        */

        if (!$documentDetails) {
            throw new RuntimeException(
                'This payment item does not generate an Operational Rights Document.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent duplicate document generation
        |--------------------------------------------------------------------------
        */

        $existingDocument = OperationalRightsDocument::where(
            'payment_id',
            $payment->id
        )->first();

        if ($existingDocument) {
            return $existingDocument;
        }

        /*
        |--------------------------------------------------------------------------
        | User
        |--------------------------------------------------------------------------
        */

        if (!$payment->user) {
            throw new RuntimeException(
                'No user was found for this payment.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Membership
        |--------------------------------------------------------------------------
        */

        $membership = $payment->user->membership;

        if (!$membership) {
            throw new RuntimeException(
                'No membership was found for this payment.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Member Profile
        |--------------------------------------------------------------------------
        */

        $memberProfile = $payment->user->profile;

        if (!$memberProfile) {
            throw new RuntimeException(
                'No member profile was found for this payment.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Create document
        |--------------------------------------------------------------------------
        */

        return DB::transaction(function () use (
            $payment,
            $membership,
            $memberProfile,
            $documentDetails
        ) {

            $document = OperationalRightsDocument::create([

                'user_id' =>
                    $payment->user_id,

                'membership_id' =>
                    $membership->id,

                'member_profile_id' =>
                    $memberProfile->id,

                'payment_id' =>
                    $payment->id,

                'membership_category_id' =>
                    $membership->membership_category_id,

                'document_number' =>
                    $this->generateDocumentNumber(
                        $documentDetails['prefix']
                    ),

                'reference_number' =>
                    $this->generateReferenceNumber(),

                'authentication_code' =>
                    $this->generateAuthenticationCode(),

                'qr_token' =>
                    $this->generateQrToken(),

                'document_type' =>
                    $documentDetails['document_type'],

                'operational_rights_category' =>
                    $documentDetails['category'],

                'issued_at' =>
                    now()->toDateString(),

                'expires_at' =>
                    now()->addYear()->toDateString(),

                'status' =>
                    'active',

                'generated_at' =>
                    now(),

            ]);

            return $document;
        });
    }


    /*
    |--------------------------------------------------------------------------
    | DOCUMENT TYPE
    |--------------------------------------------------------------------------
    */

    protected function getDocumentDetails(
        ?string $code
    ): ?array {

        return match ($code) {

            'CHARCOAL_LIFTING' => [

                'document_type' =>
                    'charcoal_lifting',

                'category' =>
                    'Charcoal Lifting Rights',

                'prefix' =>
                    'CLR',

            ],

            'CHARCOAL_LIFTING_RCG' => [

                'document_type' =>
                    'charcoal_lifting_rcg',

                'category' =>
                    'Charcoal Lifting Rights - NACPDEAN/RCG Joint Membership',

                'prefix' =>
                    'CLR-RCG',

            ],

            'CHARCOAL_DEALING_SUPPLIER' => [

                'document_type' =>
                    'charcoal_dealing_supplier',

                'category' =>
                    'Charcoal Dealing Rights - Supplier',

                'prefix' =>
                    'CDR-SLR',

            ],

            'CHARCOAL_DEALING_DEALER' => [

                'document_type' =>
                    'charcoal_dealing_dealer',

                'category' =>
                    'Charcoal Dealing Rights - Dealer',

                'prefix' =>
                    'CDR-DEA',

            ],

            'CHARCOAL_PRODUCING' => [

                'document_type' =>
                    'charcoal_producing',

                'category' =>
                    'Charcoal Producing Rights',

                'prefix' =>
                    'CPR',

            ],

            default => null,
        };
    }


    /*
    |--------------------------------------------------------------------------
    | DOCUMENT NUMBER
    |--------------------------------------------------------------------------
    */

    protected function generateDocumentNumber(
        string $prefix
    ): string {

        do {

            $number =
                $prefix .
                '-' .
                now()->format('Y') .
                '-' .
                strtoupper(
                    Str::random(8)
                );

        } while (
            OperationalRightsDocument::where(
                'document_number',
                $number
            )->exists()
        );

        return $number;
    }


    /*
    |--------------------------------------------------------------------------
    | REFERENCE NUMBER
    |--------------------------------------------------------------------------
    */

    protected function generateReferenceNumber(): string
    {
        do {

            $reference =
                'NACPDEAN-REF-' .
                now()->format('Y') .
                '-' .
                strtoupper(
                    Str::random(10)
                );

        } while (
            OperationalRightsDocument::where(
                'reference_number',
                $reference
            )->exists()
        );

        return $reference;
    }


    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATION CODE
    |--------------------------------------------------------------------------
    */

    protected function generateAuthenticationCode(): string
    {
        do {

            $code =
                'AUTH-' .
                strtoupper(
                    Str::random(16)
                );

        } while (
            OperationalRightsDocument::where(
                'authentication_code',
                $code
            )->exists()
        );

        return $code;
    }


    /*
    |--------------------------------------------------------------------------
    | QR TOKEN
    |--------------------------------------------------------------------------
    */

    protected function generateQrToken(): string
    {
        do {

            $token = Str::uuid()->toString();

        } while (
            OperationalRightsDocument::where(
                'qr_token',
                $token
            )->exists()
        );

        return $token;
    }
}
