<?php

namespace Database\Seeders;

use App\Models\PaymentItem;
use Illuminate\Database\Seeder;

class PaymentItemSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |--------------------------------------------------------------------------
        |
        | Replace the amounts below with the official NACPDEAN fees.
        |
        | They are deliberately inactive until the correct fees are entered.
        |
        */

        $items = [

            [
                'name' => 'Charcoal Lifting Rights Certificate',
                'code' => 'CLR',
                'description' =>
                    'Charcoal Lifting Rights Certificate for Regular NACPDEAN Exporters.',
                'type' => 'certificate',
                'amount' => 25000,
                'membership_category_id' => null,
                'is_active' => false,
            ],

            [
                'name' =>
                    'Charcoal Lifting Rights Certificate (NACPDEAN/RCG Joint Membership)',
                'code' => 'CLR-RCG',
                'description' =>
                    'Charcoal Lifting Rights Certificate for NACPDEAN/RCG Joint Membership exporters.',
                'type' => 'certificate',
                'amount' => 25000,
                'membership_category_id' => null,
                'is_active' => false,
            ],

            [
                'name' =>
                    'Charcoal Dealing Rights Certificate - Supplier',
                'code' => 'CDR-SLR',
                'description' =>
                    'Charcoal Dealing Rights Certificate for registered suppliers.',
                'type' => 'certificate',
                'amount' => 25000,
                'membership_category_id' => null,
                'is_active' => false,
            ],

            [
                'name' =>
                    'Charcoal Dealing Rights Certificate - Dealer',
                'code' => 'CDR-DEA',
                'description' =>
                    'Charcoal Dealing Rights Certificate for registered dealers.',
                'type' => 'certificate',
                'amount' => 25000,
                'membership_category_id' => null,
                'is_active' => false,
            ],

            [
                'name' =>
                    'Charcoal Producing Rights Certificate',
                'code' => 'CPR',
                'description' =>
                    'Charcoal Producing Rights Certificate for registered producers.',
                'type' => 'certificate',
                'amount' => 25000,
                'membership_category_id' => null,
                'is_active' => false,
            ],

            [
                'name' => 'Penalty',
                'code' => 'PENALTY',
                'description' =>
                    'Membership-related penalty payment.',
                'type' => 'penalty',
                'amount' => 10000,
                'membership_category_id' => null,
                'is_active' => false,
            ],

            [
                'name' => 'Membership Renewal',
                'code' => 'RENEWAL',
                'description' =>
                    'Membership renewal payment.',
                'type' => 'renewal',
                'amount' => 50000,
                'membership_category_id' => null,
                'is_active' => false,
            ],

            [
                'name' => 'Afforestation Payment',
                'code' => 'AFFORESTATION',
                'description' =>
                    'Afforestation payment for applicable charcoal transactions.',
                'type' => 'afforestation',
                'amount' => 15000,
                'membership_category_id' => null,
                'is_active' => false,
            ],

        ];


        foreach ($items as $item) {

            PaymentItem::updateOrCreate(

                [
                    'code' => $item['code'],
                ],

                $item

            );

        }
    }
}
