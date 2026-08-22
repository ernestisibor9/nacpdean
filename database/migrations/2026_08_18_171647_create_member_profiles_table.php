<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_profiles', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | USER
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();


            /*
            |--------------------------------------------------------------------------
            | PERSONAL INFORMATION
            |--------------------------------------------------------------------------
            */

            $table->string('surname')->nullable();

            $table->string('first_name')->nullable();

            $table->string('middle_name')->nullable();

            $table->string('phone')->nullable();

            $table->date('date_of_birth')->nullable();

            $table->string('gender')->nullable();

            $table->string('nationality')->nullable();


            /*
            |--------------------------------------------------------------------------
            | ADDRESS
            |--------------------------------------------------------------------------
            */

            $table->text('address')->nullable();

            $table->string('city')->nullable();

            $table->string('state')->nullable();

            $table->string('lga')->nullable();


            /*
            |--------------------------------------------------------------------------
            | BUSINESS INFORMATION
            |--------------------------------------------------------------------------
            */

            $table->string('business_name')->nullable();

            $table->string('business_registration_number')->nullable();

            $table->string('business_type')->nullable();

            $table->text('business_address')->nullable();


            /*
            |--------------------------------------------------------------------------
            | APPLICATION
            |--------------------------------------------------------------------------
            */

            $table->string('membership_number')->nullable();

            $table->enum('status', [
                'draft',
                'submitted',
                'approved',
                'rejected'
            ])->default('draft');


            /*
            |--------------------------------------------------------------------------
            | ADMIN REVIEW
            |--------------------------------------------------------------------------
            */

            $table->text('admin_comment')->nullable();

            $table->timestamp('submitted_at')->nullable();

            $table->timestamp('approved_at')->nullable();


            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_profiles');
    }
};
