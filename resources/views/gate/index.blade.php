@extends('layouts.app')

@section('title', 'Manage Vehicle Entries | TTC Robotronics')

@section('content')

<div class="page-header">
    <div class="page-header__inner">
        <div class="page-header__content">
            <div class="page-header__title">
                <h6>Manage Vehicle Entries</h6>
            </div>
        </div>

        <div class="page-header__actions">
            <a href="{{ route('material_in') }}"
               class="header-btn header-btn--primary">
                <i class="fas fa-plus"></i> New Vehicle Entry
            </a>
        </div>
    </div>
</div>

<div class="content_area">
    <div class="flex-grow-1 container-p-y">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close"
                    data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close"
                    data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card listing-block">
            <div class="card-header">
                <div class="heading">
                    <h5 class="mb-0">Vehicle Entry Listing</h5>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Party Name</th>
                                <th>Form List</th>
                                <th>Truck No.</th>
                                <th>Transport Name</th>
                                <th>No. Of Persons</th>
                                <th>Driver Name</th>
                                <th>Contact No.</th>
                                <th>Vehicle Entry Slip</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($materialIns as $item)
                                <tr>
                                    <td>
                                        {{ $materialIns->firstItem() + $loop->index }}
                                    </td>

                                    <td>
                                        {{ $item->customer?->company_name ?? 'N/A' }}
                                    </td>

                                    <td>
                                        {{ $item->formlist ?? '-' }}
                                    </td>

                                    <td>
                                        <span class="fw-semibold">
                                            {{ $item->truckno }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $item->transportname ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $item->noofpersons ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $item->drivername ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $item->contactno ?? '-' }}
                                    </td>

                                    <td>
                                        @if($item->vehicleentryslip)
                                            <a href="{{ asset('storage/' . $item->vehicleentryslip) }}"
                                               target="_blank"
                                               rel="noopener noreferrer"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-file-alt"></i>
                                                View Slip
                                            </a>
                                        @else
                                            <span class="text-muted">No File</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if(!in_array($item->id, $weighedIds))
                                                <a href="{{ route('material_in', ['id' => $item->id]) }}" class="btn btn-link text-warning p-0" title="Edit">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>


                                                <form action="{{ route('material_in.delete', $item->id) }}" method="POST" class="m-0">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                        class="btn btn-link text-danger p-0"
                                                        title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this vehicle entry?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted">Already Weighed</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                        <p class="mb-0">
                                            No Material In records found.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-3 py-3">
                    {{ $materialIns->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
