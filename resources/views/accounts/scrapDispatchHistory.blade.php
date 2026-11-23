@extends('layouts.app')

@section('title', 'Scrap Dispatch History')

@section('content')
<div class="container-fluid py-3">
    <div class="page-header mb-3">
        <div class="page-header__inner">
            <div class="page-header__content">
                <div class="page-header__title"><h6>Scrap Dispatch History</h6></div>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('scrapDispatch.index') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>New Scrap Dispatch
                </a>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('scrapDispatch.history') }}" class="row g-3 align-items-end mb-3">
                <div class="col-6 col-lg-2">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>
                <div class="col-6 col-lg-2">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>
                <div class="col-12 col-lg-3">
                    <label for="buyer" class="form-label">Buyer</label>
                    <input type="text" id="buyer" name="buyer" value="{{ request('buyer') }}" class="form-control" placeholder="Search buyer">
                </div>
                <div class="col-8 col-lg-3">
                    <label for="vehicle_no" class="form-label">Vehicle No.</label>
                    <input type="text" id="vehicle_no" name="vehicle_no" value="{{ request('vehicle_no') }}" class="form-control" placeholder="Search vehicle">
                </div>
                <div class="col-4 col-lg-2">
                    <label for="per_page" class="form-label">Rows</label>
                    <select id="per_page" name="per_page" class="form-select">
                        @foreach ([10, 25, 50, 100] as $limit)
                            <option value="{{ $limit }}" @selected((int) request('per_page', 25) === $limit)>{{ $limit }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Apply Filters</button>
                    <a href="{{ route('scrapDispatch.history') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Buyer</th>
                            <th>Vehicle</th>
                            <th>Invoice</th>
                            <th>Scrap Dispatched (kg)</th>
                            <th class="text-end">Amount</th>
                            <th>Payment</th>
                            <th>Documents</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($listing as $dispatch)
                            @php
                                $scrapLines = collect($categories)->map(function ($category, $key) use ($dispatch) {
                                    $quantity = (float) ($dispatch->{$key . '_dispatch'} ?? 0);
                                    if ($quantity <= 0) {
                                        return null;
                                    }

                                    return $category['label'] . ': ' . number_format($quantity, 3)
                                        . ' kg @ ' . number_format((float) ($dispatch->{$key . '_rate'} ?? 0), 2)
                                        . ' = ' . number_format((float) ($dispatch->{$key . '_total'} ?? 0), 2);
                                })->filter();
                            @endphp
                            <tr>
                                <td>{{ optional($dispatch->created_at)->format('d-m-Y') }}</td>
                                <td>{{ $dispatch->buyer_name ?: '—' }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $dispatch->vehicle_no ?: '—' }}</div>
                                    <small class="text-muted">{{ $dispatch->vehicle_type ?: '' }}</small>
                                </td>
                                <td>{{ $dispatch->invoice_no ?: '—' }}<br><small class="text-muted">{{ $dispatch->invoice_date ? \Illuminate\Support\Carbon::parse($dispatch->invoice_date)->format('d-m-Y') : '' }}</small></td>
                                <td>
                                    @if ($scrapLines->isNotEmpty())
                                        <ul class="mb-0 ps-3">
                                            @foreach ($scrapLines as $line)
                                                <li>{{ $line }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        —
                                    @endif
                                    <small class="d-block text-muted mt-1">
                                        First: {{ number_format((float) ($dispatch->first_weight ?? 0), 3) }} kg;
                                        Last: {{ number_format((float) ($dispatch->last_weight ?? 0), 3) }} kg;
                                        Net: {{ number_format((float) ($dispatch->actual_weight ?? 0), 3) }} kg
                                    </small>
                                </td>
                                <td class="text-end">
                                    <div>{{ number_format((float) ($dispatch->final_amt ?? 0), 2) }}</div>
                                    <small class="text-muted d-block">Invoice: {{ number_format((float) ($dispatch->invoice_amt ?? 0), 2) }}</small>
                                    <small class="text-muted d-block">GST: {{ number_format((float) ($dispatch->invoice_gst ?? 0), 2) }}</small>
                                    <small class="text-muted d-block">Misc: {{ number_format((float) ($dispatch->misc_amt ?? 0), 2) }}</small>
                                </td>
                                <td>
                                    {{ $dispatch->mode_of_payment ?: '—' }}
                                    @if ($dispatch->remarks)
                                        <small class="d-block text-muted">{{ $dispatch->remarks }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if ($dispatch->weight_slip)
                                        <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($dispatch->weight_slip) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary mb-1">
                                            <i class="fa-solid fa-weight-scale me-1"></i>Weight Slip
                                        </a>
                                    @endif
                                    @if ($dispatch->invoice_no)
                                        <a href="{{ route('scrapDispatch.invoice', ['scrapDispatch' => $dispatch->id]) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-eye me-1"></i>Invoice
                                        </a>
                                    @endif
                                    @if (! $dispatch->weight_slip && ! $dispatch->invoice_no)
                                        —
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No scrap dispatch records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $listing->links() }}</div>
        </div>
    </div>
</div>
@endsection
