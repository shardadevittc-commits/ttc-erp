
@extends('layouts.app')

@section('title', 'Accounts Dashboard | TTC Robotronics')

@section('content')

<style>
    .account-dashboard {
        --navy: #172554;
        --muted: #64748b;
        --border: #e8edf5;
        color: #1e293b;
    }

    .account-dashboard .dashboard-heading {
        background: linear-gradient(115deg, #172554, #263f78);
        border-radius: 16px;
        padding: 25px 28px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .account-dashboard .dashboard-heading:after {
        content: "";
        position: absolute;
        width: 220px;
        height: 220px;
        border: 35px solid rgba(255,255,255,.06);
        border-radius: 50%;
        right: 35px;
        top: -110px;
    }

    .account-dashboard .dashboard-heading h4 {
        font-weight: 700;
        margin-bottom: 5px;
    }

    .account-dashboard .dashboard-heading p {
        color: #cbd5e1;
        margin: 0;
        font-size: 13px;
    }

    .account-dashboard .period-label {
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.18);
        padding: 9px 14px;
        border-radius: 9px;
        color: white;
        font-size: 12px;
        position: relative;
        z-index: 1;
    }

    .account-dashboard .stat-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 13px;
        padding: 19px;
        height: 100%;
        transition: .2s ease;
        box-shadow: 0 3px 12px rgba(15, 23, 42, .035);
    }

    .account-dashboard .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 22px rgba(15, 23, 42, .08);
    }

    .account-dashboard .stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 17px;
    }

    .account-dashboard .stat-title {
        color: var(--muted);
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .account-dashboard .stat-icon {
        width: 39px;
        height: 39px;
        border-radius: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    .account-dashboard .stat-value {
        font-size: 23px;
        font-weight: 750;
        color: #172554;
        margin: 0;
        letter-spacing: -.5px;
        overflow-wrap: anywhere;
    }

    .account-dashboard .stat-note {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 5px;
    }

    .account-dashboard .icon-blue { background: #eaf1ff; color: #3563d9; }
    .account-dashboard .icon-green { background: #e6f8ef; color: #159957; }
    .account-dashboard .icon-purple { background: #f1eaff; color: #8755d9; }
    .account-dashboard .icon-orange { background: #fff2e5; color: #e58a27; }
    .account-dashboard .icon-red { background: #ffeded; color: #dc5555; }
    .account-dashboard .icon-cyan { background: #e5f8fc; color: #159bb8; }

    .account-dashboard .section-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: 0 3px 12px rgba(15, 23, 42, .035);
        overflow: hidden;
    }

    .account-dashboard .section-header {
        padding: 18px 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .account-dashboard .section-header h6 {
        margin: 0;
        color: #172554;
        font-weight: 700;
        font-size: 14px;
    }

    .account-dashboard .section-header small {
        color: #94a3b8;
        font-size: 11px;
    }

    .account-dashboard .section-body {
        padding: 20px;
    }

    .account-dashboard .month-card {
        border-radius: 13px;
        padding: 18px 20px;
        height: 100%;
        border: 1px solid var(--border);
        background: #fff;
    }

    .account-dashboard .month-card.highlight {
        background: #f0f5ff;
        border-color: #dbe6ff;
    }

    .account-dashboard .month-label {
        color: #64748b;
        font-size: 12px;
        font-weight: 600;
    }

    .account-dashboard .month-value {
        font-size: 23px;
        font-weight: 750;
        color: #172554;
        margin-top: 10px;
    }

    .account-dashboard .month-sub {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 4px;
    }

    .account-dashboard .invoice-table {
        margin: 0;
        white-space: nowrap;
    }

    .account-dashboard .invoice-table thead th {
        background: #f8fafc;
        color: #64748b;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .45px;
        padding: 13px 12px;
        border-bottom: 1px solid var(--border);
    }

    .account-dashboard .invoice-table tbody td {
        padding: 13px 12px;
        font-size: 12px;
        color: #475569;
        border-color: #f1f5f9;
        vertical-align: middle;
    }

    .account-dashboard .invoice-table tbody tr:hover {
        background: #f8faff;
    }

    .account-dashboard .invoice-number {
        color: #3159b7;
        font-weight: 700;
    }

    .account-dashboard .amount-text {
        font-weight: 650;
        color: #172554 !important;
    }

    .account-dashboard .view-btn {
        border-radius: 7px;
        font-size: 11px;
        padding: 5px 11px;
        font-weight: 600;
    }

    .account-dashboard .table-footer {
        padding: 14px 18px;
        border-top: 1px solid var(--border);
    }

    .account-dashboard .table-footer nav p {
        margin-bottom: 0;
    }

    .account-dashboard .table-footer .pagination {
        margin-bottom: 0;
    }

    .account-dashboard .summary-badge {
        font-size: 11px;
        padding: 7px 11px;
        border-radius: 7px;
        background: #edf3ff;
        color: #3159b7;
        font-weight: 700;
    }

    @media(max-width: 767px) {
        .account-dashboard .dashboard-heading {
            padding: 20px;
        }

        .account-dashboard .stat-value,
        .account-dashboard .month-value {
            font-size: 19px;
        }

        .account-dashboard .section-body {
            padding: 14px;
        }
    }
</style>

<div class="container-fluid py-3 account-dashboard">

    {{-- HEADER --}}
    <div class="dashboard-heading mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 position-relative">
            <div>
                <h4><i class="fas fa-chart-pie me-2"></i> Accounts Dashboard</h4>
                <p>Financial performance, invoice analytics & tax overview</p>
            </div>

            <div class="period-label">
                <i class="far fa-calendar-alt me-2"></i>
                {{ now()->format('d M Y') }}
            </div>
        </div>
    </div>

    {{-- MAIN FINANCIAL KPIs --}}
    <div class="row g-3 mb-4">

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-top">
                    <span class="stat-title">Total Invoices</span>
                    <div class="stat-icon icon-blue"><i class="fas fa-file-invoice"></i></div>
                </div>
                <h4 class="stat-value">{{ number_format($totalInvoices) }}</h4>
                <div class="stat-note">Total invoices recorded</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-top">
                    <span class="stat-title">Invoice Amount</span>
                    <div class="stat-icon icon-green"><i class="fas fa-rupee-sign"></i></div>
                </div>
                <h4 class="stat-value">₹{{ number_format($totalInvoiceAmount, 2) }}</h4>
                <div class="stat-note">Total invoice value</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-top">
                    <span class="stat-title">Taxable Amount</span>
                    <div class="stat-icon icon-purple"><i class="fas fa-calculator"></i></div>
                </div>
                <h4 class="stat-value">₹{{ number_format($totalTaxable, 2) }}</h4>
                <div class="stat-note">Total taxable value</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-top">
                    <span class="stat-title">Total Discount</span>
                    <div class="stat-icon icon-orange"><i class="fas fa-tags"></i></div>
                </div>
                <h4 class="stat-value">₹{{ number_format($totalDiscount, 2) }}</h4>
                <div class="stat-note">Total cash discount</div>
            </div>
        </div>

    </div>

    {{-- TAX SUMMARY --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="fw-bold mb-0" style="color:#172554;">
            <i class="fas fa-landmark me-2 text-primary"></i>Tax Summary
        </h6>
        <small class="text-muted">Overall tax details</small>
    </div>

    <div class="row g-3 mb-4">

        <div class="col-lg-4 col-md-4">
            <div class="stat-card">
                <div class="stat-top">
                    <span class="stat-title">Total GST</span>
                    <div class="stat-icon icon-blue"><i class="fas fa-percent"></i></div>
                </div>
                <h4 class="stat-value">₹{{ number_format($totalGst, 2) }}</h4>
                <div class="stat-note">Goods & Services Tax</div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4">
            <div class="stat-card">
                <div class="stat-top">
                    <span class="stat-title">Total TDS</span>
                    <div class="stat-icon icon-green"><i class="fas fa-file-invoice-dollar"></i></div>
                </div>
                <h4 class="stat-value">₹{{ number_format($totalTds, 2) }}</h4>
                <div class="stat-note">Tax deducted at source</div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4">
            <div class="stat-card">
                <div class="stat-top">
                    <span class="stat-title">Total TCS</span>
                    <div class="stat-icon icon-orange"><i class="fas fa-wallet"></i></div>
                </div>
                <h4 class="stat-value">₹{{ number_format($totalTcs, 2) }}</h4>
                <div class="stat-note">Tax collected at source</div>
            </div>
        </div>

    </div>

    {{-- MONTHLY PERFORMANCE --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="fw-bold mb-0" style="color:#172554;">
            <i class="fas fa-calendar-alt me-2 text-primary"></i>Monthly Performance
        </h6>
    </div>

    <div class="row g-3 mb-4">

        <div class="col-lg-4 col-md-4">
            <div class="month-card">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="month-label">Current Month Invoices</span>
                    <i class="fas fa-file-alt text-primary"></i>
                </div>
                <div class="month-value">{{ number_format($currentMonthInvoices) }}</div>
                <div class="month-sub">{{ now()->format('F Y') }}</div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4">
            <div class="month-card highlight">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="month-label">Current Month Amount</span>
                    <i class="fas fa-chart-line text-primary"></i>
                </div>
                <div class="month-value">₹{{ number_format($currentMonthAmount, 2) }}</div>
                <div class="month-sub">Invoice value this month</div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4">
            <div class="month-card">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="month-label">Previous Month Amount</span>
                    <i class="fas fa-history text-secondary"></i>
                </div>
                <div class="month-value">₹{{ number_format($previousMonthAmount, 2) }}</div>
                <div class="month-sub">{{ now()->copy()->subMonth()->format('F Y') }}</div>
            </div>
        </div>

    </div>

    {{-- CHART --}}
    <div class="section-card mb-4">
        <div class="section-header">
            <div>
                <h6><i class="fas fa-chart-bar me-2 text-primary"></i>Invoice Analytics</h6>
                <small>Monthly invoice amount and invoice count</small>
            </div>
            <span class="summary-badge">Last 6 Months</span>
        </div>

        <div class="section-body">
            <div style="height:320px; position:relative;">
                <canvas id="invoiceChart"></canvas>
            </div>
        </div>
    </div>

    {{-- RECENT INVOICES --}}
    <div class="section-card">

        <div class="section-header">
            <div>
                <h6><i class="fas fa-file-invoice me-2 text-primary"></i>Recent Invoices</h6>
                <small>Latest invoice records and financial details</small>
            </div>

            <span class="summary-badge">
                {{ number_format($totalInvoices) }} Total
            </span>
        </div>

        <div class="table-responsive">
            <table class="table invoice-table align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Invoice No.</th>
                        <th>Date</th>
                        <th>Party Name</th>
                        <th>Gate ID</th>
                        <th class="text-end">Invoice Amount</th>
                        <th class="text-end">Taxable</th>
                        <th class="text-end">GST</th>
                        <th class="text-end">TDS</th>
                        <th class="text-end">TCS</th>
                        <th class="text-end">Discount</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($invoices as $key => $invoice)
                        <tr>
                            <td>{{ $invoices->firstItem() + $key }}</td>

                            <td>
                                <span class="invoice-number">
                                    {{ $invoice->invoice_no ?? '-' }}
                                </span>
                            </td>

                            <td>
                                {{ $invoice->invoice_date
                                    ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y')
                                    : '-' }}
                            </td>

                            <td class="fw-semibold text-dark">
                                {{ $invoice->billed_to
                                    ?: $invoice->gate?->customer?->company_name
                                    ?: '-' }}
                            </td>

                            <td>{{ $invoice->gate_id ?? '-' }}</td>

                            <td class="text-end amount-text">
                                ₹{{ number_format((float) $invoice->total_amount, 2) }}
                            </td>

                            <td class="text-end">
                                ₹{{ number_format((float) $invoice->taxable_amount, 2) }}
                            </td>

                            <td class="text-end">
                                ₹{{ number_format((float) $invoice->gst_amount, 2) }}
                            </td>

                            <td class="text-end">
                                ₹{{ number_format((float) $invoice->tds_amount, 2) }}
                            </td>

                            <td class="text-end">
                                ₹{{ number_format((float) $invoice->tcs_amount, 2) }}
                            </td>

                            <td class="text-end">
                                ₹{{ number_format((float) $invoice->cash_discount_amount, 2) }}
                            </td>

                            <td class="text-center">
                                <a href="{{ route('accounts.invoice.bill', ['gate' => $invoice->gate_id]) }}"
                                   class="btn btn-outline-primary view-btn"
                                   target="_blank">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center py-5">
                                <i class="fas fa-file-invoice fa-2x text-muted mb-3 d-block"></i>
                                <span class="text-muted">No invoices found.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            {{ $invoices->links() }}
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const canvas = document.getElementById('invoiceChart');

    if (!canvas || typeof Chart === 'undefined') return;

    new Chart(canvas, {
        type: 'bar',

        data: {
            labels: @json($chartLabels),

            datasets: [
                {
                    label: 'Invoice Amount',
                    data: @json($chartAmounts),
                    backgroundColor: 'rgba(59, 130, 246, 0.75)',
                    borderColor: '#3b82f6',
                    borderWidth: 1,
                    borderRadius: 6,
                    barPercentage: 0.55,
                    yAxisID: 'y'
                },
                {
                    label: 'Invoice Count',
                    data: @json($chartCounts),
                    type: 'line',
                    borderColor: '#16a36a',
                    backgroundColor: '#16a36a',
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#16a36a',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    tension: 0.35,
                    fill: false,
                    yAxisID: 'y1'
                }
            ]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            interaction: {
                mode: 'index',
                intersect: false
            },

            plugins: {
                legend: {
                    position: 'top',
                    align: 'end',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 8,
                        padding: 18,
                        font: { size: 11 }
                    }
                },

                tooltip: {
                    backgroundColor: '#172554',
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.yAxisID === 'y') {
                                return ' Amount: ₹' +
                                    Number(context.raw).toLocaleString('en-IN');
                            }

                            return ' Invoices: ' + context.raw;
                        }
                    }
                }
            },

            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        color: '#64748b',
                        font: { size: 11 }
                    }
                },

                y: {
                    beginAtZero: true,
                    position: 'left',
                    grid: { color: '#eef2f7' },
                    title: {
                        display: true,
                        text: 'Invoice Amount (₹)',
                        color: '#64748b'
                    },
                    ticks: {
                        color: '#64748b',
                        callback: function(value) {
                            return '₹' + Number(value).toLocaleString('en-IN');
                        }
                    }
                },

                y1: {
                    beginAtZero: true,
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    title: {
                        display: true,
                        text: 'Invoice Count',
                        color: '#64748b'
                    },
                    ticks: {
                        precision: 0,
                        color: '#64748b'
                    }
                }
            }
        }
    });

});
</script>
@endpush
