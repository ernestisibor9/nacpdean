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
        Schema::create('member_categories', function (Blueprint $table) {
            $table->id();

            $table->string('name',100);

            $table->string('code',20)->unique();

            $table->decimal('registration_fee',12,2)->default(0);

            $table->decimal('annual_due',12,2)->default(0);

            $table->text('description')->nullable();

            $table->unsignedInteger('display_order')->default(1);

            $table->boolean('status')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_categories');
    }
};
