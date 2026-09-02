<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | DOCUMENT INFORMATION
            |--------------------------------------------------------------------------
            */

            $table->string('name');

            $table->string('code')
                ->unique();

            $table->text('description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | DOCUMENT TYPE
            |--------------------------------------------------------------------------
            |
            | Examples:
            |
            | certificate
            | receipt
            | transit_pass
            | right_document
            | clearance
            |
            */

            $table->string('type')
                ->default('document');

            /*
            |--------------------------------------------------------------------------
            | TEMPLATE
            |--------------------------------------------------------------------------
            |
            | Example:
            |
            | charcoal_transit_pass
            | afforestation_payment_receipt
            |
            */

            $table->string('template')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | VALIDITY
            |--------------------------------------------------------------------------
            |
            | none
            | days
            | months
            | years
            | fixed_date
            |
            */

            $table->string('validity_type')
                ->default('none');

            $table->unsignedInteger('validity_value')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | DOCUMENT FORM
            |--------------------------------------------------------------------------
            */

            $table->boolean('requires_form')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
