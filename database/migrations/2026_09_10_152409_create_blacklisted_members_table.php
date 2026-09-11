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
        Schema::create('blacklisted_members', function (Blueprint $table) {
            $table->id();

            $table->string('member_name');
            $table->string('membership_number')->nullable();
            $table->string('company_name')->nullable();
            $table->string('state')->nullable();
            $table->string('photo')->nullable();

            $table->date('effective_date');

            $table->string('status')->default('blacklisted');

            $table->text('reason')->nullable();

            $table->date('blacklisted_until')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blacklisted_members');
    }
};
