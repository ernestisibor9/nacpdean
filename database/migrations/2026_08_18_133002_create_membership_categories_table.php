<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_categories', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->string('code')->unique();

            $table->enum('member_type', [
                'regular',
                'affiliate',
            ]);

            $table->text('description')->nullable();

            $table->decimal('fee', 12, 2)->default(0);

            $table->boolean('status')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_categories');
    }
};
