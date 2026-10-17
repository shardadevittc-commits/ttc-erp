@extends('layouts.app')

@section('title', 'Vehicle Weight Record | TTC Robotronics - Steel Industry ERP')

@push('styles')
<style>
    .weigh-record-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 0;
    }

    .weigh-record-table th {
        background-color: #f5f6fa;
        font-weight: 600;
        white-space: nowrap;
    }

    .weigh-record-table td,
    .weigh-record-table th {
        vertical-align: middle;
        padding: 12px 14px;
    }

    .weigh-record-table .form-control,
    .weigh-record-table .form-select {
        min-width: 130px;
    }

    .weigh-record-table .vehicle-slip {
        min-width: 200px;
    }

    .weigh-record-table .weight-input {
        max-width: 220px;
    }

    .weigh-record-table .rst-input {
        max-width: 160px;
    }

    .required-star {
        color: #dc3545;
    }

    .slip-status {
        color: #6c757d;
        font-size: 13px;
    }

    @media (max-width: 767px) {
        .weigh-record-table {
            min-width: 900px;
        }
    }
</style>
@endpush

@section('content')

@php
    $selectedForm = old(
        'formlist',
        $formlist ?? request('formlist', 'Material In')
    );

    // Material Out aur Return dono ke liye Tare Weight.
    $isTareWeight = in_array($selectedForm, ['Material Out', 'Return']);

    $weightField = $isTareWeight ? 'tareweight' : 'loadedweight';

    $weightLabel = $isTareWeight
        ? 'Tare Weight (Kgs)'
        : 'Loaded Weight (Kgs)';

    $weightValue = old(
        $weightField,
        $isTareWeight
            ? ($materialIn->tareweight ?? '')
            : ($materialIn->loadedweight ?? '')
    );
@endphp

{{-- Page Header --}}
<div class="page-header">
    <div class="page-headerinner">

        <div class="page-headercontent">
            <div class="page-headertitle">
                <h6>Vehicle Weigh Record</h6>
            </div>
        </div>

        <div class="page-headeractions">
            <a href="{{ url()->previous() }}"
               class="header-btn header-btn--secondary">
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
            <div class="col-12">

                {{-- Error Message --}}
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show"
                         role="alert">
                        <strong>Error!</strong>
                        {{ session('error') }}

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="alert"
                                aria-label="Close"></button>
                    </div>
                @endif

                {{-- Success Message --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show"
                         role="alert">
                        {{ session('success') }}

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="alert"
                                aria-label="Close"></button>
                    </div>
                @endif

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show"
                         role="alert">

                        <strong>Please check the following:</strong>

                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="alert"
                                aria-label="Close"></button>
                    </div>
                @endif

                {{-- Vehicle Weigh Record Card --}}
                <div class="card">

                    <div class="card-header">
                        <div class="heading">
                            <h5 class="weigh-record-title">
                                Vehicle Weigh Record
                            </h5>
                        </div>
                    </div>

                    <div class="card-body">

                        <form action="{{ route('material_weight') }}"
                              method="POST"
                              enctype="multipart/form-data"
                              id="vehicleWeighRecordForm">

                            @csrf

                            {{-- Preserve selected record and party --}}
                            <input type="hidden"
                                   name="id"
                                   value="{{ $id ?? request('id', '') }}">

                            {{-- Vehicle Details --}}
                            <div class="table-responsive">
                                <table class="table table-bordered weigh-record-table mb-4">

                                    <thead>
                                        <tr>
                                            <th>Vehicle Id.</th>
                                            <th>Vehicle No.</th>
                                            <th>Party Name</th>
                                            <th>Entry Type</th>
                                            <th>Vehicle Entry Slip</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr>

                                            {{-- Vehicle ID --}}
                                            <td>
                                                <input type="text"
                                                       name="vehicle_id"
                                                       id="vehicle_id"
                                                       class="form-control"
                                                       value="{{ old('vehicle_id', $materialIn->id ?? request('id', '')) }}"
                                                       placeholder="Vehicle Id."
                                                       readonly>
                                            </td>

                                            {{-- Vehicle Number --}}
                                            <td>
                                                <input type="text"
                                                       name="truckno"
                                                       id="truckno"
                                                       class="form-control text-uppercase"
                                                       value="{{ old('truckno', $materialIn->truckno ?? '') }}"
                                                       placeholder="PB10HB5630"
                                                       maxlength="10"
                                                       pattern="[A-Za-z]{2}[0-9]{2}[A-Za-z]{2}[0-9]{4}"
                                                       title="Enter vehicle number in format PB10HB5630"
                                                       style="text-transform: uppercase;"
                                                       required>

                                                @error('truckno')
                                                    <small class="text-danger">
                                                        {{ $message }}
                                                    </small>
                                                @enderror
                                            </td>

                                            {{-- Party Name --}}
                                            <td>
                                                <select name="cust_id"
                                                        id="cust_id"
                                                        class="form-select"
                                                        required>

                                                    <option value="">
                                                        Select Party
                                                    </option>

                                                    @foreach ($parties ?? [] as $party)
                                                        <option value="{{ $party->id }}"
                                                            {{ (string) old('cust_id', $custId ?? $materialIn->cust_id ?? request('cust_id', '')) === (string) $party->id ? 'selected' : '' }}>
                                                            {{ $party->company_name }}
                                                        </option>
                                                    @endforeach

                                                </select>

                                                @error('cust_id')
                                                    <small class="text-danger">
                                                        {{ $message }}
                                                    </small>
                                                @enderror
                                            </td>

                                            {{-- Entry Type --}}
                                            <td>
                                                <select name="formlist"
                                                        id="formlist"
                                                        class="form-select"
                                                        required>

                                                    <option value="Material In"
                                                        {{ $selectedForm == 'Material In' ? 'selected' : '' }}>
                                                        Material In
                                                    </option>

                                                    <option value="Material Out"
                                                        {{ $selectedForm == 'Material Out' ? 'selected' : '' }}>
                                                        Material Out
                                                    </option>

                                                    <option value="Return"
                                                        {{ $selectedForm == 'Return' ? 'selected' : '' }}>
                                                        Return
                                                    </option>

                                                </select>

                                                @error('formlist')
                                                    <small class="text-danger">
                                                        {{ $message }}
                                                    </small>
                                                @enderror
                                            </td>

                                            {{-- Vehicle Entry Slip --}}
                                            <td>
                                                @if (!empty($materialIn->vehicleentryslip))
                                                    <a href="{{ asset('storage/' . $materialIn->vehicleentryslip) }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-file-alt"></i>
                                                        View Slip
                                                    </a>

                                                    {{-- Preserve existing file path --}}
                                                    <input type="hidden"
                                                        name="existing_vehicleentryslip"
                                                        value="{{ $materialIn->vehicleentryslip }}">
                                                @else
                                                    <span class="text-muted">No Slip Added</span>
                                                @endif
                                            </td>

                                        </tr>
                                    </tbody>

                                </table>
                            </div>

                            {{-- Weighment Details --}}
                            <div class="row">

                                {{-- RST No. --}}
                                <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-3">
                                    <div class="form-group">

                                        <label for="rstno" class="form-label">
                                            RST No.
                                        </label>

                                        <input type="text"
                                               name="rstno"
                                               id="rstno"
                                               class="form-control rst-input"
                                               value="{{ old('rstno', $materialIn->rstno ?? '') }}"
                                               placeholder="Enter RST No."
                                               required>

                                        @error('rstno')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror

                                    </div>
                                </div>

                                {{-- Weight Field --}}
                                <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-3">
                                    <div class="form-group">

                                        <label for="weight" class="form-label">
                                            <span id="weightLabel">
                                                {{ $weightLabel }}
                                            </span>
                                            {{-- <span class="required-star">*</span> --}}
                                        </label>

                                        <input type="number"
                                               name="{{ $weightField }}"
                                               id="weight"
                                               class="form-control weight-input"
                                               value="{{ $weightValue }}"
                                               placeholder="Enter {{ $weightLabel }}"
                                               min="0"
                                               step="0.01"
                                               required>

                                        @error('loadedweight')
                                            <small class="text-danger d-block">
                                                {{ $message }}
                                            </small>
                                        @enderror

                                        @error('tareweight')
                                            <small class="text-danger d-block">
                                                {{ $message }}
                                            </small>
                                        @enderror

                                    </div>
                                </div>

                                {{-- Weighment Slip --}}
                                <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-3">
                                    <div class="form-group">

                                        <label for="weighmentslip" class="form-label">
                                            Weighment Slip
                                        </label>

                                        @if (!empty($materialIn->weighmentslip))

                                            {{-- Existing slip available --}}
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $materialIn->weighmentslip) }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="btn btn-sm btn-outline-primary">

                                                    <i class="fas fa-file-alt"></i>
                                                    View Weighment Slip

                                                </a>
                                            </div>

                                        @elseif (!empty($weigh))

                                            {{-- Record exists but slip is not added --}}
                                            <span class="text-primary d-block mb-2">
                                                No Slip Added
                                            </span>

                                        @else

                                            {{-- No weight record exists, allow file upload --}}
                                            <input type="file"
                                                name="weighmentslip"
                                                id="weighmentslip"
                                                class="form-control"
                                                accept=".jpg,.jpeg,.png,.pdf">

                                            <small class="text-muted">
                                                JPG, JPEG, PNG or PDF
                                            </small>

                                        @endif

                                        @error('weighmentslip')
                                            <small class="text-danger d-block">
                                                {{ $message }}
                                            </small>
                                        @enderror

                                    </div>
                                </div>

                            </div>

                            {{-- Form Actions --}}
                            @if (empty($materialIn->vehicle_weighId))
                                <div class="border-top pt-3 mt-3">
                                    <div class="d-flex justify-content-end gap-2">

                                        <a href="{{ url()->previous() }}"
                                        class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-1"></i>
                                            Cancel
                                        </a>

                                        <button type="submit"
                                                class="btn btn-primary">
                                            <i class="fas fa-check me-1"></i>
                                            Save Record
                                        </button>

                                    </div>
                                </div>
                            @endif

                        </form>

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const truckInput = document.getElementById('truckno');
    const formList = document.getElementById('formlist');
    const weightLabel = document.getElementById('weightLabel');
    const weightInput = document.getElementById('weight');

    // Vehicle number: uppercase and remove spaces.
    if (truckInput) {
        truckInput.addEventListener('input', function () {
            this.value = this.value.toUpperCase().replace(/\s/g, '');
        });
    }

    // Material In = Loaded Weight.
    // Material Out / Return = Tare Weight.
    function updateWeightField() {

        if (!formList || !weightLabel || !weightInput) {
            return;
        }

        const selectedType = formList.value;
        const isTareWeight = ['Material Out', 'Return'].includes(selectedType);

        weightLabel.textContent = isTareWeight
            ? 'Tare Weight (Kgs)'
            : 'Loaded Weight (Kgs)';

        weightInput.name = isTareWeight
            ? 'tareweight'
            : 'loadedweight';

        weightInput.placeholder = isTareWeight
            ? 'Enter Tare Weight'
            : 'Enter Loaded Weight';
    }

    if (formList) {
        formList.addEventListener('change', updateWeightField);
        updateWeightField();
    }

    // Validate weighment slip type and file size.
    const slipInput = document.getElementById('weighmentslip');

    if (slipInput) {
        slipInput.addEventListener('change', function () {

            const file = this.files[0];

            if (!file) {
                return;
            }

            const allowedTypes = [
                'image/jpeg',
                'image/png',
                'application/pdf'
            ];

            if (!allowedTypes.includes(file.type)) {
                alert('Please upload a JPG, JPEG, PNG or PDF file.');
                this.value = '';
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert('File size must not exceed 5 MB.');
                this.value = '';
            }
        });
    }

});
</script>
@endpush
