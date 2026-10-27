@forelse ($listing->items() as $k => $item)
    @php
        $saleOrderItem = $productionHistory ? $item->saleOrderItem : $item;
        $workOrder = $productionHistory ? $item : null;
    @endphp
    <tr>
        <td> {{$k+1}} </td>
        <td>
            @if ($workOrder)
                {{ $workOrder->id }} / <strong>{{ _dt($workOrder->created_at) }}</strong>
            @else
                ---
            @endif
        </td>
        {{-- <td>
            @if ($item->saleOrder)
                <a href="{{ route('saleOrders.view', ['id' => $item->saleOrder->id]) }}" class="fw-medium">
                    #{{ $item->saleOrder->id }}
                </a>
            @else
                -
            @endif
        </td> --}}
        {{-- <td>{{ $item->saleOrder?->created_at ? _d($item->saleOrder->created_at) : '-' }}</td> --}}
        @php
        $itemQty = $saleOrderItem->item_qty !== null ? _rz($saleOrderItem->item_qty).' Tons' : '';
        $saleItemPrice = $saleOrderItem->sale_item_prices !== null ? _rz($saleOrderItem->sale_item_prices) : '';
        @endphp
        <td>{{ $saleOrderItem->saleOrder?->customer?->company_name ?: '-' }}</td>
        <td style="white-space: nowrap;">
            <b class="text-danger d-inline">{{ $saleOrderItem->saleOrder?->id }}</b>
            <b class="d-inline">{{ $saleOrderItem->product?->product_name ? ' - '.$saleOrderItem->product?->product_name : '' }}</b>
            <b class="d-inline">{{ $saleOrderItem->size?->size_name ? ' - '.$saleOrderItem->size?->size_name : '' }}</b>
            <b class="d-inline">{{ $itemQty !== null ? $itemQty : '-' }}</b>
        </td>
        <td>{{ $saleOrderItem->grade?->grade_name ?: '-' }}</td>
        <td>
            {{ $saleOrderItem->size?->size_name ?: ($saleOrderItem->size_unit ?: '-') }}
        </td>
        <td>{{ $itemQty !== null ? $itemQty : '-' }}</td>
        <td>
            @if ($workOrder)
                <span class="badge {{ $workOrder->status === 'completed' ? 'bg-success' : 'bg-info text-dark' }}">
                    {{ $workOrder->status === 'completed' ? 'Completed' : 'Under Process' }}
                </span>
            @else
                Pending
            @endif
        </td>
        
        {{-- pr($item->toArray()); die; --}}
        <td class="btn-sm">
            @if ($workOrder)
                <a href="{{ route('productions.workflow', ['workOrder' => $workOrder->id]) }}"
                    class="btn {{ $workOrder->status === 'completed' ? 'btn-primary' : 'btn-warning' }} btn-sm">
                    {{ $workOrder->status === 'completed' ? 'View' : 'Under Process' }}
                </a>
            @else
            <button type="button"
                class="btn btn-success btn-sm send-for-work"
                data-work-order-url="{{ route('productions.send-for-work', ['sale_order_item_id' => $saleOrderItem->id, 'sale_order_id' => $saleOrderItem->saleOrder->id]) }}"
                data-party="{{ $saleOrderItem->saleOrder?->customer?->company_name ?: 'Customer not available' }}"
                data-product-details="{{ implode(' - ', array_filter([$saleOrderItem->product?->product_name, $saleOrderItem->grade?->grade_name, $saleOrderItem->size?->size_name, $saleItemPrice, $itemQty])) }}"
                data-grade="{{ $saleOrderItem->grade?->grade_name ?: '-' }}"
                data-instruction="{{ $saleOrderItem->saleOrder?->remarks ?? '' }}"
                data-received-count="{{ $saleOrderItem->received_item_count }}"
                data-received-qty="{{ number_format($saleOrderItem->received_qty_total, 2, '.', '') }}"
                data-available-qty="{{ number_format($saleOrderItem->available_qty_total, 2, '.', '') }}"
                data-rm-heat-no="{{ $saleOrderItem->rm_heat_no ?? '' }}"
                data-vehicle-id="{{ $saleOrderItem->vehicle_id ?? '' }}"
                data-operation="{{ $saleOrderItem->work_operation ?? '' }}"
                data-order-qty="{{ $saleOrderItem->item_qty ?? 0 }}"
                style="font-size: 11px; padding: 10px 5px; line-height: 1;">
                Send for Work
            </button>
            @endif
        </td>
        {{-- <td>{{ $item->dispatch_qty !== null ? _rz($item->dispatch_qty) : '-' }}</td> --}}
        {{-- <td>{{ $item->dispatched_qty !== null ? _rz($item->dispatched_qty) : '-' }}</td>
        <td>{{ $item->pending_qty !== null ? _rz($item->pending_qty) : '-' }}</td>
        <td>
            @if ($item->s_marked_completed === \App\Models\SaleOrderItem::COMPLETED)
                <span class="badge bg-success">Completed</span>
            @elseif ($item->s_marked_completed === \App\Models\SaleOrderItem::NOT_COMPLETED)
                <span class="badge bg-warning text-dark">Not Completed</span>
            @else
                -
            @endif
        </td> --}}
    </tr>
@empty
    <tr>
        <td colspan="11" class="text-center py-3">
            {{ $productionHistory ? 'No production history found.' : 'No production items found.' }}
        </td>
    </tr>
@endforelse
