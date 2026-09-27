@extends('admin.admin_dashboard')
@section('title', 'NACPDEAN - New Appointment')

@section('admin')
<div class="container py-4">
    <h2 class="fw-bold mb-4" style="color:#0b3b2c;">
        New {{ $type === 'task_force' ? 'Task Force' : 'National Executive' }} Appointment
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

                {{-- Executive Position --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold">Position</label>
                    <select name="executive_id" class="form-select" required>
                        <option value="">— Select Position —</option>
                        @foreach ($positions as $p)
                            <option value="{{ $p->code }}"
                                {{ old('executive_id') === $p->code ? 'selected' : '' }}>
                                {{ $p->position }} ({{ $p->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Hidden so validation passes --}}
                {{--  <input type="hidden" name="position" value="__exec__">  --}}

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
});
</script>
@endsection
