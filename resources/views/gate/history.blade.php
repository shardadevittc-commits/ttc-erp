
@extends('layouts.app')

@section('title', 'History Gate Module | TTC Robotronics')

@section('content')

<div class="container-fluid py-3">

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Gate Check Out History</h4>
            <p class="text-muted mb-0">All checked-out vehicle entries</p>
        </div>

        <span class="badge bg-success fs-6">
            Total: {{ $materialIns->total() }}
        </span>
    </div>

    <div class="card shadow-sm border-0">

        {{-- Server-side Search Form --}}
        {{-- <div class="card-header bg-white py-3">
            <form action="{{ route('gate.history') }}" method="GET">
                <div class="row g-3 align-items-end">

                    <div class="col-lg-4 col-md-6">
                        <label for="search" class="form-label">
                            Search Vehicle ID / Customer
                        </label>

                        <input
                            type="text"
                            name="search"
                            id="search"
                            class="form-control"
                            placeholder="Enter vehicle ID or customer name"
                            value="{{ request('search') }}"
                        >
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label for="from_date" class="form-label">From Date</label>

                        <input
                            type="date"
                            name="from_date"
                            id="from_date"
                            class="form-control"
                            value="{{ request('from_date') }}"
                        >
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label for="to_date" class="form-label">To Date</label>

                        <input
                            type="date"
                            name="to_date"
                            id="to_date"
                            class="form-control"
                            value="{{ request('to_date') }}"
                        >
                    </div>

                    <div class="col-lg-2 col-md-6 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Search
                        </button>

                        <a href="{{ route('gate.history') }}"
                           class="btn btn-secondary">
                            Reset
                        </a>
                    </div>

                </div>
            </form>
        </div> --}}

        {{-- History Table --}}
        <div class="card-body">

            {{-- Live Table Filters --}}
            <div class="row g-3 mb-3">

                <div class="col-lg-3 col-md-6">
                    <label for="filterVehicle" class="form-label">
                        Vehicle ID
                    </label>
                    <input
                        type="text"
                        id="filterVehicle"
                        class="form-control"
                        placeholder="Search Vehicle ID"
                    >
                </div>

                <div class="col-lg-3 col-md-6">
                    <label for="filterCustomer" class="form-label">
                        Customer Name
                    </label>
                    <input
                        type="text"
                        id="filterCustomer"
                        class="form-control"
                        placeholder="Search Customer"
                    >
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="filterTruck" class="form-label">
                        Truck No.
                    </label>
                    <input
                        type="text"
                        id="filterTruck"
                        class="form-control"
                        placeholder="Search Truck No."
                    >
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="filterFormlist" class="form-label">
                        Formlist
                    </label>
                    <input
                        type="text"
                        id="filterFormlist"
                        class="form-control"
                        placeholder="Search Formlist"
                    >
                </div>

                <div class="col-lg-2 col-md-6">
                    <label for="filterStatus" class="form-label">
                        Status
                    </label>
                    <select id="filterStatus" class="form-select">
                        <option value="">All Status</option>
                        <option value="checked out">Checked Out</option>
                        <option value="not checked out">Not Checked Out</option>
                    </select>
                </div>

            </div>

            {{-- Filter Result Count --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <small class="text-muted">
                    Showing <span id="visibleCount">{{ $materialIns->count() }}</span>
                    records on this page
                </small>

                <button type="button" id="clearFilters"
                        class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-times"></i> Clear Table Filters
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0"
                       id="historyTable">

                    <thead class="table-dark">
                        <tr>
                            <th style="width: 70px;">Sr. No.</th>
                            <th>Vehicle ID</th>
                            <th>Customer Name</th>
                            <th>Truck No.</th>
                            <th>Formlist</th>
                            <th style="width: 150px;">Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($materialIns as $item)
                            <tr>
                                <td>
                                    {{ $materialIns->firstItem() + $loop->index }}
                                </td>

                                <td class="vehicle-id">
                                    <strong>{{ $item->id }}</strong>
                                </td>

                                <td class="customer-name">
                                    {{ $item->customer->company_name ?? 'N/A' }}
                                </td>

                                <td class="truck-no">
                                    {{ $item->truckno ?? 'N/A' }}
                                </td>

                                <td class="formlist">
                                    {{ $item->formlist ?? 'N/A' }}
                                </td>

                                <td class="checkout-status">
                                    @if($item->check_out)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle"></i>
                                            Checked Out
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            Not Checked Out
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyRow">
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-history fa-2x text-muted mb-3"></i>

                                    <h6 class="text-muted mt-2">
                                        No checkout history found
                                    </h6>

                                    <p class="text-muted mb-0">
                                        Try changing your search or date filters.
                                    </p>
                                </td>
                            </tr>
                        @endforelse

                        <tr id="noFilterResults" style="display: none;">
                            <td colspan="6" class="text-center py-4 text-muted">
                                No matching records found.
                            </td>
                        </tr>
                    </tbody>

                </table>
            </div>

            {{-- Pagination --}}
            @if($materialIns->hasPages())
                <div class="d-flex flex-column flex-md-row
                            justify-content-between align-items-center
                            mt-4 gap-3">

                    <div class="text-muted small">
                        Showing
                        {{ $materialIns->firstItem() ?? 0 }}
                        to
                        {{ $materialIns->lastItem() ?? 0 }}
                        of
                        {{ $materialIns->total() }}
                        entries
                    </div>

                    <div>
                        {{ $materialIns->withQueryString()->links() }}
                    </div>

                </div>
            @endif

        </div>
    </div>
</div>

{{-- Live Table Search Script --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const filterVehicle = document.getElementById('filterVehicle');
    const filterCustomer = document.getElementById('filterCustomer');
    const filterTruck = document.getElementById('filterTruck');
    const filterFormlist = document.getElementById('filterFormlist');
    const filterStatus = document.getElementById('filterStatus');

    const visibleCount = document.getElementById('visibleCount');
    const noFilterResults = document.getElementById('noFilterResults');
    const clearFilters = document.getElementById('clearFilters');

    const rows = Array.from(
        document.querySelectorAll('#historyTable tbody tr')
    ).filter(row => row.querySelector('.vehicle-id'));

    function normalize(value) {
        return (value || '').trim().toLowerCase();
    }

    function applyFilters() {
        const vehicleValue = normalize(filterVehicle.value);
        const customerValue = normalize(filterCustomer.value);
        const truckValue = normalize(filterTruck.value);
        const formlistValue = normalize(filterFormlist.value);
        const statusValue = normalize(filterStatus.value);

        let visible = 0;

        rows.forEach(function (row) {
            const vehicle = normalize(
                row.querySelector('.vehicle-id')?.textContent
            );

            const customer = normalize(
                row.querySelector('.customer-name')?.textContent
            );

            const truck = normalize(
                row.querySelector('.truck-no')?.textContent
            );

            const formlist = normalize(
                row.querySelector('.formlist')?.textContent
            );

            const status = normalize(
                row.querySelector('.checkout-status')?.textContent
            );

            const matches =
                vehicle.includes(vehicleValue) &&
                customer.includes(customerValue) &&
                truck.includes(truckValue) &&
                formlist.includes(formlistValue) &&
                status.includes(statusValue);

            row.style.display = matches ? '' : 'none';

            if (matches) {
                visible++;
            }
        });

        visibleCount.textContent = visible;

        noFilterResults.style.display =
            rows.length > 0 && visible === 0 ? '' : 'none';
    }

    [
        filterVehicle,
        filterCustomer,
        filterTruck,
        filterFormlist
    ].forEach(function (input) {
        input.addEventListener('input', applyFilters);
    });

    filterStatus.addEventListener('change', applyFilters);

    clearFilters.addEventListener('click', function () {
        filterVehicle.value = '';
        filterCustomer.value = '';
        filterTruck.value = '';
        filterFormlist.value = '';
        filterStatus.value = '';

        applyFilters();
    });

});
</script>

@endsection
