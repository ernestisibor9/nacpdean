<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('memberships', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | USER
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | MEMBER PROFILE
            |--------------------------------------------------------------------------
            */

            $table->foreignId('member_profile_id')
                ->constrained('member_profiles')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | MEMBERSHIP CATEGORY
            |--------------------------------------------------------------------------
            */

            $table->foreignId('membership_category_id')
                ->nullable()
                ->constrained('membership_categories')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | MEMBERSHIP NUMBER
            |--------------------------------------------------------------------------
            |
            | Generated ONLY when admin approves the application.
            |
            */

            $table->string('membership_number')->unique();

            /*
            |--------------------------------------------------------------------------
            | MEMBERSHIP STATUS
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'active',
                'expired',
                'suspended',
                'cancelled',
            ])->default('active');

            /*
            |--------------------------------------------------------------------------
            | ISSUE / EXPIRY
            |--------------------------------------------------------------------------
            */

            $table->date('issued_at')->nullable();

            $table->date('expires_at')->nullable();

            /*
            |--------------------------------------------------------------------------
            | APPROVAL INFORMATION
            |--------------------------------------------------------------------------
            */

            $table->timestamp('approved_at')->nullable();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | TIMESTAMPS
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | PREVENT DUPLICATE MEMBERSHIP FOR SAME PROFILE
            |--------------------------------------------------------------------------
            */

            $table->unique('member_profile_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
