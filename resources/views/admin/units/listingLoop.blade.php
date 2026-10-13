@foreach($listing->items() as $k => $row)
<tr>

	<td>
		{{ $row->id }}
	</td>
	<td class="unit_name">
		<div class="d-flex justify-content-start align-items-center user-name">
			<div class="d-flex flex-column">
				<a href="{{ route('units.view', ['id' => $row->id]) }}" class="text-body text-truncate">
					<span class="fw-medium">{{ $row->unit_name ?: 'Unnamed Unit' }}</span>
				</a>
			</div>
		</div>
	</td>
    <td class="short_name">
        {{ $row->short_name }}
    </td>
    <td>
        @if($row->status == 1)
            <span class="badge bg-success">Active</span>
        @else
            <span class="badge bg-secondary">Inactive</span>
        @endif
	</td>

	<td class="unit_cretaed_by">
		{{-- {{ $row->created_at ? $row->created_at->format('d M, Y') : '-' }} --}}
        {{ $row->created_by ? \App\Models\User::find($row->created_by)?->first_name : '-' }}
	</td>

    <td class="d-flex align-items-center gap-2 unit_edit_delete">
        <a href="{{ route('units.edit', ['back_url' => url()->full(), 'id' => $row->id]) }}">
            <i class="fas fa-edit text-primary"></i>
        </a>

        <a href="{{ route('units.delete', ['id' => $row->id]) }}"
        onclick="return confirm('Are you sure you want to delete this unit?');">
            <i class="fas fa-trash-alt text-danger"></i>
        </a>
    </td>

</tr>
@endforeach
