<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\PaymentItem;
use App\Models\Penalty;
use Illuminate\Database\Seeder;

class PenaltySeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. PENALTY PAYMENT ITEM
        |--------------------------------------------------------------------------
        */

        $paymentItem = PaymentItem::firstOrCreate(
            ['code' => 'PENALTY'],
            [
                'name'          => 'Penalty Payment',
                'description'   => 'Penalty charges for industry violations',
                'type'          => 'penalty',
                'payment_group' => 'penalty',
                'amount'        => 0,
                'is_active'     => 1,
                'is_renewable'  => 0,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 2. ALL PENALTY PAYMENT RECEIPT DOCUMENT
        |--------------------------------------------------------------------------
        |
        | validity_type must NOT be null — the column is NOT NULL with default 'none'.
        | Match the pattern of other non-expiring documents in this project.
        |
        */

        $document = Document::updateOrCreate(
            ['code' => 'NACPDEAN-PENALTY-RECEIPT'],
            [
                'name'           => 'All Penalty Payment Receipt',
                'description'    => 'Receipt issued for penalty payments',
                'type'           => 'receipt',
                'template'       => 'documents.penalty_receipt',
                'validity_type'  => 'none',
                'validity_value' => 0,
                'validity_date'  => null,
                'requires_form'  => 0,
                'is_active'      => 1,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 3. ATTACH DOCUMENT TO PAYMENT ITEM
        |--------------------------------------------------------------------------
        */

        $paymentItem->documents()->syncWithoutDetaching([
            $document->id => [
                'generate_after_payment' => true,
                'is_primary'             => true,
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | 4. PENALTY CATALOG
        |--------------------------------------------------------------------------
        */

        $rows = [
            // §2 — Operating without operational right
            ['Exporter — Operating Without Operational Right', 'Pay penalty + renew operational membership', 100000, false],
            ['Supplier — Operating Without Operational Right', 'Pay penalty + renew operational membership', 50000,  false],
            ['Dealer — Operating Without Operational Right',   'Pay penalty + renew operational membership', 15000,  false],
            ['Producer — Operating Without Operational Right', 'Pay penalty + renew operational membership', 15000,  false],

            // §3 — Operating without membership registration
            ['Exporter — Operating Without Membership Registration', 'Pay penalty + register', 200000, false],
            ['Supplier — Operating Without Membership Registration', 'Pay penalty + register', 60000,  false],
            ['Dealer — Operating Without Membership Registration',   'Pay penalty + register', 20000,  false],
            ['Producer — Operating Without Membership Registration', 'Pay penalty + register', 20000,  false],

            // §4 — Non-payment of afforestation (Members)
            ['Export Container — Non-Payment of Afforestation (Member)',  'Pay outstanding afforestation fee + penalty', 150000, false],
            ['Truck/Trailer Charcoal Attachment — Non-Payment of Afforestation (Member)', 'Pay outstanding afforestation fee + penalty', 22500, false],
            ['Trailer Load/Truck — Non-Payment of Afforestation (Member)', 'Pay outstanding afforestation fee + penalty', 75000, false],
            ['6-Tyre Truck — Non-Payment of Afforestation (Member)',       'Pay outstanding afforestation fee + penalty', 30000, false],
            ['10-Tyre Truck — Non-Payment of Afforestation (Member)',      'Pay outstanding afforestation fee + penalty', 37500, false],
            ['Small Vehicle (20–80 Bags) — Non-Payment of Afforestation (Member)', 'Pay outstanding afforestation fee + penalty', 15000, false],
            ['Vehicle (5–20 Bags) — Non-Payment of Afforestation (Member)',        'Pay outstanding afforestation fee + penalty', 7500,  false],

            // §5 — Non-payment of afforestation (Non-Members)
            ['Export Container — Non-Payment of Afforestation (Non-Member)',  'Pay outstanding afforestation fee + penalty + register', 200000, false],
            ['Truck/Trailer Charcoal Attachment — Non-Payment of Afforestation (Non-Member)', 'Pay outstanding afforestation fee + penalty + register', 30000, false],
            ['Trailer Load/Truck — Non-Payment of Afforestation (Non-Member)', 'Pay outstanding afforestation fee + penalty + register', 100000, false],
            ['6-Tyre Truck — Non-Payment of Afforestation (Non-Member)',       'Pay outstanding afforestation fee + penalty + register', 40000, false],
            ['10-Tyre Truck — Non-Payment of Afforestation (Non-Member)',      'Pay outstanding afforestation fee + penalty + register', 50000, false],
            ['Small Vehicle (20–80 Bags) — Non-Payment of Afforestation (Non-Member)', 'Pay outstanding afforestation fee + penalty + register', 20000, false],
            ['Vehicle (5–20 Bags) — Non-Payment of Afforestation (Non-Member)',        'Pay outstanding afforestation fee + penalty + register', 10000, false],

            // §6 — Checkpoint non-compliance
            ['Truck Carrying Export Container — Non-Compliance at Checkpoint',   'Pay checkpoint penalty', 50000, false],
            ['Truck/Trailer Charcoal Attachment — Non-Compliance at Checkpoint', 'Pay checkpoint penalty', 20000, false],
            ['Trailer Load/Truck — Non-Compliance at Checkpoint',                'Pay checkpoint penalty', 20000, false],
            ['6-Tyre Truck — Non-Compliance at Checkpoint',                      'Pay checkpoint penalty', 20000, false],
            ['10-Tyre Truck — Non-Compliance at Checkpoint',                     'Pay checkpoint penalty', 20000, false],
            ['Vehicle (5–80 Bags) — Non-Compliance at Checkpoint',               'Pay checkpoint penalty', 5000,  false],

            // §6a — Additional administrative charge
            ['Additional Administrative Charge — Executive/Taskforce Vehicle', 'Pay additional administrative charge', 20000, false],

            // §7 — Unlawful encroachment (custom amount)
            ['Unlawful Encroachment by Foreign National — Nigerian Collaborator', 'Administrative penalty + possible prosecution', 0, true],

            // §7b — Warehouse reactivation
            ['Warehouse Reactivation Fee — Unauthorized Foreign National Presence', 'Reactivate sealed warehouse + lift blacklist', 500000, false],

            // §8 — Unlawful partnership
            ['Administrative Penalty — Unlawful Partnership with Foreign National', 'Pay administrative penalty + blacklist lifted on compliance', 500000, false],
        ];

        foreach ($rows as [$name, $action, $amount, $custom]) {
            Penalty::updateOrCreate(
                ['name' => $name],
                [
                    'action'           => $action,
                    'amount'           => $amount,
                    'is_custom_amount' => $custom,
                    'status'           => 1,
                    'payment_item_id'  => $paymentItem->id,
                ]
            );
        }
    }
}
