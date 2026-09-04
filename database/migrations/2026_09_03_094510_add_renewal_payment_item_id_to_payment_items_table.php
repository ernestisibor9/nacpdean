<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_items', function (Blueprint $table) {
            $table->foreignId('renewal_payment_item_id')
                ->nullable()
                ->after('is_renewable')
                ->constrained('payment_items')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payment_items', function (Blueprint $table) {
            $table->dropForeign(['renewal_payment_item_id']);
            $table->dropColumn('renewal_payment_item_id');
        });
    }
};
