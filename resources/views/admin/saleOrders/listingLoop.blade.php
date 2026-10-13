@forelse ($listing->items() as $row)
{{-- @php
pr($row->toArray());
@endphp --}}
    <tr>
        <td><a href="{{ route('saleOrders.view', ['id' => $row->id]) }}" class="fw-medium">#{{ $row->id }}</a></td>
        <td>{{ _d($row->created_at) ?? '-' }}</td>
        <td>{{ $row->customer?->company_name ?: '-' }}</td>
        <td>{{ isset($row->order_qty) && $row->order_qty ? _rz($row->order_qty) : '---' }}</td>
        <td>---</td>
        {{-- <td>{{ $row->basic_rate }}</td> --}}
        <td>
            @foreach ($row->items as $k => $i)
                {{ '(' . ($k + 1) . ') ' . _rz($i->sale_item_prices) . ' (' . $i->product->product_name . '-' . $i->size->size_name . ')'." (". _rz($i->item_qty) ." Ton)"." (". $i->grade->grade_name .")"}}
                <br>
            @endforeach
        </td>
        {{-- <td>
            @foreach ($row->items as $k => $g)
                {{ '(' . ($k + 1) . ') ' . $g->grade->grade_name }}
                <br>
            @endforeach
        </td> --}}
        <td>{{ isset($row->total_qty) && $row->total_qty ? _rz($row->total_qty) : '---' }}</td>
        <td>---</td>
        <td>{{ isset($row->total_qty) && $row->total_qty ? _rz($row->total_qty) : '---' }}</td>
        <td>According to the Purchase order</td>
        {{-- <td></td> --}}
        <td class="d-flex align-items-center gap-2">
            <a href="{{ route('saleOrders.view', ['id' => $row->id]) }}" aria-label="View sale order" title="View">
                <i class="fas fa-eye text-info"></i>
            </a>
            <a href="{{ route('saleOrders.edit', ['id' => $row->id]) }}" aria-label="Edit sale order" title="Edit">
                <i class="fas fa-edit text-primary"></i>
            </a>
            <a href="{{ route('saleOrders.delete', ['id' => $row->id]) }}" onclick="return confirm('Are you sure you want to delete this sale order?');" aria-label="Delete sale order" title="Delete">
                <i class="fas fa-trash-alt text-danger"></i>
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-3">No sale orders found.</td>
    </tr>
@endforelse
