@extends('admin.admin_dashboard')
@section('title', 'NACPDEAN - New Appointment')

@section('admin')
<div class="container py-4">
    <h2 class="fw-bold mb-4" style="color:#0b3b2c;">
        @if ($type === 'task_force')
            New Task Force Appointment
        @elseif ($type === 'national_executive')
            New National Executive Appointment
        @else
            New State Executive Appointment
        @endif
    </h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.taskforce.store') }}" class="card p-4 shadow-sm">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">

        <div class="row g-3">

            {{-- Member --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Member</label>

                <input type="text"
                       id="memberSearch"
                       class="form-control form-control-sm mb-2"
                       placeholder="Type to search members...">

                <select name="membership_id" id="memberSelect" class="form-select" required>
                    <option value="">— Select Member —</option>
                    @foreach ($members as $m)
                        <option value="{{ $m->id }}"
                            data-search="{{ strtolower(($m->user->username ?? '') . ' ' . ($m->user->name ?? '') . ' ' . ($m->user->phone ?? '')) }}"
                            {{ (string) old('membership_id') === (string) $m->id ? 'selected' : '' }}>
                            {{ $m->user->username ?? $m->user->name }}
                            @if ($m->user->phone ?? null) — {{ $m->user->phone }} @endif
                        </option>
                    @endforeach
                </select>
            </div>


            @if ($type === 'national_executive')

                {{-- National Executive Position --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold">National Position</label>
                    <select name="executive_id" class="form-select" required>
                        <option value="">— Select Position —</option>
                        @foreach ($positions as $p)
                            <option value="{{ $p->code }}"
                                {{ old('executive_id') === $p->code ? 'selected' : '' }}
                                {{ $p->status === 'occupied' ? 'disabled' : '' }}>
                                {{ $p->position }} ({{ $p->code }})
                                @if ($p->status === 'occupied') — OCCUPIED @endif
                            </option>
                        @endforeach
                    </select>
                </div>

            @elseif ($type === 'state_executive')

                {{-- Filter State --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold">Select State</label>
                    <select id="filterStateSelect" class="form-select mb-3">
                        <option value="">— Select State First —</option>
                        @foreach ($states as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- State Executive Position --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold">State Position</label>
                    <select name="executive_id" id="statePositionSelect" class="form-select" required disabled>
                        <option value="">— Select State First —</option>
                        @foreach ($positions as $p)
                            <option value="{{ $p->code }}"
                                data-state="{{ $p->state }}"
                                {{ old('executive_id') === $p->code ? 'selected' : '' }}
                                {{ $p->status === 'occupied' ? 'disabled' : '' }}>
                                {{ $p->position }} ({{ $p->code }})
                                @if ($p->status === 'occupied') — OCCUPIED @endif
                            </option>
                        @endforeach
                    </select>
                </div>

            @else

                {{-- Task Force Position --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold">Position Title</label>
                    <select name="position" class="form-select" required>
                        <option value="">— Select Position —</option>
                        @foreach ($taskForcePositions as $title)
                            <option value="{{ $title }}"
                                {{ old('position') === $title ? 'selected' : '' }}>
                                {{ $title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Level --}}
                <div class="col-md-3">
                    <label class="form-label fw-bold">Level</label>
                    <select name="level" id="levelSelect" class="form-select" required>
                        <option value="">— Select —</option>
                        <option value="national" {{ old('level') === 'national' ? 'selected' : '' }}>National</option>
                        <option value="state"    {{ old('level') === 'state'    ? 'selected' : '' }}>State</option>
                    </select>
                </div>

                {{-- State --}}
                <div class="col-md-3" id="stateWrapper" style="display:none;">
                    <label class="form-label fw-bold">State</label>
                    <select name="state" class="form-select">
                        <option value="">— Select State —</option>
                        @foreach ($states as $s)
                            <option value="{{ $s }}" {{ old('state') === $s ? 'selected' : '' }}>
                                {{ $s }}
                            </option>
                        @endforeach
                    </select>
                </div>

            @endif

        </div>

        <div class="mt-4">
            <button class="btn btn-success">Save Appointment</button>
            <a href="{{ route('admin.taskforce.index') }}" class="btn btn-light">Cancel</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* -------- Member live search -------- */
    const search = document.getElementById('memberSearch');
    const select = document.getElementById('memberSelect');
    if (search && select) {
        const placeholder = select.options[0].cloneNode(true);
        const allOptions = Array.from(select.options).slice(1);

        search.addEventListener('input', function () {
            const q = search.value.trim().toLowerCase();
            const current = select.value;
            select.innerHTML = '';
            select.appendChild(placeholder);

            allOptions.forEach(function (opt) {
                if ((opt.dataset.search || '').includes(q)) {
                    select.appendChild(opt.cloneNode(true));
                }
            });

            const stillThere = Array.from(select.options).find(o => o.value === current);
            if (stillThere) select.value = current;
        });
    }

    /* -------- Show state only when level = state -------- */
    const level = document.getElementById('levelSelect');
    const wrapper = document.getElementById('stateWrapper');
    if (level && wrapper) {
        const toggle = () => {
            wrapper.style.display = level.value === 'state' ? '' : 'none';
        };
        level.addEventListener('change', toggle);
        toggle();
    }

    /* -------- Filter State Positions (State Executive logic) -------- */
    const stateFilter = document.getElementById('filterStateSelect');
    const positionSelect = document.getElementById('statePositionSelect');

    if (stateFilter && positionSelect) {
        // Save all original options (skip the placeholder)
        const allPositionOptions = Array.from(positionSelect.options).slice(1);

        stateFilter.addEventListener('change', function () {
            const selectedState = this.value;
            positionSelect.innerHTML = '<option value="">— Select Position —</option>';

            if (!selectedState) {
                positionSelect.disabled = true;
                return;
            }

            positionSelect.disabled = false;

            // Filter and append matching options
            allPositionOptions.forEach(opt => {
                if (opt.dataset.state === selectedState) {
                    positionSelect.appendChild(opt.cloneNode(true));
                }
            });
        });
    }
});
</script>
@endsection
