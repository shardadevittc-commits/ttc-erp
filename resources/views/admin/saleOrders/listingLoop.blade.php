@forelse ($listing->items() as $row)
    <tr>
        <td>
            <a href="{{ route('saleOrders.view', ['id' => $row->id]) }}" class="fw-medium">#{{ $row->id }}</a>
        </td>
        <td>{{ $row->customer?->company_name ?? '-' }}</td>
        <td>{{ $row->customer_po_no ?: '-' }}</td>
        <td>{{ $row->items_count }}</td>
        <td>{{ $row->freight_basis === \App\Models\SaleOrder::FREIGHT_EX ? 'EX' : ($row->freight_basis === \App\Models\SaleOrder::FREIGHT_FOR ? 'FOR' : '-') }}</td>
        <td>{{ $row->created_at?->format('d M, Y') ?? '-' }}</td>
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
