<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {

            $table->foreignId('member_fee_id')
                ->nullable()
                ->after('membership_category_fee_id')
                ->constrained('member_fees')
                ->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {

            $table->dropForeign([
                'member_fee_id'
            ]);

            $table->dropColumn('member_fee_id');

        });
    }
};
