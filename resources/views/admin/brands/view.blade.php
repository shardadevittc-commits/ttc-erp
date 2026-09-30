@extends('layouts.adminlayout')
@section('content')
@php
	$back = request('back_url');
@endphp
<div class="header-body">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-6 col-7">
				<div class="left_area">
					<h6>Manage Trainers</h6>
				</div>
			</div>
			<div class="col-lg-6 col-5">
				<div class="right_area text-right">
					@if(Permission::hasPermission('trainers','update'))
					<a href="{{ route('admin.trainers.edit', ['id' => $trainer->id]) }}" class="btn btn-default">
						<i class="far fa-edit"></i> Edit
					</a>
					@endif
					<a href="{{ @$back ? $back : route('admin.trainers') }}" class="btn btn-default ms-1">
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
			@include('admin.partials.flash_messages')
			<div class="col-xxl-8 col-xl-8 col-lg-7 col-md-6 col-sm-12 col-12">
				<!-- ==== Trainer Information -->
				<div class="card">
					<div class="card-header view_header">
			        	<div class="heading">
							<h5 class="mb-0">Trainer Information</h5>
						</div>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table">
								<tbody>
									<tr>
										<th>Id</th>
										<td>{{ $trainer->id }}</td>
									</tr>
									<tr>
										<th>Name</th>
										<td>{{ $trainer->first_name.' '.$trainer->last_name }}</td>
									</tr>
									@if(isset($trainer->designation) && $trainer->designation)
									<tr>
										<th>Designation</th>
										<td>
											<span class="badge bg-success">{{ @$trainer->designation ?? '---' }}</span>
										</td>
									</tr>
									@endif
									<tr>
										<th>Email</th>
										<td>
											<a href="mailto:{{ @$trainer->email ?? '' }}">
												{{ @$trainer->email ?? '' }}
											</a>
										</td>
									</tr>
									<tr>
										<th>Phone Number</th>
										<td>
											<a href="tel:{{ @$trainer->phonenumber ?? '' }}">
												{{ @$trainer->phonenumber ?? '' }}
											</a>
										</td>
									</tr>
									<tr>
										<th>Gender</th>
										<td>{{ ucfirst(@$trainer->gender) ?? '' }}</td>
									</tr>
									<tr>
										<th>DOB</th>
										<td>{{ date('d F Y',strtotime($trainer->dob)) }}</td>
									</tr>
									<tr>
										<td colspan="2">
											<h5>Description</h5>
											{!! $trainer->description !!}
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				@if(@$users && count($users) > 0)
				<div class="card mt-4">
					<div class="card-header view_header">
			        	<div class="heading">
							<h5 class="mb-0">Clients Information</h5>
						</div>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive" style="height: 464px;">
							<table class="table">
								<thead class="thead-light">
									<tr>
										<th>Id</th>
										<th>Name</th>
										<th>Gender</th>
										<th>Trainee Plain Expires</th>
										<th>Membership Expires</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									@foreach($users as $k => $row)
									<tr>
										<td>{{ $row->id }}</td>
										<td>
											<div class="d-flex justify-content-start align-items-center user-name">
												<div class="avatar-wrapper">
													<div class="avatar avatar-sm me-3">
														<img src="{{ General::renderImageUrl($row->image, 'medium') }}" alt="Avatar" class="rounded-circle">
													</div>
												</div>
												<div class="d-flex flex-column">
													<a href="{{ route('admin.users.view', ['id' => $row->id]) }}" class="text-body text-truncate">
														<span class="fw-medium">{{ $row->first_name.' '.$row->last_name }}</span>
													</a>
												</div>
											</div>
										</td>
										<td>{{ ucfirst(@$row->gender) ?? '' }}</td>
										<td>{{ date('d F Y',strtotime($row->trainee_plain_expires_at)) }}</td>
										<td>{{ date('d F Y',strtotime($row->membership_expires_at)) }}</td>
										<td>
											<a href="{{ route('admin.trainers.remove',['id' => $row->id]) }}">
												Remove
											</a>
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
				@endif
			</div>
			<div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-12 col-12">
				<!-- ==== Other Information -->
				@if($trainer->image)
				<!-- ==== Attachment -->
				<div class="card mb-4">
					<div class="card-body">
						<img src="{{ General::renderImageUrl($trainer->image, 'large') }}" class="mw-100">
					</div>
				</div>
				@endif
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
											{{ _dt($trainer->created_at) }}
										</td>
									</tr>
									<tr>
										<th>Updated On</th>
										<td>
											{{ _dt($trainer->updated_at) }}
										</td>
									</tr>
									<tr>
										<th>Created By</th>
										<td>
											{{ isset($trainer->owner) ? $trainer->owner->first_name . ' ' . $trainer->owner->last_name : "" }}
										</td>
									</tr>
									<tr>
										<th>Status</th>
										<th>
											{!! $trainer->status ? '<span class="badge bg-success">Publish</span>' : '<span class="badge bg-danger">Unpublish</span>' !!}
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