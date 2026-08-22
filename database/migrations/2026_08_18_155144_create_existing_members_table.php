<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('existing_members', function (Blueprint $table) {

            $table->id();

            $table->string('membership_number')->unique();

            $table->string('full_name');

            $table->string('category')->default('Exporter');

            $table->string('state_code', 10)->nullable();

            $table->string('phone')->nullable();

            $table->string('email')->nullable();

            $table->boolean('active')->default(true);

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('existing_members');
    }
};
