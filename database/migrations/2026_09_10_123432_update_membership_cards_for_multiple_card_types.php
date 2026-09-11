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
        | REMOVE UNIQUE MEMBERSHIP ID
        |--------------------------------------------------------------------------
        |
        | A membership can now have multiple cards:
        |
        | membership
        | national_executive
        | task_force
        |
        */

        Schema::table('membership_cards', function (Blueprint $table) {

            $table->dropForeign([
                'membership_id',
            ]);

            $table->dropUnique([
                'membership_id',
            ]);

            $table->enum('card_type', [
                'membership',
                'national_executive',
                'task_force',
            ])
                ->default('membership')
                ->after('membership_id');

            $table->index([
                'membership_id',
                'card_type',
            ]);

            /*
            |--------------------------------------------------------------------------
            | RESTORE FOREIGN KEY
            |--------------------------------------------------------------------------
            */

            $table->foreign('membership_id')
                ->references('id')
                ->on('memberships')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('membership_cards', function (Blueprint $table) {

            $table->dropForeign([
                'membership_id',
            ]);

            $table->dropIndex([
                'membership_id',
                'card_type',
            ]);

            $table->dropColumn('card_type');

            $table->unique('membership_id');

            $table->foreign('membership_id')
                ->references('id')
                ->on('memberships')
                ->cascadeOnDelete();
        });
    }
};
