
@extends('layouts.app')

@section('content')

<div class="page-header">
    <div class="page-header__inner">
        <div class="page-header__content">
            <div class="page-header__title">
                <h6>Material Unload History</h6>
            </div>
        </div>
        <div class="page-header__actions">
             <div class="dropdown">
                @include("unloader.filters")
                
            </div>
        </div>
    </div>
</div>

{{-- Search & Filter Header --}}
<div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    {{-- LEFT SIDE FILTER --}}
    <div class="d-flex align-items-center gap-2">
        {{-- ENTRIES PER PAGE --}}
        {{-- <form method="GET" action="{{ route('unloader.vehicleHistory') }}" class="d-flex align-items-center gap-2">

            @foreach(request()->except('per_page', 'page') as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach

            <select name="per_page" class="form-select" style="width:85px;" onchange="this.form.submit()">

                @foreach([10,20,50,100] as $limit)
                    <option value="{{ $limit }}"
                        {{ request('per_page', 20) == $limit ? 'selected' : '' }}>
                        {{ $limit }}
                    </option>
                @endforeach
            </select>
        </form> --}}
    </div>


    {{-- RIGHT SIDE SEARCH --}}
    <div style="min-width:250px; max-width:350px; width:100%;">
        <form method="GET" action="{{ route('unloader.vehicleHistory') }}">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>

                <input type="search" name="search" class="form-control" placeholder="Search vehicle / customer..." value="{{ request('search') }}">

                {{-- Preserve Filters --}}
                <input type="hidden" name="customer_id" value="{{ request('customer_id') }}">

                <input type="hidden" name="from_date" value="{{ request('from_date') }}">
                <input type="hidden" name="to_date" value="{{ request('to_date') }}">
                <input type="hidden" name="per_page" value="{{ request('per_page', 20) }}">

                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>

    </div>

</div>


{{-- EXISTING TABLE BELOW THIS --}}
<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover mb-0">
            <thead class="thead-light">
                <tr>
                    <th>#</th>
                    <th>Vehicle No.</th>
                    <th>Customer</th>
                    <th>Total Materials</th>
                    <th>Total Received Qty</th>
                    <th>Last Receiving Date</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse($listing as $key => $item)

                    <tr>
                        <td>{{ $listing->firstItem() + $key }}</td>

                        <td>
                            <strong>{{ $item->truckno ?? '---' }}</strong>
                        </td>

                        <td>{{ $item->customer->company_name ?? '---' }}</td>

                        <td>{{ $item->total_materials }}</td>

                        <td>{{ $item->total_received_qty }}</td>

                        <td>
                            {{ $item->last_receiving_date
                                ? \Carbon\Carbon::parse($item->last_receiving_date)->format('d-m-Y h:i A')
                                : '---' }}
                        </td>

                        <td>
                            <a href="{{ route('unloader.vehicleHistoryView', $item->id) }}"
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="7" class="text-center py-4">
                            No vehicle history found.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap p-3">

        <div>
            Showing {{ $listing->firstItem() ?? 0 }}
            to {{ $listing->lastItem() ?? 0 }}
            of {{ $listing->total() }} entries
        </div>

        <div>
            {{ $listing->links('pagination::bootstrap-5') }}
        </div>

    </div>

</div>


@endsection
