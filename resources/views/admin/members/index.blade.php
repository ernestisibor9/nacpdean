@extends('admin.admin_dashboard')

@section('title')
    NACPDEAN - Member Applications
@endsection

@section('admin')

<div class="container-fluid px-4">

    <h1 class="mt-4">Member Applications</h1>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.admin_dashboard') }}">
                Dashboard
            </a>
        </li>
        <li class="breadcrumb-item active">
            Member Applications
        </li>
    </ol>

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

    <div class="card mb-4">

        <div class="card-header">
            <i class="fas fa-users me-1"></i>
            Member Applications
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-striped align-middle">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Member Name</th>
                            <th>Membership Category</th>
                            <th>Payment</th>
                            <th>Application Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($applications as $application)

                            @php

                                /*
                                |--------------------------------------------------------------------------
                                | BUILD MEMBER NAME FROM MEMBER_PROFILES
                                |--------------------------------------------------------------------------
                                */

                                $fullName = trim(
                                    collect([
                                        $application->first_name,
                                        $application->middle_name,
                                        $application->surname,
                                    ])
                                        ->filter(fn ($name) => filled($name))
                                        ->implode(' ')
                                );

                                /*
                                |--------------------------------------------------------------------------
                                | GET LATEST PAID MEMBERSHIP PAYMENT
                                |--------------------------------------------------------------------------
                                */

                                $payment = $application->user
                                    ? $application->user->payments->first()
                                    : null;

                            @endphp

                            <tr>

                                {{-- NUMBER --}}
                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                {{-- MEMBER NAME --}}
                                <td>

                                    <strong>
                                        {{ $fullName ?: 'N/A' }}
                                    </strong>

                                    @if ($application->phone)

                                        <br>

                                        <small class="text-muted">

                                            <i class="fas fa-phone me-1"></i>

                                            {{ $application->phone }}

                                        </small>

                                    @endif

                                </td>

                                {{-- MEMBERSHIP CATEGORY --}}
                                <td>

                                    @if ($application->membershipCategory)

                                        <span class="badge bg-info text-dark">

                                            {{ $application->membershipCategory->name }}

                                        </span>

                                    @else

                                        <span class="text-muted">
                                            N/A
                                        </span>

                                    @endif

                                </td>

                                {{-- PAYMENT --}}
                                <td>

                                    @if ($payment)

                                        <span class="badge bg-success">
                                            PAID
                                        </span>

                                        <br>

                                        <small>
                                            ₦{{ number_format($payment->amount, 2) }}
                                        </small>

                                    @else

                                        <span class="badge bg-danger">
                                            NOT PAID
                                        </span>

                                    @endif

                                </td>

                                {{-- APPLICATION STATUS --}}
                                <td>

                                    @if ($application->status === 'approved')

                                        <span class="badge bg-success">
                                            Approved
                                        </span>

                                    @elseif ($application->status === 'rejected')

                                        <span class="badge bg-danger">
                                            Rejected
                                        </span>

                                    @elseif ($application->status === 'submitted')

                                        <span class="badge bg-warning text-dark">
                                            Submitted
                                        </span>

                                    @else

                                        <span class="badge bg-secondary">
                                            {{ ucfirst($application->status ?? 'Pending') }}
                                        </span>

                                    @endif

                                </td>

                                {{-- DATE --}}
                                <td>
                                    {{ $application->created_at?->format('d M Y') }}
                                </td>

                                {{-- ACTION --}}
                                <td>

                                    <a
                                        href="{{ route('admin.members.show', $application->id) }}"
                                        class="btn btn-primary btn-sm"
                                    >

                                        <i class="fas fa-eye me-1"></i>

                                        View

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="text-center py-4">

                                    <i class="fas fa-users fa-2x text-muted mb-2"></i>

                                    <br>

                                    No member applications found.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection
