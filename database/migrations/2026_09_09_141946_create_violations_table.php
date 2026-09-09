<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('violations', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | PERSON / MEMBER
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('membership_id')
                ->nullable()
                ->constrained('memberships')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | PAYMENT ITEM
            |--------------------------------------------------------------------------
            |
            | This is the penalty/payment item assigned to the violation.
            |
            */

            $table->foreignId('payment_item_id')
                ->nullable()
                ->constrained('payment_items')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | VIOLATION
            |--------------------------------------------------------------------------
            */

            $table->string('violation_type')
                ->index();

            $table->enum('audience', [
                'member',
                'non_member',
            ])->index();

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
            | VEHICLE / TRANSPORT INFORMATION
            |--------------------------------------------------------------------------
            */

            $table->string('vehicle_type')
                ->nullable();

            $table->string('vehicle_registration')
                ->nullable();

            $table->string('driver_name')
                ->nullable();

            $table->string('driver_phone')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | DESCRIPTION
            |--------------------------------------------------------------------------
            */

            $table->text('description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | PENALTY AMOUNT
            |--------------------------------------------------------------------------
            */

            $table->decimal('amount', 12, 2)
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | PAYMENT / RESOLUTION STATUS
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'open',
                'payment_pending',
                'paid',
                'resolved',
                'cancelled',
            ])
                ->default('open')
                ->index();

            /*
            |--------------------------------------------------------------------------
            | ENFORCEMENT ACTIONS
            |--------------------------------------------------------------------------
            */

            $table->boolean('vehicle_confiscated')
                ->default(false);

            $table->boolean('warehouse_sealed')
                ->default(false);

            $table->boolean('blacklisted')
                ->default(false);

            $table->boolean('referred_to_police')
                ->default(false);

            $table->boolean('referred_to_nscdc')
                ->default(false);

            $table->boolean('referred_to_nis')
                ->default(false);

            $table->boolean('referred_to_foreign_affairs')
                ->default(false);

            $table->boolean('referred_to_embassy')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | STAFF WHO RECORDED / RESOLVED THE CASE
            |--------------------------------------------------------------------------
            */

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('resolved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('resolved_at')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | SEARCH INDEX
            |--------------------------------------------------------------------------
            */

            // Shortened custom index name (31 chars)
            $table->index(
                [
                    'violation_type',
                    'audience',
                    'operational_category',
                    'status',
                ],
                'violations_type_aud_cat_stat_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('violations');
    }
};
