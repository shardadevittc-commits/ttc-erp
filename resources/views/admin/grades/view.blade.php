@extends('layouts.app')
@section('content')
@php
	$back = request('back_url');
@endphp
<div>
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb mb-1" style="font-size: 0.8rem;">
			<li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
			<li class="breadcrumb-item"><a href="{{ route('grades') }}" class="text-decoration-none text-muted">Grades</a></li>
			<li class="breadcrumb-item active text-danger fw-semibold" aria-current="page">View Grade</li>
		</ol>
	</nav>
</div> 

<div class="page-header">
	
	<div class="page-header__inner">
		<div class="page-header__content">
			<div class="page-header__title">
				<h6>Manage Grades</h6>
			</div>
		</div>

		<div class="page-header__actions">
			<a href="{{ route('grades.edit', ['id' => $grade->id]) }}" class="btn btn-outline-secondary px-3 py-2 fw-semibold rounded-3"><i class="far fa-edit"></i> Edit </a>
			<a href="{{ @$back ? $back : route('grades') }}" class="btn btn-outline-secondary px-3 py-2 fw-semibold rounded-3"> <i class="fa-solid fa-arrow-left me-1"></i> Back </a>
		</div>
	</div>
</div>

<div class="content_area">
	<div class="container-xxl flex-grow-1 container-p-y">
		<div class="row">
			<div class="col-xxl-8 col-xl-8 col-lg-7 col-md-6 col-sm-12 col-12">
				<!-- ==== Grades Information -->
				<div class="card">
					<div class="card-header view_header">
			        	<div class="heading">
							<h5 class="mb-0">Grade Information</h5>
						</div>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table">
								<tbody>
									<tr>
										<th>Id</th>
										<td>{{ $grade->id }}</td>
									</tr>
									<tr>
										<th>Name</th>
										<td>{{ $grade->grade_name }}</td>
									</tr>
									{{-- <tr>
										<td colspan="2">
											<h5>Description</h5>
											{!! $grade->description !!}
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
											{{ ($grade->created_at) }}
										</td>
									</tr>
									<tr>
										<th>Updated On</th>
										<td>
											{{ ($grade->updated_at) }}
										</td>
									</tr>
									<tr>
										<th>Created By</th>
										<td>
											{{ isset($grade->createdBy) ? $grade->createdBy->first_name . ' ' . $grade->createdBy->last_name : "" }}
										</td>
									</tr>
									<tr>
										<th>Status</th>
										<th>
											{!! $grade->status ? '<span class="badge bg-success">Publish</span>' : '<span class="badge bg-danger">Unpublish</span>' !!}
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