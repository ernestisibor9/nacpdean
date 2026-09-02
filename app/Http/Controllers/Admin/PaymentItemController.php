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

        return view(
            'admin.payment_items.create',
            compact(
                'documents',
                'membershipCategories'
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
                'exists:membership_categories,id',
            ],

            'document_id' => [
                'nullable',
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
        ]);

        /*
        |--------------------------------------------------------------------------
        | CREATE
        |--------------------------------------------------------------------------
        */
        $data['is_active'] = $request->boolean('is_active');
        $data['is_renewable'] = $request->boolean('is_renewable');

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
            'is_active',
            true
        )
            ->orderBy('name')
            ->get();

        return view(
            'admin.payment_items.edit',
            compact(
                'paymentItem',
                'documents',
                'membershipCategories'
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
        ]);


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
            $validated['membership_category_id'] ?? null,

            'document_id' =>
            $validated['document_id'] ?? null,

            'is_active' =>
            $request->boolean('is_active'),

            'is_renewable' =>
            $request->boolean('is_renewable'),
        ]);


        return redirect()
            ->route('payment-items.index')
            ->with(
                'success',
                'Payment item updated successfully.'
            );
    }
}
