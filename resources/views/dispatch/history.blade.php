@extends('layouts.app')

@section('title', 'Dispatch History | TTC Robotronics')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h5 class="mb-1">Dispatch History</h5>
            <small class="text-muted">Read-only record of completed material dispatches.</small>
        </div>
        <a href="{{ route('dispatch.index') }}" class="btn btn-secondary">Back to Dispatch</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Dispatch Date</th>
                            <th>Vehicle</th>
                            <th>Company</th>
                            <th>Sale Order / PO</th>
                            <th>Material</th>
                            <th>Grade / Product</th>
                            <th>Ordered Qty</th>
                            <th>Production Qty</th>
                            <th>Weight Entry</th>
                            <th>Item Actual Qty</th>
                            <th>Actual Dispatched Qty</th>
                            <th>Dispatch Request User</th>
                            <th>Status</th>
                            <th>View</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dispatches as $dispatch)
                            @php
                                $unit = (int) $dispatch->qty_type === 2 ? 'KG' : 'Tons';
                                $productionQty = (float) ($dispatch->completed_production_kg ?? 0)
                                    / ((int) $dispatch->qty_type === 2 ? 1 : 1000);
                            @endphp
                            <tr>
                                <td>{{ $dispatch->dispatched_at ? \Illuminate\Support\Carbon::parse($dispatch->dispatched_at)->format('d M Y, h:i A') : '-' }}</td>
                                <td>{{ $dispatch->truckno ?: '-' }}</td>
                                <td>{{ $dispatch->company_name ?: '-' }}</td>
                                <td>
                                    #{{ $dispatch->sale_order_id }}
                                    <small class="d-block text-muted">{{ $dispatch->customer_po_no ?: '-' }}</small>
                                </td>
                                <td>
                                    {{ $dispatch->product_name ?: '-' }}
                                    <small class="d-block text-muted">
                                        {{ $dispatch->size_name ?: $dispatch->size_unit ?: '-' }}
                                        · Item #{{ $dispatch->sale_order_item_id }}
                                    </small>
                                </td>
                                <td>
                                    {{ $dispatch->grade_name ?: '-' }} / {{ $dispatch->brand_name ?: '-' }}
                                    <small class="d-block text-muted">{{ $dispatch->product_category ?: '-' }}</small>
                                </td>
                                <td>{{ number_format((float) ($dispatch->ordered_qty ?? 0), 3) }} {{ $unit }}</td>
                                <td>{{ number_format($productionQty, 3) }} {{ $unit }}</td>
                                <td>
                                    {{ trim(($dispatch->weight_user_first_name ?? '') . ' ' . ($dispatch->weight_user_last_name ?? '')) ?: 'N/A' }}
                                    <small class="d-block text-muted">
                                        User #{{ $dispatch->weight_created_by ?? '-' }}
                                        @if($dispatch->weight_created_at)
                                            · {{ \Illuminate\Support\Carbon::parse($dispatch->weight_created_at)->format('d M Y, h:i A') }}
                                        @endif
                                    </small>
                                    <small class="d-block text-muted">
                                        Tare {{ number_format((float) ($dispatch->tareweight ?? 0), 2) }} KG
                                        · Gross {{ number_format((float) ($dispatch->unload_loaded_weight ?? 0), 2) }} KG
                                        · Net {{ number_format((float) ($dispatch->material_weight ?? 0), 2) }} KG
                                    </small>
                                </td>
                                <td>{{ number_format((float) ($dispatch->actual_qty ?? 0), 3) }} KG</td>
                                <td>{{ number_format((float) $dispatch->dispatch_qty, 3) }} {{ $dispatch->dispatch_qty_unit ?: $unit }}</td>
                                <td>
                                    {{ trim(($dispatch->dispatch_user_first_name ?? '') . ' ' . ($dispatch->dispatch_user_last_name ?? '')) ?: 'N/A' }}
                                    <small class="d-block text-muted">User #{{ $dispatch->dispatch_created_by ?? '-' }}</small>
                                </td>
                                <td><span class="badge bg-success">Final Dispatched</span></td>
                                <td class="text-center">
                                    <a href="{{ route('dispatch.history.show', ['dispatchMaterial' => $dispatch->id]) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="View completed dispatch"
                                        aria-label="View completed dispatch #{{ $dispatch->id }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="text-center text-muted py-4">No completed dispatches found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $dispatches->links() }}
        </div>
    </div>
</div>
@endsection
