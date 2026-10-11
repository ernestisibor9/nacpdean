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
        Schema::create('state_executive_positions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();
            $table->string('position');
            $table->string('state', 100);
            $table->string('status', 20)->default('vacant');
            $table->timestamps();

            $table->index(['state', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('state_executive_positions');
    }
};
