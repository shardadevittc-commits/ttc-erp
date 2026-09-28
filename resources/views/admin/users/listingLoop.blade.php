@foreach($listing->items() as $k => $row)
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
			<div class="avatar-wrapper">
				<div class="avatar avatar-sm me-3">
					<img src="{{ $row->avatar_url }}" alt="Avatar" class="rounded-circle">
				</div>
			</div>
			<div class="d-flex flex-column">
				<a href="{{ route('users.view', ['id' => $row->id]) }}" class="text-body text-truncate">
					<span class="fw-medium">{{ $row->name ?: 'Unnamed User' }}</span>
				</a>
				<a href="mailto:{{ $row->username }}">
					<small class="text-muted">
						{{ $row->username }}
					</small>
				</a>
				<small class="text-muted">
					{{ '+91-'.$row->phone_number }}
				</small>
			</div>
		</div>
	</td>
	<td>
		 @if($row->role)
            @php
                $roleSlug = strtolower($row->role->short_name ?? '');
                $badgeBg = 'var(--accent-blue-subtle)';
                $badgeColor = 'var(--accent-blue)';
                $badgeBorder = 'var(--accent-blue-border)';
                if ($roleSlug === 'admin') {
                    $badgeBg = 'var(--red-subtle)';
                    $badgeColor = 'var(--primary-red)';
                    $badgeBorder = 'var(--red-border)';
                } elseif (in_array($roleSlug, ['gate', 'weight'])) {
                    $badgeBg = 'rgba(14, 165, 233, 0.1)';
                    $badgeColor = '#0284C7';
                    $badgeBorder = 'rgba(14, 165, 233, 0.3)';
                } elseif (in_array($roleSlug, ['lab', 'production', 'lab_production'])) {
                    $badgeBg = 'rgba(245, 158, 11, 0.1)';
                    $badgeColor = '#D97706';
                    $badgeBorder = 'rgba(245, 158, 11, 0.3)';
                } elseif (in_array($roleSlug, ['sale', 'purchase', 'account'])) {
                    $badgeBg = 'rgba(16, 185, 129, 0.1)';
                    $badgeColor = '#059669';
                    $badgeBorder = 'rgba(16, 185, 129, 0.3)';
                }
            @endphp
            <span class="badge px-2.5 py-1.5 rounded-pill border fw-semibold" style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; border-color: {{ $badgeBorder }} !important; font-size: 0.75rem;">
                <i class="fa-solid fa-shield-cat me-1"></i>{{ $row->role->name }}
            </span>
        @else
            <span class="badge bg-secondary">No Role</span>
        @endif
	</td>
	
	<td>
		<div class="d-flex justify-content-start align-items-center user-name">
			<div class="d-flex flex-column">
				<span style="color: var(--text-heading);"><i class="fa-regular fa-envelope me-1.5 text-muted"></i>{{ $row->email }}</span>
                <span class="text-muted mt-0.5"><i class="fa-solid fa-phone me-1.5"></i>{{ $row->phone_number ?: 'Not provided' }}</span>
			</div>
		</div>
	</td>
    <td>
        @if($row->status == 'active')
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
        <a class="dropdown-item" href="{{ route('users.edit', ["back_url" => url()->full(),'id' => $row->id]) }}"><i class="fas fa-edit text-primary"></i> </a>
        <a class="dropdown-item" href="{{ route('users.view', ["back_url" => url()->full(),'id' => $row->id]) }}"><i class="fas fa-eye text-info"></i> </a>
        <a class="dropdown-item delete_confirm" href="{{ route('users.delete', ['id' => $row->id]) }}"><i class="fas fa-trash-alt text-danger"></i> </a>
    </td>

</tr>
@endforeach