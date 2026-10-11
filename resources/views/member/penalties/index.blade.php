@extends('member.member_dashboard')

@section('title', 'NACPDEAN - My Penalties')

@section('member')

<div class="container py-5">

    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">My Penalties</h3>
                    <p class="text-muted mb-0">
                        View and pay penalties assigned to your account.
                    </p>
                </div>

                @if($unpaidTotal > 0)
                    <div class="text-end">
                        <small class="text-muted d-block">Outstanding</small>
                        <h4 class="fw-bold text-danger mb-0">
                            ₦{{ number_format($unpaidTotal, 2) }}
                        </h4>
                    </div>
                @endif
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($penalties->isEmpty())
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-gavel fa-3x text-muted mb-3"></i>
                        <h5 class="fw-bold">No penalties assigned</h5>
                        <p class="text-muted mb-0">
                            You have no penalties on your account.
                        </p>
                    </div>
                </div>
            @else

                <form id="penaltyForm" action="{{ route('member.penalties.pay') }}" method="POST">
                    @csrf

                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:50px;">
                                                <input type="checkbox"
                                                       id="selectAll"
                                                       class="form-check-input">
                                            </th>
                                            <th>Penalty</th>
                                            <th>Action Required</th>
                                            <th class="text-end" style="width:140px;">Amount</th>
                                            <th class="text-center" style="width:120px;">Status</th>
                                            <th class="text-end" style="width:160px;">Receipt</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($penalties as $p)
                                            <tr>
                                                <td>
                                                    @if($p->payment_status === 'unpaid')
                                                        <input type="checkbox"
                                                               name="penalty_ids[]"
                                                               value="{{ $p->id }}"
                                                               class="form-check-input penalty-checkbox"
                                                               data-amount="{{ $p->amount }}">
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="fw-semibold">{{ $p->name }}</div>
                                                    @if($p->notes)
                                                        <small class="text-muted">{{ $p->notes }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $p->action }}
                                                    </small>
                                                </td>
                                                <td class="text-end fw-semibold">
                                                    ₦{{ number_format($p->amount, 2) }}
                                                </td>
                                                <td class="text-center">
                                                    @if($p->payment_status === 'paid')
                                                        <span class="badge bg-success">Paid</span>
                                                    @else
                                                        <span class="badge bg-danger">Unpaid</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    @if($p->payment_status === 'paid' && $p->generated_document_id)
                                                        <a href="{{ route('member.penalties.receipt', $p) }}"
                                                           class="btn btn-sm btn-outline-secondary">
                                                            <i class="fas fa-receipt"></i> View
                                                        </a>
                                                        <a href="{{ route('member.documents.print', $p->generated_document_id) }}"
                                                           class="btn btn-sm btn-outline-secondary"
                                                           target="_blank">
                                                            <i class="fas fa-print"></i>
                                                        </a>
                                                        <a href="{{ route('member.documents.download', $p->generated_document_id) }}"
                                                           class="btn btn-sm btn-outline-secondary">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    @else
                                                        <span class="text-muted small">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- PAY BAR --}}
                    <div id="payBar" class="card border-0 shadow-sm mt-3 d-none">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">Selected Total</small>
                                <h4 id="selectedTotal" class="fw-bold mb-0">₦0.00</h4>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                <i class="fas fa-credit-card"></i> Pay Selected
                            </button>
                        </div>
                    </div>

                </form>

            @endif

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.penalty-checkbox');
        const payBar = document.getElementById('payBar');
        const totalEl = document.getElementById('selectedTotal');

        function updateTotal() {
            let total = 0;
            let anyChecked = false;

            checkboxes.forEach(function (cb) {
                if (cb.checked) {
                    total += parseFloat(cb.dataset.amount || 0);
                    anyChecked = true;
                }
            });

            totalEl.textContent = '₦' + total.toLocaleString('en-NG', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            if (anyChecked) {
                payBar.classList.remove('d-none');
            } else {
                payBar.classList.add('d-none');
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateTotal();
            });
        }

        checkboxes.forEach(function (cb) {
            cb.addEventListener('change', updateTotal);
        });
    });
</script>

@endsection
