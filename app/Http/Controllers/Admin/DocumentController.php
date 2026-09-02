<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    /**
     * Display all documents.
     */
    public function index()
    {
        $documents = Document::query()
            ->withCount('fields')
            ->latest('id')
            ->paginate(20);

        return view(
            'admin.documents.index',
            compact('documents')
        );
    }


    /**
     * Show create document form.
     */
    public function create()
    {
        return view(
            'admin.documents.create'
        );
    }


    /**
     * Store a new document.
     */
    public function store(Request $request)
    {
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
                'alpha_dash',
                'unique:documents,code',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'validity_type' => [
                'required',
                Rule::in([
                    'none',
                    'fixed_date',
                    'days',
                    'months',
                    'years',
                    'year_end',
                ]),
            ],

            'validity_value' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'validity_date' => [
                'nullable',
                'date',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Normalize Code
        |--------------------------------------------------------------------------
        */

        $validated['code'] = strtoupper(
            Str::slug(
                $validated['code'],
                '_'
            )
        );


        /*
        |--------------------------------------------------------------------------
        | Validity Protection
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $validated['validity_type'],
                ['days', 'months', 'years']
            )
        ) {

            if (
                empty($validated['validity_value'])
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'validity_value' =>
                        'Validity value is required for this validity type.',
                    ]);
            }
        }


        if (
            $validated['validity_type'] === 'fixed_date'
            &&
            empty($validated['validity_date'])
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'validity_date' =>
                    'Validity date is required when using a fixed date.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Create Document
        |--------------------------------------------------------------------------
        */

        $document = Document::create([

            'name' =>
            $validated['name'],

            'code' =>
            $validated['code'],

            'description' =>
            $validated['description'] ?? null,

            'validity_type' =>
            $validated['validity_type'],

            'validity_value' =>
            $validated['validity_value'] ?? null,

            'validity_date' =>
            $validated['validity_date'] ?? null,

            'is_active' =>
            $request->boolean('is_active'),
        ]);


        return redirect()
            ->route(
                'admin.documents.edit',
                $document
            )
            ->with(
                'success',
                'Document created successfully. You can now configure its fields.'
            );
    }


    /**
     * Show edit document form.
     */
    public function edit(Document $document)
    {
        $document->loadCount('fields');

        return view(
            'admin.documents.edit',
            compact('document')
        );
    }


    /**
     * Update document.
     */
    public function update(
        Request $request,
        Document $document
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
                'alpha_dash',
                Rule::unique(
                    'documents',
                    'code'
                )->ignore($document->id),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'validity_type' => [
                'required',
                Rule::in([
                    'none',
                    'fixed_date',
                    'days',
                    'months',
                    'years',
                    'year_end',
                ]),
            ],

            'validity_value' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'validity_date' => [
                'nullable',
                'date',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Normalize Code
        |--------------------------------------------------------------------------
        */

        $validated['code'] = strtoupper(
            Str::slug(
                $validated['code'],
                '_'
            )
        );


        /*
        |--------------------------------------------------------------------------
        | Validity Protection
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $validated['validity_type'],
                ['days', 'months', 'years']
            )
            &&
            empty($validated['validity_value'])
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'validity_value' =>
                    'Validity value is required for this validity type.',
                ]);
        }


        if (
            $validated['validity_type'] === 'fixed_date'
            &&
            empty($validated['validity_date'])
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'validity_date' =>
                    'Validity date is required when using a fixed date.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        $document->update([

            'name' =>
            $validated['name'],

            'code' =>
            $validated['code'],

            'description' =>
            $validated['description'] ?? null,

            'validity_type' =>
            $validated['validity_type'],

            'validity_value' =>
            $validated['validity_value'] ?? null,

            'validity_date' =>
            $validated['validity_date'] ?? null,

            'is_active' =>
            $request->boolean('is_active'),
        ]);


        return redirect()
            ->route(
                'admin.documents.edit',
                $document
            )
            ->with(
                'success',
                'Document updated successfully.'
            );
    }


    /**
     * Delete document.
     *
     * We protect documents that are already being used
     * by payment items or generated documents.
     */
    public function destroy(Document $document)
    {
        if (
            $document->paymentItems()->exists()
        ) {

            return back()->with(
                'error',
                'This document cannot be deleted because it is linked to one or more payment items.'
            );
        }


        if (
            $document->generatedDocuments()->exists()
        ) {

            return back()->with(
                'error',
                'This document cannot be deleted because generated documents already exist for it.'
            );
        }


        $document->fields()->delete();

        $document->delete();


        return redirect()
            ->route(
                'admin.documents.index'
            )
            ->with(
                'success',
                'Document deleted successfully.'
            );
    }

    public function preview(Document $document)
    {
        $document->load([
            'fields' => function ($query) {
                $query->orderBy('sort_order')
                    ->orderBy('id');
            },
            'paymentItems',
        ]);

        $systemFieldKeys = [
            'receipt_no',
            'document_no',
            'document_number',
            'tracking_code',
            'issued_at',
            'issue_date',
            'document_name',
            'document_code',
            'document_id',

            'member_name',
            'membership_no',
            'membership_number',
            'phone',
            'email',
            'surname',
            'first_name',
            'middle_name',
            'date_of_birth',
            'gender',
            'nationality',
            'address',
            'city',
            'state',
            'lga',
            'business_name',
            'business_registration_number',
            'business_type',
            'business_address',

            'lifting_right_no',
            'lifting_right_number',

            'payment_reference',
            'reference',
            'payment_amount',
            'amount',
            'payment_date',

            'transaction_id',
            'transaction_date',

            'seller_member_name',
            'seller_membership_no',
            'seller_membership_number',
            'seller_phone',
            'seller_dealing_right_no',
            'seller_dealing_right_number',
        ];

        $fields = $document->fields->map(function ($field) use ($systemFieldKeys) {

            $isSupportedSystemField =
                in_array(
                    $field->field_key,
                    $systemFieldKeys,
                    true
                );

            $field->is_supported_system_field =
                !$field->is_system || $isSupportedSystemField;

            return $field;
        });

        $warnings = [];

        foreach ($fields as $field) {

            if (
                $field->is_system &&
                !$field->is_supported_system_field
            ) {
                $warnings[] =
                    'System field "' .
                    $field->field_key .
                    '" is not supported by the current DocumentGenerationService.';
            }

            if (
                $field->field_type === 'select' &&
                empty($field->options)
            ) {
                $warnings[] =
                    'Select field "' .
                    $field->field_key .
                    '" does not have any options configured.';
            }

            if (
                empty($field->label)
            ) {
                $warnings[] =
                    'Field "' .
                    $field->field_key .
                    '" has no label.';
            }
        }

        return view(
            'admin.documents.preview',
            compact(
                'document',
                'fields',
                'warnings'
            )
        );
    }
}
