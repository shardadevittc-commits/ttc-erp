@extends('layouts.app')

@section('title', 'Accounts')

@section('content')
<div class="container-fluid py-3">
    <div class="page-header mb-3">
        <div class="page-header__inner">
            <div class="page-header__content">
                <div class="page-header__title">
                    <h6>Dispatch Material Details</h6>
                </div>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-home me-1"></i>Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <h5 class="mb-0">Dispatch Material Details</h5>
                <small class="text-muted">{{ $listing->total() }} vehicles</small>
            </div>

            <form method="GET" action="{{ route('accounts') }}" class="row g-3 align-items-end mb-3">
                <div class="col-12 col-md-3">
                    <label class="form-label" for="party_name">Party Name</label>
                    <input type="text" class="form-control" id="party_name" name="party_name"
                        value="{{ request('party_name') }}" placeholder="Search Party Name">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label" for="date_from">Date From</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label" for="date_to">Date To</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-6 col-md-1">
                    <label class="form-label" for="token_no">Token No.</label>
                    <input type="text" class="form-control" id="token_no" name="token_no"
                        value="{{ request('token_no') }}" placeholder="Token">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label" for="vehicle_no">Vehicle No.</label>
                    <input type="text" class="form-control" id="vehicle_no" name="vehicle_no"
                        value="{{ request('vehicle_no') }}" placeholder="Vehicle">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label" for="status">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>
                                {{ ucwords(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Apply Filter</button>
                    <a href="{{ route('accounts') }}" class="btn btn-outline-secondary" title="Reset">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>SR No.</th>
                            <th>Date</th>
                            <th>Party Name</th>
                            <th>Token No.</th>
                            <th>Vehicle No.</th>
                            <th>Sale Order</th>
                            <th>Material / Size</th>
                            <th>Grade / Brand</th>
                            <th class="text-end">Actual Qty</th>
                            <th class="text-end">Dispatch Qty</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($listing as $k => $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->created_at ? _dt($item->created_at) : '-' }}</td>
                                <td>{{ $item->company_name ?: '-' }}</td>
                                <td>{{ $item->tokenno ?: '-' }}</td>
                                <td>{{ $item->truckno ?: '-' }}</td>
                                <td>
                                    @foreach ($item->materials as $material)
                                        <div @if (!$loop->last) class="pb-1 mb-1 border-bottom" @endif>
                                            #{{ $material->sale_order_id }}
                                            @if ($material->customer_po_no)
                                                <small class="d-block text-muted">{{ $material->customer_po_no }}</small>
                                            @endif
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($item->materials as $material)
                                        <div @if (!$loop->last) class="pb-1 mb-1 border-bottom" @endif>
                                            {{ $material->product_name ?: '-' }}
                                            <small class="d-block text-muted">{{ $material->size_name ?: $material->size_unit ?: '-' }}</small>
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($item->materials as $material)
                                        <div @if (!$loop->last) class="pb-1 mb-1 border-bottom" @endif>
                                            {{ $material->grade_name ?: '-' }}
                                            <small class="d-block text-muted">{{ $material->brand_name ?: '-' }}</small>
                                        </div>
                                    @endforeach
                                </td>
                                <td class="text-end">
                                    @foreach ($item->materials as $material)
                                        <div @if (!$loop->last) class="pb-1 mb-1 border-bottom" @endif>
                                            {{ number_format((float) ($material->actual_qty ?? 0), 3) }} KG
                                        </div>
                                    @endforeach
                                </td>
                                <td class="text-end">
                                    @foreach ($item->materials as $material)
                                        @php $qtyUnit = (int) $material->qty_type === 2 ? 'KG' : 'Tons'; @endphp
                                        <div @if (!$loop->last) class="pb-1 mb-1 border-bottom" @endif>
                                            {{ number_format((float) ($material->dispatch_qty ?? 0), 3) }} {{ $qtyUnit }}
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($item->materials as $material)
                                        <div class="text-capitalize @if (!$loop->last) pb-1 mb-1 border-bottom @endif">
                                            {{ str_replace('_', ' ', $material->status ?: '-') }}
                                        </div>
                                    @endforeach
                                </td>

                                <td>
                                    @if ($item->invoice_submitted)
                                        <a href="{{ route('accounts.addInvoice', ['gate' => $item->gate_id]) }}"
                                            class="btn btn-sm btn-outline-primary" title="View Invoice"
                                            aria-label="View Invoice">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('accounts.addInvoice', ['gate' => $item->gate_id]) }}"
                                            class="btn btn-sm btn-outline-primary" title="Add Invoice">
                                            Add Invoice
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">No dispatch material details found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $listing->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
</div>
@endsection
