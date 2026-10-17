@extends('layouts.app')

@section('title', 'Manage Weight Entries | TTC Robotronics')

@section('content')

<style>
    .vehicle-page { padding-bottom:25px; color:#263238; }
    .vehicle-panel {
        background:#fff;
        border:1px solid #e3e8ec;
        border-radius:7px;
        box-shadow:0 2px 10px rgba(0,0,0,.06);
        margin-bottom:24px;
        padding:20px 16px 25px;
    }
    .vehicle-panel-header {
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:15px;
        padding-bottom:16px;
        margin-bottom:20px;
        border-bottom:1px solid #e9ecef;
    }
    .vehicle-panel-header h5 {
        font-size:16px;
        font-weight:600;
        margin:0;
        color:#263238;
    }
    .vehicle-grid {
        display:grid;
        grid-template-columns:repeat(4,minmax(0,1fr));
        gap:22px;
    }
    .vehicle-card {
        min-width:0;
        border:1px solid rgba(0,0,0,.06);
        border-radius:20px;
        padding:20px 24px;
        transition:transform .2s ease,box-shadow .2s ease;
    }
    .vehicle-card:hover {
        transform:translateY(-3px);
        box-shadow:0 6px 18px rgba(0,0,0,.09);
    }
    .vehicle-card-in {
        background:#f0f3f5;
        min-height:172px;
    }
    .vehicle-card-out {
        background:#22a447;
        min-height:170px;
        color:#10251a;
    }
    .vehicle-card-return {
        background:#fff0f0;
        color:#842029;
        min-height:170px;
        border-color:#f1c4c8;
    }
    .vehicle-card-top {
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:10px;
        margin-bottom:22px;
    }
    .vehicle-time-label,
    .vehicle-info-label {
        font-size:12px;
        margin-bottom:4px;
    }
    .vehicle-time {
        font-size:12px;
        overflow-wrap:anywhere;
    }
    .vehicle-actions {
        display:flex;
        align-items:center;
        gap:5px;
        flex-shrink:0;
    }
    .vehicle-action-btn {
        height:31px;
        min-width:37px;
        padding:5px 9px;
        border:0;
        border-radius:5px;
        background:#087bff;
        color:#fff;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        text-decoration:none;
        cursor:pointer;
        font-size:13px;
    }
    .vehicle-action-btn:hover {
        background:#0065d5;
        color:#fff;
    }
    .vehicle-out-btn {
        background:#ed354a;
        min-width:42px;
    }
    .vehicle-out-btn:hover { background:#cf2337; }
    .vehicle-return-badge {
        background:#dc3545;
        color:#fff;
        padding:6px 10px;
        border-radius:5px;
        font-size:12px;
        font-weight:600;
    }
    .vehicle-party-name {
        font-size:14px;
        font-weight:700;
        text-transform:uppercase;
        margin-bottom:15px;
        overflow-wrap:anywhere;
    }
    .vehicle-info-grid {
        display:grid;
        grid-template-columns:1fr auto;
        gap:12px;
        align-items:start;
    }
    .vehicle-info-value {
        font-size:13px;
        font-weight:500;
        overflow-wrap:anywhere;
    }
    .vehicle-filters {
        display:flex;
        margin-bottom:25px;
    }
    .vehicle-filter-btn {
        background:#fff;
        color:#087bff;
        border:1px solid #087bff;
        padding:9px 18px;
        font-size:13px;
        cursor:pointer;
        min-width:55px;
    }
    .vehicle-filter-btn:first-child {
        border-radius:5px 0 0 5px;
    }
    .vehicle-filter-btn:last-child {
        border-radius:0 5px 5px 0;
    }
    .vehicle-filter-btn + .vehicle-filter-btn {
        border-left:0;
    }
    .vehicle-filter-btn[data-filter="IN"] {
        color:#20a64a;
        border-color:#20a64a;
    }
    .vehicle-filter-btn[data-filter="OUT"] {
        color:#ed354a;
        border-color:#ed354a;
    }
    .vehicle-filter-btn.active {
        background:#087bff;
        color:#fff;
    }
    .vehicle-filter-btn[data-filter="IN"].active {
        background:#20a64a;
        color:#fff;
    }
    .vehicle-filter-btn[data-filter="OUT"].active {
        background:#ed354a;
        color:#fff;
    }
    .vehicle-empty {
        display:none;
        text-align:center;
        padding:35px 15px;
        color:#7b858d;
        font-size:14px;
    }
    .vehicle-count {
        display:inline-block;
        background:#e9ecef;
        color:#263238;
        border-radius:12px;
        padding:3px 9px;
        font-size:12px;
        margin-left:6px;
    }
    .vehicle-legend {
        display:flex;
        flex-wrap:wrap;
        gap:12px 20px;
        margin:0;
    }
    .legend-item {
        display:inline-flex;
        align-items:center;
        gap:7px;
        font-size:13px;
        color:#424b52;
    }
    .legend-color {
        width:15px;
        height:15px;
        border-radius:4px;
        display:inline-block;
    }
    .legend-in { background:#f0f3f5; }
    .legend-out { background:#22a447; }
    .legend-return {
        background:#fff0f0;
        border:1px solid #dc3545;
    }
    .vehicle-pagination { margin-top:20px; }

    @media(max-width:1200px) {
        .vehicle-grid {
            grid-template-columns:repeat(3,minmax(0,1fr));
        }
    }
    @media(max-width:900px) {
        .vehicle-grid {
            grid-template-columns:repeat(2,minmax(0,1fr));
        }
    }
    @media(max-width:576px) {
        .vehicle-panel { padding:15px 12px 20px; }
        .vehicle-panel-header { align-items:flex-start; }
        .vehicle-grid {
            grid-template-columns:1fr;
            gap:15px;
        }
        .vehicle-card { padding:18px; }
        .vehicle-panel-header h5 { font-size:14px; }
    }
</style>

@php
    $formatDate = function ($date) {
        return $date
            ? \Carbon\Carbon::parse($date)->format('d-m-Y h:i A')
            : '-';
    };
@endphp

<div class="vehicle-page">

    {{-- PAGE HEADER --}}
    <div class="page-header mb-4">
        <div class="page-header__inner">
            <div class="page-header__content">
                <div class="page-header__title">
                    <h6>Manage Weight Management</h6>
                </div>
            </div>

            <div class="page-header__actions">
                <div class="vehicle-legend">
                    <div class="legend-item">
                        <span class="legend-color legend-in"></span>
                        Material In
                    </div>
                    <div class="legend-item">
                        <span class="legend-color legend-out"></span>
                        Material Out
                    </div>
                    <div class="legend-item">
                        <span class="legend-color legend-return"></span>
                        Scrap
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content_area">
        <div class="flex-grow-1 container-p-y">

            {{-- SUCCESS MESSAGE --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close"
                            data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- ERROR MESSAGE --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close"
                            data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- MATERIAL IN --}}
            <div class="vehicle-panel">
                <div class="vehicle-panel-header">
                    <h5>
                        <i class="fas fa-truck-loading"></i>
                        Vehicles In Yard
                        <span class="vehicle-count">
                            {{ $vehiclesInYard->count() }}
                        </span>
                    </h5>
                </div>

                <div class="vehicle-grid">
                    @forelse($vehiclesInYard as $vehicle)
                        <div class="vehicle-card vehicle-card-in">
                            <div class="vehicle-card-top">
                                <div>
                                    <div class="vehicle-time-label">
                                        Entry Date
                                    </div>
                                    <div class="vehicle-time">
                                        {{ $formatDate($vehicle->created_at) }}
                                    </div>
                                </div>

                                <div class="vehicle-actions">
                                    <a href="{{ route('material_weight', ['formlist' => $vehicle->formlist, 'id' => $vehicle->id, 'cust_id' => $vehicle->cust_id]) }}"
                                       class="vehicle-action-btn"
                                       title="Edit Vehicle">
                                        <i class="fas fa-pen"></i>
                                    </a>

                                    {{-- <button type="button"
                                            class="vehicle-action-btn vehicle-out-btn"
                                            title="Vehicle Out"
                                            onclick="confirmVehicleOut({{ $vehicle->id }})">
                                        OUT
                                    </button> --}}
                                </div>
                            </div>

                            <div class="vehicle-party-name">
                                {{ optional($vehicle->customer)->company_name ?? '-' }}
                            </div>

                            <div class="vehicle-info-grid">
                                <div>
                                    <div class="vehicle-info-label">Vehicle No.</div>
                                    <div class="vehicle-info-value">
                                        {{ $vehicle->truckno ?: '-' }}
                                    </div>
                                </div>
                                <div>
                                    <div class="vehicle-info-label">Token No.</div>
                                    <div class="vehicle-info-value">
                                        {{ $vehicle->tokenno ?: '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="vehicle-empty"
                             style="display:block;grid-column:1/-1;">
                            No Material In vehicles found.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- MATERIAL OUT --}}
            <div class="vehicle-panel">
                <div class="vehicle-panel-header">
                    <h5>
                        <i class="fas fa-truck"></i>
                        Vehicles Outside Yard
                        <span class="vehicle-count">
                            {{ $vehiclesOutsideYard->count() }}
                        </span>
                    </h5>
                </div>

                {{-- <div class="vehicle-filters">
                    <button type="button"
                            class="vehicle-filter-btn active"
                            data-filter="ALL">All</button>
                    <button type="button"
                            class="vehicle-filter-btn"
                            data-filter="IN">IN</button>
                    <button type="button"
                            class="vehicle-filter-btn"
                            data-filter="OUT">OUT</button>
                </div> --}}

                <div class="vehicle-grid" id="outsideVehicleGrid">
                    @forelse($vehiclesOutsideYard as $vehicle)
                        <div class="vehicle-card vehicle-card-out"
                             data-status="OUT">
                            <div class="vehicle-card-top">
                                <div>
                                    <div class="vehicle-time-label">
                                        Entry Date
                                    </div>
                                    <div class="vehicle-time">
                                        {{ $formatDate($vehicle->created_at) }}
                                    </div>
                                </div>

                                <div class="vehicle-actions">
                                    <a href="{{ route('material_weight', ['formlist' => $vehicle->formlist, 'id' => $vehicle->id, 'cust_id' => $vehicle->cust_id]) }}"
                                       class="vehicle-action-btn"
                                       title="Edit Vehicle">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="vehicle-party-name">
                                {{ optional($vehicle->customer)->company_name ?? '-' }}
                            </div>

                            <div class="vehicle-info-grid">
                                <div>
                                    <div class="vehicle-info-label">Vehicle No.</div>
                                    <div class="vehicle-info-value">
                                        {{ $vehicle->truckno ?: '-' }}
                                    </div>
                                </div>
                                <div>
                                    <div class="vehicle-info-label">Token No.</div>
                                    <div class="vehicle-info-value">
                                        {{ $vehicle->tokenno ?: '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="vehicle-empty"
                             style="display:block;grid-column:1/-1;">
                            No Material Out vehicles found.
                        </div>
                    @endforelse
                </div>

                <div class="vehicle-empty" id="noFilteredVehicles">
                    No vehicles available for this filter.
                </div>
            </div>

            {{-- RETURN --}}
            <div class="vehicle-panel">
                <div class="vehicle-panel-header">
                    <h5>
                        <i class="fas fa-recycle"
                           style="color:#dc3545;margin-right:7px;"></i>
                        Scrap Vehicles
                        <span class="vehicle-count">
                            {{ $scrapVehicles->count() }}
                        </span>
                    </h5>
                </div>

                <div class="vehicle-grid">
                    @forelse($scrapVehicles as $vehicle)
                        <div class="vehicle-card vehicle-card-return">
                            <div class="vehicle-card-top">
                                <div>
                                    <div class="vehicle-time-label">
                                        Return Date
                                    </div>
                                    <div class="vehicle-time">
                                        {{ $formatDate($vehicle->created_at) }}
                                    </div>
                                </div>

                                <div class="vehicle-actions">
                                    <a href="{{ route('material_weight', ['formlist' => $vehicle->formlist, 'id' => $vehicle->id, 'cust_id' => $vehicle->cust_id]) }}"
                                       class="vehicle-action-btn"
                                       title="Edit Vehicle">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="vehicle-party-name">
                                {{ optional($vehicle->customer)->company_name ?? '-' }}
                            </div>

                            <div class="vehicle-info-grid">
                                <div>
                                    <div class="vehicle-info-label">Vehicle No.</div>
                                    <div class="vehicle-info-value">
                                        {{ $vehicle->truckno ?: '-' }}
                                    </div>
                                </div>
                                <div>
                                    <div class="vehicle-info-label">Token No.</div>
                                    <div class="vehicle-info-value">
                                        {{ $vehicle->tokenno ?: '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="vehicle-empty"
                             style="display:block;grid-column:1/-1;">
                            No Return vehicles found.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- PAGINATION --}}
            <div class="vehicle-pagination">
                {{ $materialEntries->links() }}
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterButtons = document.querySelectorAll('.vehicle-filter-btn');
    const vehicleCards = document.querySelectorAll(
        '#outsideVehicleGrid .vehicle-card'
    );
    const emptyMessage = document.getElementById('noFilteredVehicles');

    filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const selectedFilter = this.dataset.filter;
            let visibleCount = 0;

            filterButtons.forEach(function (item) {
                item.classList.remove('active');
            });

            this.classList.add('active');

            vehicleCards.forEach(function (card) {
                const shouldShow =
                    selectedFilter === 'ALL' ||
                    card.dataset.status === selectedFilter;

                card.style.display = shouldShow ? '' : 'none';

                if (shouldShow) {
                    visibleCount++;
                }
            });

            emptyMessage.style.display =
                visibleCount === 0 ? 'block' : 'none';
        });
    });
});

function confirmVehicleOut(vehicleId) {
    if (!vehicleId) {
        alert('Vehicle ID is missing.');
        return;
    }

    if (confirm('Are you sure you want to mark this vehicle as OUT?')) {
        alert(
            'Vehicle ID ' + vehicleId +
            ' ko OUT karne ke liye apna Laravel route connect karein.'
        );
    }
}
</script>

@endsection
