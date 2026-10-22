@extends('layouts.app')

@section('title', 'Material In | TTC Robotronics - Steel Industry ERP')

@push('styles')
@endpush

@section('content')

    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-headerinner">

            <div class="page-headercontent">
                <div class="page-headertitle">
                    <h6>Material In</h6>
                </div>
            </div>

            <div class="page-headeractions">
                <a href="{{ url()->previous() }}" class="header-btn header-btn--secondary">
                    <i class="fas fa-arrow-left"></i>
                    Back
                </a>
            </div>

        </div>
    </div>

    {{-- Content Area --}}
    <div class="content_area">
        <div class="flex-grow-1 container-p-y">

            <div class="row">
                <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

                    {{-- Error Message --}}
                    {{-- @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Oh snap!</strong>
                            {{ session('error') }}

                            <button type="button"
                                class="btn-close"
                                data-bs-dismiss="alert"
                                aria-label="Close">
                            </button>
                        </div>
                    @endif --}}

                    {{-- Success Message --}}
                    {{-- @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}

                            <button type="button"
                                class="btn-close"
                                data-bs-dismiss="alert"
                                aria-label="Close">
                            </button>
                        </div>
                    @endif --}}

                    {{-- Material In Card --}}
                    <div class="card">

                        {{-- Card Header --}}
                        <div class="card-header">
                            <div class="heading">
                                <h5 class="mb-0">Material In Details</h5>
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="card-body">

                            <form action="{{ route('material_in') }}" method="POST" enctype="multipart/form-data" id="materialInForm">

                                @csrf

                                @if($materialIn)
                                    <input type="hidden" name="id" value="{{ $materialIn->id }}">
                                @endif

                                <div class="row">

                                    {{-- Token No --}}
                                    {{-- <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 mb-3">
                                        <div class="form-group">

                                            <label for="tokenno" class="form-label">
                                                Token No.
                                            </label>

                                            <div class="input-group">
                                                <input type="text"
                                                    name="tokenno"
                                                    id="tokenno"
                                                    class="form-control"
                                                    value="{{ old('tokenno', $materialIn->tokenno ?? '') }}"
                                                    readonly>

                                                <button type="button"
                                                    class="btn btn-primary"
                                                    id="generateTokenBtn">
                                                    <i class="fas fa-sync-alt me-1"></i>
                                                    Generate
                                                </button>
                                            </div>

                                            @error('tokenno')
                                                <label class="error">
                                                    {{ $message }}
                                                </label>
                                            @enderror

                                        </div>
                                    </div> --}}

                                    {{-- Party Name --}}
                                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 mb-3">
                                        <div class="form-group">

                                            <label for="partyname" class="form-label">
                                                Party Name
                                            </label>

                                            <select name="cust_id"
                                                id="cust_id"
                                                class="form-select" required>

                                                <option value="">
                                                    Select Party
                                                </option>

                                                @foreach ($parties ?? [] as $party)
                                                    <option value="{{ $party->id }}"
                                                        {{ old('cust_id', $materialIn->cust_id ?? '') == $party->id ? 'selected' : '' }}>
                                                        {{ $party->company_name ?? $party->company_name }}
                                                    </option>
                                                @endforeach

                                            </select>

                                            @error('cust_id')
                                                <label class="error">
                                                    {{ $message }}
                                                </label>
                                            @enderror

                                        </div>
                                    </div>

                                    {{-- Form List --}}
                                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 mb-3">
                                        <div class="form-group">

                                            <label for="formlist" class="form-label">
                                                Form List
                                            </label>

                                            <select name="formlist"
                                                id="formlist"
                                                class="form-select">

                                                <option value="">
                                                    Select Form List
                                                </option>

                                                <option value="Material In"
                                                    {{ old('formlist', $materialIn->formlist ?? '') == 'Material In' ? 'selected' : '' }}>
                                                    Material In
                                                </option>

                                                <option value="Material Out"
                                                    {{ old('formlist', $materialIn->formlist ?? '') == 'Material Out' ? 'selected' : '' }}>
                                                    Material Out
                                                </option>

                                                <option value="Return"
                                                    {{ old('formlist', $materialIn->formlist ?? '') == 'Return' ? 'selected' : '' }}>
                                                    Return
                                                </option>

                                            </select>

                                            @error('formlist')
                                                <label class="error">
                                                    {{ $message }}
                                                </label>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Truck No --}}
                                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 mb-3">
                                        <div class="form-group">
                                            <label for="truckno" class="form-label">Truck No.</label>
                                            <input type="text" name="truckno" id="truckno" class="form-control text-uppercase" placeholder="PB10EW1234" value="{{ old('truckno', $materialIn->truckno ?? '') }}" maxlength="10" pattern="[A-Za-z]{2}[0-9]{2}[A-Za-z]{2}[0-9]{4}" title="Enter truck number in format PB10EW1234" style="text-transform: uppercase;" required>
                                            @error('truckno')<label class="error">{{ $message }}</label>@enderror
                                        </div>
                                    </div>

                                    {{-- Vehicle Entry Slip --}}
                                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 mb-3">
                                        <div class="form-group">
                                            <label for="vehicleentryslip" class="form-label">Vehicle Entry Slip</label>
                                            <input type="file" name="vehicleentryslip" id="vehicleentryslip" class="form-control" accept=".jpg,.jpeg,.png,.pdf">

                                            @error('vehicleentryslip')<label class="error">{{ $message }}</label>@enderror
                                            <small class="text-muted">JPG, JPEG, PNG or PDF</small>
                                        </div>
                                    </div>

                                    {{-- Transport Name --}}
                                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 mb-3">
                                        <div class="form-group">
                                            <label for="transportname" class="form-label">Transport Name</label>
                                            <input type="text" name="transportname" id="transportname" class="form-control" placeholder="Enter Transport Name" value="{{ old('transportname', $materialIn->transportname ?? '') }}">
                                            @error('transportname')<label class="error">{{ $message }}</label>@enderror
                                        </div>
                                    </div>

                                    {{-- No. Of Persons --}}
                                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 mb-3">
                                        <div class="form-group">
                                            <label for="noofpersons" class="form-label">No. Of Persons</label>
                                            <input type="number" name="noofpersons" id="noofpersons" class="form-control" min="0" placeholder="Enter No. Of Persons" value="{{ old('noofpersons', $materialIn->noofpersons ?? '') }}">
                                            @error('noofpersons')<label class="error">{{ $message }}</label>@enderror
                                        </div>
                                    </div>

                                    {{-- Driver Name --}}
                                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 mb-3">
                                        <div class="form-group">
                                            <label for="drivername" class="form-label">Driver Name</label>
                                            <input type="text" name="drivername" id="drivername" class="form-control" placeholder="Enter Driver Name" value="{{ old('drivername', $materialIn->drivername ?? '') }}">
                                            @error('drivername')<label class="error">{{ $message }}</label>@enderror
                                        </div>
                                    </div>

                                    {{-- Contact No --}}
                                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 mb-3">
                                        <div class="form-group">
                                            <label for="contactno" class="form-label">Contact No.</label>
                                            <input type="text" name="contactno" id="contactno" class="form-control number_only" maxlength="10" inputmode="numeric" placeholder="Enter Contact No." value="{{ old('contactno', $materialIn->contactno ?? '') }}">
                                            @error('contactno')<label class="error">{{ $message }}</label>@enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Form Actions --}}
                                <div class="border-top pt-3 mt-3">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary"> <i class="fas fa-times me-1"></i>
                                            Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-check me-1"></i>
                                            Save
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
