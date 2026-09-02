<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_fields', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | DOCUMENT
            |--------------------------------------------------------------------------
            */

            $table->foreignId('document_id')
                ->constrained('documents')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | FIELD KEY
            |--------------------------------------------------------------------------
            |
            | Internal name used by the application.
            |
            | Example:
            |
            | driver_name
            | membership_no
            | vehicle_number
            |
            */

            $table->string('field_key');

            /*
            |--------------------------------------------------------------------------
            | DISPLAY LABEL
            |--------------------------------------------------------------------------
            */

            $table->string('label');

            /*
            |--------------------------------------------------------------------------
            | FIELD TYPE
            |--------------------------------------------------------------------------
            |
            | text
            | textarea
            | number
            | date
            | time
            | select
            |
            */

            $table->string('field_type')
                ->default('text');

            /*
            |--------------------------------------------------------------------------
            | SECTION
            |--------------------------------------------------------------------------
            |
            | Example:
            |
            | Header
            | Seller Details
            | Movement Details
            | Buyer Details
            | Payment Details
            | Official Use
            |
            */

            $table->string('section')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | PLACEHOLDER
            |--------------------------------------------------------------------------
            */

            $table->string('placeholder')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | DEFAULT VALUE
            |--------------------------------------------------------------------------
            */

            $table->text('default_value')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | OPTIONS
            |--------------------------------------------------------------------------
            |
            | Used mainly for select fields.
            |
            | Example:
            |
            | [
            |     "Dealer",
            |     "Supplier",
            |     "Exporter"
            | ]
            |
            */

            $table->json('options')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | REQUIRED
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_required')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | SYSTEM FIELD
            |--------------------------------------------------------------------------
            |
            | true:
            | Automatically populated by the system.
            |
            | false:
            | Member enters the value.
            |
            */

            $table->boolean('is_system')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | SORT ORDER
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | PREVENT DUPLICATE FIELD KEYS
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'document_id',
                'field_key',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_fields');
    }
};
