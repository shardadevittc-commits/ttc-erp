@extends('layouts.app')

@section('title', ($customer ? 'Edit' : 'Add') . ' Customer')

@section('content')
{{-- @php
    $detailsValue = old('gst_details', json_encode($customer?->gst_details ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
@endphp --}}
<div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1" style="font-size: 0.8rem;">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customers') }}" class="text-decoration-none text-muted">Customers</a></li>
            <li class="breadcrumb-item active text-danger fw-semibold" aria-current="page">{{ $customer ? 'Edit Customer' : 'Add Customer' }}</li>
        </ol>
    </nav>
</div>

<div class="page-header">
    <div class="page-header__inner">
        <div class="page-header__content"><div class="page-header__title"><h6>{{ $customer ? 'Edit Customer' : 'Add Customer' }}</h6></div></div>
        <div class="page-header__actions">
            <a href="{{ route('customers') }}" class="btn btn-outline-secondary px-3 py-2 fw-semibold rounded-3"><i class="fa-solid fa-arrow-left me-1"></i> Back</a>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ $customer ? route('customers.edit', ['id' => $customer->id]) : route('customers.add') }}"
                method="POST" class="common-form customer-form"
                {{-- data-states-url="{{ route('customers.location.states') }}" --}}
                data-cities-url="{{ route('customers.location.cities') }}"
                data-gst-url="{{ url('https://sheet.gstincheck.co.in/check/18189796bdcab5783baf477710e2562f') }}">
                @csrf
                <input type="hidden" name="gst_details" value="{{ old('gst_details', $customer?->gst_details) }}">
                <input type="hidden" name="customer_name" value="{{ old('customer_name', $customer?->customer_name) }}">
                <div class="row g-4">
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                        <label class="form-label" for="gst_no">GST No <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input id="gst_no" type="text" name="gst_no" placeholder="Enter Your Valid GST No" class="form-control @error('gst_no') is-invalid @enderror" value="{{ old('gst_no', $customer?->gst_no) }}" maxlength="15" autocomplete="off" required>
                            <button type="button" class="btn btn-outline-primary" id="verifyGst"><i class="fa-solid fa-shield-check me-1"></i> Verify</button>
                        </div>
                        @error('gst_no')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        <div class="small mt-1" id="gstStatus" role="status" aria-live="polite"></div>
                    </div>
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <label class="form-label" for="company_name">Party Name <span class="text-danger">*</span></label>
                        <input id="company_name" type="text" name="company_name" placeholder="Enter Party Name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name', $customer?->company_name) }}" required>
                        @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <label class="form-label" for="cust_code">Short Code <span class="text-danger">*</span></label>
                        <input id="cust_code" type="text" name="cust_code" placeholder="Enter Party Short Code" class="form-control @error('cust_code') is-invalid @enderror" value="{{ old('cust_code', $customer?->cust_code) }}" required>
                        @error('cust_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                        <label class="form-label" for="mobile">Contact No. <span class="text-danger">*</span></label>
                        <input id="mobile" type="text" name="mobile" placeholder="Enter Contact No." class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile', $customer?->mobile) }}" required>
                        @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                        <label class="form-label" for="email">E-mail <span class="text-danger">*</span></label>
                        <input id="email" type="email" name="email" placeholder="Enter E-mail" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $customer?->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <input type="hidden" name="country_id" value="19">
                    {{-- <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
                        <label class="form-label" for="country_id">Country <span class="text-danger">*</span></label>
                        <select id="country_id" name="country_id" class="form-select @error('country_id') is-invalid @enderror" required>
                            <option value="">Select country</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" @selected((string) old('country_id', $customer?->country_id) === (string) $country->id)>{{ $country->name }}</option>
                            @endforeach
                        </select>
                        @error('country_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div> --}}

                    {{-- @php
                    pr($states);
                    @endphp --}}
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                        <label class="form-label" for="state_id">State <span class="text-danger">*</span></label>
                        <select id="state_id" name="state_id" class="form-select select2 @error('state_id') is-invalid @enderror" required>
                            <option value="">Select state</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}" @selected((string) old('state_id', $customer?->state_id) === (string) $state->id)>{{ $state->name }}</option>
                            @endforeach
                        </select>
                        @error('state_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    {{-- <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                        <label class="form-label" for="city_id">City</label>
                        <select id="city_id" name="city_id" class="form-select select2 @error('city_id') is-invalid @enderror">
                            <option value="">Select city</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" @selected((string) old('city_id', $customer?->city_id) === (string) $city->id)>{{ $city->name }}</option>
                            @endforeach
                        </select>
                        @error('city_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div> --}}
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                        <label class="form-label" for="city">City</label>
                        <input id="city" type="text" name="city_name" placeholder="Enter City" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $customer?->city) }}">
                        @error('city_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                        <label class="form-label" for="pincode">Pincode / Zip Code</label>
                        <input id="pincode" type="text" name="pincode" placeholder="Enter Pincode / Zip Code" class="form-control @error('pincode') is-invalid @enderror" value="{{ old('pincode', $customer?->pincode) }}">
                        @error('pincode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <label class="form-label" for="cc">Address</label>
                        <textarea id="address" name="address" placeholder="Enter Full Address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $customer?->address) }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                        <label class="form-label d-block">Party Type <span class="text-danger">*</span></label>
                        <input type="hidden" name="buyer" value="2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="buyer" name="buyer" value="1" @checked((string) old('buyer', $customer?->buyer ?? 2) === '1')>
                            <label class="form-check-label" for="buyer">Buyer</label>
                        </div>
                        <input type="hidden" name="supplier" value="2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="supplier" name="supplier" value="1" @checked((string) old('supplier', $customer?->supplier ?? 2) === '1')>
                            <label class="form-check-label" for="supplier">Supplier</label>
                        </div>
                    </div>
                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                        <label class="form-label">Publish or Unpublish Customer</label>
                        <div class="form-check form-switch mt-2">
                            <input type="hidden" name="status" value="2">
                            <input type="checkbox" name="status" class="form-check-input me-1" id="status" value="1" @checked((string) old('status', $customer?->status ?? 1) === '1')>
                            <label class="form-check-label" for="status">Publish this customer</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('customers') }}" class="btn btn-light border px-4 py-2">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 py-2"><i class="fa-solid fa-check me-1"></i> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection