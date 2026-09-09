<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_item_categories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payment_item_id')
                ->constrained('payment_items')
                ->cascadeOnDelete();

            $table->foreignId('membership_category_id')
                ->constrained('membership_categories')
                ->cascadeOnDelete();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Prevent Duplicate Relationships
            |--------------------------------------------------------------------------
            */

            // Custom short name to stay under MySQL's 64-character limit
            $table->unique(
                ['payment_item_id', 'membership_category_id'],
                'pic_payment_item_membership_cat_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_item_categories');
    }
};
