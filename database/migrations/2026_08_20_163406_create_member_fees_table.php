<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_fees', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('fee_type');

            $table->string('title');

            $table->text('description')->nullable();

            $table->decimal('amount', 12, 2);

            $table->string('reference')->nullable();

            $table->enum('status', [
                'unpaid',
                'paid',
                'cancelled'
            ])->default('unpaid');

            $table->date('due_date')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_fees');
    }
};
