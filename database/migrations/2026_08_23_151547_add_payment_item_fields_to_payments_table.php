<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {

            if (!Schema::hasColumn('payments', 'payment_item_id')) {
                $table->unsignedBigInteger('payment_item_id')
                    ->nullable()
                    ->after('id');
            }

            if (!Schema::hasColumn('payments', 'description')) {
                $table->text('description')
                    ->nullable()
                    ->after('payment_type');
            }

            // Add these ONLY if they don't already exist.

            if (!Schema::hasColumn('payments', 'fee_type')) {
                $table->string('fee_type')
                    ->nullable()
                    ->after('payment_type');
            }

        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {

            if (Schema::hasColumn('payments', 'payment_item_id')) {
                $table->dropColumn('payment_item_id');
            }

            if (Schema::hasColumn('payments', 'description')) {
                $table->dropColumn('description');
            }

            if (Schema::hasColumn('payments', 'fee_type')) {
                $table->dropColumn('fee_type');
            }

        });
    }
};
