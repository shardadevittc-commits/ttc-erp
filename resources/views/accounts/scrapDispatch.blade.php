@extends('layouts.app')

@section('title', 'Scrap Dispatch')

@section('content')
<div class="container-fluid py-3">
    <div class="page-header mb-3">
        <div class="page-header__inner">
            <div class="page-header__content">
                <div class="page-header__title"><h6>Scrap Dispatch</h6></div>
            </div>
            <div class="page-header__actions d-flex gap-2">
                <a href="{{ route('scrapDispatch.history') }}" class="btn btn-outline-primary">
                    <i class="fas fa-clock-rotate-left me-1"></i>Dispatch History
                </a>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-1">Please correct the following errors:</div>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $recoverableStock = (float) $stock->pointing + (float) $stock->point_discard
            + (float) $stock->bedsawcut + (float) $stock->misc;
        $nonRecoverableStock = (float) $stock->acid_pickling + (float) $stock->short_blasting;
    @endphp

    <div class="row g-3 mb-3">
        <div class="col-sm-6 col-xl-4">
            <div class="card border-start border-4 border-danger h-100">
                <div class="card-body">
                    <div class="text-muted small">Non-recoverable available stock</div>
                    <div class="fs-4 fw-semibold">{{ number_format($nonRecoverableStock, 3) }} <small class="fs-6 text-muted">kg</small></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card border-start border-4 border-success h-100">
                <div class="card-body">
                    <div class="text-muted small">Recoverable available stock</div>
                    <div class="fs-4 fw-semibold">{{ number_format($recoverableStock, 3) }} <small class="fs-6 text-muted">kg</small></div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-xl-4">
            <div class="card border-start border-4 border-primary h-100">
                <div class="card-body">
                    <div class="text-muted small">Total available scrap stock</div>
                    <div class="fs-4 fw-semibold">{{ number_format($recoverableStock + $nonRecoverableStock, 3) }} <small class="fs-6 text-muted">kg</small></div>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('scrapDispatch.store') }}" enctype="multipart/form-data" id="scrapDispatchForm">
        @csrf
        <input type="hidden" name="submission_token" value="{{ old('submission_token', $submissionToken) }}">
        <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id') }}">

        <div class="card mb-3">
            <div class="card-header fw-semibold">Available Scrap & Dispatch Details</div>
            <div class="card-body p-0">
                {{-- <div class="alert alert-info rounded-0 mb-0 small">
                    Available stock is the current balance from ScrapTotal. The database does not keep a separate opening-stock and today-stock split.
                    All category quantities are entered in kilograms.
                </div> --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Scrap Category</th>
                                <th>Classification</th>
                                <th class="text-end">Opening Stock</th>
                                <th class="text-end">Today</th>
                                <th class="text-end">Available Stock (kg)</th>
                                <th style="min-width: 150px">Dispatch Qty (kg)</th>
                                <th style="min-width: 140px">Rate / kg</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $key => $category)
                                @php
                                    $available = (float) ($stock->{$category['stock']} ?? 0);
                                    $quantityField = $key . '_dispatch';
                                    $rateField = $key . '_rate';
                                @endphp
                                <tr>
                                    <td class="fw-semibold">{{ $category['label'] }}</td>
                                    <td>
                                        <span class="badge {{ $category['recoverability'] === 'Recoverable' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $category['recoverability'] }}
                                        </span>
                                    </td>
                                    <td class="text-end text-muted">—</td>
                                    <td class="text-end text-muted">—</td>
                                    <td class="text-end fw-semibold">{{ number_format($available, 3) }}</td>
                                    <td>
                                        <input type="number" step="any" min="0" max="{{ $available }}"
                                            class="form-control form-control-sm scrap-quantity"
                                            name="{{ $quantityField }}" id="{{ $quantityField }}"
                                            value="{{ old($quantityField) }}" aria-label="{{ $category['label'] }} dispatch quantity">
                                        @error($quantityField)<small class="text-danger">{{ $message }}</small>@enderror
                                    </td>
                                    <td>
                                        <input type="number" step="any" min="0"
                                            class="form-control form-control-sm scrap-rate"
                                            name="{{ $rateField }}" id="{{ $rateField }}"
                                            value="{{ old($rateField, 0) }}" aria-label="{{ $category['label'] }} rate per kilogram">
                                    </td>
                                    <td class="text-end"><span id="{{ $key }}_amount">0.00</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="7" class="text-end">Projected Scrap Amount</th>
                                <th class="text-end"><span id="projectedAmount">0.00</span></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header fw-semibold">Vehicle & Weight Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6 col-xl-4">
                        <label for="vehicle_id" class="form-label">Vehicle No. & Buyer <span class="text-danger">*</span></label>
                        <select name="vehicle_id" id="vehicle_id" class="form-select" required>
                            <option value="">Select vehicle</option>
                            @foreach ($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}"
                                    data-customer-id="{{ $vehicle->cust_id }}"
                                    data-vehicle-type="{{ $vehicle->formlist }}"
                                    @selected((string) old('vehicle_id') === (string) $vehicle->id)>
                                    {{ $vehicle->truckno }}{{ $vehicle->customer ? ' — ' . $vehicle->customer->company_name : '' }}                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <label for="vehicle_type" class="form-label">Vehicle Type</label>
                        <input type="text" id="vehicle_type" class="form-control" value="{{ old('vehicle_type') }}" readonly>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <label for="weight_slip" class="form-label">Weight Slip (PDF/JPG/PNG)</label>
                        <input type="file" name="weight_slip" id="weight_slip" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        @error('weight_slip')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="first_weight" class="form-label">First Weight (kg) </label>
                        <input type="number" step="any" min="0" name="first_weight" id="first_weight"
                            class="form-control" value="{{ old('first_weight') }}" required>
                        @error('first_weight')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="last_weight" class="form-label">Last Weight (kg) </label>
                        <input type="number" step="any" min="0" name="last_weight" id="last_weight"
                            class="form-control" value="{{ old('last_weight') }}" required>
                        @error('last_weight')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="actual_weight" class="form-label">Actual Weight (kg)</label>
                        <input type="number" step="any" id="actual_weight" class="form-control" value="0" readonly>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header fw-semibold">Invoice & Payment Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4 col-xl-2">
                        <label for="invoice_no" class="form-label">Invoice No. </label>
                        <input type="text" maxlength="10" name="invoice_no" id="invoice_no" class="form-control"
                            value="{{ old('invoice_no') }}" required>
                        @error('invoice_no')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-4 col-xl-2">
                        <label for="invoice_date" class="form-label">Invoice Date </label>
                        <input type="date" name="invoice_date" id="invoice_date" class="form-control"
                            value="{{ old('invoice_date', now()->toDateString()) }}" required>
                        @error('invoice_date')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-4 col-xl-2">
                        <label for="invoice_amt" class="form-label">Invoice Amount (A) </label>
                        <input type="number" step="any" min="0" name="invoice_amt" id="invoice_amt" class="form-control"
                            value="{{ old('invoice_amt') }}" data-manual="{{ old('invoice_amt') !== null ? '1' : '0' }}" required>
                        @error('invoice_amt')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-4 col-xl-2">
                        <label for="invoice_gst" class="form-label">GST Amount (B)</label>
                        <input type="number" step="any" min="0" name="invoice_gst" id="invoice_gst" class="form-control"
                            value="{{ old('invoice_gst', 0) }}">
                        @error('invoice_gst')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-4 col-xl-2">
                        <label for="misc_amt" class="form-label">Misc. Amount (C)</label>
                        <input type="number" step="any" min="0" name="misc_amt" id="misc_amt" class="form-control"
                            value="{{ old('misc_amt', 0) }}">
                        @error('misc_amt')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-4 col-xl-2">
                        <label for="final_amt" class="form-label">Total Amount (A+B+C)</label>
                        <input type="number" step="any" id="final_amt" class="form-control" value="0" readonly>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <label for="mode_of_payment" class="form-label">Mode of Payment <span class="text-danger">*</span></label>
                        <select name="mode_of_payment" id="mode_of_payment" class="form-select" required>
                            <option value="">Select payment mode</option>
                            @foreach (['Cash', 'Bank Transfer', 'Cheque', 'UPI', 'Credit'] as $mode)
                                <option value="{{ $mode }}" @selected(old('mode_of_payment') === $mode)>{{ $mode }}</option>
                            @endforeach
                        </select>
                        @error('mode_of_payment')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-6 col-xl-8">
                        <label for="remarks" class="form-label">Remarks</label>
                        <input type="text" maxlength="255" name="remarks" id="remarks" class="form-control"
                            value="{{ old('remarks') }}">
                        @error('remarks')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <button type="reset" class="btn btn-outline-secondary">Reset</button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-check me-1"></i>Submit Scrap Dispatch
            </button>
        </div>
    </form>
</div>

<script>
    (() => {
        const categories = @json(array_keys($categories));
        const quantity = (id) => Number(document.getElementById(id)?.value || 0);
        const format = (value) => Number.isFinite(value) ? value.toFixed(2) : '0.00';
        const invoiceAmount = document.getElementById('invoice_amt');

        function updateAmounts() {
            let projected = 0;
            categories.forEach((category) => {
                const amount = quantity(`${category}_dispatch`) * quantity(`${category}_rate`);
                projected += amount;
                const amountElement = document.getElementById(`${category}_amount`);
                if (amountElement) {
                    amountElement.textContent = format(amount);
                }
            });

            const projectedAmount = document.getElementById('projectedAmount');
            if (projectedAmount) {
                projectedAmount.textContent = format(projected);
            }
            if (invoiceAmount && invoiceAmount.dataset.manual !== '1') {
                invoiceAmount.value = projected;
            }
            const finalAmount = document.getElementById('final_amt');
            if (finalAmount) {
                finalAmount.value = format(
                    quantity('invoice_amt') + quantity('invoice_gst') + quantity('misc_amt')
                );
            }
        }

        function updateWeight() {
            const actualWeight = document.getElementById('actual_weight');
            if (actualWeight) {
                actualWeight.value = Math.abs(
                    quantity('first_weight') - quantity('last_weight')
                );
            }
        }

        const customer = document.getElementById('customer_id');
        const vehicle = document.getElementById('vehicle_id');

        function updateVehicleDetails() {
            if (!vehicle) {
                return;
            }
            const selectedVehicle = vehicle.options[vehicle.selectedIndex];
            if (customer) {
                customer.value = selectedVehicle?.dataset.customerId || '';
            }
            const vehicleType = document.getElementById('vehicle_type');
            if (vehicleType) {
                vehicleType.value = selectedVehicle?.dataset.vehicleType || '';
            }
        }

        categories.forEach((category) => {
            const dispatchField = document.getElementById(`${category}_dispatch`);
            const rateField = document.getElementById(`${category}_rate`);

            if (dispatchField) {
                dispatchField.addEventListener('input', updateAmounts);
            }
            if (rateField) {
                rateField.addEventListener('input', updateAmounts);
            }
        });
        ['invoice_gst', 'misc_amt'].forEach((id) => {
            const field = document.getElementById(id);
            if (field) {
                field.addEventListener('input', updateAmounts);
            }
        });
        if (invoiceAmount) {
            invoiceAmount.addEventListener('input', () => {
                invoiceAmount.dataset.manual = '1';
                updateAmounts();
            });
        }
        ['first_weight', 'last_weight'].forEach((id) => {
            const field = document.getElementById(id);
            if (field) {
                field.addEventListener('input', updateWeight);
            }
        });
        if (vehicle) {
            vehicle.addEventListener('change', updateVehicleDetails);
        }
        updateVehicleDetails();
        updateAmounts();
        updateWeight();
    })();
</script>
@endsection
