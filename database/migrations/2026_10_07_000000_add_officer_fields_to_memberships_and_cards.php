<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            if (!Schema::hasColumn('memberships', 'parent_membership_id')) {
                $table->unsignedBigInteger('parent_membership_id')
                    ->nullable()
                    ->after('membership_category_id');
            }

            if (!Schema::hasColumn('memberships', 'officer_type')) {
                $table->string('officer_type', 40)
                    ->nullable()
                    ->after('parent_membership_id');
            }

            if (!Schema::hasColumn('memberships', 'appointment_id')) {
                $table->unsignedBigInteger('appointment_id')
                    ->nullable()
                    ->after('officer_type');
            }

            if (!Schema::hasColumn('memberships', 'officer_code')) {
                $table->string('officer_code', 40)
                    ->nullable()
                    ->after('appointment_id');
            }
        });

        Schema::table('membership_cards', function (Blueprint $table) {
            if (!Schema::hasColumn('membership_cards', 'appointment_id')) {
                $table->unsignedBigInteger('appointment_id')
                    ->nullable()
                    ->after('membership_category_id');

                $table->index('appointment_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->dropColumn([
                'parent_membership_id',
                'officer_type',
                'appointment_id',
                'officer_code',
            ]);
        });

        Schema::table('membership_cards', function (Blueprint $table) {
            $table->dropColumn('appointment_id');
        });
    }
};
