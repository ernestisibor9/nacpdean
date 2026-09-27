<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-bold">Penalty Name</label>
        <input type="text" name="name" class="form-control"
               value="{{ old('name', $penalty->name ?? '') }}" required>
    </div>

    <div class="col-md-3">
        <label class="form-label fw-bold">Amount (₦)</label>
        <input type="number" step="0.01" name="amount" class="form-control"
               value="{{ old('amount', $penalty->amount ?? '') }}" required>
    </div>

    <div class="col-md-3">
        <label class="form-label fw-bold">Action</label>
        <input type="text" name="action" class="form-control"
               value="{{ old('action', $penalty->action ?? '') }}"
               placeholder="e.g. Lift Right Suspension" required>
    </div>

    <div class="col-12">
        <label class="form-label fw-bold">Description</label>
        <textarea name="description" rows="3" class="form-control">{{ old('description', $penalty->description ?? '') }}</textarea>
    </div>

    <div class="col-12">
        <div class="form-check">
            <input type="hidden" name="status" value="0">
            <input type="checkbox" name="status" value="1" class="form-check-input" id="status"
                   {{ old('status', $penalty->status ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="status">Active</label>
        </div>
    </div>
</div>
