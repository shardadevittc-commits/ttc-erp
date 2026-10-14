@forelse ($listing->items() as $row)
{{-- @php
pr($row->toArray()); die;
@endphp --}}
    <tr>
        <td><a href="#" class="fw-medium">{{ $row->id ?? '---' }}</a></td>
        <td>{{ $row->purchase_order_id ?? '---' }}</td>
        <td>{{ $row->customer->company_name ?? '---' }}</td>
        <td>{{ $row->product->product_name ?? '---' }}</td>
        <td>---</td>
        <td>{{ $row->size->size_name ?? '---' }}</td>
        <td>{{ $row->size->length ?? '0.00' }}</td>
        <td>{{ $row->grade->grade_name ?? '0.00' }}</td>
        <td>14-09-2026 11:13:14</td>
        <td>10.210</td>
        <td>0.000</td>
        <td>10.210</td>
        <td>TC</td>
    </tr>    
@empty
    <tr>
        <td colspan="7" class="text-center py-3">No sale orders found.</td>
    </tr>
@endforelse
