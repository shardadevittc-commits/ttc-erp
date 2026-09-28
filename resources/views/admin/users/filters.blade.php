<a href="javascript:;" class="btn btn-default web-filter" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	@if(isset($_GET) && (isset($_GET['created_on']) && $_GET['created_on']) || (isset($_GET['last_login']) && $_GET['last_login']) || (isset($_GET['status']) && $_GET['status']))
	<span class="filter-dot text-info"><i class="fas fa-circle"></i></span>
	@endif
	<i class="fas fa-filter"></i> Filters
</a>
<div class="dropdown-menu dropdown-menu-end" >
	<form action="{{ route('users.users') }}" id="filters-form">
		<a href="javascript:;" class="float-end px-2 closeit">
			<i class="fa fa-times-circle"></i>
		</a>
		<div class="dropdown-item">
			<div class="row">
				<div class="col-md-6">
					<label class="form-label">Created On</label>
					<input class="form-control" type="date" name="created_on[0]" value="{{ (isset($_GET['created_on'][0]) && !empty($_GET['created_on'][0]) ? $_GET['created_on'][0] : '' ) }}" placeholder="DD-MM-YYYY" >
				</div>
				<div class="col-md-6">
					<label class="form-label">&nbsp;</label>
					<input class="form-control" type="date" name="created_on[1]" value="{{ (isset($_GET['created_on'][1]) && !empty($_GET['created_on'][1]) ? $_GET['created_on'][1] : '' ) }}" placeholder="DD-MM-YYYY">
				</div>
			</div>
		</div>
		<div class="dropdown-divider"></div>
		<div class="dropdown-item">
			<div class="row">
				<div class="col-md-6">
					<label class="form-label">Last Login</label>
					<input class="form-control" type="date" name="last_login[0]" value="{{ (isset($_GET['last_login'][0]) && !empty($_GET['last_login'][0]) ? $_GET['last_login'][0] : '' ) }}" placeholder="DD-MM-YYYY" >
				</div>
				<div class="col-md-6">
					<label class="form-label">&nbsp;</label>
					<input class="form-control" type="date" name="last_login[1]" value="{{ (isset($_GET['last_login'][1]) && !empty($_GET['last_login'][1]) ? $_GET['last_login'][1] : '' ) }}" placeholder="DD-MM-YYYY">
				</div>
			</div>
		</div>
		<div class="dropdown-divider"></div>
		<div class="dropdown-bottom clearfix">
			<a href="{{ route('users.users') }}" class="btn btn-sm btn-danger px-3 float-start">
				Reset All
			</a>
			<button type="submit" class="btn btn-sm px-3 btn-primary float-end">Submit</button>
		</div>
	</form>
</div>



{{-- <div class="card border mb-4 rounded-3" style="background: var(--bg-card); border-color: var(--border-card) !important; box-shadow: var(--card-shadow);">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('users.users') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text border-end-0" style="background: var(--bg-input); border-color: var(--border-color); color: var(--text-muted);">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="Search by name, username, email, phone..." style="background: var(--bg-input); border-color: var(--border-color); color: var(--text-heading);">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="role_id" class="form-select" style="background: var(--bg-input); border-color: var(--border-color); color: var(--text-heading);">
                    <option value="">-- All Assigned Roles --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="status" class="form-select" style="background: var(--bg-input); border-color: var(--border-color); color: var(--text-heading);">
                    <option value="">-- Status --</option>
                    <option value="{{ \App\Models\User::STATUS_ACTIVE }}" {{ request('status') == \App\Models\User::STATUS_ACTIVE ? 'selected' : '' }}>Active</option>
                    <option value="{{ \App\Models\User::STATUS_INACTIVE }}" {{ request('status') == \App\Models\User::STATUS_INACTIVE ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-semibold rounded-3" style="background: var(--accent-blue); border-color: var(--accent-blue);">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'role_id', 'status']))
                    <a href="{{ route('users.users') }}" class="btn btn-outline-secondary rounded-3" title="Clear Filters">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div> --}}