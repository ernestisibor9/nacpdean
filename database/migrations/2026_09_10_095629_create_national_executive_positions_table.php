<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('national_executive_positions', function (Blueprint $table) {
            $table->id();

            // NEM-01 ... NEM-39
            $table->string('code', 20)->unique();

            // Official National Executive position
            $table->string('position', 200);

            // Whether the position is currently occupied
            $table->enum('status', [
                'occupied',
                'vacant',
            ])->default('vacant');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('national_executive_positions');
    }
};
