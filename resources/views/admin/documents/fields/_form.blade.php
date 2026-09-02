@php
    $field = $field ?? null;
@endphp


<div class="row g-3">

    {{-- Field Key --}}
    <div class="col-md-6">

        <label class="form-label">
            Field Key
            <span class="text-danger">*</span>
        </label>

        <input type="text"
               name="field_key"
               class="form-control @error('field_key') is-invalid @enderror"
               value="{{ old('field_key', $field?->field_key) }}"
               placeholder="e.g. container_number"
               required>

        <div class="form-text">
            Use lowercase letters, numbers, hyphens or underscores.
        </div>

        @error('field_key')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Label --}}
    <div class="col-md-6">

        <label class="form-label">
            Field Label
            <span class="text-danger">*</span>
        </label>

        <input type="text"
               name="label"
               class="form-control @error('label') is-invalid @enderror"
               value="{{ old('label', $field?->label) }}"
               placeholder="e.g. Container Number"
               required>

        @error('label')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Field Type --}}
    <div class="col-md-4">

        <label class="form-label">
            Field Type
            <span class="text-danger">*</span>
        </label>

        <select name="field_type"
                id="field_type"
                class="form-select @error('field_type') is-invalid @enderror"
                required>

            @php
                $fieldType = old(
                    'field_type',
                    $field?->field_type ?? 'text'
                );
            @endphp

            <option value="text"
                {{ $fieldType === 'text' ? 'selected' : '' }}>
                Text
            </option>

            <option value="textarea"
                {{ $fieldType === 'textarea' ? 'selected' : '' }}>
                Textarea
            </option>

            <option value="number"
                {{ $fieldType === 'number' ? 'selected' : '' }}>
                Number
            </option>

            <option value="date"
                {{ $fieldType === 'date' ? 'selected' : '' }}>
                Date
            </option>

            <option value="email"
                {{ $fieldType === 'email' ? 'selected' : '' }}>
                Email
            </option>

            <option value="tel"
                {{ $fieldType === 'tel' ? 'selected' : '' }}>
                Telephone
            </option>

            <option value="select"
                {{ $fieldType === 'select' ? 'selected' : '' }}>
                Select
            </option>

            <option value="checkbox"
                {{ $fieldType === 'checkbox' ? 'selected' : '' }}>
                Checkbox
            </option>

        </select>

        @error('field_type')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Section --}}
    <div class="col-md-4">

        <label class="form-label">
            Section
        </label>

        <input type="text"
               name="section"
               class="form-control @error('section') is-invalid @enderror"
               value="{{ old('section', $field?->section) }}"
               placeholder="e.g. Seller Details">

        @error('section')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Sort Order --}}
    <div class="col-md-4">

        <label class="form-label">
            Sort Order
        </label>

        <input type="number"
               name="sort_order"
               min="1"
               class="form-control @error('sort_order') is-invalid @enderror"
               value="{{ old('sort_order', $field?->sort_order ?? 1) }}">

        @error('sort_order')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Placeholder --}}
    <div class="col-md-6">

        <label class="form-label">
            Placeholder
        </label>

        <input type="text"
               name="placeholder"
               class="form-control @error('placeholder') is-invalid @enderror"
               value="{{ old('placeholder', $field?->placeholder) }}"
               placeholder="e.g. Enter container number">

        @error('placeholder')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Default Value --}}
    <div class="col-md-6">

        <label class="form-label">
            Default Value
        </label>

        <input type="text"
               name="default_value"
               class="form-control @error('default_value') is-invalid @enderror"
               value="{{ old('default_value', $field?->default_value) }}">

        @error('default_value')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Select Options --}}
    <div class="col-12"
         id="optionsContainer">

        <label class="form-label">
            Select Options
        </label>

        <textarea name="options"
                  rows="4"
                  class="form-control @error('options') is-invalid @enderror"
                  placeholder="Enter one option per line">{{ old('options', $field?->options) }}</textarea>

        <div class="form-text">
            Only required for Select fields. Enter one option per line.
        </div>

        @error('options')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Required --}}
    <div class="col-md-6">

        <div class="form-check form-switch mt-4">

            <input type="hidden"
                   name="is_required"
                   value="0">

            <input class="form-check-input"
                   type="checkbox"
                   name="is_required"
                   value="1"
                   id="is_required"
                   {{ old(
                       'is_required',
                       $field?->is_required ?? false
                   ) ? 'checked' : '' }}>

            <label class="form-check-label"
                   for="is_required">

                Required Field

            </label>

        </div>

        <small class="text-muted">
            The document cannot be generated without this value.
        </small>

    </div>


    {{-- System --}}
    <div class="col-md-6">

        <div class="form-check form-switch mt-4">

            <input type="hidden"
                   name="is_system"
                   value="0">

            <input class="form-check-input"
                   type="checkbox"
                   name="is_system"
                   value="1"
                   id="is_system"
                   {{ old(
                       'is_system',
                       $field?->is_system ?? false
                   ) ? 'checked' : '' }}>

            <label class="form-check-label"
                   for="is_system">

                System Generated

            </label>

        </div>

        <small class="text-muted">
            Automatically resolved by the document generation service.
        </small>

    </div>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const type =
        document.getElementById('field_type');

    const options =
        document.getElementById('optionsContainer');


    function toggleOptions() {

        if (!type || !options) {
            return;
        }

        if (type.value === 'select') {

            options.style.display = '';

        } else {

            options.style.display = 'none';

        }

    }


    if (type) {

        type.addEventListener(
            'change',
            toggleOptions
        );

        toggleOptions();

    }

});

</script>
