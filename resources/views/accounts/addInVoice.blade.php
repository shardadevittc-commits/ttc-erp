@extends('layouts.app')

@section('title', 'Add Invoice | Accounts')

@section('content')
<div class="container-fluid py-3">
    <div class="page-header mb-3">
        <div class="page-header__inner">
            <div class="page-header__content">
                <div class="page-header__title"><h6>Add Invoice</h6></div>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('accounts') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Accounts
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

    @php $materials = $gate->dispatchMaterials; @endphp
    <div class="card mb-4 gate-account-group" data-gate-id="{{ $gate->id }}">
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header fw-semibold">Vehicle Details (Gate)</div>
                        <div class="card-body p-2">
                            <table class="table table-bordered table-sm mb-0">
                                <tbody>
                                    <tr>
                                        <th>Token No.</th>
                                        <td>{{ $gate->tokenno ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Party Name</th>
                                        <td>{{ $partyName }}</td>
                                    </tr>
                                    <tr>
                                        <th>Vehicle No.</th>
                                        <td>{{ $gate->truckno ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Vehicle Type</th>
                                        <td>{{ $gate->formlist ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Transporter</th>
                                        <td>{{ $gate->transportname ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Driver</th>
                                        <td>{{ $gate->drivername ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Mobile No.</th>
                                        <td>{{ $gate->contactno ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Gate Details</th>
                                        <td>{{ $gate->tokenno ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Vehicle Details (Material Out)</th>
                                        <td>{{ $gate->formlist ?: '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header fw-semibold">Check In / Check Out</div>
                        <div class="card-body p-2">
                            <table class="table table-bordered table-sm mb-0">
                                <tbody>
                                    <tr>
                                        <th>Check In</th>
                                        <td>{{ $gate->created_at ? _dt($gate->created_at) : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Gate Security Incharge</th>
                                        <td>{{ $gate->entrySecurity ? trim(($gate->entrySecurity->first_name ?? '') . ' ' . ($gate->entrySecurity->last_name ?? '')) : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Check Out</th>
                                        <td>{{ $gate->check_out ? \Illuminate\Support\Carbon::parse($gate->check_out)->format('d-m-Y h:i A') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Gate Security Incharge</th>
                                        <td>{{ $gate->exitSecurity?->first_name ? trim(($gate->exitSecurity->first_name ?? '') . ' ' . ($gate->exitSecurity->last_name ?? '')) : '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Weight &amp; Material (Dispatch Slip No.: {{ $gate->id }})</strong>
                </div>
                <div class="card-body">
                    <div class="mb-2"><strong>Party Name:</strong> {{ $partyName }}</div>
                    <div class="mb-3"><strong>Vehicle Tare Weight:</strong> {{ _rz($tareWeight) }} Kgs</div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2">ORDER NO.</th>
                                    <th rowspan="2">PRODUCT</th>
                                    <th rowspan="2">SIZE/DIA(MM)</th>
                                    {{-- <th rowspan="2">LENGTH</th> --}}
                                    <th rowspan="2">GRADE</th>
                                    <th rowspan="2">BRAND</th>

                                    <th colspan="2" class="text-center">PLANNED</th>
                                    {{-- <th colspan="2" class="text-center">ACTUAL</th> --}}
                                    <th colspan="2" class="text-center">DISPATCH ACTUAL</th>
                                    {{-- <th colspan="2" class="text-center">RETURN</th> --}}
                                    <th colspan="4" class="text-center">PRICING</th>
                                </tr>

                                <tr>
                                    <!-- PLANNED -->
                                    <th class="text-end">WEIGHT QTY (Kgs)</th>
                                    <th class="text-end">PCS/BUNDLES (Kgs)</th>

                                    <!-- ACTUAL -->
                                    <th class="text-end">WEIGHT (Kgs)</th>
                                    <th class="text-end">DISPATCH QTY (TONS)</th>

                                    <!-- RETURN -->
                                    {{-- <th class="text-end">RETURN QTY (MT)</th>
                                    <th class="text-end">TOTAL QTY - AFTER RETURN (MT)</th> --}}

                                    <!-- PRICING -->
                                    <th class="text-end">PRICE</th>
                                    <th class="text-end">SIZE EXTRA</th>
                                    <th class="text-end">NET RATE</th>
                                    <th class="text-end">SUBTOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($materials as $dispatch)
                                    @php
                                        $item = $dispatch->saleOrderItem;
                                        $actual = max(0, (float) ($dispatch->actual_qty ?? 0));
                                        $dispatchQty = max(0, (float) ($dispatch->dispatch_qty ?? 0));
                                        $dispatchTons = strtolower($dispatch->dispatch_qty_unit) == "kgs" || strtolower($dispatch->dispatch_qty_unit) == "kg"
                                            ? $dispatchQty / 1000
                                            : $dispatchQty;
                                        $price = (float) ($item?->sale_item_prices ?? 0);
                                        $extra = (float) ($item?->size_extra ?? 0);
                                        $netRate = $price + $extra;
                                        $lineSubtotal = $dispatchTons * $netRate;
                                    @endphp
                                    <tr>
                                        <td>{{ $dispatch->sale_order_id }}</td>
                                        <td>{{ $item?->product?->product_name ?: '-' }}</td>
                                        <td>{{ $item?->size?->size_name ?: ($item?->size_unit ?: '-') }}</td>
                                        {{-- <td>{{ $item?->pcs_length_unit ?: '-' }}</td> --}}
                                        <td>{{ $item?->grade?->grade_name ?: '-' }}</td>
                                        <td>{{ $item?->brand?->brand_name ?: '-' }}</td>
                                         <!-- Start PLANNED -->
                                        <td class="text-end">
                                            {{ _rz(($item?->item_qty ?? 0) * ($item?->qty_type == 1 ? 1000 : 1)) }}
                                        </td>

                                        <td class="text-end">
                                            {{ _rz(($item?->fs_bundle_weight ?? 0) * ($item?->fs_bundle_unit == 2 ? 1000 : 1)) }}
                                        </td>
                                        <!-- End PLANNED -->
                                        {{-- ACTUAL --}}
                                        <td class="text-end">{{ _rz($actual) ?? 0 }}</td>
                                        <td class="text-end">{{ _rz($dispatchTons) }}</td>
                                        {{-- ACTUAL --}}
                                        {{-- <td class="text-end">{{ _rz($returned, 3) }}</td>
                                        <td class="text-end">{{ _rz($remaining, 3) }}</td> --}}
                                        {{-- PRICING --}}
                                        <td class="text-end">{{ _rz($price) ?? 0 }}</td>
                                        <td class="text-end">{{ _rz($extra) ?? 0 }}</td>
                                        <td class="text-end">{{ _rz($netRate) ?? 0 }}</td>
                                        <td class="text-end">{{ _rz($lineSubtotal) }}</td>
                                        {{-- PRICING --}}
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="13" class="text-center text-muted py-3">No dispatch materials found.</td>
                                    </tr>
                                @endforelse
                                <tr class="table-light fw-semibold">
                                    <td colspan="12" class="text-end">Total</td>
                                    <td class="text-end">{{ _rz($subtotal) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @php
                        $displayInvoice = $invoice;

                        $labor = (float) ($displayInvoice?->labor_charges ?? 0);
                        $other = (float) ($displayInvoice?->other_charges ?? 0);
                        $freight = (float) ($displayInvoice?->freight_amount ?? 0);
                        $cd = (float) ($displayInvoice?->cash_discount_amount ?? 0);
                        $taxable = (float) ($displayInvoice?->taxable_amount ?? max(0, $subtotal - $cd + $labor + $freight + $other));
                        $sgst = (float) ($displayInvoice?->sgst_amount ?? 0);
                        $cgst = (float) ($displayInvoice?->cgst_amount ?? 0);
                        $igst = (float) ($displayInvoice?->igst_amount ?? 0);
                        $tcs = (float) ($displayInvoice?->tcs_amount ?? 0);
                        $grand = (float) ($displayInvoice?->total_amount ?? 0);
                    @endphp
                    @if(isset($invoice) && $invoice)
                    <table class="table table-bordered table-sm mt-3 mb-3">
                        <tbody>
                            <tr>
                                <td class="text-end">Cash Discount (CD) ({{ number_format((float) ($displayInvoice?->cash_discount_percent ?? 0), 3) }}%)</td>
                                <td class="text-end">{{ number_format($cd, 3) }}</td>
                            </tr>
                            <tr>
                                <td class="text-end">Labor</td>
                                <td class="text-end">{{ number_format($labor, 3) }}</td>
                            </tr>
                            <tr>
                                <td class="text-end">Other Charges</td>
                                <td class="text-end">{{ number_format($other, 3) }}</td>
                            </tr>
                            <tr>
                                <td class="text-end">Freight ({{ (int) ($displayInvoice?->freight_type ?? 1) === 2 ? 'FOR' : 'EX' }})</td>
                                <td class="text-end">{{ number_format($freight, 3) }}</td>
                            </tr>
                            <tr>
                                <td class="text-end fw-semibold">Total (After CD, Labor, Other Charges, Freight)</td>
                                <td class="text-end fw-semibold">{{ number_format($taxable, 3) }}</td>
                            </tr>
                            @if ($displayInvoice?->gst_type === 'SGST_CGST')
                                <tr>
                                    <td class="text-end">S-GST {{ number_format((float) $displayInvoice->sgst_percent, 3) }}%</td>
                                    <td class="text-end">{{ number_format($sgst, 3) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-end">C-GST {{ number_format((float) $displayInvoice->cgst_percent, 3) }}%</td>
                                    <td class="text-end">{{ number_format($cgst, 3) }}</td>
                                </tr>
                            @elseif ($displayInvoice?->gst_type === 'IGST')
                                <tr>
                                    <td class="text-end">I-GST {{ number_format((float) $displayInvoice->igst_percent, 3) }}%</td>
                                    <td class="text-end">{{ number_format($igst, 3) }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td class="text-end">TCS Amount</td>
                                <td class="text-end">{{ number_format($tcs, 3) }}</td>
                            </tr>
                            <tr>
                                <td class="text-end fw-semibold">Grand Total</td>
                                <td class="text-end fw-semibold">{{ number_format($grand ?: $taxable + $sgst + $cgst + $igst + $tcs, 3) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    @endif
                    <div {{-- class="text-end" --}}>
                        <strong>Vehicle Loaded Weight:</strong>
                        {{ _rz($loadedWeight) }} Kgs
                    </div>

                    {{-- <div class="card mt-3">
                        <div class="card-header fw-semibold">
                            Return Items
                        </div>
                        <div class="card-body p-2">
                            @foreach ($materials as $dispatch)
                                @php $item = $dispatch->saleOrderItem; @endphp
                                <form method="POST" action="{{ route('accounts.return.store') }}" class="row g-2 align-items-end mb-2">
                                    @csrf
                                    <input type="hidden" name="dispatch_material_id" value="{{ $dispatch->id }}">
                                    <div class="col-md-1 small fw-semibold">{{ $dispatch->sale_order_id }}</div>
                                    <div class="col-md-2">{{ $item?->product?->product_name ?: '-' }}</div>
                                    <div class="col-md-1">{{ $item?->size?->size_name ?: ($item?->size_unit ?: '-') }}</div>
                                    <div class="col-md-1">{{ number_format((float) ($dispatch->actual_qty ?? $dispatch->dispatch_qty ?? 0), 3) }}</div>
                                    <div class="col-md-2"><input type="number" step="0.001" min="0" max="{{ (float) ($dispatch->actual_qty ?? $dispatch->dispatch_qty ?? 0) }}" class="form-control form-control-sm" name="return_qty" value="{{ $dispatch->return_qty }}" required></div>
                                    <div class="col-md-3"><input type="text" class="form-control form-control-sm" name="return_remarks" value="{{ $dispatch->return_remarks }}" placeholder="Remarks"></div>
                                    <div class="col-md-2"><button class="btn btn-secondary btn-sm">Return</button></div>
                                </form>
                            @endforeach
                        </div>
                    </div> --}}

                </div>
            </div>
        </div>
    </div>

    @if (!$invoice)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Add Invoice - {{ $partyName }} / {{ $gate->truckno }}</h5>
                <div class="small text-muted">Review the material subtotal above, enter invoice details, and check the live totals before submitting.</div>
            </div>
            <form method="POST" action="{{ route('accounts.invoice.store') }}" class="invoice-form" data-gate-id="{{ $gate->id }}" data-subtotal="{{ $subtotal }}" data-dispatch-tons="{{ $totalDispatchTons }}">
                @csrf
                <input type="hidden" name="gate_id" value="{{ $gate->id }}">
                <div class="card-body">
                        <fieldset class="mb-4">
                            <legend class="fs-6 fw-semibold">Invoice Details</legend>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="invoice-billed-to-{{ $gate->id }}">Billed To</label>
                                    <input id="invoice-billed-to-{{ $gate->id }}" type="text" class="form-control" name="billed_to" value="{{ old('billed_to', $partyName !== '-' ? $partyName : '') }}" maxlength="255">
                                    <div class="invalid-feedback" data-error-for="billed_to"></div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" for="invoice-number-{{ $gate->id }}">Invoice No.</label>
                                    <input id="invoice-number-{{ $gate->id }}" type="text" class="form-control" name="invoice_no" value="{{ old('invoice_no', $suggestedInvoiceNo) }}" maxlength="100" required>
                                    <div class="form-text">Suggested number; you can edit it.</div>
                                    <div class="invalid-feedback" data-error-for="invoice_no"></div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" for="invoice-date-{{ $gate->id }}">Invoice Date</label>
                                    <input id="invoice-date-{{ $gate->id }}" type="date" class="form-control" name="invoice_date" value="{{ old('invoice_date', now()->toDateString()) }}" required>
                                    <div class="invalid-feedback" data-error-for="invoice_date"></div>
                                </div>
                            </div>
                        </fieldset>

                        <div class="card bg-light mb-4">
                            <div class="card-body d-flex flex-wrap justify-content-between gap-2">
                                <div><span class="text-muted">Party:</span> <strong>{{ $partyName }}</strong></div>
                                <div><span class="text-muted">Vehicle:</span> <strong>{{ $gate->truckno ?: '-' }}</strong></div>
                                <div><span class="text-muted">Dispatch Slip:</span> <strong>{{ $gate->id }}</strong></div>
                                <div><span class="text-muted">Total Dispatch Quantity:</span> <strong>{{ _rz($totalDispatchTons) }} Tons</strong></div>
                                <div><span class="text-muted">Material Subtotal:</span> <strong>INR {{ _rz($subtotal) }}</strong></div>
                            </div>
                        </div>

                        <fieldset class="mb-3">
                            <legend class="fs-6 fw-semibold">Charges &amp; Discount</legend>
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label class="form-label" for="invoice-discount-{{ $gate->id }}">Cash Discount (%)</label>
                                    <input id="invoice-discount-{{ $gate->id }}" type="number" step="0.001" min="0" max="100" class="form-control" name="cash_discount_percent" value="{{ old('cash_discount_percent', '0') }}">
                                    <div class="invalid-feedback" data-error-for="cash_discount_percent"></div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" for="invoice-labor-{{ $gate->id }}">Labor Rate per Ton (INR)</label>
                                    <input id="invoice-labor-{{ $gate->id }}" type="number" step="0.001" min="0" class="form-control" name="labor_rate_per_ton" value="{{ old('labor_rate_per_ton', '0') }}">
                                    {{-- <div class="form-text">Applied to {{ number_format((float) $totalDispatchTons, 6) }} dispatch tons.</div> --}}
                                    <div class="invalid-feedback" data-error-for="labor_rate_per_ton"></div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" for="invoice-other-{{ $gate->id }}">Other Charges (INR)</label>
                                    <input id="invoice-other-{{ $gate->id }}" type="number" step="0.001" min="0" class="form-control" name="other_charges" value="{{ old('other_charges', '0') }}">
                                    <div class="invalid-feedback" data-error-for="other_charges"></div>
                                </div>
                                 <div class="col-md-2">
                                    <label class="form-label" for="invoice-tds-{{ $gate->id }}">TDS Amount (INR)</label>
                                    <input id="invoice-tds-{{ $gate->id }}" type="number" step="0.001" min="0" class="form-control" name="tds_amount" value="{{ old('tds_amount', '0') }}">
                                    <div class="invalid-feedback" data-error-for="tds_amount"></div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" for="invoice-insurance-{{ $gate->id }}">Insurance Amount (INR)</label>
                                    <input id="invoice-insurance-{{ $gate->id }}" type="number" step="0.001" min="0" class="form-control" name="insurance_amount" value="{{ old('insurance_amount', '0') }}">
                                    <div class="invalid-feedback" data-error-for="insurance_amount"></div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" for="invoice-tcs-{{ $gate->id }}">TCS Amount (INR)</label>
                                    <input id="invoice-tcs-{{ $gate->id }}" type="number" step="0.001" min="0" class="form-control" name="tcs_amount" value="{{ old('tcs_amount', '0') }}">
                                    <div class="invalid-feedback" data-error-for="tcs_amount"></div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" for="invoice-due-days-{{ $gate->id }}">Due Days</label>
                                    <input id="invoice-due-days-{{ $gate->id }}" type="text" class="form-control" placeholder="Enter Due Days" name="due_days" value="{{ old('due_days') }}">
                                    <div class="invalid-feedback" data-error-for="due_days"></div>
                                </div>
                                {{-- Freight Type --}}
                                <div class="col-md-2">
                                    <label class="form-label d-block">Freight Type</label>

                                    <div class="d-flex align-items-center gap-3 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input freight-type-radio" type="radio"
                                                name="freight_type" id="freight-ex-{{ $gate->id }}"
                                                value="1" {{ old('freight_type', '1') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="freight-ex-{{ $gate->id }}">EX</label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input freight-type-radio" type="radio"
                                                name="freight_type" id="freight-for-{{ $gate->id }}"
                                                value="2" {{ old('freight_type') == '2' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="freight-for-{{ $gate->id }}">FOR</label>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback" data-error-for="freight_type"></div>
                                </div>

                                <div class="col-md-2" id="freight-amount-wrapper-{{ $gate->id }}"
                                    style="{{ old('freight_type', '1') == '2' ? '' : 'display:none;' }}">

                                    <label class="form-label" for="freight-amount-{{ $gate->id }}">Freight</label>

                                    <input id="freight-amount-{{ $gate->id }}" type="number"
                                        step="0.001" min="0" class="form-control"
                                        placeholder="Enter Freight Amount"
                                        name="freight_amount" value="{{ old('freight_amount') }}">

                                    <div class="invalid-feedback" data-error-for="freight_amount"></div>
                                </div>
                            </div>
                            <div class="row g-3 mt-3">
                                <div class="col-md-4">
                                    <label class="form-label" for="invoice-sgst-{{ $gate->id }}">S-GST (%)</label>
                                    <input id="invoice-sgst-{{ $gate->id }}" type="number" step="0.001" min="0" max="100" class="form-control gst-mode-local" name="sgst_percent" value="{{ old('sgst_percent', '0') }}">
                                    <div class="invalid-feedback" data-error-for="sgst_percent"></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="invoice-cgst-{{ $gate->id }}">C-GST (%)</label>
                                    <input id="invoice-cgst-{{ $gate->id }}" type="number" step="0.001" min="0" max="100" class="form-control gst-mode-local" name="cgst_percent" value="{{ old('cgst_percent', '0') }}">
                                    <div class="invalid-feedback" data-error-for="cgst_percent"></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="invoice-igst-{{ $gate->id }}">I-GST (%)</label>
                                    <input id="invoice-igst-{{ $gate->id }}" type="number" step="0.001" min="0" max="100" class="form-control gst-mode-local" name="igst_percent" value="{{ old('igst_percent', '0') }}">
                                    <div class="invalid-feedback" data-error-for="igst_percent"></div>
                                </div>
                            </div>
                            <div class="form-text">Use either S-GST/C-GST or I-GST, not both.</div>
                        </fieldset>

                       {{--  <fieldset class="mb-4">
                            <legend class="fs-6 fw-semibold">Tax Rates</legend>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label" for="invoice-sgst-{{ $gate->id }}">S-GST (%)</label>
                                    <input id="invoice-sgst-{{ $gate->id }}" type="number" step="0.001" min="0" max="100" class="form-control gst-mode-local" name="sgst_percent" value="{{ old('sgst_percent', '0') }}">
                                    <div class="invalid-feedback" data-error-for="sgst_percent"></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="invoice-cgst-{{ $gate->id }}">C-GST (%)</label>
                                    <input id="invoice-cgst-{{ $gate->id }}" type="number" step="0.001" min="0" max="100" class="form-control gst-mode-local" name="cgst_percent" value="{{ old('cgst_percent', '0') }}">
                                    <div class="invalid-feedback" data-error-for="cgst_percent"></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="invoice-igst-{{ $gate->id }}">I-GST (%)</label>
                                    <input id="invoice-igst-{{ $gate->id }}" type="number" step="0.001" min="0" max="100" class="form-control gst-mode-local" name="igst_percent" value="{{ old('igst_percent', '0') }}">
                                    <div class="invalid-feedback" data-error-for="igst_percent"></div>
                                </div>
                            </div>
                            <div class="form-text">Use either S-GST/C-GST or I-GST, not both.</div>
                        </fieldset> --}}

                        {{-- <fieldset>
                            <legend class="fs-6 fw-semibold">Payments &amp; Adjustments</legend>
                            <div class="row g-3">

                                <div class="col-md-3">
                                    <label class="form-label" for="invoice-advance-{{ $gate->id }}">Advance Payment (INR)</label>
                                    <input id="invoice-advance-{{ $gate->id }}" type="number" step="0.001" min="0" class="form-control" name="advance_payment" value="{{ old('advance_payment', '0') }}">
                                    <div class="invalid-feedback" data-error-for="advance_payment"></div>
                                </div>

                            </div>
                        </fieldset> --}}

                        <div class="card border-primary mt-4">
                            <div class="card-header fw-semibold">Invoice Calculation Preview (INR)</div>
                            <div class="card-body">
                                <div class="row g-3 text-end">
                                    <div class="col-6 col-md-3"><div class="small text-muted">Material Subtotal</div><strong data-total="subtotal">{{ number_format((float) $subtotal, 3) }}</strong></div>
                                    <div class="col-6 col-md-3"><div class="small text-muted">Cash Discount</div><strong data-total="discount">0.000</strong></div>
                                    <div class="col-6 col-md-3"><div class="small text-muted">Labor Charges</div><strong data-total="labor">0.000</strong></div>
                                    <div class="col-6 col-md-3"><div class="small text-muted">Other Charges</div><strong data-total="other">0.000</strong></div>
                                    <div class="col-6 col-md-3"><div class="small text-muted">Freight Amount</div><strong data-total="freight">0.000</strong></div>
                                    <div class="col-6 col-md-3"><div class="small text-muted">Taxable Amount</div><strong data-total="taxable">{{ number_format((float) $subtotal, 3) }}</strong></div>
                                    <div class="col-6 col-md-3"><div class="small text-muted">GST Amount</div><strong data-total="gst">0.000</strong></div>
                                    <div class="col-6 col-md-3"><div class="small text-muted">TCS Amount</div><strong data-total="tcs">0.000</strong></div>
                                    <div class="col-6 col-md-3"><div class="small text-muted">Invoice Total</div><strong data-total="grand">{{ number_format((float) $subtotal, 3) }}</strong></div>
                                </div>
                                <div class="small text-muted mt-2">TDS, insurance, and advance are recorded separately and are not included in the invoice total, matching the saved invoice calculation.</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary invoice-submit">Submit Invoice</button>
                    </div>
            </form>
        </div>
    @else
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Invoice Details</h5>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('accounts.invoice.bill', ['gate' => $gate->id]) }}" class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener">
                        <i class="fas fa-file-invoice me-1"></i>View Invoice Bill
                    </a>
                    <span class="badge bg-success">Invoice Submitted</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3"><label class="form-label">Billed To</label><input class="form-control" value="{{ $invoice->billed_to }}" readonly></div>
                    <div class="col-md-3"><label class="form-label">Invoice No.</label><input class="form-control" value="{{ $invoice->invoice_no }}" readonly></div>
                    <div class="col-md-3"><label class="form-label">Invoice Date</label><input class="form-control" value="{{ $invoice->invoice_date->format('d-m-Y') }}" readonly></div>
                    <div class="col-md-3"><label class="form-label">GST Type</label><input class="form-control" value="{{ $invoice->gst_type ?: '-' }}" readonly></div>

                    <div class="col-md-3"><label class="form-label">Cash Discount (%)</label><input class="form-control" value="{{ number_format((float) $invoice->cash_discount_percent, 3) }}" readonly></div>
                    <div class="col-md-3"><label class="form-label">Cash Discount (INR)</label><input class="form-control" value="{{ number_format((float) $invoice->cash_discount_amount, 3) }}" readonly></div>
                    <div class="col-md-3"><label class="form-label">Labor Charges (INR)</label><input class="form-control" value="{{ number_format((float) $invoice->labor_charges, 3) }}" readonly></div>
                    <div class="col-md-3"><label class="form-label">Other Charges (INR)</label><input class="form-control" value="{{ number_format((float) $invoice->other_charges, 3) }}" readonly></div>
                    <div class="col-md-3"><label class="form-label">Freight Type</label><input class="form-control" value="{{ (int) ($invoice->freight_type ?? 1) === 2 ? 'FOR' : 'EX' }}" readonly></div>
                    <div class="col-md-3"><label class="form-label">Freight Amount (INR)</label><input class="form-control" value="{{ number_format((float) ($invoice->freight_amount ?? 0), 3) }}" readonly></div>

                    <div class="col-md-3"><label class="form-label">Taxable Amount (INR)</label><input class="form-control" value="{{ number_format((float) $invoice->taxable_amount, 3) }}" readonly></div>
                    <div class="col-md-3"><label class="form-label">S-GST (%) / Amount (INR)</label><input class="form-control" value="{{ number_format((float) $invoice->sgst_percent, 3) }}% / {{ number_format((float) $invoice->sgst_amount, 3) }}" readonly></div>
                    <div class="col-md-3"><label class="form-label">C-GST (%) / Amount (INR)</label><input class="form-control" value="{{ number_format((float) $invoice->cgst_percent, 3) }}% / {{ number_format((float) $invoice->cgst_amount, 3) }}" readonly></div>
                    <div class="col-md-3"><label class="form-label">I-GST (%) / Amount (INR)</label><input class="form-control" value="{{ number_format((float) $invoice->igst_percent, 3) }}% / {{ number_format((float) $invoice->igst_amount, 3) }}" readonly></div>

                    <div class="col-md-3"><label class="form-label">GST Amount (INR)</label><input class="form-control" value="{{ number_format((float) $invoice->gst_amount, 3) }}" readonly></div>
                    <div class="col-md-3"><label class="form-label">TDS Amount (INR)</label><input class="form-control" value="{{ number_format((float) $invoice->tds_amount, 3) }}" readonly></div>
                    <div class="col-md-3"><label class="form-label">Insurance Amount (INR)</label><input class="form-control" value="{{ number_format((float) $invoice->insurance_amount, 3) }}" readonly></div>
                    <div class="col-md-3"><label class="form-label">TCS Amount (INR)</label><input class="form-control" value="{{ number_format((float) $invoice->tcs_amount, 3) }}" readonly></div>

                    <div class="col-md-3"><label class="form-label">Due Days</label><input class="form-control" value="{{ $invoice->due_days }}" readonly></div>
                    <div class="col-md-3"><label class="form-label">Advance Payment (INR)</label><input class="form-control" value="{{ number_format((float) $invoice->advance_payment, 3) }}" readonly></div>
                    <div class="col-md-3"><label class="form-label">Invoice Total (INR)</label><input class="form-control fw-semibold" value="{{ number_format((float) $invoice->total_amount, 3) }}" readonly></div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.invoice-form').forEach(function (form) {
        const sgst = form.querySelector('[name="sgst_percent"]');
        const cgst = form.querySelector('[name="cgst_percent"]');
        const igst = form.querySelector('[name="igst_percent"]');

        function syncTaxMode(changed) {
            const sgstValue = parseFloat(sgst.value || '0');
            const cgstValue = parseFloat(cgst.value || '0');
            const igstValue = parseFloat(igst.value || '0');
            if (changed === igst && igstValue > 0) {
                sgst.value = 0;
                cgst.value = 0;
            } else if ((changed === sgst || changed === cgst) && (sgstValue > 0 || cgstValue > 0)) {
                igst.value = 0;
            }
        }

        [sgst, cgst, igst].forEach(function (input) {
            input.addEventListener('input', function () { syncTaxMode(input); });
        });

        function fieldValue(name) {
            const input = form.querySelector('[name="' + name + '"]');
            return input ? (parseFloat(input.value || '0') || 0) : 0;
        }

        function updateTotals() {
            const subtotal = parseFloat(form.dataset.subtotal) || 0;
            const labor = (parseFloat(form.dataset.dispatchTons) || 0) * fieldValue('labor_rate_per_ton');
            const discount = subtotal * fieldValue('cash_discount_percent') / 100;
            const other = fieldValue('other_charges');
            const freightType = form.querySelector('[name="freight_type"]:checked');
            const freight = freightType && freightType.value === '2' ? fieldValue('freight_amount') : 0;
            const taxable = Math.max(0, subtotal - discount + labor + freight + other);
            const sgstAmount = taxable * fieldValue('sgst_percent') / 100;
            const cgstAmount = taxable * fieldValue('cgst_percent') / 100;
            const igstAmount = taxable * fieldValue('igst_percent') / 100;
            const gst = sgstAmount + cgstAmount + igstAmount;
            const tcs = fieldValue('tcs_amount');
            const grand = taxable + gst + tcs;
            const totals = { subtotal: subtotal, discount: discount, labor: labor, other: other, freight: freight, taxable: taxable, gst: gst, tcs: tcs, grand: grand };

            Object.keys(totals).forEach(function (key) {
                const value = totals[key];
                form.querySelector('[data-total="' + key + '"]').textContent = String(value);
            });
        }

        ['cash_discount_percent', 'labor_rate_per_ton', 'other_charges', 'freight_amount', 'sgst_percent', 'cgst_percent', 'igst_percent', 'tcs_amount']
            .forEach(function (name) {
                const input = form.querySelector('[name="' + name + '"]');
                if (input) input.addEventListener('input', updateTotals);
            });

        form.querySelectorAll('[name="freight_type"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                const gateId = form.dataset.gateId;
                const wrapper = document.getElementById('freight-amount-wrapper-' + gateId);
                const freightInput = wrapper ? wrapper.querySelector('[name="freight_amount"]') : null;

                if (radio.checked && radio.value === '2') {
                    if (wrapper) wrapper.style.display = '';
                } else if (radio.checked) {
                    if (wrapper) wrapper.style.display = 'none';
                    if (freightInput) freightInput.value = '';
                }
                updateTotals();
            });
        });

        updateTotals();
    });
});
</script>
@endpush
@endsection
