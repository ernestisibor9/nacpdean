<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\DocumentField;
use Illuminate\Database\Seeder;

class PenaltyReceiptFieldsSeeder extends Seeder
{
    public function run(): void
    {
        $document = Document::where('code', 'NACPDEAN-PENALTY-RECEIPT')->first();

        if (!$document) {
            $this->command->error('NACPDEAN-PENALTY-RECEIPT document not found. Run PenaltySeeder first.');
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | FIELD DEFINITIONS
        |--------------------------------------------------------------------------
        |
        | Every field_key here must match a case in
        | DocumentGenerationService::resolveSystemField()
        | — the PENALTY RECEIPT FIELDS block.
        |
        */

        $fields = [
            ['receipt_no',           'Receipt No',           'text',     true],
            ['payment_date',         'Payment Date',         'text',     true],
            ['member_name',          'Payer / Member Name',  'text',     true],
            ['membership_number',    'Membership No',        'text',     true],
            ['member_address',       'Address',              'textarea', true],
            ['email',                'Email',                'text',     true],
            ['phone',                'Phone',                'text',     true],
            ['purpose_of_payment',   'Purpose of Payment',   'textarea', true],
            ['payment_mode',         'Payment Mode',         'text',     true],
            ['bank_name',            'Bank Name',            'text',     true],
            ['payment_reference',    'Transaction Ref',      'text',     true],
            ['amount_in_figure',     'Amount Paid',          'text',     true],
            ['amount_in_words',      'Amount in Words',      'textarea', true],
            ['penalties_list',       'Penalties',            'textarea', true],
            ['tracking_code',        'Verification Code',    'text',     true],
        ];

        $sort = 1;

        foreach ($fields as [$key, $label, $type, $system]) {
            DocumentField::updateOrCreate(
                [
                    'document_id' => $document->id,
                    'field_key'   => $key,
                ],
                [
                    'label'         => $label,
                    'field_type'    => $type,
                    'section'       => 'general',
                    'placeholder'   => null,
                    'default_value' => null,
                    'options'       => null,
                    'is_required'   => false,
                    'is_system'     => $system,
                    'sort_order'    => $sort++,
                ]
            );
        }

        $this->command->info(
            "Seeded " . count($fields) . " fields for NACPDEAN-PENALTY-RECEIPT (document_id={$document->id})."
        );
    }
}
