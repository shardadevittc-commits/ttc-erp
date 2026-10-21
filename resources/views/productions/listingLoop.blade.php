@forelse ($listing->items() as $k => $item)
    <tr>
        <td> {{$k+1}} </td>
        <td> --- </td>
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
        $itemQty = $item->item_qty !== null ? _rz($item->item_qty).' Tons' : '';
        $saleItemPrice = $item->sale_item_prices !== null ? _rz($item->sale_item_prices) : '';
        @endphp
        <td>{{ $item->saleOrder?->customer?->company_name ?: '-' }}</td>
        <td style="white-space: nowrap;">
            <b class="text-danger d-inline">{{ $item->saleOrder->id }}</b>
            <b class="d-inline">{{ $item->product?->product_name ? ' - '.$item->product?->product_name : '' }}</b>
            <b class="d-inline">{{ $item->size?->size_name ? ' - '.$item->size?->size_name : '' }}</b>
            <b class="d-inline">{{ $itemQty !== null ? $itemQty : '-' }}</b>
        </td>
        <td>{{ $item->grade?->grade_name ?: '-' }}</td>
        <td>
            {{ $item->size?->size_name ?: ($item->size_unit ?: '-') }}
        </td>
        <td>{{ $itemQty !== null ? $itemQty : '-' }}</td>
        <td>Pending</td>
        
        {{-- pr($item->toArray()); die; --}}
        <td class="btn-sm">
            <button type="button"
                class="btn btn-success btn-sm send-for-work"
                data-work-order-url="{{ route('productions.send-for-work', ['sale_order_item_id' => $item->id, 'sale_order_id' => $item->saleOrder->id]) }}"
                data-party="{{ $item->saleOrder?->customer?->company_name ?: 'Customer not available' }}"
                data-product-details="{{ implode(' - ', array_filter([$item->product?->product_name, $item->grade?->grade_name, $item->size?->size_name, $saleItemPrice, $itemQty])) }}"
                data-grade="{{ $item->grade?->grade_name ?: '-' }}"
                data-received-count="{{ $item->received_item_count }}"
                data-received-qty="{{ number_format($item->received_qty_total, 2, '.', '') }}"
                data-available-qty="{{ number_format($item->available_qty_total, 2, '.', '') }}"
                data-order-qty="{{ $item->item_qty ?? 0 }}"
                style="font-size: 11px; padding: 10px 5px; line-height: 1;">
                Send for Work
            </button>
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
        <td colspan="11" class="text-center py-3">No production items found.</td>
    </tr>
@endforelse
