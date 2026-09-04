<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\MembershipCategory;
use App\Models\PaymentItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentItemController extends Controller
{
    /**
     * Display payment items.
     */
    public function index()
    {
        $paymentItems = PaymentItem::with([
            'membershipCategory',
            'document',
            'renewalPaymentItem',
        ])
            ->latest('id')
            ->paginate(20);

        return view(
            'admin.payment_items.index',
            compact('paymentItems')
        );
    }


    /**
     * Show create form.
     */
    public function create()
    {
        $documents = Document::where('is_active', true)
            ->orderBy('name')
            ->get();

        $membershipCategories = MembershipCategory::where(
            'status',
            true
        )
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Renewal Payment Items
        |--------------------------------------------------------------------------
        |
        | These are the payment items that can be selected as the
        | renewal item for another payment item.
        |
        */
        $renewalPaymentItems = PaymentItem::where(
            'is_active',
            true
        )
            ->orderBy('name')
            ->get();

        return view(
            'admin.payment_items.create',
            compact(
                'documents',
                'membershipCategories',
                'renewalPaymentItems'
            )
        );
    }


    /**
     * Store payment item.
     */
    public function store(Request $request)
    {
        $data = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'code' => [
                'required',
                'string',
                'max:100',
                'unique:payment_items,code',
            ],

            'type' => [
                'required',
                'string',
                'max:100',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'membership_category_id' => [
                'nullable',
                'integer',
                'exists:membership_categories,id',
            ],

            'document_id' => [
                'nullable',
                'integer',
                'exists:documents,id',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

            'is_renewable' => [
                'nullable',
                'boolean',
            ],

            'renewal_payment_item_id' => [
                'nullable',
                'integer',
                'exists:payment_items,id',
            ],
        ]);



        /*
        |--------------------------------------------------------------------------
        | CREATE
        |--------------------------------------------------------------------------
        */

        $data['is_active'] =
            $request->boolean('is_active');

        $data['is_renewable'] =
            $request->boolean('is_renewable');


        /*
        |--------------------------------------------------------------------------
        | RENEWAL CONFIGURATION
        |--------------------------------------------------------------------------
        |
        | If the payment item is not renewable, there should be no
        | renewal payment item attached to it.
        |
        */

        if (!$data['is_renewable']) {

            $data['renewal_payment_item_id'] = null;
        }


        PaymentItem::create($data);


        return redirect()
            ->route('admin.payment-items.index')
            ->with(
                'success',
                'Payment item created successfully.'
            );
    }


    /**
     * Show edit form.
     */
    public function edit(PaymentItem $paymentItem)
    {
        $documents = Document::where('is_active', true)
            ->orderBy('name')
            ->get();

        $membershipCategories = MembershipCategory::where(
            'status',
            true
        )
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Renewal Payment Items
        |--------------------------------------------------------------------------
        |
        | Exclude the current payment item so an item cannot be configured
        | to renew itself.
        |
        */

        $renewalPaymentItems = PaymentItem::where(
            'is_active',
            true
        )
            ->where(
                'id',
                '!=',
                $paymentItem->id
            )
            ->orderBy('name')
            ->get();


        return view(
            'admin.payment_items.edit',
            compact(
                'paymentItem',
                'documents',
                'membershipCategories',
                'renewalPaymentItems'
            )
        );
    }


    /**
     * Update payment item.
     */
    public function update(
        Request $request,
        PaymentItem $paymentItem
    ) {

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique(
                    'payment_items',
                    'code'
                )->ignore($paymentItem->id),
            ],

            'type' => [
                'required',
                'string',
                'max:100',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'membership_category_id' => [
                'nullable',
                'integer',
                'exists:membership_categories,id',
            ],

            'document_id' => [
                'nullable',
                'integer',
                'exists:documents,id',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

            'is_renewable' => [
                'nullable',
                'boolean',
            ],

            'renewal_payment_item_id' => [
                'nullable',
                'integer',
                'exists:payment_items,id',
                Rule::notIn([
                    $paymentItem->id,
                ]),
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | UPDATE FLAGS
        |--------------------------------------------------------------------------
        */

        $isActive =
            $request->boolean('is_active');

        $isRenewable =
            $request->boolean('is_renewable');


        /*
        |--------------------------------------------------------------------------
        | RENEWAL CONFIGURATION
        |--------------------------------------------------------------------------
        |
        | A non-renewable payment item must not have a renewal item.
        |
        */

        $renewalPaymentItemId =
            $validated['renewal_payment_item_id']
            ?? null;


        if (!$isRenewable) {

            $renewalPaymentItemId = null;
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        */

        $paymentItem->update([

            'name' =>
                $validated['name'],

            'code' =>
                $validated['code'],

            'type' =>
                $validated['type'],

            'amount' =>
                $validated['amount'],

            'membership_category_id' =>
                $validated['membership_category_id']
                ?? null,

            'document_id' =>
                $validated['document_id']
                ?? null,

            'is_active' =>
                $isActive,

            'is_renewable' =>
                $isRenewable,

            'renewal_payment_item_id' =>
                $renewalPaymentItemId,
        ]);


        return redirect()
            ->route('admin.payment-items.index')
            ->with(
                'success',
                'Payment item updated successfully.'
            );
    }
}
