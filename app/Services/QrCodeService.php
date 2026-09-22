<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeService
{
    /**
     * Generate a QR code containing the public
     * document verification URL.
     *
     * Returns a complete Base64 image data URI
     * that can be used directly inside an <img> tag.
     */
    public function generate(string $trackingCode): string
    {
        $verificationUrl = route(
            'documents.verify',
            ['trackingCode' => $trackingCode],
            true
        );

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($verificationUrl)
            ->size(300)
            ->margin(10)
            ->build();

        return 'data:image/png;base64,' .
            base64_encode(
                $result->getString()
            );
    }

    /**
     * Generate a QR code for a membership card.
     *
     * The QR code contains the public membership-card
     * verification URL using the card's unique QR token.
     */
public function generateMembershipCard(string $qrToken): string
{
    $verificationUrl = route(
        'membership.verify',
        ['qrToken' => $qrToken],
        true
    );

    $result = Builder::create()
        ->writer(new PngWriter())
        ->data($verificationUrl)
        ->size(300)
        ->margin(10)
        ->build();

    return 'data:image/png;base64,' .
        base64_encode(
            $result->getString()
        );
}
}
