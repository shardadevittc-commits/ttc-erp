@extends('layouts.app')
@section('content')
@php
	$back = request('back_url');
@endphp
<div>
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb mb-1" style="font-size: 0.8rem;">
			<li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
			<li class="breadcrumb-item"><a href="{{ route('customers') }}" class="text-decoration-none text-muted">Customers</a></li>
			<li class="breadcrumb-item active text-danger fw-semibold" aria-current="page">View Customer</li>
		</ol>
	</nav>
</div> 

<div class="page-header">
    <div class="page-header__inner">
        <div class="page-header__content"><div class="page-header__title"><h6>Customer Details</h6></div></div>
        <div class="page-header__actions">
            <a href="{{ route('customers.edit', ['id' => $customer->id]) }}" class="btn btn-outline-primary"><i class="far fa-edit me-1"></i> Edit</a>
            <a href="{{ $back ?? route('customers') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Back</a>
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
										<td>{{ $customer->id }}</td>
									</tr>
									<tr>
										<th>Customer Code</th>
										<td>{{ $customer->cust_code }}</td>
									</tr>
									<tr>
										<th>Party Name</th>
										<td>{{ $customer->company_name ?: '-' }} </td>
									</tr>
									<tr>
										<th>GST Number</th>
										<td>{{ $customer->gst_no ?: '-' }}</td>
									</tr>
									<tr>
										<th>Email</th>
										<td>{{ $customer->email ?: '-' }}</td>
									</tr>
									<tr>
										<th>Contact</th>
										<td>{{ trim(($customer->country_code ? '+' . $customer->country_code . ' ' : '') . ($customer->mobile ?? '')) ?: '-' }}</td>
									</tr>
									<tr>
										<th>Country</th>
										<td>{{ $customer->country?->name ?: '-' }}</td>
									</tr>
									<tr>
										<th>State</th>
										<td>{{ $customer->state?->name ?: '-' }}</td>
									</tr>
									<tr>
										<th>City</th>
										<td>{{ $customer->city_name ?: '-' }}</td>
									</tr>
									<tr>
										<th>Pincode</th>
										<td>{{ $customer->pincode ?: '-' }}</td>
									</tr>
									<tr>
										<th>Party Type</th>
										<td>{{ $customer->buyer === 1 ? 'Buyer' : '' }}
											{{ $customer->buyer === 1 && $customer->supplier === 1 ? ' / ' : '' }}
											{{ $customer->supplier === 1 ? 'Supplier' : '' }}
											{{ $customer->buyer !== 1 && $customer->supplier !== 1 ? '-' : '' }}
										</td>
									</tr>
									<tr>
										<th>Address</th>
										<td colspan="2">{{ $customer->address ?: '-' }}</td>
									</tr>
									{{-- <tr>
										<th>Status</th>
										<td colspan="3">{{ $customer->status === 1 ? 'Active' : 'Inactive' }}</td>
									</tr> --}}
									{{-- <tr>
										<td colspan="2">
											<h5>Description</h5>
											{!! $customer->description !!}
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
											{{ ($customer->created_at) }}
										</td>
									</tr>
									<tr>
										<th>Updated On</th>
										<td>
											{{ ($customer->updated_at) }}
										</td>
									</tr>
									<tr>
										<th>Created By</th>
										<td>
											{{ isset($customer->owner) ? $customer->owner->first_name . ' ' . $customer->owner->last_name : "" }}
										</td>
									</tr>
									<tr>
										<th>Status</th>
										<th>
											{!! $customer->status ? '<span class="badge bg-success">Publish</span>' : '<span class="badge bg-danger">Unpublish</span>' !!}
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