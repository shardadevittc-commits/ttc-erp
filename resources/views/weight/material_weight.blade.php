@extends('layouts.app')

@section('title', 'Vehicle Weight Record | TTC Robotronics - Steel Industry ERP')

@push('styles')
<style>
    .weigh-record-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 0;
    }

    .weigh-record-table {
        width: 100%;
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

    .weigh-record-table .weight-input {
        max-width: 220px;
    }

    .weigh-record-table .rst-input {
        max-width: 160px;
    }

    .required-star {
        color: #dc3545;
    }

    .section-heading {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 16px;
    }

    .pending-summary {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding: 12px;
        background: #f5f6fa;
        border-radius: 5px;
    }

    .total-row {
        font-weight: 600;
        background: #f8f9fa;
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

    $isMaterialIn = $selectedForm === 'Material In';
    $isMaterialOut = $selectedForm === 'Material Out';
    $isReturn = $selectedForm === 'Return';

    /*
    |--------------------------------------------------------------------------
    | Upper Form Weight
    |--------------------------------------------------------------------------
    */

    $weightField = $isMaterialIn ? 'loadedweight' : 'tareweight';

    $weightLabel = $isMaterialIn
        ? 'Loaded Weight (Kgs)'
        : 'Tare Weight (Kgs)';

    $weightValue = old(
        $weightField,
        $isMaterialIn
            ? ($materialIn->loadedweight ?? '')
            : ($materialIn->tareweight ?? '')
    );

    /*
    |--------------------------------------------------------------------------
    | Pending Second Weight
    |--------------------------------------------------------------------------
    */

    $isSecondWeightPending = strtolower(
        trim((string) ($materialIn->second_weight ?? ''))
    ) === 'pending';

    /*
    |--------------------------------------------------------------------------
    | Weight Values
    |--------------------------------------------------------------------------
    */

    $loadedWeight = (float) old(
        'loadedweight',
        $materialIn->loadedweight ?? 0
    );

    $tareWeight = (float) old(
        'tareweight',
        $materialIn->tareweight ?? 0
    );

    $unloadTareWeight = old(
        'unload_tare_weight',
        $materialIn->unload_tare_weight ?? ''
    );

    $unloadLoadedWeight = old(
        'unload_loaded_weight',
        $materialIn->unload_loaded_weight ?? ''
    );

    /*
    |--------------------------------------------------------------------------
    | Calculate Initial Material Weight
    |--------------------------------------------------------------------------
    */

    if ($isMaterialIn) {
        $materialWeight = old(
            'material_weight',
            $materialIn->material_weight ?? ''
        );

        if ($materialWeight === '' && $unloadTareWeight !== '') {
            $materialWeight = max(
                0,
                $loadedWeight - (float) $unloadTareWeight
            );
        }
    } elseif ($isMaterialOut) {
        $materialWeight = old(
            'material_weight',
            $materialIn->material_weight ?? ''
        );

        if ($materialWeight === '' && $unloadLoadedWeight !== '') {
            $materialWeight = max(
                0,
                (float) $unloadLoadedWeight - $tareWeight
            );
        }
    } else {
        $materialWeight = old(
            'material_weight',
            $materialIn->material_weight ?? ''
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PO Receiving Items
    |--------------------------------------------------------------------------
    */

    $poReceiving = $materialIn->po_receiving ?? [];

    if ($poReceiving instanceof \Illuminate\Support\Collection) {
        $poReceiving = $poReceiving->all();
    }

    $poReceiving = is_array($poReceiving)
        ? $poReceiving
        : [];

    $totalUnloadQty = 0;
    $totalActualQty = 0;

    foreach ($poReceiving as $index => $item) {
        $totalUnloadQty += (float) data_get(
            $item,
            'received_qty',
            data_get($item, 'po_item_qty', 0)
        );

        $totalActualQty += (float) old(
            "actual_qty.$index",
            data_get($item, 'actual_qty', 0)
        );
    }
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
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>

<div class="content_area">
    <div class="flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">

                {{-- Flash Messages --}}
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close"
                                data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close"
                                data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong>Please check the following:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close"
                                data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h5 class="weigh-record-title">Vehicle Weigh Record</h5>
                    </div>

                    <div class="card-body">

                        {{-- ==================================================
                             FORM 1: INITIAL VEHICLE WEIGHT
                        =================================================== --}}
                        <form action="{{ route('material_weight') }}"
                              method="POST"
                              enctype="multipart/form-data"
                              id="vehicleWeighRecordForm">

                            @csrf

                            <input type="hidden"
                                   name="id"
                                   value="{{ $id ?? request('id', '') }}">

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
                                            <td>
                                                <input type="text"
                                                       name="vehicle_id"
                                                       class="form-control"
                                                       value="{{ old('vehicle_id', $materialIn->id ?? request('id', '')) }}"
                                                       readonly>
                                            </td>

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
                                                       required>
                                            </td>

                                            <td>
                                                <select name="cust_id"
                                                        id="cust_id"
                                                        class="form-select"
                                                        required>
                                                    <option value="">Select Party</option>

                                                    @foreach ($parties ?? [] as $party)
                                                        <option value="{{ $party->id }}"
                                                            {{ (string) old('cust_id', $custId ?? $materialIn->cust_id ?? request('cust_id', '')) === (string) $party->id ? 'selected' : '' }}>
                                                            {{ $party->company_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>

                                            <td>
                                                <select name="formlist"
                                                        id="formlist"
                                                        class="form-select"
                                                        required>
                                                    <option value="Material In"
                                                        {{ $isMaterialIn ? 'selected' : '' }}>
                                                        Material In
                                                    </option>

                                                    <option value="Material Out"
                                                        {{ $isMaterialOut ? 'selected' : '' }}>
                                                        Material Out
                                                    </option>

                                                    <option value="Return"
                                                        {{ $isReturn ? 'selected' : '' }}>
                                                        Return
                                                    </option>
                                                </select>
                                            </td>

                                            <td>
                                                @if (!empty($materialIn->vehicleentryslip))
                                                    <a href="{{ asset('storage/' . $materialIn->vehicleentryslip) }}"
                                                       target="_blank"
                                                       rel="noopener noreferrer"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-file-alt"></i>
                                                        View Slip
                                                    </a>

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

                            <div class="row">
                                <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 mb-3">
                                    <label for="rstno" class="form-label">RST No.</label>

                                    <input type="text"
                                           name="rstno"
                                           id="rstno"
                                           class="form-control"
                                           value="{{ old('rstno', $materialIn->rstno ?? '') }}"
                                           placeholder="Enter RST No."
                                           required>
                                </div>

                                <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 mb-3">
                                    <label for="weight" class="form-label">
                                        <span id="weightLabel">{{ $weightLabel }}</span>
                                        <span class="required-star">*</span>
                                    </label>

                                    <input type="number"
                                           name="{{ $weightField }}"
                                           id="weight"
                                           class="form-control"
                                           value="{{ $weightValue }}"
                                           placeholder="Enter {{ $weightLabel }}"
                                           min="0"
                                           step="0.01"
                                           required>
                                </div>

                                <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 mb-3">
                                    <label for="weighmentslip" class="form-label">
                                        Weighment Slip
                                    </label>

                                    @if (!empty($materialIn->weighmentslip))
                                        <div class="mt-2">
                                            <a href="{{ asset('storage/' . $materialIn->weighmentslip) }}"
                                               target="_blank"
                                               rel="noopener noreferrer"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-file-alt"></i>
                                                View Weighment Slip
                                            </a>
                                        </div>
                                    @elseif (empty($weigh))
                                        <input type="file"
                                               name="weighmentslip"
                                               id="weighmentslip"
                                               class="form-control"
                                               accept=".jpg,.jpeg,.png,.pdf">

                                        <small class="text-muted">
                                            JPG, JPEG, PNG or PDF; maximum 5 MB.
                                        </small>
                                    @else
                                        <span class="text-primary">No Slip Added</span>
                                    @endif
                                </div>
                            </div>

                            @if (empty($materialIn->vehicle_weighId))
                                <div class="border-top pt-3 mt-3">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ url()->previous() }}"
                                           class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-1"></i> Cancel
                                        </a>

                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-check me-1"></i> Save Record
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </form>

                        {{-- ==================================================
                             FORM 2: PENDING SECOND WEIGHT
                             Separate form and separate route
                        =================================================== --}}
                        @if ($isSecondWeightPending)

                            <form action="{{ route('weight.unload_weight') }}"
                                  method="POST"
                                  id="secondWeightForm">

                                @csrf

                                <input type="hidden"
                                       name="id"
                                       value="{{ $id ?? request('id', '') }}">

                                <input type="hidden"
                                       name="vehicle_id"
                                       value="{{ $materialIn->id ?? '' }}">

                                <input type="hidden"
                                       name="truckno"
                                       value="{{ old('truckno', $materialIn->truckno ?? '') }}">

                                <input type="hidden"
                                       name="cust_id"
                                       value="{{ old('cust_id', $custId ?? $materialIn->cust_id ?? '') }}">

                                <input type="hidden"
                                       name="formlist"
                                       id="secondFormType"
                                       value="{{ $selectedForm }}">

                                <input type="hidden"
                                       name="loadedweight"
                                       id="loadedWeightHidden"
                                       value="{{ $loadedWeight }}">

                                <input type="hidden"
                                       name="tareweight"
                                       id="tareWeightHidden"
                                       value="{{ $tareWeight }}">

                                <div class="border-top pt-3 mt-3">
                                    <h5 class="section-heading">
                                        Pending Second Weight Details
                                    </h5>

                                    <div class="row">

                                        {{-- Material In: unload tare --}}
                                        <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-6 mb-3"
                                             id="unloadTareWrapper"
                                             style="{{ $isMaterialIn ? '' : 'display:none;' }}">

                                            <label for="unload_tare_weight" class="form-label">
                                                Unload Tare Weight (Kgs)
                                                <span class="required-star">*</span>
                                            </label>

                                            <input type="number"
                                                   name="unload_tare_weight"
                                                   id="unload_tare_weight"
                                                   class="form-control"
                                                   value="{{ $unloadTareWeight }}"
                                                   min="0"
                                                   max="{{ $loadedWeight > 0 ? $loadedWeight : '' }}"
                                                   step="0.01"
                                                   {{ $isMaterialIn ? 'required' : 'disabled' }}>
                                        </div>

                                        {{-- Material Out: unload loaded weight --}}
                                        <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-6 mb-3"
                                             id="unloadLoadedWrapper"
                                             style="{{ $isMaterialOut ? '' : 'display:none;' }}">

                                            <label for="unload_loaded_weight" class="form-label">
                                                {{-- Unload Loaded Weight (Kgs) --}}
                                                Net loaded Weight (Kgs)
                                                <span class="required-star">*</span>
                                            </label>

                                            <input type="number"
                                                   name="unload_loaded_weight"
                                                   id="unload_loaded_weight"
                                                   class="form-control"
                                                   value="{{ $unloadLoadedWeight }}"
                                                   min="0"
                                                   step="0.01"
                                                   {{ $isMaterialOut ? 'required' : 'disabled' }}>
                                        </div>

                                        {{-- Calculated material weight --}}
                                        <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-6 mb-3">
                                            <label for="material_weight" class="form-label">
                                                Material Weight (Kgs)
                                            </label>

                                            <input type="number"
                                                   name="material_weight"
                                                   id="material_weight"
                                                   class="form-control"
                                                   value="{{ $materialWeight }}"
                                                   readonly>
                                        </div>
                                    </div>

                                    {{-- Unloaded Material --}}
                                    <div class="mt-3">
                                        <h6 class="section-heading">Unloaded Material</h6>

                                        <div class="table-responsive">
                                            <table class="table table-bordered weigh-record-table mb-3">
                                                <thead>
                                                    <tr>
                                                        <th style="width:4%">#</th>
                                                        <th style="width:22%">Sizes</th>
                                                        <th style="width:11%">Grade</th>
                                                        <th style="width:11%">Brand</th>
                                                        <th style="width:25%">Unload Qty (Tons)</th>
                                                        <th style="width:27%">
                                                            Actual Qty (KGS)
                                                            <span class="required-star">*</span>
                                                        </th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @forelse ($poReceiving as $index => $item)

                                                        @php
                                                            $sizeName = data_get($item, 'size_name', '');
                                                            $gradeName = data_get($item, 'grade_name', '');
                                                            $productName = data_get($item, 'product_name', '');

                                                            $unloadQty = (float) data_get(
                                                                $item,
                                                                'received_qty',
                                                                data_get($item, 'po_item_qty', 0)
                                                            );

                                                            $actualQty = old(
                                                                "actual_qty.$index",
                                                                data_get($item, 'actual_qty', '')
                                                            );
                                                        @endphp

                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>

                                                            <td>
                                                                {{ $sizeName }}

                                                                @if ($productName !== '')
                                                                    <small class="text-muted d-block">
                                                                        {{ $productName }}
                                                                    </small>
                                                                @endif

                                                                <input type="hidden"
                                                                       name="po_receiving[{{ $index }}][size_id]"
                                                                       value="{{ data_get($item, 'size_id', '') }}">

                                                                <input type="hidden"
                                                                       name="po_receiving[{{ $index }}][purchase_order_item_id]"
                                                                       value="{{ data_get($item, 'purchase_order_item_id', '') }}">

                                                                <input type="hidden"
                                                                       name="po_receiving[{{ $index }}][product_id]"
                                                                       value="{{ data_get($item, 'product_id', '') }}">

                                                                <input type="hidden"
                                                                       name="po_receiving[{{ $index }}][product_name]"
                                                                       value="{{ $productName }}">
                                                            </td>

                                                            <td>
                                                                {{ $gradeName }}

                                                                <input type="hidden"
                                                                       name="po_receiving[{{ $index }}][grade_id]"
                                                                       value="{{ data_get($item, 'grade_id', '') }}">
                                                            </td>

                                                            <td>
                                                                {{ data_get($item, 'brand_name', data_get($item, 'brand', '')) }}
                                                            </td>

                                                            <td>
                                                                {{ number_format($unloadQty, 3, '.', '') }}

                                                                <input type="hidden"
                                                                       name="po_receiving[{{ $index }}][received_qty]"
                                                                       value="{{ $unloadQty }}">
                                                            </td>

                                                            <td>
                                                                <input type="number"
                                                                       name="actual_qty[{{ $index }}]"
                                                                       class="form-control actual-qty-input"
                                                                       value="{{ $actualQty }}"
                                                                       min="0"
                                                                       step="0.001"
                                                                       placeholder="Enter actual quantity"
                                                                       required>
                                                            </td>
                                                        </tr>

                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center text-muted">
                                                                No unloaded material found.
                                                            </td>
                                                        </tr>
                                                    @endforelse

                                                    @if (count($poReceiving) > 0)
                                                        <tr class="total-row">
                                                            <td colspan="4" class="text-end">TOTAL</td>

                                                            <td>
                                                                {{ number_format($totalUnloadQty, 3, '.', '') }}
                                                            </td>

                                                            <td>
                                                                <span id="actualQtyTotal">
                                                                    {{ number_format($totalActualQty, 3, '.', '') }}
                                                                </span>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td colspan="4"></td>

                                                            <td colspan="2">
                                                                <div class="pending-summary">
                                                                    <div>
                                                                        <strong>MATERIAL WT:</strong>
                                                                        <span id="materialWeightTotal">
                                                                            {{ number_format($totalActualQty, 3, '.', '') }}
                                                                        </span>
                                                                        KGS
                                                                    </div>

                                                                    <div>
                                                                        <strong>PENDING WT:</strong>
                                                                        <span id="pendingWeightTotal">
    {{ is_numeric($materialWeight) ? number_format((float) $materialWeight, 2, '.', '') : '0.00' }}
</span>
                                                                        KGS
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="border-top pt-3 mt-3">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ url()->previous() }}"
                                               class="btn btn-outline-secondary">
                                                <i class="fas fa-times me-1"></i> Cancel
                                            </a>

                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-check me-1"></i> Submit Second Weight
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @endif

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

    const secondWeightForm = document.getElementById('secondWeightForm');
    const secondFormType = document.getElementById('secondFormType');

    const unloadTareWrapper = document.getElementById('unloadTareWrapper');
    const unloadLoadedWrapper = document.getElementById('unloadLoadedWrapper');

    const unloadTareInput = document.getElementById('unload_tare_weight');
    const unloadLoadedInput = document.getElementById('unload_loaded_weight');
    const materialWeightInput = document.getElementById('material_weight');

    const loadedWeightHidden = document.getElementById('loadedWeightHidden');
    const tareWeightHidden = document.getElementById('tareWeightHidden');

    const actualInputs = document.querySelectorAll('.actual-qty-input');

    const actualQtyTotal = document.getElementById('actualQtyTotal');
    const materialWeightTotal = document.getElementById('materialWeightTotal');
    const pendingWeightTotal = document.getElementById('pendingWeightTotal');

    const initialLoadedWeight = Number(@json($loadedWeight));
    const initialTareWeight = Number(@json($tareWeight));

    /*
    |--------------------------------------------------------------------------
    | Vehicle Number
    |--------------------------------------------------------------------------
    */

    if (truckInput) {
        truckInput.addEventListener('input', function () {
            this.value = this.value.toUpperCase().replace(/\s/g, '');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Upper Form Weight Field
    |--------------------------------------------------------------------------
    */

    function updateWeightField() {
        if (!formList || !weightInput || !weightLabel) {
            return;
        }

        const isMaterialIn = formList.value === 'Material In';

        weightLabel.textContent = isMaterialIn
            ? 'Loaded Weight (Kgs)'
            : 'Tare Weight (Kgs)';

        weightInput.name = isMaterialIn
            ? 'loadedweight'
            : 'tareweight';

        weightInput.placeholder = isMaterialIn
            ? 'Enter Loaded Weight'
            : 'Enter Tare Weight';

        updateSecondWeightFields();
    }

    /*
    |--------------------------------------------------------------------------
    | Lower Form Field Visibility
    |--------------------------------------------------------------------------
    */

    function updateSecondWeightFields() {
        if (!formList) {
            return;
        }

        const type = formList.value;
        const isMaterialIn = type === 'Material In';
        const isMaterialOut = type === 'Material Out';

        if (secondFormType) {
            secondFormType.value = type;
        }

        if (unloadTareWrapper && unloadTareInput) {
            unloadTareWrapper.style.display = isMaterialIn ? '' : 'none';
            unloadTareInput.disabled = !isMaterialIn;
            unloadTareInput.required = isMaterialIn;
        }

        if (unloadLoadedWrapper && unloadLoadedInput) {
            unloadLoadedWrapper.style.display = isMaterialOut ? '' : 'none';
            unloadLoadedInput.disabled = !isMaterialOut;
            unloadLoadedInput.required = isMaterialOut;
        }

        calculateMaterialWeight();
        updateActualTotals();
    }

    /*
    |--------------------------------------------------------------------------
    | Material Weight
    |
    | Material In:
    | Loaded Weight - Unload Tare Weight
    |
    | Material Out:
    | Unload Loaded Weight - Tare Weight
    |--------------------------------------------------------------------------
    */

    function calculateMaterialWeight() {
        if (!formList || !materialWeightInput) {
            return;
        }

        const type = formList.value;
        let materialWeight = 0;

        if (type === 'Material In') {
            if (!unloadTareInput || unloadTareInput.value.trim() === '') {
                materialWeightInput.value = '';
                updatePendingWeight(0);
                return;
            }

            const loaded = Number(
                loadedWeightHidden ? loadedWeightHidden.value : initialLoadedWeight
            );
            const tare = Number(unloadTareInput.value);

            if (!Number.isFinite(loaded) || !Number.isFinite(tare) ||
                tare < 0 || tare > loaded) {
                materialWeightInput.value = '';
                updatePendingWeight(0);
                return;
            }

            materialWeight = loaded - tare;

        } else if (type === 'Material Out') {
            if (!unloadLoadedInput || unloadLoadedInput.value.trim() === '') {
                materialWeightInput.value = '';
                updatePendingWeight(0);
                return;
            }

            const tare = Number(
                tareWeightHidden ? tareWeightHidden.value : initialTareWeight
            );
            const loaded = Number(unloadLoadedInput.value);

            if (!Number.isFinite(tare) || !Number.isFinite(loaded) ||
                tare < 0 || loaded < tare) {
                materialWeightInput.value = '';
                updatePendingWeight(0);
                return;
            }

            materialWeight = loaded - tare;

        } else {
            materialWeightInput.value = '';
            updatePendingWeight(0);
            return;
        }

        materialWeightInput.value = materialWeight.toFixed(2);
        updatePendingWeight(materialWeight);
    }

    function updatePendingWeight(value) {
        if (pendingWeightTotal) {
            pendingWeightTotal.textContent = Number(value).toFixed(2);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Actual Quantity Totals
    |--------------------------------------------------------------------------
    */

    function updateActualTotals() {
        let total = 0;

        actualInputs.forEach(function (input) {
            if (input.value.trim() === '') {
                return;
            }

            const value = Number(input.value);

            if (Number.isFinite(value) && value >= 0) {
                total += value;
            }
        });

        // Actual Qty ka total
        if (actualQtyTotal) {
            actualQtyTotal.textContent = total.toFixed(3);
        }

        // MATERIAL WT = Actual Qty total
        if (materialWeightTotal) {
            materialWeightTotal.textContent = total.toFixed(3);
        }

        // PENDING WT = Material Weight - Actual Qty total
        if (pendingWeightTotal && materialWeightInput) {
            const materialWeight = Number(materialWeightInput.value);

            const pending = materialWeightInput.value.trim() !== '' &&
                Number.isFinite(materialWeight)
                    ? Math.max(0, materialWeight - total)
                    : 0;

            pendingWeightTotal.textContent = pending.toFixed(2);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Weighment Slip Validation
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Event Listeners
    |--------------------------------------------------------------------------
    */

    if (formList) {
        formList.addEventListener('change', updateWeightField);
    }

    if (unloadTareInput) {
        unloadTareInput.addEventListener('input', calculateMaterialWeight);
    }

    if (unloadLoadedInput) {
        unloadLoadedInput.addEventListener('input', calculateMaterialWeight);
    }

    actualInputs.forEach(function (input) {
        input.addEventListener('input', updateActualTotals);
    });

    /*
    |--------------------------------------------------------------------------
    | Synchronize Weights Before Second Form Submission
    |--------------------------------------------------------------------------
    */

    if (secondWeightForm) {
        secondWeightForm.addEventListener('submit', function (event) {
            calculateMaterialWeight();

            if (formList && formList.value === 'Material In') {
                const loaded = Number(
                    loadedWeightHidden ? loadedWeightHidden.value : 0
                );

                const tare = unloadTareInput
                    ? Number(unloadTareInput.value)
                    : NaN;

                if (
                    !unloadTareInput ||
                    unloadTareInput.value.trim() === '' ||
                    !Number.isFinite(tare) ||
                    tare < 0 ||
                    tare > loaded
                ) {
                    event.preventDefault();
                    alert('Please enter a valid Unload Tare Weight.');
                    return;
                }
            }

            if (formList && formList.value === 'Material Out') {
                const tare = Number(
                    tareWeightHidden ? tareWeightHidden.value : 0
                );

                const loaded = unloadLoadedInput
                    ? Number(unloadLoadedInput.value)
                    : NaN;

                if (
                    !unloadLoadedInput ||
                    unloadLoadedInput.value.trim() === '' ||
                    !Number.isFinite(loaded) ||
                    loaded < tare
                ) {
                    event.preventDefault();
                    alert('Unload Loaded Weight must be greater than or equal to Tare Weight.');
                    return;
                }
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Initial Calculations
    |--------------------------------------------------------------------------
    */

    updateWeightField();
    updateActualTotals();
});
</script>
@endpush
