<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\DocumentField;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. REGULAR EXPORTER MEMBERSHIP CERTIFICATE
        |--------------------------------------------------------------------------
        */

        $regularExporter = Document::updateOrCreate(
            ['code' => 'NACPDEAN-MEMBERSHIP-REGULAR-EXPORTER'],
            [
                'name' => 'NACPDEAN Membership Certificate - Regular Exporter',
                'description' => 'NACPDEAN membership certificate issued to regular exporter members.',
                'type' => 'certificate',
                'template' => 'membership_certificate_regular_exporter',
                'validity_type' => 'fixed_date',
                'validity_value' => null,
                'requires_form' => false,
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 2. RCG EXPORTER MEMBERSHIP CERTIFICATE
        |--------------------------------------------------------------------------
        */

        $rcgExporter = Document::updateOrCreate(
            ['code' => 'NACPDEAN-MEMBERSHIP-RCG-EXPORTER'],
            [
                'name' => 'NACPDEAN Membership Certificate - RCG Exporter',
                'description' => 'NACPDEAN/RCG joint membership certificate issued to RCG exporter members.',
                'type' => 'certificate',
                'template' => 'membership_certificate_rcg_exporter',
                'validity_type' => 'fixed_date',
                'validity_value' => null,
                'requires_form' => false,
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 3. EXPORTER MEMBERSHIP CONFIRMATION LETTER
        |--------------------------------------------------------------------------
        */

        $confirmationLetter = Document::updateOrCreate(
            ['code' => 'NACPDEAN-EXPORTER-CONFIRMATION'],
            [
                'name' => 'NACPDEAN Exporter Membership Confirmation Letter',
                'description' => 'Official confirmation letter issued to approved exporter members.',
                'type' => 'confirmation_letter',
                'template' => 'exporter_membership_confirmation_letter',
                'validity_type' => 'none',
                'validity_value' => null,
                'requires_form' => false,
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 4. CHARCOAL TRACEABILITY TRANSIT PASS
        |--------------------------------------------------------------------------
        */

        $transitPass = Document::updateOrCreate(
            ['code' => 'NACPDEAN-CHARCOAL-TRANSIT-PASS'],
            [
                'name' => 'NACPDEAN Charcoal Traceability Transit Pass',
                'description' => 'Traceability and movement document for charcoal transportation.',
                'type' => 'transit_pass',
                'template' => 'charcoal_traceability_transit_pass',
                'validity_type' => 'none',
                'validity_value' => null,
                'requires_form' => true,
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 5. RCG CHARCOAL LIFTING RIGHT
        |--------------------------------------------------------------------------
        */

        $rcgLiftingRight = Document::updateOrCreate(
            ['code' => 'NACPDEAN-LIFTING-RIGHT-RCG'],
            [
                'name' => 'NACPDEAN Charcoal Lifting Right - RCG Member',
                'description' => 'Charcoal lifting right issued to NACPDEAN/RCG members.',
                'type' => 'right_document',
                'template' => 'charcoal_lifting_right_rcg',
                'validity_type' => 'fixed_date',
                'validity_value' => null,
                'requires_form' => false,
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 6. EXPORTER CHARCOAL LIFTING RIGHT
        |--------------------------------------------------------------------------
        */

        $exporterLiftingRight = Document::updateOrCreate(
            ['code' => 'NACPDEAN-LIFTING-RIGHT-EXPORTER'],
            [
                'name' => 'NACPDEAN Charcoal Lifting Right - Exporter',
                'description' => 'Charcoal lifting right issued to exporter members.',
                'type' => 'right_document',
                'template' => 'charcoal_lifting_right_exporter',
                'validity_type' => 'fixed_date',
                'validity_value' => null,
                'requires_form' => false,
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 7. PRODUCER CHARCOAL LIFTING RIGHT
        |--------------------------------------------------------------------------
        */

        $producerLiftingRight = Document::updateOrCreate(
            ['code' => 'NACPDEAN-LIFTING-RIGHT-PRODUCER'],
            [
                'name' => 'NACPDEAN Charcoal Lifting Right - Producer',
                'description' => 'Charcoal lifting right issued to producer members.',
                'type' => 'right_document',
                'template' => 'charcoal_lifting_right_producer',
                'validity_type' => 'fixed_date',
                'validity_value' => null,
                'requires_form' => false,
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 8. DEALER CHARCOAL LIFTING RIGHT
        |--------------------------------------------------------------------------
        */

        $dealerLiftingRight = Document::updateOrCreate(
            ['code' => 'NACPDEAN-LIFTING-RIGHT-DEALER'],
            [
                'name' => 'NACPDEAN Charcoal Lifting Right - Dealer',
                'description' => 'Charcoal dealing/lifting right issued to dealer members.',
                'type' => 'right_document',
                'template' => 'charcoal_lifting_right_dealer',
                'validity_type' => 'fixed_date',
                'validity_value' => null,
                'requires_form' => false,
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 9. SUPPLIER CHARCOAL LIFTING RIGHT
        |--------------------------------------------------------------------------
        */

        $supplierLiftingRight = Document::updateOrCreate(
            ['code' => 'NACPDEAN-LIFTING-RIGHT-SUPPLIER'],
            [
                'name' => 'NACPDEAN Charcoal Lifting Right - Supplier',
                'description' => 'Charcoal dealing/lifting right issued to supplier members.',
                'type' => 'right_document',
                'template' => 'charcoal_lifting_right_supplier',
                'validity_type' => 'fixed_date',
                'validity_value' => null,
                'requires_form' => false,
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 10. ANNUAL MEMBERSHIP PAYMENT RECEIPT
        |--------------------------------------------------------------------------
        */

        $membershipReceipt = Document::updateOrCreate(
            ['code' => 'NACPDEAN-ANNUAL-MEMBERSHIP-RECEIPT'],
            [
                'name' => 'NACPDEAN Annual Membership Payment Receipt',
                'description' => 'Official receipt for annual NACPDEAN membership payments.',
                'type' => 'receipt',
                'template' => 'annual_membership_payment_receipt',
                'validity_type' => 'none',
                'validity_value' => null,
                'requires_form' => false,
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 11. AFFORESTATION PAYMENT RECEIPT
        |--------------------------------------------------------------------------
        */

        $afforestationReceipt = Document::updateOrCreate(
            ['code' => 'NACPDEAN-AFFORESTATION-RECEIPT'],
            [
                'name' => 'NACPDEAN Afforestation Payment Receipt',
                'description' => 'Official receipt for NACPDEAN afforestation payments.',
                'type' => 'receipt',
                'template' => 'afforestation_payment_receipt',
                'validity_type' => 'none',
                'validity_value' => null,
                'requires_form' => true,
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 12. AFFORESTATION COMPLIANCE PAYMENT RECEIPT
        |--------------------------------------------------------------------------
        */

        $afforestationComplianceReceipt = Document::updateOrCreate(
            ['code' => 'NACPDEAN-AFFORESTATION-COMPLIANCE-RECEIPT'],
            [
                'name' => 'NACPDEAN Afforestation Compliance Payment Receipt',
                'description' => 'Official receipt confirming payment of an afforestation compliance fee.',
                'type' => 'receipt',
                'template' => 'afforestation_compliance_payment_receipt',
                'validity_type' => 'none',
                'validity_value' => null,
                'requires_form' => true,
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | DOCUMENT FIELDS
        |--------------------------------------------------------------------------
        */

        /*
        |--------------------------------------------------------------------------
        | AFFORESTATION COMPLIANCE PAYMENT RECEIPT
        |--------------------------------------------------------------------------
        */

        $this->createFields(
            $afforestationComplianceReceipt,
            [
                [
                    'receipt_no',
                    'RECEIPT NO',
                    'text',
                    'Header / Document Details',
                    true,
                    true
                ],

                [
                    'member_name',
                    'Member Name',
                    'text',
                    'Shipper Details',
                    true,
                    true
                ],

                [
                    'membership_no',
                    'Membership NO',
                    'text',
                    'Shipper Details',
                    true,
                    true
                ],

                [
                    'lifting_right_no',
                    'Lifting Right NO',
                    'text',
                    'Shipper Details',
                    true,
                    true
                ],

                [
                    'phone',
                    'Phone',
                    'text',
                    'Shipper Details',
                    true,
                    true
                ],

                [
                    'container_number',
                    'Container Number',
                    'text',
                    'Shipper Details',
                    false,
                    false
                ],

                [
                    'truck_number',
                    'Truck Number',
                    'text',
                    'Shipper Details',
                    false,
                    false
                ],

                [
                    'dealer_supplier_name',
                    'Dealer/Supplier Name',
                    'text',
                    'Supplier Details',
                    true,
                    false
                ],

                [
                    'supplier_membership_no',
                    'Membership NO',
                    'text',
                    'Supplier Details',
                    true,
                    false
                ],

                [
                    'dealing_right_no',
                    'Dealing Right NO',
                    'text',
                    'Supplier Details',
                    false,
                    false
                ],

                [
                    'loading_point',
                    'Loading Point',
                    'text',
                    'Supplier Details',
                    true,
                    false
                ],

                [
                    'state',
                    'State',
                    'text',
                    'Supplier Details',
                    true,
                    false
                ],

                [
                    'lga',
                    'LGA',
                    'text',
                    'Supplier Details',
                    true,
                    false
                ],

                [
                    'amount_paid',
                    'Amount Paid',
                    'number',
                    'Payment Details',
                    true,
                    true
                ],

                [
                    'amount_in_figure',
                    'Amount in Figure',
                    'number',
                    'Payment Details',
                    true,
                    true
                ],

                [
                    'payment_date',
                    'Date',
                    'date',
                    'Payment Details',
                    true,
                    true
                ],

                [
                    'payment_time',
                    'Time',
                    'time',
                    'Payment Details',
                    true,
                    true
                ],

                [
                    'tracking_code',
                    'Tracking Code',
                    'text',
                    'Official Use / Security',
                    true,
                    true
                ],
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | AFFORESTATION PAYMENT RECEIPT
        |--------------------------------------------------------------------------
        */

        $this->createFields(
            $afforestationReceipt,
            [
                [
                    'receipt_no',
                    'RECEIPT NO',
                    'text',
                    'Header / Document Details',
                    true,
                    true
                ],

                [
                    'seller_member_name',
                    'Member Name',
                    'text',
                    'Seller Details',
                    true,
                    true
                ],

                [
                    'seller_membership_no',
                    'Membership No',
                    'text',
                    'Seller Details',
                    true,
                    true
                ],

                [
                    'seller_dealing_right_no',
                    'Dealing Right No',
                    'text',
                    'Seller Details',
                    false,
                    true
                ],

                [
                    'seller_phone',
                    'Phone',
                    'text',
                    'Seller Details',
                    true,
                    true
                ],

                [
                    'loading_point',
                    'Loading Point',
                    'text',
                    'Seller Details',
                    true,
                    false
                ],

                [
                    'buyer_member_name',
                    'Member Name',
                    'text',
                    'Buyer Details',
                    true,
                    false
                ],

                [
                    'buyer_membership_no',
                    'Membership NO',
                    'text',
                    'Buyer Details',
                    true,
                    false
                ],

                [
                    'buyer_dealing_right_no',
                    'Dealing Right NO',
                    'text',
                    'Buyer Details',
                    false,
                    false
                ],

                [
                    'state',
                    'State',
                    'text',
                    'Buyer Details',
                    true,
                    false
                ],

                [
                    'vehicle_number',
                    'Vehicle/Truck/Trailer/Container NO',
                    'text',
                    'Buyer Details',
                    true,
                    false
                ],

                [
                    'amount_paid',
                    'Amount Paid',
                    'number',
                    'Payment Details',
                    true,
                    true
                ],

                [
                    'amount_in_figure',
                    'Amount in Figure',
                    'number',
                    'Payment Details',
                    true,
                    true
                ],

                [
                    'payment_date',
                    'Date',
                    'date',
                    'Payment Details',
                    true,
                    true
                ],

                [
                    'payment_time',
                    'Time',
                    'time',
                    'Payment Details',
                    true,
                    true
                ],

                [
                    'tracking_code',
                    'Tracking Code',
                    'text',
                    'Official Use / Security',
                    true,
                    true
                ],
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | ANNUAL MEMBERSHIP PAYMENT RECEIPT
        |--------------------------------------------------------------------------
        */

        $this->createFields(
            $membershipReceipt,
            [
                [
                    'receipt_no',
                    'RECEIPT NO',
                    'text',
                    'Header / Document Details',
                    true,
                    true
                ],

                [
                    'date_of_payment',
                    'Date of Payment',
                    'date',
                    'Name Information',
                    true,
                    true
                ],

                [
                    'membership_id',
                    'Membership ID',
                    'text',
                    'Name Information',
                    true,
                    true
                ],

                [
                    'member_name',
                    'Member Name',
                    'text',
                    'Name Information',
                    true,
                    true
                ],

                [
                    'business_name',
                    'Business Name',
                    'text',
                    'Name Information',
                    false,
                    true
                ],

                [
                    'membership_category',
                    'Membership Category',
                    'text',
                    'Name Information',
                    true,
                    true
                ],

                [
                    'phone_number',
                    'Phone Number',
                    'text',
                    'Name Information',
                    true,
                    true
                ],

                [
                    'email',
                    'Email',
                    'text',
                    'Name Information',
                    true,
                    true
                ],

                [
                    'state',
                    'State',
                    'text',
                    'Name Information',
                    true,
                    true
                ],

                [
                    'date_joined',
                    'Date Joined',
                    'date',
                    'Name Information',
                    true,
                    true
                ],

                [
                    'payment_for',
                    'Payment for',
                    'text',
                    'Payment Details',
                    true,
                    false
                ],

                [
                    'membership_year',
                    'Membership Year',
                    'text',
                    'Payment Details',
                    true,
                    true
                ],

                [
                    'amount_paid',
                    'Amount Paid',
                    'number',
                    'Payment Details',
                    true,
                    true
                ],

                [
                    'transaction_reference',
                    'Transaction Reference',
                    'text',
                    'Payment Details',
                    true,
                    true
                ],

                [
                    'verification_code',
                    'VERIFICATION CODE',
                    'text',
                    'Authentication / Security',
                    true,
                    true
                ],
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | CHARCOAL TRACEABILITY TRANSIT PASS
        |--------------------------------------------------------------------------
        */

        $this->createFields(
            $transitPass,
            [
                [
                    'transit_no',
                    'TRANSIT NO',
                    'text',
                    'Header / Document Details',
                    true,
                    true
                ],

                [
                    'seller_name',
                    'Dealer/Supplier Name',
                    'text',
                    'Seller Details',
                    true,
                    false
                ],

                [
                    'seller_membership_no',
                    'Membership NO',
                    'text',
                    'Seller Details',
                    true,
                    true
                ],

                [
                    'seller_dealing_right_no',
                    'Dealing Right NO',
                    'text',
                    'Seller Details',
                    false,
                    true
                ],

                [
                    'community_village',
                    'Community/Village',
                    'text',
                    'Seller Details',
                    true,
                    false
                ],

                [
                    'lga',
                    'LGA',
                    'text',
                    'Seller Details',
                    true,
                    false
                ],

                [
                    'state',
                    'State',
                    'text',
                    'Seller Details',
                    true,
                    false
                ],

                [
                    'phone',
                    'Phone',
                    'text',
                    'Seller Details',
                    true,
                    false
                ],

                [
                    'driver_name',
                    'Drivers Name',
                    'text',
                    'Movement Details',
                    true,
                    false
                ],

                [
                    'driver_number',
                    'Drivers Number',
                    'text',
                    'Movement Details',
                    true,
                    false
                ],

                [
                    'destination',
                    'Destination',
                    'text',
                    'Movement Details',
                    true,
                    false
                ],

                [
                    'vehicle_number',
                    'Vehicle/Truck/Trailer/Container NO',
                    'text',
                    'Movement Details',
                    true,
                    false
                ],

                [
                    'buyer_name',
                    'Name',
                    'text',
                    'Buyer Details',
                    true,
                    false
                ],

                [
                    'buyer_category',
                    'Dealer/Supplier/Exporter',
                    'select',
                    'Buyer Details',
                    true,
                    false
                ],

                [
                    'buyer_right_no',
                    'Dealing/Lifting Right NO',
                    'text',
                    'Buyer Details',
                    false,
                    false
                ],

                [
                    'buyer_phone',
                    'Phone',
                    'text',
                    'Buyer Details',
                    true,
                    false
                ],

                [
                    'tracking_code',
                    'Tracking Code',
                    'text',
                    'Official Use / Security',
                    true,
                    true
                ],
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | EXPORTER MEMBERSHIP CONFIRMATION LETTER
        |--------------------------------------------------------------------------
        */

        $this->createFields(
            $confirmationLetter,
            [
                [
                    'date',
                    'Date',
                    'date',
                    'Header / Document Details',
                    true,
                    true
                ],

                [
                    'ref',
                    'Ref',
                    'text',
                    'Header / Document Details',
                    true,
                    true
                ],

                [
                    'recipient',
                    'Recipient Title/Company',
                    'text',
                    'Recipient Details',
                    true,
                    false
                ],

                [
                    'attn',
                    'Attn',
                    'text',
                    'Recipient Details',
                    false,
                    false
                ],

                [
                    'membership_name',
                    'Membership Name',
                    'text',
                    'Membership Details',
                    true,
                    true
                ],

                [
                    'membership_representative',
                    'Membership Representative',
                    'text',
                    'Membership Details',
                    true,
                    true
                ],

                [
                    'membership_number',
                    'Membership Number',
                    'text',
                    'Membership Details',
                    true,
                    true
                ],
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | CHARCOAL LIFTING / DEALING RIGHTS
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | The document objects are VALUES.
        | The category strings are KEYS.
        |
        | This prevents:
        |
        | Illegal offset type
        |
        */

        foreach (
            [
                'rcg' => $rcgLiftingRight,
                'exporter' => $exporterLiftingRight,
                'producer' => $producerLiftingRight,
                'dealer' => $dealerLiftingRight,
                'supplier' => $supplierLiftingRight,
            ] as $category => $document
        ) {
            $this->createFields(
                $document,
                [
                    [
                        'member_name',
                        'Name / Business Name',
                        'text',
                        'Certificate Fields',
                        true,
                        true
                    ],

                    [
                        'membership_number',
                        'Membership Number',
                        'text',
                        'Certificate Fields',
                        true,
                        true
                    ],

                    [
                        'issued_at',
                        'Given on this date',
                        'date',
                        'Certificate Fields',
                        true,
                        true
                    ],

                    [
                        'expires_at',
                        'Valid till',
                        'date',
                        'Certificate Fields',
                        true,
                        true
                    ],

                    [
                        'ref_no',
                        'REF NO',
                        'text',
                        'Certificate Fields',
                        true,
                        true
                    ],
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP CERTIFICATE FIELDS
        |--------------------------------------------------------------------------
        |
        | We have not added fields to the two membership certificates yet
        | because the exact layouts/screenshots need to be confirmed.
        |
        | The documents themselves have already been created above.
        |
        */
    }

    /**
     * Create or update document fields.
     */
    private function createFields(
        Document $document,
        array $fields
    ): void {
        foreach ($fields as $index => $field) {

            [
                $fieldKey,
                $label,
                $fieldType,
                $section,
                $required,
                $system,
            ] = $field;

            DocumentField::updateOrCreate(
                [
                    'document_id' => $document->id,
                    'field_key' => $fieldKey,
                ],
                [
                    'label' => $label,

                    'field_type' => $fieldType,

                    'section' => $section,

                    'placeholder' => null,

                    'default_value' => null,

                    'options' => $fieldType === 'select'
                        ? [
                            'Dealer',
                            'Supplier',
                            'Exporter',
                        ]
                        : null,

                    'is_required' => $required,

                    'is_system' => $system,

                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}
