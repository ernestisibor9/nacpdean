<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentField;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RuntimeException;

class DocumentFieldController extends Controller
{
    /**
     * Display all fields belonging to a document.
     */
    public function index(Document $document)
    {
        $fields = $document->fields()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view(
            'admin.document_fields.index',
            compact('document', 'fields')
        );
    }

    /**
     * Show create form.
     */
    public function create(Document $document)
    {
        return view(
            'admin.document_fields.create',
            compact('document')
        );
    }

    /**
     * Store a new document field.
     */
    public function store(Request $request, Document $document)
    {
        $validated = $request->validate([
            'field_key' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9_.]+$/',
                Rule::unique('document_fields')
                    ->where(function ($query) use ($document) {
                        return $query->where(
                            'document_id',
                            $document->id
                        );
                    }),
            ],

            'label' => [
                'required',
                'string',
                'max:255',
            ],

            'field_type' => [
                'required',
                Rule::in([
                    'text',
                    'textarea',
                    'number',
                    'date',
                    'email',
                    'phone',
                    'select',
                    'checkbox',
                    'file',
                ]),
            ],

            'section' => [
                'nullable',
                'string',
                'max:255',
            ],

            'placeholder' => [
                'nullable',
                'string',
                'max:255',
            ],

            'default_value' => [
                'nullable',
                'string',
            ],

            'options' => [
                'nullable',
                'array',
            ],

            'options.*.label' => [
                'nullable',
                'string',
                'max:255',
            ],

            'options.*.value' => [
                'nullable',
                'string',
                'max:255',
            ],

            'is_required' => [
                'nullable',
                'boolean',
            ],

            'is_system' => [
                'nullable',
                'boolean',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ]);

        $isSystem = $request->boolean('is_system');

        /*
         * System-generated fields cannot be file fields.
         */
        if (
            $isSystem &&
            $validated['field_type'] === 'file'
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'field_type' =>
                        'System-generated fields cannot use the file field type.',
                ]);
        }

        /*
         * Only select fields should have options.
         */
        $options = [];

        if ($validated['field_type'] === 'select') {
            $options = $this->cleanOptions(
                $validated['options'] ?? []
            );

            if (empty($options)) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'options' =>
                            'Select fields must have at least one option.',
                    ]);
            }
        }

        $field = new DocumentField();

        $field->document_id = $document->id;
        $field->field_key = $validated['field_key'];
        $field->label = $validated['label'];
        $field->field_type = $validated['field_type'];
        $field->section = $validated['section'] ?? null;
        $field->placeholder = $validated['placeholder'] ?? null;
        $field->default_value = $validated['default_value'] ?? null;

        $field->options = $options;

        /*
         * System fields are never required from the member.
         */
        $field->is_required = $isSystem
            ? false
            : $request->boolean('is_required');

        $field->is_system = $isSystem;

        $field->sort_order =
            $validated['sort_order']
            ?? $this->getNextSortOrder($document);

        $field->save();

        return redirect()
            ->route(
                'admin.documents.fields.index',
                $document
            )
            ->with(
                'success',
                'Document field created successfully.'
            );
    }

    /**
     * Show edit form.
     */
    public function edit(
        Document $document,
        DocumentField $field
    ) {
        $this->ensureFieldBelongsToDocument(
            $document,
            $field
        );

        return view(
            'admin.document_fields.edit',
            compact('document', 'field')
        );
    }

    /**
     * Update a document field.
     */
    public function update(
        Request $request,
        Document $document,
        DocumentField $field
    ) {
        $this->ensureFieldBelongsToDocument(
            $document,
            $field
        );

        $validated = $request->validate([
            'field_key' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9_.]+$/',
                Rule::unique('document_fields')
                    ->ignore($field->id)
                    ->where(function ($query) use ($document) {
                        return $query->where(
                            'document_id',
                            $document->id
                        );
                    }),
            ],

            'label' => [
                'required',
                'string',
                'max:255',
            ],

            'field_type' => [
                'required',
                Rule::in([
                    'text',
                    'textarea',
                    'number',
                    'date',
                    'email',
                    'phone',
                    'select',
                    'checkbox',
                    'file',
                ]),
            ],

            'section' => [
                'nullable',
                'string',
                'max:255',
            ],

            'placeholder' => [
                'nullable',
                'string',
                'max:255',
            ],

            'default_value' => [
                'nullable',
                'string',
            ],

            'options' => [
                'nullable',
                'array',
            ],

            'options.*.label' => [
                'nullable',
                'string',
                'max:255',
            ],

            'options.*.value' => [
                'nullable',
                'string',
                'max:255',
            ],

            'is_required' => [
                'nullable',
                'boolean',
            ],

            'is_system' => [
                'nullable',
                'boolean',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ]);

        $isSystem = $request->boolean('is_system');

        /*
         * System-generated fields cannot be file fields.
         */
        if (
            $isSystem &&
            $validated['field_type'] === 'file'
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'field_type' =>
                        'System-generated fields cannot use the file field type.',
                ]);
        }

        /*
         * Only select fields should have options.
         */
        $options = [];

        if ($validated['field_type'] === 'select') {
            $options = $this->cleanOptions(
                $validated['options'] ?? []
            );

            if (empty($options)) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'options' =>
                            'Select fields must have at least one option.',
                    ]);
            }
        }

        $field->field_key = $validated['field_key'];
        $field->label = $validated['label'];
        $field->field_type = $validated['field_type'];
        $field->section = $validated['section'] ?? null;
        $field->placeholder = $validated['placeholder'] ?? null;
        $field->default_value = $validated['default_value'] ?? null;

        $field->options = $options;

        $field->is_system = $isSystem;

        $field->is_required = $isSystem
            ? false
            : $request->boolean('is_required');

        $field->sort_order =
            $validated['sort_order']
            ?? $field->sort_order;

        $field->save();

        return redirect()
            ->route(
                'admin.documents.fields.index',
                $document
            )
            ->with(
                'success',
                'Document field updated successfully.'
            );
    }

    /**
     * Delete a document field.
     */
    public function destroy(
        Document $document,
        DocumentField $field
    ) {
        $this->ensureFieldBelongsToDocument(
            $document,
            $field
        );

        if ($field->is_system) {
            return back()->with(
                'error',
                'System-generated fields cannot be deleted. Edit the field instead.'
            );
        }

        $field->delete();

        return redirect()
            ->route(
                'admin.documents.fields.index',
                $document
            )
            ->with(
                'success',
                'Document field deleted successfully.'
            );
    }

    /**
     * Move field up.
     */
    public function moveUp(
        Document $document,
        DocumentField $field
    ) {
        $this->ensureFieldBelongsToDocument(
            $document,
            $field
        );

        $previous = $document->fields()
            ->where(function ($query) use ($field) {
                $query
                    ->where(
                        'sort_order',
                        '<',
                        $field->sort_order
                    )
                    ->orWhere(function ($query) use ($field) {
                        $query
                            ->where(
                                'sort_order',
                                $field->sort_order
                            )
                            ->where(
                                'id',
                                '<',
                                $field->id
                            );
                    });
            })
            ->orderByDesc('sort_order')
            ->orderByDesc('id')
            ->first();

        if (!$previous) {
            return back()->with(
                'info',
                'This field is already at the top.'
            );
        }

        $currentOrder = $field->sort_order;

        $field->sort_order = $previous->sort_order;
        $previous->sort_order = $currentOrder;

        $field->save();
        $previous->save();

        return back()->with(
            'success',
            'Field order updated.'
        );
    }

    /**
     * Move field down.
     */
    public function moveDown(
        Document $document,
        DocumentField $field
    ) {
        $this->ensureFieldBelongsToDocument(
            $document,
            $field
        );

        $next = $document->fields()
            ->where(function ($query) use ($field) {
                $query
                    ->where(
                        'sort_order',
                        '>',
                        $field->sort_order
                    )
                    ->orWhere(function ($query) use ($field) {
                        $query
                            ->where(
                                'sort_order',
                                $field->sort_order
                            )
                            ->where(
                                'id',
                                '>',
                                $field->id
                            );
                    });
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        if (!$next) {
            return back()->with(
                'info',
                'This field is already at the bottom.'
            );
        }

        $currentOrder = $field->sort_order;

        $field->sort_order = $next->sort_order;
        $next->sort_order = $currentOrder;

        $field->save();
        $next->save();

        return back()->with(
            'success',
            'Field order updated.'
        );
    }

    /**
     * Clean select options before saving.
     */
    protected function cleanOptions(array $options): array
    {
        $cleaned = [];

        foreach ($options as $option) {
            $label = trim($option['label'] ?? '');
            $value = trim($option['value'] ?? '');

            /*
             * Ignore completely empty rows.
             */
            if ($label === '' && $value === '') {
                continue;
            }

            /*
             * Both label and value are required for a real option.
             */
            if ($label === '' || $value === '') {
                continue;
            }

            $cleaned[] = [
                'label' => $label,
                'value' => $value,
            ];
        }

        return $cleaned;
    }

    /**
     * Get the next field sort order.
     */
    protected function getNextSortOrder(
        Document $document
    ): int {
        return ((int) $document
            ->fields()
            ->max('sort_order')) + 1;
    }

    /**
     * Ensure the field belongs to the selected document.
     */
    protected function ensureFieldBelongsToDocument(
        Document $document,
        DocumentField $field
    ): void {
        if (
            (int) $field->document_id !==
            (int) $document->id
        ) {
            throw new RuntimeException(
                'The selected document field does not belong to this document.'
            );
        }
    }
}
