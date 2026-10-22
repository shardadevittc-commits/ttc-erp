
@extends('layouts.app')

@section('title', 'Manage Material Out | TTC Robotronics')

@section('content')

<div class="page-header">
    <div class="page-header__inner">
        <div class="page-header__content">
            <div class="page-header__title">
                <h6>Manage Dispatch</h6>
            </div>
        </div>
    </div>
</div>

<div class="card mt-3 border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-1">Vehicle Entry Records</h5>
        <small class="text-muted">Total Records: {{ $gate->count() }}</small>
    </div>

    <div class="card-body">
        <div class="row g-4">

            @forelse($gate as $item)
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <div class="card vehicle-card h-100">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                <div>
                                    <small class="text-muted d-block">Truck Number</small>
                                    <h6 class="truck-number mb-0">
                                        {{ $item->truckno ?: '-' }}
                                    </h6>
                                </div>

                                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                    <span class="badge bg-primary">
                                        {{ $item->formlist ?: '-' }}
                                    </span>

                                    {{-- EDIT BUTTON --}}
                                    <button type="button"
                                        class="action-icon edit-icon"
                                        title="Edit Dispatch Plan"
                                        data-bs-toggle="offcanvas"
                                        data-bs-target="#editVehicleOffcanvas"
                                        data-id="{{ $item->id }}"
                                        data-truckno="{{ $item->truckno }}"
                                        data-entrytype="{{ $item->entrytype ?? 'Material Out' }}"
                                        data-vehicletype="{{ $item->vehicletype ?? '' }}"
                                        onclick="editVehicle(this)">
                                        <i class="fas fa-pen"></i>
                                    </button>

                                    {{-- DELETE BUTTON --}}
                                    <form action="{{ url('/manage-dispatch/' . $item->id) }}"
                                        method="POST"
                                        class="m-0"
                                        onsubmit="return confirm('Are you sure you want to delete this record?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            class="action-icon delete-icon"
                                            title="Delete Record">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="vehicle-detail">
                                <span><i class="fas fa-hashtag"></i> Token ID</span>
                                <strong>{{ $item->id }}</strong>
                            </div>

                            <div class="vehicle-detail">
                                <span><i class="fas fa-user"></i> Customer</span>
                                <strong>{{ $item->customer->company_name ?? '-' }}</strong>
                            </div>

                            <div class="vehicle-detail">
                                <span><i class="fas fa-user-tie"></i> Driver Name</span>
                                <strong>{{ $item->drivername ?: '-' }}</strong>
                            </div>

                            <div class="vehicle-detail">
                                <span><i class="fas fa-phone"></i> Contact Number</span>
                                <strong>{{ $item->contactno ?: '-' }}</strong>
                            </div>

                            <div class="vehicle-detail">
                                <span><i class="fas fa-truck"></i> Transport</span>
                                <strong>{{ $item->transportname ?: '-' }}</strong>
                            </div>

                            <div class="vehicle-detail">
                                <span><i class="fas fa-users"></i> No. of Persons</span>
                                <strong>{{ $item->noofpersons ?? '-' }}</strong>
                            </div>

                            <div class="vehicle-date">
                                <i class="far fa-calendar-alt me-1"></i>
                                {{ $item->created_at ? $item->created_at->format('d M Y, h:i A') : '-' }}
                            </div>

                            @if(!empty($item->vehicleentryslip))
                                <a href="{{ asset('storage/' . $item->vehicleentryslip) }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="btn btn-primary w-100 mt-3">
                                    <i class="fas fa-eye me-1"></i> View Entry Slip
                                </a>
                            @else
                                <button type="button" class="btn btn-secondary w-100 mt-3" disabled>
                                    <i class="fas fa-image me-1"></i> Image Not Available
                                </button>
                            @endif

                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-state">
                        <i class="fas fa-truck"></i>
                        <h5>No Material Out Records Found</h5>
                        <p class="text-muted mb-0">Vehicle entry records will appear here.</p>
                    </div>
                </div>
            @endforelse

        </div>
    </div>
</div>


{{-- RIGHT SIDE DISPATCH PLAN --}}
<div class="offcanvas offcanvas-end dispatch-offcanvas"
     tabindex="-1"
     id="editVehicleOffcanvas"
     aria-labelledby="dispatchPlanLabel">

    <div class="offcanvas-header border-bottom">
        <div>
            <h5 class="offcanvas-title" id="dispatchPlanLabel">
                <i class="fas fa-truck me-2 text-primary"></i>
                Dispatch Plan
            </h5>
            <small class="text-muted">Vehicle dispatch information</small>
        </div>

        <button type="button"
            class="btn-close"
            data-bs-dismiss="offcanvas"
            aria-label="Close"></button>
    </div>

    <div class="offcanvas-body">
        <form id="editVehicleForm" method="POST" action="">
            @csrf
            @method('PUT')

            <div class="table-responsive">
                <table class="table table-bordered dispatch-table">
                    <thead>
                        <tr>
                            <th>Token No.</th>
                            <th>Vehicle No.</th>
                            <th>Entry Type</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>
                                <input type="text"
                                    class="form-control"
                                    id="edit_token"
                                    name="id"
                                    readonly>
                            </td>

                            <td>
                                <input type="text"
                                    class="form-control"
                                    id="edit_truckno"
                                    name="truckno"
                                    required>
                            </td>

                            <td>
                                <select class="form-select"
                                    id="edit_entrytype"
                                    name="entrytype"
                                    required>
                                    <option value="">Select Entry Type</option>
                                    <option value="Material In">Material In</option>
                                    <option value="Material Out">Material Out</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>


<style>
    .vehicle-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 3px 12px rgba(0,0,0,.05);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .vehicle-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 22px rgba(0,0,0,.10);
    }

    .vehicle-card .card-body {
        padding: 18px;
    }

    .truck-number {
        font-size: 17px;
        font-weight: 700;
        color: #1f2937;
        overflow-wrap: anywhere;
    }

    .vehicle-detail {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        padding: 9px 0;
        border-bottom: 1px solid #f0f1f3;
        font-size: 12px;
    }

    .vehicle-detail span {
        color: #6b7280;
        flex-shrink: 0;
    }

    .vehicle-detail span i {
        width: 16px;
        margin-right: 3px;
        text-align: center;
    }

    .vehicle-detail strong {
        text-align: right;
        color: #374151;
        font-weight: 600;
        overflow-wrap: anywhere;
    }

    .vehicle-date {
        margin-top: 12px;
        font-size: 11px;
        color: #6b7280;
    }

    .vehicle-card .btn {
        font-size: 13px;
        padding: 9px 12px;
        border-radius: 6px;
    }

    .empty-state {
        padding: 60px 20px;
        text-align: center;
        background: #f9fafb;
        border: 1px dashed #d1d5db;
        border-radius: 10px;
    }

    .empty-state i {
        font-size: 48px;
        color: #9ca3af;
        margin-bottom: 16px;
    }

    .empty-state h5 {
        color: #374151;
    }

    .action-icon {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        border-radius: 7px;
        text-decoration: none;
        font-size: 13px;
        cursor: pointer;
        transition: all .2s ease;
        flex-shrink: 0;
    }

    .edit-icon {
        background: #eaf2ff;
        color: #0d6efd;
    }

    .edit-icon:hover {
        background: #0d6efd;
        color: #fff;
        transform: translateY(-2px);
    }

    .delete-icon {
        background: #fff0f0;
        color: #dc3545;
    }

    .delete-icon:hover {
        background: #dc3545;
        color: #fff;
        transform: translateY(-2px);
    }

    .action-icon:focus-visible {
        outline: 2px solid #6c757d;
        outline-offset: 2px;
    }

    .dispatch-offcanvas {
        width: 850px !important;
        max-width: 100%;
    }

    .dispatch-offcanvas .offcanvas-header {
        padding: 20px;
    }

    .dispatch-offcanvas .offcanvas-title {
        font-size: 20px;
        font-weight: 700;
        color: #1f2937;
    }

    .dispatch-offcanvas .offcanvas-body {
        padding: 20px;
    }

    .dispatch-table {
        min-width: 720px;
        margin-bottom: 0;
        vertical-align: middle;
    }

    .dispatch-table thead th {
        background: #f3f6fa;
        color: #374151;
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
        padding: 12px;
    }

    .dispatch-table tbody td {
        padding: 10px;
        vertical-align: middle;
    }

    .dispatch-table .form-control,
    .dispatch-table .form-select {
        min-width: 130px;
        font-size: 13px;
        padding: 9px;
    }

    .dispatch-offcanvas .btn {
        font-size: 13px;
        padding: 10px 16px;
        border-radius: 7px;
    }

    @media (max-width: 575.98px) {
        .vehicle-card .card-body {
            padding: 14px;
        }

        .dispatch-offcanvas {
            width: 100% !important;
        }

        .dispatch-offcanvas .offcanvas-body {
            padding: 14px;
        }
    }
</style>


<script>
    function editVehicle(button) {
        const data = button.dataset;

        document.getElementById('edit_token').value = data.id || '';
        document.getElementById('edit_truckno').value = data.truckno || '';
        document.getElementById('edit_entrytype').value = data.entrytype || '';
        document.getElementById('edit_vehicletype').value = data.vehicletype || '';

        document.getElementById('editVehicleForm').action =
            "{{ url('/manage-dispatch') }}/" + encodeURIComponent(data.id);
    }
</script>

@endsection
