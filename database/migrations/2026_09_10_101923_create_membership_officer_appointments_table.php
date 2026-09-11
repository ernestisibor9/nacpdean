<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_officer_appointments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('membership_id')
                ->constrained('memberships')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Appointment Type
            |--------------------------------------------------------------------------
            |
            | A member can simultaneously be:
            | - National Executive
            | - Task Force Officer
            |
            | Therefore these are independent appointment records.
            |
            */
            $table->enum('appointment_type', [
                'national_executive',
                'task_force',
            ]);

            /*
            |--------------------------------------------------------------------------
            | National Executive
            |--------------------------------------------------------------------------
            */
            $table->string('executive_id', 20)
                ->nullable()
                ->index();

            /*
            |--------------------------------------------------------------------------
            | Task Force
            |--------------------------------------------------------------------------
            */
            $table->string('taskforce_id', 50)
                ->nullable()
                ->index();

            $table->string('position', 200)
                ->nullable();

            $table->enum('level', [
                'national',
                'state',
            ])->nullable();

            $table->string('state', 100)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Approval Workflow
            |--------------------------------------------------------------------------
            */
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
            ])->default('pending');

            $table->timestamp('appointed_at')
                ->nullable();

            $table->timestamp('approved_at')
                ->nullable();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('rejected_at')
                ->nullable();

            $table->foreignId('rejected_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('rejection_reason')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_officer_appointments');
    }
};
