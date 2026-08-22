@extends('member.member_dashboard')

@section('member')

@section('title')
    NACPDEAN - Payment Successful
@endsection

    <div class="container py-5">

        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">

                <div class="mb-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 70px;"></i>
                </div>

                <h2 class="fw-bold text-success">
                    Payment Successful
                </h2>

                <p class="text-muted">
                    Your membership payment has been received successfully.
                </p>

                <hr>

                <div class="row justify-content-center">

                    <div class="col-md-6">

                        <table class="table table-bordered text-start">

                            <tr>
                                <th>Reference</th>
                                <td>{{ $payment->reference }}</td>
                            </tr>

                            <tr>
                                <th>Amount</th>
                                <td>
                                    ₦{{ number_format($payment->amount, 2) }}
                                </td>
                            </tr>

                            <tr>
                                <th>Payment Type</th>
                                <td>
                                    {{ ucfirst($payment->payment_type) }}
                                </td>
                            </tr>

                            <tr>
                                <th>Fee Type</th>
                                <td>
                                    {{ ucfirst($payment->fee_type) }}
                                </td>
                            </tr>

                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge bg-success">
                                        PAID
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <th>Date</th>
                                <td>
                                    {{ $payment->updated_at->format('d M Y, h:i A') }}
                                </td>
                            </tr>

                        </table>

                    </div>

                </div>

                {{--  <a href="{{ route('payment.index') }}" class="btn btn-primary mt-3">
                    Continue
                </a>  --}}

            </div>
        </div>

    </div>
@endsection
