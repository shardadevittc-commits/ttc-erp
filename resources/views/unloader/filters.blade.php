<button class="btn btn-outline-primary dropdown-toggle" type="button" id="historyFilterBtn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
    <i class="fas fa-filter me-1"></i>
    Filter

    @if(request()->filled('customer_id') || request()->filled('from_date') || request()->filled('to_date'))
        <span class="badge bg-primary ms-1">Active</span>
    @endif

</button>

{{-- FILTER DROPDOWN --}}
<div class="dropdown-menu p-3 shadow" id="historyFilterDropdown" aria-labelledby="historyFilterBtn" style="width:320px;">

    <form method="GET"
        action="{{ route('unloader.vehicleHistory') }}">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Filter Vehicles</h6>

            <button type="button"
                    class="btn-close"
                    data-bs-toggle="dropdown"
                    aria-label="Close"></button>
        </div>

        {{-- Customer --}}
        <div class="mb-3">
            <label class="form-label">Customer</label>

            <select name="customer_id"
                    id="history_customer"
                    class="form-control select2-filter"
                    data-placeholder="Select Customer">

                <option value="">All Customers</option>

                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}"
                        {{ request('customer_id') == $customer->id ? 'selected' : '' }}>

                        {{ $customer->company_name }}

                    </option>
                @endforeach

            </select>
        </div>

        {{-- From Date --}}
        <div class="mb-3">
            <label class="form-label">From Date</label>

            <input type="date"
                name="from_date"
                class="form-control"
                value="{{ request('from_date') }}">
        </div>

        {{-- To Date --}}
        <div class="mb-3">
            <label class="form-label">To Date</label>

            <input type="date"
                name="to_date"
                class="form-control"
                value="{{ request('to_date') }}">
        </div>

        {{-- Preserve Search --}}
        <input type="hidden"
            name="search"
            value="{{ request('search') }}">

        <input type="hidden"
            name="per_page"
            value="{{ request('per_page', 20) }}">

        <input type="hidden"
            name="sort_by"
            value="{{ request('sort_by', 'id') }}">

        <input type="hidden"
            name="sort_order"
            value="{{ request('sort_order', 'desc') }}">

        <div class="d-flex justify-content-between gap-2">

            <a href="{{ route('unloader.vehicleHistory', [
                'search' => request('search'),
                'per_page' => request('per_page', 20)
            ]) }}"
            class="btn btn-light border w-50">
                Reset
            </a>

            <button type="submit"
                    class="btn btn-primary w-50">
                <i class="fas fa-check me-1"></i>
                Apply
            </button>

        </div>

    </form>

</div>