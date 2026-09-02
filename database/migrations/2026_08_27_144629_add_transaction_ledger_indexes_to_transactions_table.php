<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->index(
                ['user_id', 'type'],
                'transactions_user_type_index'
            );

            $table->index(
                ['user_id', 'status'],
                'transactions_user_status_index'
            );

            $table->index(
                ['debit_transaction_id'],
                'transactions_debit_transaction_id_index'
            );

            $table->index(
                ['user_id', 'payment_item_id'],
                'transactions_user_payment_item_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_user_type_index');
            $table->dropIndex('transactions_user_status_index');
            $table->dropIndex('transactions_debit_transaction_id_index');
            $table->dropIndex('transactions_user_payment_item_index');
        });
    }
};
