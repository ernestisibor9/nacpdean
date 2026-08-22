<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_cards', function (Blueprint $table) {

            $table->id();


            /*
            |--------------------------------------------------------------------------
            | MEMBERSHIP
            |--------------------------------------------------------------------------
            */

            $table->foreignId('membership_id')
                ->constrained('memberships')
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
            | CARD NUMBER
            |--------------------------------------------------------------------------
            */

            $table->string('card_number')
                ->unique();


            /*
            |--------------------------------------------------------------------------
            | MEMBERSHIP NUMBER
            |--------------------------------------------------------------------------
            */

            $table->string('membership_number');


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
            | ISSUE / EXPIRY
            |--------------------------------------------------------------------------
            */

            $table->date('issued_at')
                ->nullable();

            $table->date('expires_at')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | QR TOKEN
            |--------------------------------------------------------------------------
            */

            $table->string('qr_token')
                ->unique();


            /*
            |--------------------------------------------------------------------------
            | CARD STATUS
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'active',
                'expired',
                'revoked',
                'replaced',
            ])->default('active');


            /*
            |--------------------------------------------------------------------------
            | REPLACED CARD
            |--------------------------------------------------------------------------
            */

            $table->foreignId('replaced_card_id')
                ->nullable()
                ->constrained('membership_cards')
                ->nullOnDelete();


            /*
            |--------------------------------------------------------------------------
            | GENERATED
            |--------------------------------------------------------------------------
            */

            $table->timestamp('generated_at')
                ->nullable();


            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | PREVENT DUPLICATE CARD FOR MEMBERSHIP
            |--------------------------------------------------------------------------
            */

            $table->unique('membership_id');

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('membership_cards');
    }
};
