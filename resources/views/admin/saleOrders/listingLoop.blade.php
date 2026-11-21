
@forelse ($listing->items() as $row)

    <tr>

        <td>
            <a href="{{ route('saleOrders.view', ['id' => $row->id]) }}"
               class="fw-medium">
                #{{ $row->id }}
            </a>
        </td>

        <td>
            {{ _d($row->created_at) ?? '-' }}
        </td>

        <td>
            {{ $row->customer?->company_name ?: '-' }}
        </td>

        <td>
            {{ isset($row->order_qty) && $row->order_qty
                ? _rz($row->order_qty)
                : '---' }}
        </td>

        <td>---</td>

        <td>
            @foreach ($row->items as $k => $i)
                {{ '(' . ($k + 1) . ') ' .
                    _rz($i->sale_item_prices) .
                    ' (' . ($i->product?->product_name ?? '-') .
                    '-' . ($i->size?->size_name ?? $i->size_unit ?? '-') . ')' .
                    ' (' . _rz($i->item_qty) . ' ' .
                    ((int) $i->qty_type === \App\Models\SaleOrderItem::QTY_KGS ? 'KG' : 'Tons') . ')' .
                    ' (' . ($i->grade?->grade_name ?? '-') . ')' }}
                <br>
            @endforeach
        </td>

        <td>
            @foreach ($row->items as $k => $i)
                @php
                    $orderedQty = (float) ($i->item_qty ?? 0);
                    $quantityUnit = (int) $i->qty_type === \App\Models\SaleOrderItem::QTY_KGS
                        ? 'KG'
                        : 'Tons';
                @endphp

                {{ '(' . ($k + 1) . ') ' . _rz($orderedQty) . ' ' . $quantityUnit }}
                <br>
            @endforeach
        </td>

        <td>
            @foreach ($row->items as $k => $i)
                {{ '(' . ($k + 1) . ') ' . _rz($i->dispatched_qty_total ?? 0) . ' ' .
                    ((int) $i->qty_type === \App\Models\SaleOrderItem::QTY_KGS ? 'KG' : 'Tons') }}
                <br>
            @endforeach
        </td>

        <td>
            @foreach ($row->items as $k => $i)
                @php
                    $orderedQty = (float) ($i->item_qty ?? 0);
                    $dispatchedQty = (float) ($i->dispatched_qty_total ?? 0);
                    $remainingQty = max(0, $orderedQty - $dispatchedQty);
                @endphp
                {{ '(' . ($k + 1) . ') ' . _rz($remainingQty) . ' ' .
                    ((int) $i->qty_type === \App\Models\SaleOrderItem::QTY_KGS ? 'KG' : 'Tons') }}
                <br>
            @endforeach
        </td>

        <td>
            @foreach ($row->items as $k => $i)
                @php
                    $orderedQty = (float) ($i->item_qty ?? 0);
                    $dispatchedQty = (float) ($i->dispatched_qty_total ?? 0);

                    if ($orderedQty > 0 && $dispatchedQty >= $orderedQty) {
                        $status = 'Dispatched';
                    } elseif ($dispatchedQty > 0 && $orderedQty > 0) {
                        $status = (int) round(($dispatchedQty / $orderedQty) * 100) . '% Dispatched';
                    } elseif ($i->has_production) {
                        $status = 'Under Production';
                    } elseif ($i->has_lab_testing) {
                        $status = 'Under Lab Testing';
                    } elseif ($i->has_receiving) {
                        $status = 'Under Unloader';
                    } elseif ($i->has_purchase) {
                        $status = 'Under Purchase';
                    } else {
                        $status = 'Not Started';
                    }
                @endphp

                {{ '(' . ($k + 1) . ') ' . $status }}
                @if ($orderedQty > 0 && $dispatchedQty <= 0)
                    (0% Dispatched)
                @endif
                @if ($dispatchedQty > 0 && $dispatchedQty < $orderedQty && $orderedQty > 0)
                    (Remaining: {{ _rz(max(0, $orderedQty - $dispatchedQty)) }}
                    {{ (int) $i->qty_type === \App\Models\SaleOrderItem::QTY_KGS ? 'KG' : 'Tons' }})
                @endif
                <br>
            @endforeach
        </td>

        <td>
            <div class="d-flex align-items-center gap-2">

                <a href="{{ route('saleOrders.view', ['id' => $row->id]) }}"
                   aria-label="View sale order"
                   title="View">
                    <i class="fas fa-eye text-info"></i>
                </a>

                {{-- Print --}}
                <a href="{{ route('saleOrders.print', ['id' => $row->id]) }}"
                    aria-label="Print sale order"
                    title="Print Sale Order"
                    target="_blank">
                    <i class="fas fa-print text-success"></i>
                </a>

                <a href="{{ route('saleOrders.edit', ['id' => $row->id]) }}"
                   aria-label="Edit sale order"
                   title="Edit">
                    <i class="fas fa-edit text-primary"></i>
                </a>

                <a href="{{ route('saleOrders.delete', ['id' => $row->id]) }}"
                   onclick="return confirm('Are you sure you want to delete this sale order?');"
                   aria-label="Delete sale order"
                   title="Delete">
                    <i class="fas fa-trash-alt text-danger"></i>
                </a>

            </div>
        </td>

    </tr>

@empty

    <tr>
        <td colspan="11" class="text-center py-3">
            No sale orders found.
        </td>
    </tr>

@endforelse
