<div class="document-field border rounded p-3 mb-3 bg-light">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <strong>
            Field #{{ $index + 1 }}
        </strong>

        <button
            type="button"
            class="btn btn-sm btn-outline-danger remove-field"
        >
            Remove
        </button>

    </div>

    <input
        type="hidden"
        name="fields[{{ $index }}][id]"
        value="{{ $field->id }}"
    >

    <div class="row g-3">

        <div class="col-md-4">

            <label class="form-label">
                Field Key
            </label>

            <input
                type="text"
                name="fields[{{ $index }}][field_key]"
                class="form-control"
                value="{{ $field->field_key }}"
                required
            >

            <small class="text-muted">
                Example: member_name, receipt_no,
                container_number
            </small>

        </div>

        <div class="col-md-4">

            <label class="form-label">
                Label
            </label>

            <input
                type="text"
                name="fields[{{ $index }}][label]"
                class="form-control"
                value="{{ $field->label }}"
                required
            >

        </div>

        <div class="col-md-4">

            <label class="form-label">
                Field Type
            </label>

            <select
                name="fields[{{ $index }}][field_type]"
                class="form-select"
                required
            >

                @foreach([
                    'text' => 'Text',
                    'textarea' => 'Textarea',
                    'number' => 'Number',
                    'date' => 'Date',
                    'email' => 'Email',
                    'tel' => 'Telephone',
                    'select' => 'Select',
                    'checkbox' => 'Checkbox',
                    'file' => 'File',
                ] as $type => $label)

                    <option
                        value="{{ $type }}"
                        @selected($field->field_type === $type)
                    >
                        {{ $label }}
                    </option>

                @endforeach

            </select>

        </div>

        <div class="col-md-4">

            <label class="form-label">
                Section
            </label>

            <input
                type="text"
                name="fields[{{ $index }}][section]"
                class="form-control"
                value="{{ $field->section }}"
                placeholder="Seller Details"
            >

        </div>

        <div class="col-md-4">

            <label class="form-label">
                Placeholder
            </label>

            <input
                type="text"
                name="fields[{{ $index }}][placeholder]"
                class="form-control"
                value="{{ $field->placeholder }}"
                placeholder="Enter value"
            >

        </div>

        <div class="col-md-4">

            <label class="form-label">
                Sort Order
            </label>

            <input
                type="number"
                name="fields[{{ $index }}][sort_order]"
                class="form-control"
                value="{{ $field->sort_order ?: ($index + 1) }}"
                min="1"
            >

        </div>

        <div class="col-md-6">

            <label class="form-label">
                Default Value
            </label>

            <textarea
                name="fields[{{ $index }}][default_value]"
                class="form-control"
                rows="2"
            >{{ $field->default_value }}</textarea>

        </div>

        <div class="col-md-6">

            <label class="form-label">
                Options
            </label>

            <textarea
                name="fields[{{ $index }}][options]"
                class="form-control"
                rows="2"
                placeholder="For select fields"
            >{{ $field->options }}</textarea>

        </div>

        <div class="col-md-6">

            <div class="form-check form-switch mt-3">

                <input
                    type="hidden"
                    name="fields[{{ $index }}][is_required]"
                    value="0"
                >

                <input
                    type="checkbox"
                    name="fields[{{ $index }}][is_required]"
                    value="1"
                    class="form-check-input"
                    @checked($field->is_required)
                >

                <label class="form-check-label">
                    Required field
                </label>

            </div>

        </div>

        <div class="col-md-6">

            <div class="form-check form-switch mt-3">

                <input
                    type="hidden"
                    name="fields[{{ $index }}][is_system]"
                    value="0"
                >

                <input
                    type="checkbox"
                    name="fields[{{ $index }}][is_system]"
                    value="1"
                    class="form-check-input"
                    @checked($field->is_system)
                >

                <label class="form-check-label">

                    <strong>System Generated</strong>

                </label>

                <div class="small text-muted">
                    Automatically supplied by the portal.
                </div>

            </div>

        </div>

    </div>

</div>
