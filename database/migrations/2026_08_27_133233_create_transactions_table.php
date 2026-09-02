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
        Schema::create('transactions', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | PRIMARY KEY
            |--------------------------------------------------------------------------
            */

            $table->increments('id');


            /*
            |--------------------------------------------------------------------------
            | USER
            |--------------------------------------------------------------------------
            |
            | The member whose account this transaction belongs to.
            |
            */

            $table->unsignedBigInteger('user_id');

            $table->index('user_id');


            /*
            |--------------------------------------------------------------------------
            | PAYMENT ITEM
            |--------------------------------------------------------------------------
            |
            | Identifies the membership/payment item responsible for
            | this transaction.
            |
            */

            $table->unsignedBigInteger('payment_item_id');

            $table->index('payment_item_id');


            /*
            |--------------------------------------------------------------------------
            | NARRATION
            |--------------------------------------------------------------------------
            |
            | Description of the transaction.
            |
            | Examples:
            |
            | Membership Registration
            | Membership Renewal
            | ID Card
            | Certificate
            | Payment for Membership
            |
            */

            $table->text('narration');


            /*
            |--------------------------------------------------------------------------
            | TRANSACTION TYPE
            |--------------------------------------------------------------------------
            |
            | debit  = amount charged/owed
            | credit = amount paid/credited
            |
            */

            $table->enum(
                'type',
                [
                    'debit',
                    'credit',
                ]
            );


            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            |
            | Status is applicable to DEBIT transactions.
            |
            | Debit:
            |     not paid
            |     paid
            |
            | Credit:
            |     NULL
            |
            */

            $table->enum(
                'status',
                [
                    'not paid',
                    'paid',
                ]
            )->nullable();


            /*
            |--------------------------------------------------------------------------
            | AMOUNT
            |--------------------------------------------------------------------------
            */

            $table->decimal(
                'amount',
                10,
                2
            );


            /*
            |--------------------------------------------------------------------------
            | TRANSACTION ID
            |--------------------------------------------------------------------------
            |
            | Stores the related transaction/payment ID where applicable.
            |
            | Nullable because a debit can exist before payment is made.
            |
            */

            $table->unsignedInteger('transaction_id')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | TIMESTAMPS
            |--------------------------------------------------------------------------
            */

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
