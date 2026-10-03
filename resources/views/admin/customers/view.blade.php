@extends('layouts.app')
@section('content')
@php
	$back = request('back_url');
@endphp
<div>
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb mb-1" style="font-size: 0.8rem;">
			<li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
			<li class="breadcrumb-item"><a href="{{ route('sizes') }}" class="text-decoration-none text-muted">Sizes</a></li>
			<li class="breadcrumb-item active text-danger fw-semibold" aria-current="page">View Size</li>
		</ol>
	</nav>
</div> 

<div class="page-header">
	
	<div class="page-header__inner">
		<div class="page-header__content">
			<div class="page-header__title">
				<h6>Manage Sizes</h6>
			</div>
		</div>

		<div class="page-header__actions">
			<a href="{{ route('sizes.edit', ['id' => $sizes->id]) }}" class="btn btn-outline-secondary px-3 py-2 fw-semibold rounded-3"><i class="far fa-edit"></i> Edit </a>
			<a href="{{ @$back ? $back : route('sizes') }}" class="btn btn-outline-secondary px-3 py-2 fw-semibold rounded-3"> <i class="fa-solid fa-arrow-left me-1"></i> Back </a>
		</div>
	</div>
</div>

<div class="content_area">
	<div class="container-xxl flex-grow-1 container-p-y">
		<div class="row">
			<div class="col-xxl-8 col-xl-8 col-lg-7 col-md-6 col-sm-12 col-12">
				<!-- ==== Size Information -->
				<div class="card">
					<div class="card-header view_header">
			        	<div class="heading">
							<h5 class="mb-0">Size Information</h5>
						</div>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table">
								<tbody>
									<tr>
										<th>Id</th>
										<td>{{ $sizes->id }}</td>
									</tr>
									<tr>
										<th>Name</th>
										<td>{{ $sizes->size_name }}</td>
									</tr>
									{{-- <tr>
										<td colspan="2">
											<h5>Description</h5>
											{!! $sizes->description !!}
										</td>
									</tr> --}}
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
											{{ ($sizes->created_at) }}
										</td>
									</tr>
									<tr>
										<th>Updated On</th>
										<td>
											{{ ($sizes->updated_at) }}
										</td>
									</tr>
									<tr>
										<th>Created By</th>
										<td>
											{{ isset($sizes->owner) ? $sizes->owner->first_name . ' ' . $sizes->owner->last_name : "" }}
										</td>
									</tr>
									<tr>
										<th>Status</th>
										<th>
											{!! $sizes->status ? '<span class="badge bg-success">Publish</span>' : '<span class="badge bg-danger">Unpublish</span>' !!}
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