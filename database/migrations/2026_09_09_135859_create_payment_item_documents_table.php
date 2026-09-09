<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_item_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payment_item_id')
                ->constrained('payment_items')
                ->cascadeOnDelete();

            $table->foreignId('document_id')
                ->constrained('documents')
                ->cascadeOnDelete();

            $table->boolean('is_primary')
                ->default(true)
                ->index();

            $table->boolean('generate_after_payment')
                ->default(true);

            $table->timestamps();

            $table->unique([
                'payment_item_id',
                'document_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_item_documents');
    }
};
