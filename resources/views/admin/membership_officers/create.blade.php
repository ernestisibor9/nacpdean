@extends('admin.admin_dashboard')

@section('admin')

@section('title')
    NACPDEAN - Admin Dashboard
@endsection


<div class="container-fluid">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Officer Appointment</h3>
            <p class="text-muted mb-0">
                Appoint an approved active member as a National Executive or Task Force Officer.
            </p>
        </div>

        <a href="{{ url('/admin') }}" class="btn btn-secondary">
            Back to Dashboard
        </a>
    </div>


    {{-- SUCCESS MESSAGE --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    {{-- ERROR MESSAGE --}}
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif


    {{-- VALIDATION ERRORS --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct the following:</strong>

            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div class="card shadow-sm">

        <div class="card-header">
            <h5 class="mb-0">
                Create Officer Appointment
            </h5>
        </div>

        <div class="card-body">

            <form method="POST" action="{{ route('admin.membership-officers.store') }}">

                @csrf


                {{-- MEMBER --}}
                <div class="mb-4">

                    <label for="membership_id" class="form-label fw-bold">
                        Approved Active Member
                    </label>

                    <select name="membership_id" id="membership_id" class="form-select" required>

                        <option value="">
                            -- Select Member --
                        </option>

                        @foreach ($members as $member)
                            @php
                                $membership = $member->membership;
                                $category = $membership?->membershipCategory;
                                $fullName = trim(
                                    implode(
                                        ' ',
                                        array_filter([$member->first_name, $member->middle_name, $member->surname]),
                                    ),
                                );

                                if (!$fullName) {
                                    $fullName = $member->user?->name ?? ($member->user?->username ?? 'Unnamed Member');
                                }
                            @endphp

                            @if ($membership)
                                <option value="{{ $membership->id }}"
                                    {{ old('membership_id') == $membership->id ? 'selected' : '' }}>
                                    {{ $fullName }}
                                    -
                                    {{ $membership->membership_number }}
                                    -
                                    {{ $category?->name ?? 'Membership' }}
                                </option>
                            @endif
                        @endforeach

                    </select>

                    <small class="text-muted">
                        Only approved members with active memberships are listed.
                    </small>

                </div>


                {{-- APPOINTMENT TYPE --}}
                <div class="mb-4">

                    <label class="form-label fw-bold">
                        Appointment Type
                    </label>

                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-check border rounded p-3">

                                <input class="form-check-input appointment-type" type="radio" name="appointment_type"
                                    id="national_executive" value="national_executive"
                                    {{ old('appointment_type', 'national_executive') === 'national_executive' ? 'checked' : '' }}>

                                <label class="form-check-label" for="national_executive">
                                    <strong>National Executive</strong>

                                    <br>

                                    <small class="text-muted">
                                        NEM-01 through NEM-39
                                    </small>
                                </label>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="form-check border rounded p-3">

                                <input class="form-check-input appointment-type" type="radio" name="appointment_type"
                                    id="task_force" value="task_force"
                                    {{ old('appointment_type') === 'task_force' ? 'checked' : '' }}>

                                <label class="form-check-label" for="task_force">
                                    <strong>Task Force Officer</strong>

                                    <br>

                                    <small class="text-muted">
                                        Can coexist with a National Executive appointment.
                                    </small>
                                </label>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- NATIONAL EXECUTIVE SECTION --}}
                <div id="nationalExecutiveSection" class="border rounded p-4 mb-4">

                    <h5 class="mb-3">
                        National Executive Appointment
                    </h5>

                    <div class="mb-3">

                        <label for="executive_id" class="form-label fw-bold">
                            National Executive Position
                        </label>

                        <select name="executive_id" id="executive_id" class="form-select">

                            <option value="">
                                -- Select Vacant NEM Position --
                            </option>

                            @foreach ($executivePositions as $position)
                                <option value="{{ $position->code }}" data-position="{{ $position->position }}"
                                    {{ old('executive_id') === $position->code ? 'selected' : '' }}>
                                    {{ $position->code }}
                                    -
                                    {{ $position->position }}
                                </option>
                            @endforeach

                        </select>

                        <small class="text-muted">
                            Only positions currently marked as vacant are available.
                        </small>

                    </div>


                    <div id="selectedExecutivePosition" class="alert alert-info d-none">
                        <strong>Position:</strong>

                        <span id="executivePositionText"></span>
                    </div>

                </div>


                {{-- TASK FORCE SECTION --}}
                <div id="taskForceSection" class="border rounded p-4 mb-4 d-none">

                    <h5 class="mb-3">
                        Task Force Appointment
                    </h5>


                    {{-- TASK FORCE ID --}}
                    <div class="mb-3">

                        <label for="taskforce_id" class="form-label fw-bold">
                            Task Force ID
                        </label>

                        <input type="text" name="taskforce_id" id="taskforce_id" class="form-control"
                            value="{{ old('taskforce_id') }}" placeholder="e.g. TF-001">

                    </div>


                    {{-- TASK FORCE POSITION --}}
                    <div class="mb-3">

                        <label for="taskforce_position" class="form-label fw-bold">
                            Task Force Position
                        </label>

                        <input type="text" name="taskforce_position" id="taskforce_position" class="form-control"
                            value="{{ old('taskforce_position') }}" placeholder="Enter Task Force position">

                    </div>


                    {{-- TASK FORCE LEVEL --}}
                    <div class="mb-3">

                        <label for="taskforce_level" class="form-label fw-bold">
                            Task Force Level
                        </label>

                        <select name="taskforce_level" id="taskforce_level" class="form-select">

                            <option value="">
                                -- Select Level --
                            </option>

                            <option value="national" {{ old('taskforce_level') === 'national' ? 'selected' : '' }}>
                                National
                            </option>

                            <option value="state" {{ old('taskforce_level') === 'state' ? 'selected' : '' }}>
                                State
                            </option>

                        </select>

                    </div>


                    {{-- TASK FORCE STATE --}}
                    <div id="taskForceStateSection" class="mb-3 d-none">

                        <label for="taskforce_state" class="form-label fw-bold">
                            State
                        </label>

                        <input type="text" name="taskforce_state" id="taskforce_state" class="form-control"
                            value="{{ old('taskforce_state') }}" placeholder="Enter state">

                    </div>

                </div>


                {{-- INFORMATION --}}
                <div class="alert alert-warning">

                    <strong>Important:</strong>

                    <ul class="mb-0 mt-2">

                        <li>
                            Only approved and active members can be appointed.
                        </li>

                        <li>
                            National Executive codes are organizational positions,
                            not membership categories.
                        </li>

                        <li>
                            A member can hold both a National Executive and a Task Force appointment.
                        </li>

                        <li>
                            The existing membership approval process is not changed by this workflow.
                        </li>

                    </ul>

                </div>


                {{-- SUBMIT --}}
                <div class="d-flex justify-content-end">

                    <button type="submit" class="btn btn-primary">
                        Create Appointment
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<script>
    document.addEventListener(
        'DOMContentLoaded',
        function() {

            const nationalExecutiveSection =
                document.getElementById(
                    'nationalExecutiveSection'
                );

            const taskForceSection =
                document.getElementById(
                    'taskForceSection'
                );

            const executiveId =
                document.getElementById(
                    'executive_id'
                );

            const executivePositionBox =
                document.getElementById(
                    'selectedExecutivePosition'
                );

            const executivePositionText =
                document.getElementById(
                    'executivePositionText'
                );

            const taskForceLevel =
                document.getElementById(
                    'taskforce_level'
                );

            const taskForceStateSection =
                document.getElementById(
                    'taskForceStateSection'
                );


            /*
            |--------------------------------------------------------------------------
            | APPOINTMENT TYPE
            |--------------------------------------------------------------------------
            */

            function updateAppointmentType() {
                const selected =
                    document.querySelector(
                        '.appointment-type:checked'
                    );

                if (!selected) {
                    return;
                }

                if (
                    selected.value ===
                    'national_executive'
                ) {

                    nationalExecutiveSection
                        .classList
                        .remove('d-none');

                    taskForceSection
                        .classList
                        .add('d-none');

                } else {

                    nationalExecutiveSection
                        .classList
                        .add('d-none');

                    taskForceSection
                        .classList
                        .remove('d-none');

                }
            }


            /*
            |--------------------------------------------------------------------------
            | EXECUTIVE POSITION
            |--------------------------------------------------------------------------
            */

            function updateExecutivePosition() {
                const selectedOption =
                    executiveId.options[
                        executiveId.selectedIndex
                    ];

                if (
                    !selectedOption ||
                    !selectedOption.value
                ) {

                    executivePositionBox
                        .classList
                        .add('d-none');

                    executivePositionText
                        .textContent = '';

                    return;
                }

                executivePositionText
                    .textContent =
                    selectedOption.dataset.position ||
                    '';

                executivePositionBox
                    .classList
                    .remove('d-none');
            }


            /*
            |--------------------------------------------------------------------------
            | TASK FORCE LEVEL
            |--------------------------------------------------------------------------
            */

            function updateTaskForceLevel() {
                if (
                    taskForceLevel.value ===
                    'state'
                ) {

                    taskForceStateSection
                        .classList
                        .remove('d-none');

                } else {

                    taskForceStateSection
                        .classList
                        .add('d-none');

                }
            }


            /*
            |--------------------------------------------------------------------------
            | EVENTS
            |--------------------------------------------------------------------------
            */

            document
                .querySelectorAll(
                    '.appointment-type'
                )
                .forEach(
                    function(radio) {

                        radio.addEventListener(
                            'change',
                            updateAppointmentType
                        );

                    }
                );


            executiveId.addEventListener(
                'change',
                updateExecutivePosition
            );


            taskForceLevel.addEventListener(
                'change',
                updateTaskForceLevel
            );


            /*
            |--------------------------------------------------------------------------
            | INITIAL STATE
            |--------------------------------------------------------------------------
            */

            updateAppointmentType();

            updateExecutivePosition();

            updateTaskForceLevel();

        }
    );
</script>

@endsection
