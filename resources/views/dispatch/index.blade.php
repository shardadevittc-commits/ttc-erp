@extends('layouts.app')

@section('title', 'Manage Material Out | TTC Robotronics')

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="page-header">
    <div class="page-header__inner">
        <div class="page-header__content">
            <div class="page-header__title">
                <h6>Manage Dispatch</h6>
            </div>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('dispatch.history') }}" class="btn btn-outline-primary">
                <i class="fas fa-history me-1"></i>Dispatch History
            </a>
        </div>
    </div>
</div>

{{-- VEHICLE ENTRY RECORDS --}}
<div class="card mt-3 border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-1">Vehicle Entry Records</h5>
        <small class="text-muted">
            Total Records: {{ $gate->total() }}
        </small>
    </div>
    <div class="card-body">
        <div class="row g-4">

            @forelse($gate as $item)
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <div class="card vehicle-card h-100">
                        <div class="card-body">
                                @php
                                    $pencilAction = $dispatchStatusesByGate->get($item->id, collect())->pluck('status')->first();
                                    $vehicleStatuses = $dispatchStatusesByGate
                                        ->get($item->id, collect())
                                        ->pluck('status');
                                @endphp

                                <div class="d-flex flex-column gap-2 mb-3">

                                    {{-- TRUCK NUMBER LEFT + ACTION ICONS RIGHT --}}
                                    <div class="d-flex justify-content-between align-items-center gap-2">

                                        {{-- TRUCK NUMBER --}}
                                        <div class="flex-grow-1 min-w-0">
                                            <small class="text-muted d-block">Truck Number</small>

                                            <h6 class="truck-number mb-0 text-nowrap">
                                                <a href="{{ route('dispatch.truck-orders', ['gate' => $item->id]) }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="text-decoration-none">
                                                    {{ $item->truckno ?: '-' }}
                                                </a>
                                            </h6>
                                        </div>

                                        {{-- EDIT + DELETE RIGHT SIDE --}}
                                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                            {{-- DISPATCH ACTION --}}
                                            @if($item->id && $pencilAction === 'dispatched')
                                                <a href="{{ route('dispatch.truck-orders', ['gate' => $item->id]) }}"
                                                class="action-icon view-icon"
                                                title="View Final Dispatch Details"
                                                aria-label="View Final Dispatch Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                            @elseif($item->id && $pencilAction === 'weight_completed')
                                                <a href="{{ route('dispatch.selected-orders.form', ['gate_id' => $item->id]) }}"
                                                class="action-icon edit-icon weight-completed"
                                                title="Edit Dispatch Entry"
                                                aria-label="Edit Dispatch Entry">
                                                    <i class="fas fa-pen"></i>
                                                </a>

                                            @else

                                                <button type="button"
                                                    class="action-icon edit-icon"
                                                    title="Edit Dispatch Plan"
                                                    data-bs-toggle="offcanvas"
                                                    data-bs-target="#editVehicleOffcanvas"
                                                    data-id="{{ $item->id }}"
                                                    data-truckno="{{ $item->truckno }}"
                                                    data-entrytype="{{ $item->entrytype ?? 'Material Out' }}"
                                                    data-vehicletype="{{ $item->vehicletype ?? '' }}"
                                                    data-pending-url="{{ route('manage-dispatch.pending-orders', ['gate' => $item->id]) }}"
                                                    data-partyname="{{ $item->customer->company_name ?? '-' }}"
                                                    onclick="editVehicle(this)">
                                                    <i class="fas fa-pen"></i>
                                                </button>

                                            @endif

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

                                    {{-- MATERIAL OUT + VEHICLE STATUS --}}
                                    <div class="d-flex flex-wrap align-items-center gap-2">

                                        <span class="badge bg-primary">
                                            {{ $item->formlist ?: '-' }}
                                        </span>

                                        @if($vehicleStatuses->contains('dispatched'))

                                            <span class="badge bg-success">
                                                Final Dispatched
                                            </span>

                                        @elseif($vehicleStatuses->contains('weight_completed'))

                                            <span class="badge bg-info text-dark">
                                                Weight Department Completed
                                            </span>

                                        @elseif($vehicleStatuses->contains('sent_for_weight'))
                                            <span class="badge bg-info text-dark">
                                                Vehicle Sent for Weight
                                            </span>
                                        @endif
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
                                <a href="{{ asset('storage/' . $item->vehicleentryslip) }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary w-100 mt-3">
                                    <i class="fas fa-eye me-1"></i>
                                    View Entry Slip
                                </a>
                            @else
                                <button type="button" class="btn btn-secondary w-100 mt-3" disabled>
                                    <i class="fas fa-image me-1"></i>
                                    Image Not Available
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
                        <p class="text-muted mb-0">
                            Vehicle entry records will appear here.
                        </p>
                    </div>
                </div>
            @endforelse

        </div>
    </div>
</div>
<div class="mt-3">{{ $gate->links() }}</div>


{{-- RIGHT SIDE DISPATCH PLAN OFFCANVAS --}}
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

            <small class="text-muted">
                Vehicle dispatch information
            </small>
        </div>

        <button type="button"
            class="btn-close"
            data-bs-dismiss="offcanvas"
            aria-label="Close">
        </button>
    </div>

    <div class="offcanvas-body">

        {{-- EDIT VEHICLE FORM --}}
        <form id="editVehicleForm" method="POST" action="">

            @csrf
            @method('PUT')

            <h6 class="fw-bold mb-3">Vehicle Information</h6>

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
                                <input type="text" class="form-control pe-none bg-body-secondary" id="edit_token" name="id" readonly>
                            </td>

                            <td>
                                <input type="text" class="form-control pe-none bg-body-secondary" id="edit_truckno" name="truckno" required>
                            </td>

                            <td>
                                <select class="form-select pe-none bg-body-secondary" id="edit_entrytype" name="entrytype" required>

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


        {{-- CUSTOMER WISE PENDING ORDERS --}}

        <div class="pending-orders-section mt-4">

            <div class="mb-3">
                <h5 class="fw-bold mb-1">Customer Wise Pending Orders</h5>

                <label class="form-label fw-semibold mt-2">
                    Party Name
                </label>

                <div class="form-control bg-light" id="selected_party_name">
                    -
                </div>
            </div>

            <div id="pending_orders_loading"
                class="text-muted small mb-3 d-none">
                Loading pending orders...
            </div>

            <label class="form-check d-flex align-items-center gap-2 mb-2">
                <input type="checkbox" class="form-check-input" id="select_all_dispatch_items">
                <span class="form-check-label fw-semibold">Select all production-eligible items</span>
            </label>

            <div id="pending_orders_container" class="pending-orders-grid">
                <div class="pending-order-message">
                    Edit vehicle to view pending orders.
                </div>
            </div>

            <div class="mt-3">
                <button type="button" class="btn btn-success w-100" id="send_selected_items" onclick="sendSelectedOrders()" disabled>

                    <i class="fas fa-paper-plane me-1"></i>
                    Send for Weight
                </button>
            </div>

        </div>


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

    .edit-icon.weight-completed:hover {
        background: #ffc107;
        color: #212529;
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

    .edit-icon.weight-completed {
        background: #fff3cd;
        color: #997404;
    }

    .view-icon {
        background: #e8f5e9;
        color: #198754;
    }

    .edit-icon:hover {
        background: #0d6efd;
        color: #fff;
        transform: translateY(-2px);
    }

    .view-icon:hover {
        background: #198754;
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
        width: 950px !important;
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
        min-width: 650px;
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

    /* CUSTOMER WISE PENDING ORDERS */
    .pending-orders-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 18px;
        background: #fff;
    }

    .pending-orders-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .pending-orders-heading h5 {
        font-size: 17px;
        font-weight: 700;
        color: #1f2937;
    }

    .pending-orders-table {
        min-width: 650px;
        margin-bottom: 0;
    }

    .pending-orders-table thead th {
        background: #f3f6fa;
        color: #374151;
        font-size: 12px;
        white-space: nowrap;
        padding: 11px;
    }

    .pending-orders-table tbody td {
        font-size: 12px;
        padding: 11px;
        vertical-align: middle;
    }

    .orders-count {
        font-size: 12px;
    }

    /* PENDING ORDERS LIST */
    .pending-orders-grid {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 12px;
    }

    /* ONE MAIN DIV PER SALE ORDER */
    .sale-order-card {
        width: 100%;
        min-width: 0;
        padding: 12px;
        border: 1px solid #dfe3e8;
        border-radius: 9px;
        background: #fff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .03);
    }

    /* ORDER NUMBER + BASIC RATE */
    .sale-order-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 10px;
        padding-bottom: 9px;
        border-bottom: 1px solid #e9edf2;
        font-size: 12px;
    }

    .sale-order-heading strong {
        color: #1f2937;
        font-size: 13px;
        font-weight: 700;
    }

    .sale-order-heading span {
        color: #0d6efd;
        font-size: 12px;
        font-weight: 700;
    }

    /* ALL ITEMS INSIDE THE SAME ORDER */
    .sale-order-items-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
        align-items: stretch;
    }

    /* SMALL ITEM DIV */
    .sale-order-item {
        min-width: 0;
        padding: 9px;
        border: 1px solid #e1e6ed;
        border-radius: 7px;
        background: #f9fafc;
        transition: border-color .2s ease;
    }

    .sale-order-item:hover {
        border-color: #b9cbea;
    }

    /* ITEM HEADING */
    .sale-item-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 4px;
        margin-bottom: 6px;
        padding-bottom: 7px;
        border-bottom: 1px solid #e4e9f0;
        color: #334155;
        font-size: 11px;
        font-weight: 700;
    }

    /* ITEM DETAILS */
    .sale-item-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        align-items: start;
        gap: 6px;
        padding: 5px 0;
        border-bottom: 1px solid #e9edf2;
        font-size: 10px;
        line-height: 1.4;
    }

    .sale-item-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .sale-item-row span {
        color: #718096;
        min-width: 0;
        overflow-wrap: anywhere;
    }

    .sale-item-row strong {
        color: #2d3748;
        text-align: right;
        font-size: 10px;
        font-weight: 600;
        min-width: 0;
        overflow-wrap: anywhere;
    }

    .pending-order-message {
        grid-column: 1 / -1;
        padding: 18px;
        text-align: center;
        color: #6b7280;
        background: #f9fafb;
        border: 1px dashed #d1d5db;
        border-radius: 8px;
        font-size: 12px;
    }

    /* TABLET AND MOBILE */
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

        .pending-orders-card {
            padding: 12px;
        }

        .sale-order-card {
            padding: 9px;
        }

        .sale-order-items-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 6px;
        }

        .sale-order-item {
            padding: 7px;
        }

        .sale-item-heading {
            font-size: 10px;
        }

        .sale-item-row {
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 4px;
            font-size: 9px;
        }

        .sale-item-row strong {
            font-size: 9px;
        }
    }

    /* VERY SMALL SCREENS */
    @media (max-width: 360px) {
        .sale-order-items-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
function editVehicle(button) {
    const data = button.dataset;

    document.getElementById('edit_token').value = data.id || '';
    document.getElementById('edit_truckno').value = data.truckno || '';
    document.getElementById('edit_entrytype').value =
        data.entrytype || 'Material Out';

    document.getElementById('selected_party_name').textContent =
        data.partyname || '-';

    loadPendingOrders(data.pendingUrl);
}

async function loadPendingOrders(pendingUrl) {
    const container = document.getElementById('pending_orders_container');
    const loading = document.getElementById('pending_orders_loading');

    container.replaceChildren();

    if (!pendingUrl) {
        container.textContent = 'Truck details could not be identified.';
        return;
    }

    loading.classList.remove('d-none');

    try {
        const response = await fetch(
            pendingUrl,
            {
                headers: {
                    'Accept': 'application/json'
                }
            }
        );

        if (!response.ok) {
            throw new Error('Failed to fetch orders');
        }

        const result = await response.json();

        if (!result.success || !Array.isArray(result.orders)) {
            throw new Error('Invalid response');
        }

        let totalItems = 0;

        result.orders.forEach(order => {
            const items = Array.isArray(order.items) ? order.items : [];

            if (!items.length) return;

            totalItems += items.length;

            // Main card for each Sale Order
            const orderCard = document.createElement('div');
            orderCard.className = 'sale-order-card';

            // Order heading
            const heading = document.createElement('div');
            heading.className = 'sale-order-heading';

            const orderTitle = document.createElement('strong');
            orderTitle.textContent = 'Sale Order #' + order.id;

            // Basic Rate and checkbox
            const rateSection = document.createElement('div');
            rateSection.className = 'd-flex align-items-center gap-2 flex-wrap';

            const basicRate = document.createElement('span');
            basicRate.textContent = 'Basic Rate: ' + (order.basic_rate ?? '-');

            const checkboxLabel = document.createElement('label');
            checkboxLabel.className = 'd-flex align-items-center gap-1 mb-0';
            checkboxLabel.style.cursor = 'pointer';

            const checkboxText = document.createElement('small');
            checkboxText.textContent = 'Select order items';
            const orderCheckbox = document.createElement('input');
            orderCheckbox.type = 'checkbox';
            orderCheckbox.className = 'form-check-input select-order-items-checkbox';
            orderCheckbox.dataset.orderId = order.id;

            checkboxLabel.append(orderCheckbox, checkboxText);
            rateSection.append(basicRate, checkboxLabel);

            heading.append(orderTitle, rateSection);
            orderCard.appendChild(heading);

            // Items grid
            const itemsGrid = document.createElement('div');
            itemsGrid.className = 'sale-order-items-grid';

            items.forEach((item, index) => {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'sale-order-item';

                const itemHeading = document.createElement('div');
                itemHeading.className = 'sale-item-heading';
                itemHeading.classList.add('d-flex', 'justify-content-between', 'align-items-center');

                const itemTitle = document.createElement('span');
                itemTitle.textContent = 'Item ' + (index + 1);
                const itemSelectLabel = document.createElement('label');
                itemSelectLabel.className = 'd-flex align-items-center gap-1 mb-0';
                itemSelectLabel.style.cursor = 'pointer';
                const itemCheckbox = document.createElement('input');
                itemCheckbox.type = 'checkbox';
                itemCheckbox.className = 'form-check-input selected-item-checkbox';
                itemCheckbox.value = item.id;
                itemCheckbox.dataset.itemId = item.id;
                itemCheckbox.dataset.orderId = order.id;
                const alreadySent = ['sent_for_weight', 'weight_completed', 'dispatched'].includes(item.dispatch_status);
                const noQuantityAvailable = parseFloat(item.dispatchable_qty) <= 0;
                itemCheckbox.disabled = alreadySent || noQuantityAvailable;
                const itemSelectText = document.createElement('small');
                itemSelectText.textContent = alreadySent
                    ? (item.dispatch_status === 'sent_for_weight' ? 'Sent for weight' : 'Already weighed')
                    : (noQuantityAvailable ? 'No quantity available' : 'Select');
                itemSelectLabel.append(itemCheckbox, itemSelectText);
                itemHeading.append(itemTitle, itemSelectLabel);

                itemDiv.appendChild(itemHeading);

                const fields = [
                    ['Product', item.product_name],
                    ['Grade', item.grade_name],
                    ['Size', item.size_name],
                    ['Item Price', item.sale_item_prices],
                    ['Order Quantity', `${item.item_qty ?? 0} ${item.qty_type_name ?? ''}`],
                    ['Purchase', item.purchase_entry_count > 0 ? `Yes (${item.purchase_entry_count})` : 'No'],
                    ['Production Entries', item.production_entry_count],
                    ['Production Status', item.production_status],
                    ['Completed Production', `${item.completed_production_qty ?? 0} ${item.qty_type_name ?? ''}`],
                    ['Production Remaining', `${item.production_remaining_qty ?? 0} ${item.qty_type_name ?? ''}`],
                    ['Available to Dispatch', `${item.dispatchable_qty ?? 0} ${item.qty_type_name ?? ''}`],
                    ['Bundle Weight', item.fs_bundle_weight],
                    ['Bundle Unit', item.fs_bundle_unit_name]
                ];

                fields.forEach(([labelText, value]) => {
                    const row = document.createElement('div');
                    row.className = 'sale-item-row';

                    const label = document.createElement('span');
                    label.textContent = labelText;

                    const val = document.createElement('strong');
                    val.textContent = value ?? '-';

                    row.append(label, val);
                    itemDiv.appendChild(row);
                });

                itemsGrid.appendChild(itemDiv);
            });

            orderCard.appendChild(itemsGrid);
            container.appendChild(orderCard);
        });

        updateSelectionState();

        if (totalItems === 0) {
            const message = document.createElement('div');
            message.className = 'pending-order-message';
            message.textContent = 'No order items found.';
            container.appendChild(message);
        }

    } catch (error) {
        console.error(error);
        container.textContent = 'Unable to load pending orders.';
    } finally {
        loading.classList.add('d-none');
    }
}


// Send all selected orders to another page without putting IDs in URL
function sendSelectedOrders() {
    const selectedCheckboxes = document.querySelectorAll('#pending_orders_container .selected-item-checkbox:checked:not(:disabled)');

    if (selectedCheckboxes.length === 0) {
        alert('Please select at least one order.');
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "{{ route('dispatch.send-for-weight') }}";

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = "{{ csrf_token() }}";
    form.appendChild(csrf);

    appendHidden('gate_id', document.getElementById('edit_token').value);

    const orderIds = new Set();
    selectedCheckboxes.forEach(checkbox => {
        appendHidden('item_ids[]', checkbox.dataset.itemId);
        orderIds.add(checkbox.dataset.orderId);
    });
    orderIds.forEach(orderId => appendHidden('order_ids[]', orderId));

    document.body.appendChild(form);
    form.submit();

    function appendHidden(name, value) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    }
}

const pendingOrdersContainer = document.getElementById('pending_orders_container');
const selectAllItems = document.getElementById('select_all_dispatch_items');

pendingOrdersContainer.addEventListener('change', function (event) {
    if (event.target.matches('.select-order-items-checkbox')) {
        const orderItems = pendingOrdersContainer.querySelectorAll(
            `.selected-item-checkbox[data-order-id="${event.target.dataset.orderId}"]:not(:disabled)`
        );
        orderItems.forEach(checkbox => { checkbox.checked = event.target.checked; });
    }
    updateSelectionState();
});

selectAllItems.addEventListener('change', function () {
    pendingOrdersContainer.querySelectorAll('.selected-item-checkbox:not(:disabled)')
        .forEach(checkbox => { checkbox.checked = selectAllItems.checked; });
    updateSelectionState();
});

function updateSelectionState() {
    const availableItems = [...pendingOrdersContainer.querySelectorAll('.selected-item-checkbox:not(:disabled)')];
    const selectedItems = availableItems.filter(checkbox => checkbox.checked);

    selectAllItems.checked = availableItems.length > 0 && selectedItems.length === availableItems.length;
    selectAllItems.indeterminate = selectedItems.length > 0 && selectedItems.length < availableItems.length;
    document.getElementById('send_selected_items').disabled = selectedItems.length === 0;

    pendingOrdersContainer.querySelectorAll('.select-order-items-checkbox').forEach(orderCheckbox => {
        const orderItems = availableItems.filter(item => item.dataset.orderId === orderCheckbox.dataset.orderId);
        const selectedOrderItems = orderItems.filter(item => item.checked);
        orderCheckbox.checked = orderItems.length > 0 && selectedOrderItems.length === orderItems.length;
        orderCheckbox.indeterminate = selectedOrderItems.length > 0 && selectedOrderItems.length < orderItems.length;
    });
}
</script>

@endsection
