<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | CRITICAL: penalties MUST be created before member_penalties.
        | Having them in the same migration guarantees that order.
        |--------------------------------------------------------------------------
        */

        Schema::create('penalties', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payment_item_id')
                ->nullable()
                ->constrained('payment_items')
                ->nullOnDelete();

            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->boolean('is_custom_amount')->default(false);
            $table->string('action');
            $table->boolean('status')->default(true);

            $table->timestamps();
        });

        Schema::create('member_penalties', function (Blueprint $table) {
            $table->id();

            $table->foreignId('penalty_id')
                ->constrained('penalties')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('non_member_name')->nullable();
            $table->string('non_member_phone')->nullable();

            $table->string('name');
            $table->decimal('amount', 12, 2);
            $table->string('action');

            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->timestamp('paid_at')->nullable();

            $table->text('notes')->nullable();

            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('debit_transaction_id')
                ->nullable()
                ->constrained('transactions')
                ->nullOnDelete();

            $table->foreignId('credit_transaction_id')
                ->nullable()
                ->constrained('transactions')
                ->nullOnDelete();

            $table->foreignId('generated_document_id')
                ->nullable()
                ->constrained('generated_documents')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_penalties');
        Schema::dropIfExists('penalties');
    }
};
