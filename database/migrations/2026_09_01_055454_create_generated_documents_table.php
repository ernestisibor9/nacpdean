<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_documents', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('document_id')
                ->constrained('documents')
                ->cascadeOnDelete();

            $table->unsignedInteger('transaction_id')
                ->nullable();

            $table->string('document_number')
                ->unique();

            $table->string('tracking_code')
                ->unique();

            $table->date('issued_at')
                ->nullable();

            $table->date('expires_at')
                ->nullable();

            $table->enum('status', [
                'active',
                'expired',
                'revoked',
            ])->default('active');

            $table->json('field_values')
                ->nullable();

            $table->timestamps();

            $table->foreign('transaction_id')
                ->references('id')
                ->on('transactions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_documents');
    }
};
