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
					<a href="{{ route('admin.states.edit', ['id' => $states->id]) }}" class="btn btn-default">
						<i class="far fa-edit"></i> Edit
					</a>
					<a href="{{ route('admin.states') }}" class="btn btn-default ms-1">
						<i class="far fa-angle-left"></i> Back
					</a>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="content_area">
	<div class="container-xxl flex-grow-1 container-p-y">
		<div class="row">
			<div class="col-xxl-8 col-xl-8 col-lg-7 col-md-6 col-sm-12 col-12">
				<!-- ==== State Information -->
				<div class="card">
					<div class="card-header view_header">
			        	<div class="heading">
							<h5 class="mb-0">State Information</h5>
						</div>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive text-nowrap">
							<table class="table">
								<tbody>
									<tr>
										<th>Id</th>
										<td>{{ $states->id }}</td>
									</tr>
									<tr>
										<th>Country Name</th>
										<th>{{ $states->country->name }}</th>
									</tr>
									<tr>
										<th>State Name</th>
										<td>{{ $states->name }}</td>
									</tr>
									@if(isset($states->state_code) && $states->state_code)
									<tr>
										<th>State Code</th>
										<td>{{ $states->state_code }}</td>
									</tr>
									@endif
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-12 col-12">
				<!-- ==== Other Information -->
				<div class="card">
					<div class="card-header view_header">
			        	<div class="heading">
							<h5 class="mb-0">Other Information</h5>
						</div>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive text-nowrap">
							<table class="table">
								<tbody>
									<tr>
										<th>Created On</th>
										<td>
											{{ _dt($states->created_at) }}
										</td>
									</tr>
									<tr>
										<th>Updated On</th>
										<td>
											{{ _dt($states->updated_at) }}
										</td>
									</tr>
									<tr>
										<th>Status</th>
										<th>
											{!! $states->status ? '<span class="badge bg-success">Publish</span>' : '<span class="badge bg-danger">Unpublish</span>' !!}
										</th>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection