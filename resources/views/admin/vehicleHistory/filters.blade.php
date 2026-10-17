<a href="javascript:;" class="btn btn-default web-filter" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	@if(isset($_GET) && !empty($_GET))
	<span class="filter-dot text-info"><i class="fas fa-circle"></i></span>
	@endif
	<i class="fas fa-filter"></i> Filters
</a>
<div class="dropdown-menu dropdown-menu-end">
	<form action="{{ route('products') }}" id="filters-form">
		<a href="javascript:;" class="float-end px-2 closeit">
			<i class="fa fa-times-circle"></i>
		</a>
		{{-- <div class="dropdown-item">
			<div class="form-group mb-0">
				<label class="form-label">Created By</label>
				<select class="select2 form-select" name="admins[]" data-placeholder="Nothing selected" multiple>
			      	@foreach($admins as $c)
			      		<option value="{{ $c->id }}" {{ isset($_GET['admins']) && in_array($c->id, $_GET['admins'])  ? 'selected' : '' }}>{{ $c->first_name . ' ' . $c->last_name }}</option>
			  		@endforeach
			    </select>
			</div>
		</div> --}}
		
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
			<div class="form-group mt-2 mb-0">
				<label class="form-label d-none">Status</label>
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="status" id="all" value="" {{ (!isset($_GET['status']) || $_GET['status'] === '' || $_GET['status'] === null ? 'checked' : '') }}/>
					<label class="form-check-label" for="all">All</label>
				</div>
				<div class="form-check form-check-inline">
					<input type="radio" class="form-check-input" name="status" id="publish" value="1" {{ (isset($_GET['status']) && $_GET['status'] == '1' ? 'checked' : '') }}/>
					<label class="form-check-label" for="publish">Publish</label>
				</div>
				<div class="form-check form-check-inline">
					<input type="radio" class="form-check-input" name="status" id="unpublish" value="0" {{ (isset($_GET['status']) && $_GET['status'] == '0' ? 'checked' : '') }}/>
					<label class="form-check-label" for="unpublish">Unpublish</label>
				</div>
			</div>
		</div>
		<div class="dropdown-divider"></div>
		<div class="dropdown-bottom clearfix">
			<a href="{{ route('products') }}" class="btn btn-sm btn-danger px-3 float-start">
				Reset All
			</a>
			<button type="submit" class="btn btn-sm px-3 btn-primary float-end">Submit</button>
		</div>
	</form>
</div>