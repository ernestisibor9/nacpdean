@extends('admin.admin_dashboard')
@section('title', 'NACPDEAN - Assign Penalty')

@section('admin')

<div class="container-fluid">

    <h1 class="mt-4">Assign Penalty</h1>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.penalties.assign.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Penalty <span class="text-danger">*</span></label>
                    <select name="penalty_id" id="penalty_id" class="form-select" required>
                        <option value="">— Select a penalty —</option>
                        @foreach($catalog as $p)
                            <option value="{{ $p->id }}"
                                    data-amount="{{ $p->amount }}"
                                    data-custom="{{ $p->is_custom_amount ? 1 : 0 }}"
                                    @selected(old('penalty_id') == $p->id)>
                                {{ $p->name }} — ₦{{ number_format($p->amount, 2) }}
                                {{ $p->is_custom_amount ? '(custom amount)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <hr>
                <h5>Assign to</h5>

                <div class="mb-3">
                    <label class="form-label">Member (optional — leave blank for a non-member)</label>
                    <select name="user_id" id="user_id" class="form-select">
                        <option value="">— Non-member —</option>
                        @foreach($members as $m)
                            <option value="{{ $m->id }}"
                                    @selected(old('user_id', $member?->id) == $m->id)>
                                {{ $m->username }} — {{ $m->email ?? $m->phone }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="non_member_block">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Non-Member Name</label>
                            <input type="text" name="non_member_name" class="form-control"
                                   value="{{ old('non_member_name') }}" maxlength="255">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Non-Member Phone</label>
                            <input type="text" name="non_member_phone" class="form-control"
                                   value="{{ old('non_member_phone') }}" maxlength="30">
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Amount & Options</h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Amount (₦)</label>
                        <input type="number" step="0.01" min="0" name="amount" id="amount"
                               class="form-control" value="{{ old('amount') }}">
                        <small class="text-muted">
                            Leave blank to use the catalog amount. Only editable for custom-amount penalties.
                        </small>
                    </div>

                    <div class="col-md-6 mb-3 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="include_admin_charge" id="include_admin_charge"
                                   value="1" @checked(old('include_admin_charge'))>
                            <label class="form-check-label" for="include_admin_charge">
                                Add additional administrative charge (₦20,000)
                                <small class="d-block text-muted">
                                    For executive / taskforce vehicles (§6a)
                                </small>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
                </div>

                <button class="btn btn-primary">
                    <i class="fas fa-gavel"></i> Assign Penalty
                </button>
                <a href="{{ route('admin.penalties.assignments') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const penaltySelect = document.getElementById('penalty_id');
        const amountInput   = document.getElementById('amount');
        const userSelect    = document.getElementById('user_id');
        const nonMemberBlock = document.getElementById('non_member_block');

        // Prefill amount when a penalty is chosen
        penaltySelect.addEventListener('change', function () {
            const opt = penaltySelect.options[penaltySelect.selectedIndex];
            const amount = opt.getAttribute('data-amount');
            const isCustom = opt.getAttribute('data-custom') === '1';

            if (isCustom) {
                amountInput.value = '';
                amountInput.focus();
                amountInput.placeholder = 'Enter custom amount';
            } else if (amount !== null) {
                amountInput.value = amount;
                amountInput.placeholder = '';
            } else {
                amountInput.value = '';
            }
        });

        // Toggle non-member block
        function toggleNonMember() {
            if (userSelect.value) {
                nonMemberBlock.style.display = 'none';
            } else {
                nonMemberBlock.style.display = 'block';
            }
        }

        userSelect.addEventListener('change', toggleNonMember);
        toggleNonMember();
    });
</script>
@endpush

@endsection
