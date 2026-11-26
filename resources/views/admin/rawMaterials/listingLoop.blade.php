@forelse ($listing->items() as $row)
    @php
        $receiving = collect($row->poitemReceiving ?? [])->first();

        $receivedQty = (float) data_get($receiving, 'received_qty', 0);
        $usedQty = (float) data_get($receiving, 'production_used_qty', 0);

        $remainingQty = $receivedQty - $usedQty;
    @endphp
    <tr>
        <td><a href="#" class="fw-medium">{{ $row->id ?? '---' }}</a></td>
        <td>{{ $row->purchase_order_id ?? '---' }}</td>
        <td>{{ $row->customer->company_name ?? '---' }}</td>
        <td>{{ $row->product->product_name ?? '---' }}</td>
        <td>---</td>
        <td>{{ $row->size->size_name ?? '---' }}</td>
        <td>{{ $row->size->length ?? '0.00' }}</td>
        <td>{{ $row->grade->grade_name ?? '0.00' }}</td>
        <td>
            {{ data_get($receiving, 'created_at')
                ? \Carbon\Carbon::parse($receiving->created_at)->format('d-m-Y')
                : '---' }}
        </td>

        <td>{{ number_format($receivedQty, 3) }}</td>
        <td>{{ number_format($usedQty, 3) }}</td>
        <td>{{ number_format($remainingQty, 3) }}</td>
        <td>
            @if($row->lab && $row->lab->isNotEmpty())
                <button style="background-color: green; color: white; padding: 8px 15px; border: none; cursor: pointer;"
                    onclick="window.open('{{ route('rawMaterials.print', $row->id) }}', '_blank')">
                    TC
                </button>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-3">No sale orders found.</td>
    </tr>
@endforelse
