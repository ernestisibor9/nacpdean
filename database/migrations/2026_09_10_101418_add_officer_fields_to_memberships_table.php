<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->string('executive_id', 20)
                ->nullable()
                ->unique()
                ->after('membership_position');

            $table->string('taskforce_id', 50)
                ->nullable()
                ->index()
                ->after('executive_id');

            $table->string('taskforce_position', 200)
                ->nullable()
                ->after('taskforce_id');

            $table->enum('taskforce_level', [
                'national',
                'state',
            ])
                ->nullable()
                ->after('taskforce_position');

            $table->string('taskforce_state', 100)
                ->nullable()
                ->after('taskforce_level');
        });
    }

    public function down(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->dropUnique([
                'executive_id',
            ]);

            $table->dropIndex([
                'taskforce_id',
            ]);

            $table->dropColumn([
                'executive_id',
                'taskforce_id',
                'taskforce_position',
                'taskforce_level',
                'taskforce_state',
            ]);
        });
    }
};
