<a href="javascript:;" class="btn btn-default web-filter" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    @if (request()->filled('created_on.0') || request()->filled('created_on.1') || request()->filled('status'))
        <span class="filter-dot text-info"><i class="fas fa-circle"></i></span>
    @endif
    <i class="fas fa-filter"></i> Filters
</a>
<div class="dropdown-menu dropdown-menu-end">
    <form action="{{ route(request()->routeIs('production-history') ? 'production-history' : 'productions') }}" id="filters-form">
        <a href="javascript:;" class="float-end px-2 closeit">
            <i class="fa fa-times-circle"></i>
        </a>
        <div class="dropdown-item">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Order Date From</label>
                    <input class="form-control" type="date" name="created_on[0]" value="{{ request('created_on.0', '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Order Date To</label>
                    <input class="form-control" type="date" name="created_on[1]" value="{{ request('created_on.1', '') }}">
                </div>
            </div>
        </div>
        <div class="dropdown-divider"></div>
        <div class="dropdown-item">
            <div class="form-group mt-2 mb-0">
                <label class="form-label">{{ request()->routeIs('production-history') ? 'Work Order Status' : 'Completion' }}</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="production-all" value="" {{ request('status', '') === '' ? 'checked' : '' }}>
                    <label class="form-check-label" for="production-all">All</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="production-incomplete" value="{{ request()->routeIs('production-history') ? 'under_process' : '1' }}" {{ request('status') === (request()->routeIs('production-history') ? 'under_process' : '1') ? 'checked' : '' }}>
                    <label class="form-check-label" for="production-incomplete">{{ request()->routeIs('production-history') ? 'Under Process' : 'Not Completed' }}</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="production-complete" value="{{ request()->routeIs('production-history') ? 'completed' : '2' }}" {{ request('status') === (request()->routeIs('production-history') ? 'completed' : '2') ? 'checked' : '' }}>
                    <label class="form-check-label" for="production-complete">Completed</label>
                </div>
            </div>
        </div>
        <div class="dropdown-divider"></div>
        <div class="dropdown-bottom clearfix">
            <a href="{{ route(request()->routeIs('production-history') ? 'production-history' : 'productions') }}" class="btn btn-sm btn-danger px-3 float-start">Reset All</a>
            <button type="submit" class="btn btn-sm px-3 btn-primary float-end">Submit</button>
        </div>
    </form>
</div>
