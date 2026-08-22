<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | User
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Membership Category
            |--------------------------------------------------------------------------
            */

            $table->foreignId('membership_category_id')
                ->constrained('membership_categories')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Fee Configuration
            |--------------------------------------------------------------------------
            */

            $table->foreignId('membership_category_fee_id')
                ->constrained('membership_category_fees')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Payment Information
            |--------------------------------------------------------------------------
            */

            $table->string('payment_type')->default('membership');

            $table->string('fee_type');

            /*
            |--------------------------------------------------------------------------
            | IMPORTANT:
            | This is the actual amount charged/paid.
            | It must remain unchanged even if the fee configuration changes later.
            |--------------------------------------------------------------------------
            */

            $table->decimal('amount', 12, 2);

            /*
            |--------------------------------------------------------------------------
            | Paystack
            |--------------------------------------------------------------------------
            */

            $table->string('reference')->unique();

            $table->string('gateway')->default('paystack');

            $table->string('gateway_transaction_id')->nullable();

            $table->string('gateway_status')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Payment Status
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'pending',
                'successful',
                'failed',
                'abandoned',
            ])->default('pending');

            /*
            |--------------------------------------------------------------------------
            | Gateway Data
            |--------------------------------------------------------------------------
            */

            $table->json('gateway_response')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Dates
            |--------------------------------------------------------------------------
            */

            $table->timestamp('paid_at')->nullable();

            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index([
                'user_id',
                'status',
            ]);

            $table->index([
                'membership_category_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
