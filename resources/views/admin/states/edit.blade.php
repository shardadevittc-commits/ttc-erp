@extends('layouts.adminlayout')
@section('content')

<div class="header-body">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-6 col-7">
				<div class="left_area">
					<h6>Manage State</h6>
				</div>
			</div>
			<div class="col-lg-6 col-5">
				<div class="right_area text-right">
					<a href="{{ route('admin.states') }}" class="btn btn-default">
						<i class="fas fa-angle-left"></i> Back
					</a>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="content_area">
    <div class="container-xxl flex-grow-1 container-p-y">
    	<!--!! FLAST MESSAGES !!-->
    	@include('admin.partials.flash_messages')
    	<div class="row">
    		<div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    			<div class="card">
    				<h5 class="card-header">Update State Details Here.</h5>
    				<hr class="my-0" />
    				<div class="card-body">
    					<form action="{{ route('admin.states.edit', ['id' => $states->id]) }}" method="post" class="form-validation">
    						<!--!! CSRF FIELD !!-->
    						{{ csrf_field() }}
    						<div class="row">
    							<div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    								<div class="form-group">
    									<label class="form-label">Select Country</label>
    									<select class="select2 form-select" name="country_id" data-placeholder="Nothing selected" required>
    										<option value=""></option>
    								      	@foreach($countries as $c)
    								      		<option value="{{ $c['id'] }}" {{ isset($states->country_id) && $states->country_id == $c['id'] ? 'selected' : '' }}>{{ $c->name }}</option>
    								  		@endforeach
    								    </select>
    								    <label id="country_id-error" class="error" for="country_id">@error('country_id') {{ $message }} @enderror</label>
    								</div>
    							</div>
    							<div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    								<div class="form-group">
    									<label class="form-label">State Name</label>
    									<input type="text" class="form-control" name="name" value="{{ old('name', $states->name) }}" placeholder="Enter state name" required />
    									<label id="name-error" class="error" for="name">@error('name') {{ $message }} @enderror</label>
    								</div>
    							</div>
    							{{-- <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    								<div class="form-group">
    									<label class="form-label">State Code</label>
    									<input type="text" class="form-control" name="state_code" value="{{ old('state_code', $states->state_code) }}" placeholder="Enter state code" required />
    									<label id="state_code-error" class="error" for="state_code">@error('state_code') {{ $message }} @enderror</label>
    								</div>
    							</div> --}}
    							<div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    								<div class="form-group">
    									<label class="form-label">Publish or Unpublish State</label>
    									<div class="form-check form-switch mt-2">
    										<input type="hidden" name="status" value="0">
    			                    		<input type="checkbox" name="status" class="form-check-input" id="status" value="1" {{ old('status', $states->status) == 1 ? 'checked' : '' }}/>
    										<label class="form-check-label" for="status">Do you want to publish this state ?</label>
    			                      	</div>
    			                    </div>
    			                </div>
    						</div>
    						<div class="form-group mt-2 clearfix">
    							<button type="submit" class="btn btn-primary float-end">Submit</button>
    						</div>
    					</form>
    				</div>
    			</div>
    		</div>
    	</div>
    </div>
</div>

@endsection