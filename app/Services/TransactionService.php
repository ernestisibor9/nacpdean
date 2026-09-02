<?php

namespace App\Services;

use App\Models\Membership;
use App\Models\MembershipCategory;
use App\Models\MembershipCategoryFee;
use App\Models\MemberFee;
use App\Models\PaymentItem;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class TransactionService
{
    /*
    |--------------------------------------------------------------------------
    | CREATE DEBIT
    |--------------------------------------------------------------------------
    |
    | Central method for creating anything a member owes.
    |
    | Every debit starts as:
    |
    | type   = debit
    | status = not paid
    |
    */

    public function createDebit(
        int $userId,
        float $amount,
        string $narration,
        ?int $paymentItemId = null
    ): Transaction {

        if ($amount <= 0) {

            throw new RuntimeException(
                'Transaction amount must be greater than zero.'
            );
        }

        return Transaction::create([

            'user_id' => $userId,

            'payment_item_id' => $paymentItemId,

            'debit_transaction_id' => null,

            'narration' => $narration,

            'type' => 'debit',

            'status' => 'not paid',

            'amount' => $amount,

            'transaction_id' => null,

            'gateway' => null,

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | FIND OR CREATE DEBIT
    |--------------------------------------------------------------------------
    |
    | Prevents duplicate unpaid debits for the same obligation.
    |
    */

    public function findOrCreateDebit(
        int $userId,
        float $amount,
        string $narration,
        ?int $paymentItemId = null
    ): Transaction {

        $existing = Transaction::where(
            'user_id',
            $userId
        )
            ->where(
                'type',
                'debit'
            )
            ->where(
                'status',
                'not paid'
            )
            ->where(
                'amount',
                $amount
            )
            ->where(
                'narration',
                $narration
            )
            ->where(
                function ($query) use ($paymentItemId) {

                    if ($paymentItemId === null) {

                        $query->whereNull(
                            'payment_item_id'
                        );

                    } else {

                        $query->where(
                            'payment_item_id',
                            $paymentItemId
                        );
                    }
                }
            )
            ->latest()
            ->first();


        if ($existing) {

            return $existing;
        }


        return $this->createDebit(
            $userId,
            $amount,
            $narration,
            $paymentItemId
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE MEMBERSHIP DEBIT
    |--------------------------------------------------------------------------
    |
    | Creates the membership amount the member owes.
    |
    | For new members:
    |
    | fee_type = new
    |
    | For renewal:
    |
    | fee_type = existing
    |
    */

    public function createMembershipDebit(
        int $userId,
        int $membershipCategoryId,
        bool $renewal = false
    ): Transaction {

        return DB::transaction(function () use (
            $userId,
            $membershipCategoryId,
            $renewal
        ) {

            /*
            |--------------------------------------------------------------------------
            | FIND CATEGORY
            |--------------------------------------------------------------------------
            */

            $category =
                MembershipCategory::where(
                    'id',
                    $membershipCategoryId
                )
                ->where(
                    'status',
                    true
                )
                ->first();


            if (!$category) {

                throw new RuntimeException(
                    'Membership category could not be found.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | DETERMINE FEE TYPE
            |--------------------------------------------------------------------------
            */

            $feeType = $renewal
                ? 'existing'
                : 'new';


            /*
            |--------------------------------------------------------------------------
            | FIND APPLICABLE CATEGORY FEE
            |--------------------------------------------------------------------------
            */

            $categoryFee =
                MembershipCategoryFee::where(
                    'membership_category_id',
                    $category->id
                )
                ->where(
                    'status',
                    true
                )
                ->where(
                    'fee_type',
                    $feeType
                )
                ->first();


            /*
            |--------------------------------------------------------------------------
            | STANDARD FALLBACK
            |--------------------------------------------------------------------------
            |
            | Used only for new membership when no "new" fee exists.
            |
            */

            if (
                !$categoryFee &&
                !$renewal
            ) {

                $categoryFee =
                    MembershipCategoryFee::where(
                        'membership_category_id',
                        $category->id
                    )
                    ->where(
                        'status',
                        true
                    )
                    ->where(
                        'fee_type',
                        'standard'
                    )
                    ->first();
            }


            if (!$categoryFee) {

                throw new RuntimeException(
                    'Membership fee could not be found for the selected category.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | VALIDATE FEE AMOUNT
            |--------------------------------------------------------------------------
            */

            $amount =
                (float) $categoryFee->amount;


            if ($amount <= 0) {

                throw new RuntimeException(
                    'Membership fee amount is invalid.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | MEMBERSHIP PAYMENT ITEM
            |--------------------------------------------------------------------------
            |
            | Membership payment does not require a payment item.
            |
            | Your payment_items table currently contains:
            |
            | CLR
            | CLR-RCG
            | CDR-SLR
            | CDR-DEA
            | CPR
            | PENALTY
            | AFFORESTATION
            |
            | There is no registration item.
            |
            | Therefore payment_item_id can remain NULL for
            | membership transactions.
            |
            */

            $paymentItem = null;


            /*
            |--------------------------------------------------------------------------
            | NARRATION
            |--------------------------------------------------------------------------
            */

            $narration = $renewal

                ? 'Membership renewal - ' .
                    $category->name

                : 'Membership payment - ' .
                    $category->name;


            /*
            |--------------------------------------------------------------------------
            | DUPLICATE PROTECTION
            |--------------------------------------------------------------------------
            |
            | Never create another unpaid membership debit for
            | the same user, category and obligation.
            |
            */

            $existing =
                Transaction::where(
                    'user_id',
                    $userId
                )
                ->where(
                    'type',
                    'debit'
                )
                ->where(
                    'status',
                    'not paid'
                )
                ->where(
                    'amount',
                    $amount
                )
                ->where(
                    'narration',
                    $narration
                )
                ->first();


            if ($existing) {

                return $existing;
            }


            /*
            |--------------------------------------------------------------------------
            | CREATE MEMBERSHIP DEBIT
            |--------------------------------------------------------------------------
            */

            return $this->createDebit(
                $userId,
                $amount,
                $narration,
                $paymentItem?->id
            );
        });
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE MEMBER FEE DEBIT
    |--------------------------------------------------------------------------
    */

    public function createMemberFeeDebit(
        MemberFee $memberFee
    ): Transaction {

        /*
        |--------------------------------------------------------------------------
        | VERIFY MEMBER FEE
        |--------------------------------------------------------------------------
        */

        if (
            $memberFee->status !== 'unpaid'
        ) {

            throw new RuntimeException(
                'This member fee has already been paid.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | NARRATION
        |--------------------------------------------------------------------------
        */

        $narration =
            'Member fee - ' .
            $memberFee->name;


        /*
        |--------------------------------------------------------------------------
        | DUPLICATE PROTECTION
        |--------------------------------------------------------------------------
        */

        $existing =
            Transaction::where(
                'user_id',
                $memberFee->user_id
            )
            ->where(
                'type',
                'debit'
            )
            ->where(
                'status',
                'not paid'
            )
            ->where(
                'amount',
                $memberFee->amount
            )
            ->where(
                'narration',
                $narration
            )
            ->first();


        if ($existing) {

            return $existing;
        }


        /*
        |--------------------------------------------------------------------------
        | CREATE DEBIT
        |--------------------------------------------------------------------------
        */

        return $this->createDebit(
            $memberFee->user_id,
            (float) $memberFee->amount,
            $narration
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE ADDITIONAL PAYMENT DEBIT
    |--------------------------------------------------------------------------
    |
    | Used for items such as:
    |
    | CLR
    | CLR-RCG
    | CDR-SLR
    | CDR-DEA
    | CPR
    | PENALTY
    | AFFORESTATION
    |
    */

    public function createAdditionalPaymentDebit(
        int $userId,
        PaymentItem $paymentItem
    ): Transaction {

        /*
        |--------------------------------------------------------------------------
        | VERIFY PAYMENT ITEM
        |--------------------------------------------------------------------------
        */

        if (!$paymentItem->is_active) {

            throw new RuntimeException(
                'This payment item is not available.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | GET DATABASE AMOUNT
        |--------------------------------------------------------------------------
        |
        | Never trust the frontend amount.
        |
        */

        $amount =
            (float) $paymentItem->amount;


        if ($amount <= 0) {

            throw new RuntimeException(
                'Payment item amount is invalid.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | NARRATION
        |--------------------------------------------------------------------------
        */

        $narration =
            $paymentItem->name;


        /*
        |--------------------------------------------------------------------------
        | DUPLICATE UNPAID PAYMENT
        |--------------------------------------------------------------------------
        */

        $existing =
            Transaction::where(
                'user_id',
                $userId
            )
            ->where(
                'payment_item_id',
                $paymentItem->id
            )
            ->where(
                'type',
                'debit'
            )
            ->where(
                'status',
                'not paid'
            )
            ->first();


        if ($existing) {

            return $existing;
        }


        /*
        |--------------------------------------------------------------------------
        | CREATE DEBIT
        |--------------------------------------------------------------------------
        */

        return $this->createDebit(
            $userId,
            $amount,
            $narration,
            $paymentItem->id
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CHECK MEMBERSHIP EXPIRY
    |--------------------------------------------------------------------------
    |
    | Renewal is NOT created while the membership is still active.
    |
    */

    public function createRenewalDebitIfExpired(
        int $userId
    ): ?Transaction {

        return DB::transaction(function () use ($userId) {

            /*
            |--------------------------------------------------------------------------
            | GET LATEST MEMBERSHIP
            |--------------------------------------------------------------------------
            */

            $membership =
                Membership::where(
                    'user_id',
                    $userId
                )
                ->latest()
                ->lockForUpdate()
                ->first();


            if (!$membership) {

                return null;
            }


            /*
            |--------------------------------------------------------------------------
            | MEMBERSHIP STILL VALID
            |--------------------------------------------------------------------------
            */

            if (
                $membership->expires_at &&
                now()->lt(
                    $membership->expires_at
                )
            ) {

                return null;
            }


            /*
            |--------------------------------------------------------------------------
            | GET CATEGORY
            |--------------------------------------------------------------------------
            */

            $categoryId =
                $membership->membership_category_id;


            if (!$categoryId) {

                return null;
            }


            /*
            |--------------------------------------------------------------------------
            | CREATE RENEWAL DEBIT
            |--------------------------------------------------------------------------
            */

            return $this->createMembershipDebit(
                $userId,
                $categoryId,
                true
            );
        });
    }
}
