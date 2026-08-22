@extends('admin.admin_dashboard')

@section('admin')

@section('title')
    NACPDEAN - Members Applications
@endsection

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


    <div class="card mb-4">

        <div class="card-header">

            <i class="fas fa-users me-1"></i>

            Member Applications

        </div>


        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-striped">

                    <thead>

                        <tr>

                            <th>#</th>

                            <th>Member</th>

                            <th>Email</th>

                            <th>Payment</th>

                            <th>Application Status</th>

                            <th>Date</th>

                            <th>Action</th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($applications as $application)
                            @php

                                $payment = $application->user->payments->first();

                            @endphp

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>


                                <td>
                                    {{ trim(
                                        ($application->first_name ?? '') . ' ' . ($application->middle_name ?? '') . ' ' . ($application->surname ?? ''),
                                    ) ?:
                                        'N/A' }}
                                </td>


                                <td>
                                    {{ $application->user->email ?? 'N/A' }}
                                </td>


                                <td>

                                    @if ($payment)
                                        <span class="badge bg-success">
                                            PAID
                                        </span>

                                        <br>

                                        ₦{{ number_format($payment->amount, 2) }}
                                    @else
                                        <span class="badge bg-danger">
                                            NOT PAID
                                        </span>
                                    @endif

                                </td>


                                <td>

                                    @if ($application->status === 'approved')
                                        <span class="badge bg-success">
                                            Approved
                                        </span>
                                    @elseif($application->status === 'rejected')
                                        <span class="badge bg-danger">
                                            Rejected
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            Pending
                                        </span>
                                    @endif

                                </td>


                                <td>
                                    {{ $application->created_at?->format('d M Y') }}
                                </td>


                                <td>

                                    <a href="{{ route('admin.members.show', $application->id) }}"
                                        class="btn btn-primary btn-sm">

                                        <i class="fas fa-eye"></i>

                                        View

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="text-center">
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
