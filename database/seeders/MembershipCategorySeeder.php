<?php

namespace Database\Seeders;

use App\Models\MembershipCategory;
use App\Models\MembershipCategoryFee;
use Illuminate\Database\Seeder;

class MembershipCategorySeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Exporter
        |--------------------------------------------------------------------------
        */

        $exporter = MembershipCategory::updateOrCreate(
            ['code' => 'EXP'],
            [
                'name' => 'Exporter',
                'description' => 'NACPDEAN Regular Exporter Membership',
                'status' => true,
            ]
        );

        MembershipCategoryFee::updateOrCreate(
            [
                'membership_category_id' => $exporter->id,
                'fee_type' => 'new',
            ],
            [
                'amount' => 100000,
                'status' => true,
            ]
        );

        MembershipCategoryFee::updateOrCreate(
            [
                'membership_category_id' => $exporter->id,
                'fee_type' => 'existing',
            ],
            [
                'amount' => 50000,
                'status' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Supplier
        |--------------------------------------------------------------------------
        */

        $supplier = MembershipCategory::updateOrCreate(
            ['code' => 'SLR'],
            [
                'name' => 'Supplier',
                'description' => 'NACPDEAN Regular Supplier Membership',
                'status' => true,
            ]
        );

        MembershipCategoryFee::updateOrCreate(
            [
                'membership_category_id' => $supplier->id,
                'fee_type' => 'standard',
            ],
            [
                'amount' => 30000,
                'status' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Dealer
        |--------------------------------------------------------------------------
        */

        $dealer = MembershipCategory::updateOrCreate(
            ['code' => 'DEA'],
            [
                'name' => 'Dealer',
                'description' => 'NACPDEAN Regular Dealer Membership',
                'status' => true,
            ]
        );

        MembershipCategoryFee::updateOrCreate(
            [
                'membership_category_id' => $dealer->id,
                'fee_type' => 'standard',
            ],
            [
                'amount' => 10000,
                'status' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Producer
        |--------------------------------------------------------------------------
        */

        $producer = MembershipCategory::updateOrCreate(
            ['code' => 'PRD'],
            [
                'name' => 'Producer',
                'description' => 'NACPDEAN Regular Producer Membership',
                'status' => true,
            ]
        );

        MembershipCategoryFee::updateOrCreate(
            [
                'membership_category_id' => $producer->id,
                'fee_type' => 'standard',
            ],
            [
                'amount' => 10000,
                'status' => true,
            ]
        );
    }
}
