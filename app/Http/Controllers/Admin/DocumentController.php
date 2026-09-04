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
        /*
        |--------------------------------------------------------------------------
        | Normalize Code First
        |--------------------------------------------------------------------------
        */

        $normalizedCode = strtoupper(
            Str::slug(
                trim($request->input('code')),
                '_'
            )
        );


        /*
        |--------------------------------------------------------------------------
        | Validate
        |--------------------------------------------------------------------------
        */

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
        | Normalized Code Uniqueness
        |--------------------------------------------------------------------------
        */

        if (
            Document::where(
                'code',
                $normalizedCode
            )->exists()
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'code' =>
                        'A document with this code already exists.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Validity Configuration
        |--------------------------------------------------------------------------
        */

        $validityType =
            $validated['validity_type'];


        /*
        |--------------------------------------------------------------------------
        | Days / Months / Years
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $validityType,
                ['days', 'months', 'years'],
                true
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


        /*
        |--------------------------------------------------------------------------
        | Fixed Date
        |--------------------------------------------------------------------------
        */

        if (
            $validityType === 'fixed_date'
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
        | Normalize Unused Validity Values
        |--------------------------------------------------------------------------
        */

        $validityValue = null;
        $validityDate = null;


        if (
            in_array(
                $validityType,
                ['days', 'months', 'years'],
                true
            )
        ) {
            $validityValue =
                $validated['validity_value'];
        }


        if (
            $validityType === 'fixed_date'
        ) {
            $validityDate =
                $validated['validity_date'];
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
                $normalizedCode,

            'description' =>
                $validated['description'] ?? null,

            'validity_type' =>
                $validityType,

            'validity_value' =>
                $validityValue,

            'validity_date' =>
                $validityDate,

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
        /*
        |--------------------------------------------------------------------------
        | Normalize Code First
        |--------------------------------------------------------------------------
        */

        $normalizedCode = strtoupper(
            Str::slug(
                trim($request->input('code')),
                '_'
            )
        );


        /*
        |--------------------------------------------------------------------------
        | Validate
        |--------------------------------------------------------------------------
        */

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
        | Normalized Code Uniqueness
        |--------------------------------------------------------------------------
        */

        $codeExists = Document::query()
            ->where('code', $normalizedCode)
            ->where(
                'id',
                '!=',
                $document->id
            )
            ->exists();


        if ($codeExists) {
            return back()
                ->withInput()
                ->withErrors([
                    'code' =>
                        'A document with this code already exists.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Validity Configuration
        |--------------------------------------------------------------------------
        */

        $validityType =
            $validated['validity_type'];


        if (
            in_array(
                $validityType,
                ['days', 'months', 'years'],
                true
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
            $validityType === 'fixed_date'
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
        | Normalize Unused Validity Values
        |--------------------------------------------------------------------------
        */

        $validityValue = null;
        $validityDate = null;


        if (
            in_array(
                $validityType,
                ['days', 'months', 'years'],
                true
            )
        ) {
            $validityValue =
                $validated['validity_value'];
        }


        if (
            $validityType === 'fixed_date'
        ) {
            $validityDate =
                $validated['validity_date'];
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
                $normalizedCode,

            'description' =>
                $validated['description'] ?? null,

            'validity_type' =>
                $validityType,

            'validity_value' =>
                $validityValue,

            'validity_date' =>
                $validityDate,

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
     * Documents already used by payment items
     * or generated documents cannot be deleted.
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


    /**
     * Preview document configuration.
     */
    public function preview(Document $document)
    {
        $document->load([
            'fields' => function ($query) {
                $query
                    ->orderBy('sort_order')
                    ->orderBy('id');
            },

            'paymentItems',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Supported System Fields
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Inspect Fields
        |--------------------------------------------------------------------------
        */

        $fields = $document->fields->map(
            function ($field) use ($systemFieldKeys) {

                $isSupportedSystemField =
                    in_array(
                        $field->field_key,
                        $systemFieldKeys,
                        true
                    );


                $field->is_supported_system_field =
                    !$field->is_system
                    ||
                    $isSupportedSystemField;


                return $field;
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Configuration Warnings
        |--------------------------------------------------------------------------
        */

        $warnings = [];


        foreach ($fields as $field) {

            /*
            |--------------------------------------------------------------------------
            | Unsupported System Field
            |--------------------------------------------------------------------------
            */

            if (
                $field->is_system &&
                !$field->is_supported_system_field
            ) {
                $warnings[] =
                    'System field "' .
                    $field->field_key .
                    '" is not supported by the current DocumentGenerationService.';
            }


            /*
            |--------------------------------------------------------------------------
            | Select Without Options
            |--------------------------------------------------------------------------
            */

            if (
                $field->field_type === 'select'
                &&
                empty($field->options)
            ) {
                $warnings[] =
                    'Select field "' .
                    $field->field_key .
                    '" does not have any options configured.';
            }


            /*
            |--------------------------------------------------------------------------
            | Empty Label
            |--------------------------------------------------------------------------
            */

            if (
                empty($field->label)
            ) {
                $warnings[] =
                    'Field "' .
                    $field->field_key .
                    '" has no label.';
            }


            /*
            |--------------------------------------------------------------------------
            | System Field Marked Required
            |--------------------------------------------------------------------------
            */

            if (
                $field->is_system &&
                $field->is_required
            ) {
                $warnings[] =
                    'System field "' .
                    $field->field_key .
                    '" should not be marked as required.';
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
