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
                    <div>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#invoiceModal{{ $gate->id }}" @disabled($invoice)>
                            <i class="fas fa-plus me-1"></i>{{ $invoice ? 'Invoice Added' : 'Add Invoice' }}
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-2"><strong>Party Name:</strong> {{ $partyName }}</div>
                    <div class="mb-3"><strong>Vehicle Tare Weight:</strong> {{ number_format((float) $tareWeight, 3) }} Tons</div>
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
                                    <th colspan="2" class="text-center">ACTUAL</th>
                                    {{-- <th colspan="2" class="text-center">RETURN</th> --}}
                                    <th colspan="4" class="text-center">PRICING</th>
                                </tr>

                                <tr>
                                    <!-- PLANNED -->
                                    <th class="text-end">WEIGHT QTY (MT)</th>
                                    <th class="text-end">PCS/BUNDLES</th>

                                    <!-- ACTUAL -->
                                    <th class="text-end">WEIGHT (MT)</th>
                                    <th class="text-end">PCS/BUNDLES</th>

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
                                        $actual = (float) ($dispatch->actual_qty ?? $dispatch->dispatch_qty ?? 0);
                                        $returned = (float) ($dispatch->return_qty ?? 0);
                                        $remaining = max(0, $actual - $returned);
                                        $price = (float) ($item?->sale_item_prices ?? 0);
                                        $extra = (float) ($item?->size_extra ?? 0);
                                        $netRate = $price + $extra;
                                    @endphp
                                    <tr>
                                        <td>{{ $dispatch->sale_order_id }}</td>
                                        <td>{{ $item?->product?->product_name ?: '-' }}</td>
                                        <td>{{ $item?->size?->size_name ?: ($item?->size_unit ?: '-') }}</td>
                                        {{-- <td>{{ $item?->pcs_length_unit ?: '-' }}</td> --}}
                                        <td>{{ $item?->grade?->grade_name ?: '-' }}</td>
                                        <td>{{ $item?->brand?->brand_name ?: '-' }}</td>
                                        <td class="text-end">{{ number_format((float) ($item?->item_qty ?? 0), 3) }}</td>
                                        <td class="text-end">{{ number_format((float) ($item?->fs_bundle_weight ?? 0), 3) }}</td>
                                        <td class="text-end">{{ number_format($actual, 3) }}</td>
                                        <td class="text-end">{{ number_format((float) ($dispatch->dispatch_qty ?? 0), 3) }}</td>
                                        {{-- <td class="text-end">{{ number_format($returned, 3) }}</td>
                                        <td class="text-end">{{ number_format($remaining, 3) }}</td> --}}
                                        <td class="text-end">{{ number_format($price, 3) }}</td>
                                        <td class="text-end">{{ number_format($extra, 3) }}</td>
                                        <td class="text-end">{{ number_format($netRate, 3) }}</td>
                                        <td class="text-end">{{ number_format($remaining * $netRate, 3) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="16" class="text-center text-muted py-3">No dispatch materials found.</td>
                                    </tr>
                                @endforelse
                                <tr class="table-light fw-semibold">
                                    <td colspan="12" class="text-end">Total</td>
                                    <td class="text-end">{{ number_format($subtotal, 3) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @php
                        $displayInvoice = $invoice;
                        $labor = (float) ($displayInvoice?->labor_charges ?? 0);
                        $other = (float) ($displayInvoice?->other_charges ?? 0);
                        $cd = (float) ($displayInvoice?->cash_discount_amount ?? 0);
                        $taxable = (float) ($displayInvoice?->taxable_amount ?? max(0, $subtotal - $cd + $labor + $other));
                        $sgst = (float) ($displayInvoice?->sgst_amount ?? 0);
                        $cgst = (float) ($displayInvoice?->cgst_amount ?? 0);
                        $igst = (float) ($displayInvoice?->igst_amount ?? 0);
                        $tcs = (float) ($displayInvoice?->tcs_amount ?? 0);
                        $grand = (float) ($displayInvoice?->total_amount ?? 0);
                    @endphp
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
                                <td class="text-end fw-semibold">Total (After CD, Labor, Other Charges)</td>
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
                    <div {{-- class="text-end" --}}>
                        <strong>Vehicle Loaded Weight:</strong> 
                        {{ number_format((float) $loadedWeight, 3) }}
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

                    @if ($invoice)
                        <div class="card mt-3">
                            <div class="card-header fw-semibold">Add Invoice Details</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Billed To</label>
                                        <input class="form-control" value="{{ $invoice->billed_to }}" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Invoice No.</label>
                                        <input class="form-control" value="{{ $invoice->invoice_no }}" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Invoice Date</label>
                                        <input class="form-control" value="{{ $invoice->invoice_date->format('d-m-Y') }}" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">GST Type</label>
                                        <input class="form-control" value="{{ $invoice->gst_type ?: '-' }}" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Taxable Amount (INR)</label>
                                        <input class="form-control" value="{{ number_format((float) $invoice->taxable_amount, 3) }}" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">GST (INR)</label>
                                        <input class="form-control" value="{{ number_format((float) $invoice->gst_amount, 3) }}" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">TDS Amount (INR)</label>
                                        <input class="form-control" value="{{ number_format((float) $invoice->tds_amount, 3) }}" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Insurance Amount (INR)</label>
                                        <input class="form-control" value="{{ number_format((float) $invoice->insurance_amount, 3) }}" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">TCS Amount (INR)</label>
                                        <input class="form-control" value="{{ number_format((float) $invoice->tcs_amount, 3) }}" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Cash Discount (%)</label>
                                        <input class="form-control" value="{{ number_format((float) $invoice->cash_discount_percent, 3) }}" >
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Due Days</label>
                                        <input class="form-control" value="{{ $invoice->due_days }}" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Advance Payment</label>
                                        <input class="form-control" value="{{ number_format((float) $invoice->advance_payment, 3) }}" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Other Charges (INR)</label>
                                        <input class="form-control" value="{{ number_format((float) $invoice->other_charges, 3) }}" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Labor Charges (INR)</label>
                                        <input class="form-control" value="{{ number_format((float) $invoice->labor_charges, 3) }}" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Total Amount (INR)</label>
                                        <input class="form-control" value="{{ number_format((float) $invoice->total_amount, 3) }}" readonly>
                                    </div>
                                    @if ($invoice->gst_type === 'SGST_CGST')
                                    <div class="col-md-3">
                                        <label class="form-label">S-GST</label>
                                        <input class="form-control" value="{{ number_format((float) $invoice->sgst_percent, 3) }}% / {{ number_format((float) $invoice->sgst_amount, 3) }}" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">C-GST</label>
                                        <input class="form-control" value="{{ number_format((float) $invoice->cgst_percent, 3) }}% / {{ number_format((float) $invoice->cgst_amount, 3) }}" readonly>
                                    </div>
                                    @elseif ($invoice->gst_type === 'IGST')
                                    <div class="col-md-3">
                                        <label class="form-label">I-GST</label>
                                        <input class="form-control" value="{{ number_format((float) $invoice->igst_percent, 3) }}% / {{ number_format((float) $invoice->igst_amount, 3) }}" readonly>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="invoiceModal{{ $gate->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <form method="POST" action="{{ route('accounts.invoice.store') }}" class="invoice-form" data-gate-id="{{ $gate->id }}">
                    @csrf
                    <input type="hidden" name="gate_id" value="{{ $gate->id }}">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Invoice - {{ $partyName }} / {{ $gate->truckno }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Billed To</label>
                                <input type="text" class="form-control" name="billed_to" value="{{ $partyName }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Invoice No.</label>
                                <input type="text" class="form-control" name="invoice_no" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Invoice Date</label>
                                <input type="date" class="form-control" name="invoice_date" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Cash Discount (%)</label>
                                <input type="number" step="0.001" min="0" max="100" class="form-control" name="cash_discount_percent" value="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Due Days</label>
                                <input type="number" min="0" class="form-control" name="due_days" value="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Advance Payment</label>
                                <input type="number" step="0.001" min="0" class="form-control" name="advance_payment" value="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">TDS Amount</label>
                                <input type="number" step="0.001" min="0" class="form-control" name="tds_amount" value="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Insurance Amount</label>
                                <input type="number" step="0.001" min="0" class="form-control" name="insurance_amount" value="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">TCS Amount</label>
                                <input type="number" step="0.001" min="0" class="form-control" name="tcs_amount" value="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Other Charges</label>
                                <input type="number" step="0.001" min="0" class="form-control" name="other_charges" value="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Labor Charges</label>
                                <input type="number" step="0.001" min="0" class="form-control" name="labor_charges" value="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">S-GST (%)</label>
                                <input type="number" step="0.001" min="0" max="100" class="form-control gst-mode-local" name="sgst_percent" value="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">C-GST (%)</label>
                                <input type="number" step="0.001" min="0" max="100" class="form-control gst-mode-local" name="cgst_percent" value="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">I-GST (%)</label>
                                <input type="number" step="0.001" min="0" max="100" class="form-control gst-mode-local" name="igst_percent" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary invoice-submit">Submit Invoice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="invoicePageLoader" style="display:none;position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,.55);align-items:center;justify-content:center;">
    <div class="text-center text-white">
        <div class="spinner-border mb-3" role="status"></div>
        <div>Please wait, invoice is being processed...</div>
    </div>
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
                sgst.value = 0; cgst.value = 0;
                sgst.disabled = true; cgst.disabled = true;
            } else if (changed === sgst || changed === cgst) {
                if (sgstValue > 0 || cgstValue > 0) {
                    igst.value = 0; igst.disabled = true;
                } else {
                    igst.disabled = false;
                }
            } else if (sgstValue > 0 || cgstValue > 0) {
                igst.disabled = true;
            } else if (igstValue > 0) {
                sgst.disabled = true; cgst.disabled = true;
            } else {
                sgst.disabled = false; cgst.disabled = false; igst.disabled = false;
            }
        }
        [sgst, cgst, igst].forEach(function (input) {
            input.addEventListener('input', function () { syncTaxMode(input); });
        });

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            if (form.dataset.submitting === '1') return;
            form.dataset.submitting = '1';
            const submit = form.querySelector('.invoice-submit');
            submit.disabled = true;
            const loader = document.getElementById('invoicePageLoader');
            loader.style.display = 'flex';
            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            }).then(async function (response) {
                const data = await response.json().catch(function () { return {}; });
                if (!response.ok) {
                    const message = data.message || Object.values(data.errors || {}).flat().join(' ');
                    throw new Error(message || 'Unable to create invoice.');
                }
                window.location.reload();
            }).catch(function (error) {
                alert(error.message);
            }).finally(function () {
                loader.style.display = 'none';
                submit.disabled = false;
                form.dataset.submitting = '0';
            });
        });
    });
});
</script>
@endpush
@endsection