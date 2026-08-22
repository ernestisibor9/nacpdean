<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_category_fees', function (Blueprint $table) {
            $table->id();

            $table->foreignId('membership_category_id')
                ->constrained('membership_categories')
                ->cascadeOnDelete();

            $table->string('fee_type');

            $table->decimal('amount', 12, 2);

            $table->boolean('status')->default(true);

            $table->timestamps();

            $table->unique([
                'membership_category_id',
                'fee_type',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_category_fees');
    }
};
