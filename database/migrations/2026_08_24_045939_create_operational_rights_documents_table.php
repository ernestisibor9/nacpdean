<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operational_rights_documents', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | OWNER
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();


            /*
            |--------------------------------------------------------------------------
            | MEMBERSHIP
            |--------------------------------------------------------------------------
            */

            $table->foreignId('membership_id')
                ->constrained('memberships')
                ->cascadeOnDelete();


            $table->foreignId('member_profile_id')
                ->constrained('member_profiles')
                ->cascadeOnDelete();


            $table->foreignId('membership_category_id')
                ->constrained('membership_categories')
                ->restrictOnDelete();


            /*
            |--------------------------------------------------------------------------
            | PAYMENT
            |--------------------------------------------------------------------------
            */

            $table->foreignId('payment_id')
                ->nullable()
                ->constrained('payments')
                ->nullOnDelete();


            /*
            |--------------------------------------------------------------------------
            | DOCUMENT IDENTIFICATION
            |--------------------------------------------------------------------------
            */

            $table->string('document_number')
                ->unique();

            $table->string('reference_number')
                ->unique();

            $table->string('authentication_code')
                ->unique();


            /*
            |--------------------------------------------------------------------------
            | DOCUMENT TYPE
            |--------------------------------------------------------------------------
            */

            $table->string('document_type');

            $table->string('document_title');


            /*
            |--------------------------------------------------------------------------
            | DATES
            |--------------------------------------------------------------------------
            */

            $table->date('issued_at');

            $table->date('expires_at');


            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'active',
                'expired',
                'suspended',
                'revoked',
            ])->default('active');


            /*
            |--------------------------------------------------------------------------
            | QR
            |--------------------------------------------------------------------------
            */

            $table->string('qr_token')
                ->unique();


            /*
            |--------------------------------------------------------------------------
            | PDF
            |--------------------------------------------------------------------------
            */

            $table->string('pdf_path')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | VERIFICATION
            |--------------------------------------------------------------------------
            */

            $table->timestamp('generated_at')
                ->nullable();

            $table->timestamp('revoked_at')
                ->nullable();

            $table->text('revocation_reason')
                ->nullable();


            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | INDEXES
            |--------------------------------------------------------------------------
            */

            $table->index('document_type');

            $table->index('status');

            $table->index('issued_at');

            $table->index('expires_at');

        });
    }


    public function down(): void
    {
        Schema::dropIfExists(
            'operational_rights_documents'
        );
    }
};
