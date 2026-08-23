<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_items', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | BASIC INFORMATION
            |--------------------------------------------------------------------------
            */

            $table->string('name');

            $table->string('code')
                ->unique();

            $table->text('description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | PAYMENT TYPE
            |--------------------------------------------------------------------------
            |
            | certificate
            | penalty
            | renewal
            | afforestation
            | other
            |
            */

            $table->string('type')
                ->default('other');

            /*
            |--------------------------------------------------------------------------
            | PRICE
            |--------------------------------------------------------------------------
            */

            $table->decimal('amount', 12, 2);

            /*
            |--------------------------------------------------------------------------
            | MEMBERSHIP CATEGORY
            |--------------------------------------------------------------------------
            |
            | NULL = available to all approved members.
            |
            */

            $table->foreignId('membership_category_id')
                ->nullable()
                ->constrained('membership_categories')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | ACTIVE / INACTIVE
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | INDEXES
            |--------------------------------------------------------------------------
            */

            $table->index([
                'type',
                'is_active',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_items');
    }
};
