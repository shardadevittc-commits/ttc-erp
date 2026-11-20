{{-- resources/views/admin/finidhedMaterial/listingLoop.blade.php (sirf <tr> rows, tbody ke andar include hota hai) --}}
@forelse ($listing as $workOrder)
    @php
        $item = $workOrder->saleOrderItem;
        $summary = $workOrder->finished_summary;
        $sizeLabel = $item?->size?->size_name ?? $item?->size_unit ?? '-';
        $productName = $productTypes[(int) $item?->product_type] ?? ($item?->product?->product_name ?? '-');
    @endphp
    <tr>
        <td>{{ $listing->firstItem() + $loop->index }}</td>
        <td>{{ $workOrder->id }} / <strong>{{ _dt($workOrder->created_at) }}</strong></td>
        <td>{{ $item?->saleOrder?->customer?->company_name ?? '-' }}</td>
        <td>
            <strong class="text-danger">{{ $item?->saleOrder?->id ?? '-' }}</strong>
            - {{ $productName }}
            - {{ $sizeLabel }}
        </td>
        <td>{{ $item?->grade?->grade_name ?? '-' }}</td>
        <td>{{ $workOrder->heat_no ?: '-' }}</td>
        <td class="text-end">{{ number_format($summary['sent_kg'], 3) }}</td>
        <td class="text-end">{{ number_format($summary['scrap_kg'], 3) }}</td>
        <td class="text-end"><strong>{{ number_format($summary['finished_kg'], 3) }}</strong></td>
        <td>{{ $summary['packing'] }}</td>
        <td class="no-print">
            <a href="{{ route('finishedMaterial.view', ['id' => $workOrder->id]) }}" class="btn btn-sm btn-outline-primary">View</a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="11" class="text-center text-muted py-4">No finished material found.</td>
    </tr>
@endforelse