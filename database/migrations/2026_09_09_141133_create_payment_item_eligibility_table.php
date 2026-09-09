<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_item_eligibility', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payment_item_id')
                ->constrained('payment_items')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | WHO IS THE PAYMENT FOR?
            |--------------------------------------------------------------------------
            */

            $table->enum('audience', [
                'member',
                'non_member',
                'restricted',
            ])->index();

            /*
            |--------------------------------------------------------------------------
            | WHERE CAN IT BE SEEN?
            |--------------------------------------------------------------------------
            */

            $table->enum('visibility', [
                'public',
                'member',
                'assigned',
                'admin',
            ])
                ->default('member')
                ->index();

            /*
            |--------------------------------------------------------------------------
            | MEMBER TYPE
            |--------------------------------------------------------------------------
            |
            | regular   = normal NACPDEAN member
            | affiliate = RCG / affiliate member
            |
            */

            $table->enum('member_type', [
                'regular',
                'affiliate',
            ])
                ->nullable()
                ->index();

            /*
            |--------------------------------------------------------------------------
            | OPERATIONAL CATEGORY
            |--------------------------------------------------------------------------
            */

            $table->enum('operational_category', [
                'exporter',
                'supplier',
                'dealer',
                'producer',
                'rcg',
            ])
                ->nullable()
                ->index();

            /*
            |--------------------------------------------------------------------------
            | REQUIREMENTS
            |--------------------------------------------------------------------------
            */

            $table->boolean('requires_membership')
                ->default(false);

            $table->boolean('requires_operational_right')
                ->default(false);

            $table->boolean('requires_violation')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_active')
                ->default(true)
                ->index();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | LOOKUP INDEX
            |--------------------------------------------------------------------------
            */

            // Custom short name to stay well within MySQL's 64-character limit
            $table->index(
                [
                    'payment_item_id',
                    'audience',
                    'member_type',
                    'operational_category',
                ],
                'pie_item_aud_type_cat_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_item_eligibility');
    }
};
