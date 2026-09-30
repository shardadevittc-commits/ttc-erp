@forelse($listing->items() as $k => $row)
<tr>
	{{-- <td>
		<!-- MAKE SURE THIS HAS ID CORRECT AND VALUES CORRENCT. THIS WILL EFFECT ON BULK CRUTIAL ACTIONS -->
		<div class="form-check">
        	<input type="checkbox" class="form-check-input listing_check" value="{{ $row->id }}" id="listing_check{{ $row->id }}">
        	<label class="form-check-label" for="listing_check{{ $row->id }}"></label>
      	</div>
	</td> --}}
	<td>
		{{ $row->id }}
	</td>
	<td>
		<div class="d-flex justify-content-start align-items-center user-name">
			<div class="d-flex flex-column">
				<a href="{{ route('grades.view', ['id' => $row->id]) }}" class="text-body text-truncate">
					<span class="fw-medium">{{ $row->grade_name ?: 'Unnamed Grades' }}</span>
				</a>
			</div>
		</div>
	</td>
    <td>
        @if($row->status == 1)
            <span class="badge bg-success">Active</span>
        @else
            <span class="badge bg-secondary">Inactive</span>
        @endif
	</td>
	
	<td>
		{{ $row->created_at ? $row->created_at->format('d M, Y') : '-' }}
	</td>
	{{-- <td class="text-right">
		<div class="action_dropdown btn-group">
			<a href="javascript:;"
                class="btn dropdown-toggle"
                data-bs-toggle="dropdown"
                data-bs-display="dynamic"
                data-bs-boundary="viewport"
                aria-haspopup="true"
                aria-expanded="false">

                    <i class="fas fa-ellipsis-v"></i>

            </a>

			<div class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('users.edit', ['id' => $row->id]) }}">
                        <i class="fas fa-edit text-primary"></i>
                        <span class="status">Edit</span>
                    </a>
                </li>

                <li>
                    <a class="dropdown-item" href="{{ route('users.view', ['id' => $row->id]) }}">
                        <i class="fas fa-eye text-info"></i>
                        <span class="status">View</span>
                    </a>
                </li>

                <li>
                    <a class="dropdown-item delete_confirm" href="{{ route('users.delete', ['id' => $row->id]) }}">
                        <i class="fas fa-trash-alt text-danger"></i>
                        <span class="status">Delete</span>
                    </a>
                </li>
			</div>
		</div>
	</td> --}}
    <td class="d-flex align-items-center">
        <a class="dropdown-item" href="{{ route('grades.edit', ["back_url" => url()->full(),'id' => $row->id]) }}"><i class="fas fa-edit text-primary"></i> </a>
        <a class="dropdown-item" href="{{ route('grades.view', ['id' => $row->id]) }}"><i class="fas fa-eye text-info"></i><span class="status"></span></a>
		<a class="dropdown-item" href="{{ route('grades.delete', ['id' => $row->id]) }}" onclick="return confirm('Are you sure you want to delete this Grade?');"><i class="fas fa-trash-alt text-danger"></i></a>
    </td>
</tr>
@empty
<tr>
    <td colspan="5" class="text-center py-3">No records found!</td>
</tr>
@endforelse