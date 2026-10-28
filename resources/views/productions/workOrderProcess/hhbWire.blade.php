@extends('layouts.app')

@section('title', ($saleOrderItem->product?->product_name ?? 'HHB Wire') . ' Work Order #' . $workOrder->id)

@section('content')
@php
    $sizeLabel = $saleOrderItem->size?->size_name ?? $saleOrderItem->size_unit ?? '-';
    $bundleUnit = match ((int) $saleOrderItem->fs_bundle_unit) {
        \App\Models\SaleOrderItem::BUNDLE_KG => 'KG',
        \App\Models\SaleOrderItem::BUNDLE_TONS => 'Tons',
        default => '-',
    };
    $materialSentKg = (float) ($workOrder->material_send_for_work ?? 0) * 1000;
    $nonRecoverableScrap = (float) ($totalNonRecScrap ?? 0);
    $recoverableScrap = (float) ($totalRecScrap ?? 0);
    $finishedMaterialKg = max(0, $materialSentKg - $nonRecoverableScrap - $recoverableScrap);
@endphp

<style>
    #productionOperationLoader {
        position: fixed;
        inset: 0;
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.8);
    }

    #productionOperationLoader.is-visible {
        display: flex;
    }
</style>

<div id="productionOperationLoader" role="status" aria-live="assertive" aria-hidden="true">
    <div class="text-center">
        <div class="spinner-border text-primary" aria-hidden="true"></div>
        <div class="mt-2">Saving operation...</div>
    </div>
</div>

<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h4 class="mb-1">{{ $saleOrderItem->product?->product_name ?? 'HHB Wire' }} Production</h4>
                    <div class="text-muted">Work Order #{{ $workOrder->id }}</div>
                </div>
                <a href="{{ route('productions') }}" class="btn btn-outline-secondary">Back</a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>S No.</th>
                            <th>WO. No./Date</th>
                            <th>Party Name</th>
                            <th>SO No - Product - Size - Rate - Qty</th>
                            <th>Grade</th>
                            <th>Pending Qty. (Tons)</th>
                            <th class="no-print">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>{{ $workOrder->id }} / <strong>{{ _dt($workOrder->created_at) }}</strong></td>
                            <td>{{ $saleOrderItem->saleOrder?->customer?->company_name ?? '-' }}</td>
                            <td>
                                <strong class="text-danger">{{ $saleOrderItem->saleOrder?->id ?? '-' }}</strong>
                                - {{ $saleOrderItem->product?->product_name ?? '-' }}
                                - {{ $sizeLabel }}
                                - {{ $saleOrderItem->sale_item_prices ?? '-' }}
                                - {{ _rz($saleOrderItem->item_qty) }}
                                {{ $saleOrderItem->qty_type === \App\Models\SaleOrderItem::QTY_KGS ? 'KG' : 'Tons' }}
                            </td>
                            <td>{{ $saleOrderItem->grade?->grade_name ?? '-' }}</td>
                            <td>{{ $workOrder->material_send_for_work ? _rz($workOrder->material_send_for_work) : '0' }}</td>
                            <td class="text-success">
                                <strong class="text-capitalize">{{ str_replace('_', ' ', $workOrder->status) }}</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
    @endif

    <div class="card-body">
        <div class="accordion accordion-flush" id="accordionFlushExample">
            @if ($currentOperation >= 1)
                <div class="accordion-item shadow-sm border rounded mt-4">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $currentOperation === 1 ? '' : 'collapsed' }} bg-success text-white"
                            type="button" data-bs-toggle="collapse" data-bs-target="#hhbOperation1"
                            aria-expanded="{{ $currentOperation === 1 ? 'true' : 'false' }}" aria-controls="hhbOperation1">
                            <b>ACID PICKLING | PHOSPHATING (Operation 1)</b>
                        </button>
                    </h2>
                    <div id="hhbOperation1" class="accordion-collapse collapse {{ $currentOperation === 1 ? 'show' : '' }}"
                        data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            <form method="post" action="{{ route('productions.operation', ['workOrder' => $workOrder->id, 'operation' => 1]) }}">
                                @csrf
                                <input type="hidden" name="workStatus" value="operation1">
                                <input type="hidden" name="processtype" value="acid_pickling_phosphating">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="acid_pickling" id="hhbAcidPickling"
                                                value="1" @checked(old('acid_pickling', $workOrder->acid_pickling ?? '') == '1')>
                                            <label class="form-check-label" for="hhbAcidPickling">Acid Pickling</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="phosphating" id="hhbPhosphating"
                                                value="1" @checked(old('phosphating', $workOrder->phosphating ?? '') == '1')>
                                            <label class="form-check-label" for="hhbPhosphating">Phosphating</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <label class="col-form-label" for="hhbAcidRemarks">Remarks</label>
                                        <textarea class="form-control" name="acid_remarks" id="hhbAcidRemarks">{{ old('acid_remarks', $workOrder->acid_remarks) }}</textarea>
                                    </div>
                                    @if ($currentOperation === 1)
                                        <div class="col-12 text-end mt-3">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            @if ($currentOperation >= 2)
                <div class="accordion-item shadow-sm border rounded mt-4">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $currentOperation === 2 ? '' : 'collapsed' }} bg-success text-white"
                            type="button" data-bs-toggle="collapse" data-bs-target="#hhbOperation2"
                            aria-expanded="{{ $currentOperation === 2 ? 'true' : 'false' }}" aria-controls="hhbOperation2">
                            <b>WIRE DRAWING (Operation 2)</b>
                        </button>
                    </h2>
                    <div id="hhbOperation2" class="accordion-collapse collapse {{ $currentOperation === 2 ? 'show' : '' }}"
                        data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            <form method="post" action="{{ route('productions.operation', ['workOrder' => $workOrder->id, 'operation' => 2]) }}">
                                @csrf
                                <input type="hidden" name="workStatus" value="operation2">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="col-form-label" for="hhbBundleWeight">FS Bundle Weight (From SO)</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="hhbBundleWeight"
                                                value="{{ $saleOrderItem->fs_bundle_weight ? _rz($saleOrderItem->fs_bundle_weight) : '' }}" readonly>
                                            <span class="input-group-text">{{ $bundleUnit }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="col-form-label" for="hhbFinalBundleCount">Final Bundle Count</label>
                                        <input type="number" min="1" step="1" class="form-control" id="hhbFinalBundleCount"
                                            name="final_bundle_count"
                                            value="{{ old('final_bundle_count', (int) ($workOrder->total_full_bundle_count ?? 0) + (int) ($workOrder->lose_bundle_count ?? 0)) }}"
                                            required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="col-form-label">Available Material (Kg.)</label>
                                        <input type="text" class="form-control" value="{{ _rz($materialSentKg) }}" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label">Calculated Bundle Count</label>
                                        <input type="text" class="form-control"
                                            value="{{ (int) ($workOrder->total_full_bundle_count ?? 0) }} full + {{ (int) ($workOrder->lose_bundle_count ?? 0) }} loose" readonly>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mt-3">
                                        <div class="fw-semibold border-bottom pb-1">Raw Material</div>
                                        <div class="row">
                                            <div class="col-md-6 mt-3">
                                                <label class="col-form-label" for="hhbRmSize">RM Size (Purchase)</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="hhbRmSize" name="rm_size"
                                                        value="{{ old('rm_size', $saleOrderItem->size?->size_name) }}" readonly required>
                                                    <select class="form-select" name="rm_size_unit" aria-label="Raw material size unit" required>
                                                        <option value="MM" @selected(old('rm_size_unit', $saleOrderItem->size_unit) === 'MM')>MM</option>
                                                        <option value="THOU" @selected(old('rm_size_unit', $saleOrderItem->size_unit) === 'THOU')>THOU</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label class="col-form-label" for="hhbRmObservedSize">Observed Size</label>
                                                <div class="input-group">
                                                    <input type="number" step="0.001" min="0" class="form-control" id="hhbRmObservedSize"
                                                        name="rm_observed_size" value="{{ old('rm_observed_size', $workOrder->rm_observed_size) }}" required>
                                                    <select class="form-select" name="rm_observed_unit" aria-label="Observed raw material size unit" required>
                                                        <option value="MM" @selected(old('rm_observed_unit', $workOrder->rm_observed_unit) === 'MM')>MM</option>
                                                        <option value="THOU" @selected(old('rm_observed_unit', $workOrder->rm_observed_unit) === 'THOU')>THOU</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-3">
                                        <div class="fw-semibold border-bottom pb-1">Finished Material</div>
                                        <div class="row">
                                            <div class="col-md-6 mt-3">
                                                <label class="col-form-label" for="hhbFsSize">FM Size (Sale)</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="hhbFsSize" name="fs_size"
                                                        value="{{ old('fs_size', $saleOrderItem->size?->size_name) }}" readonly required>
                                                    <select class="form-select" name="fs_size_unit" aria-label="Finished material size unit" required>
                                                        <option value="MM" @selected(old('fs_size_unit', $saleOrderItem->size_unit) === 'MM')>MM</option>
                                                        <option value="THOU" @selected(old('fs_size_unit', $saleOrderItem->size_unit) === 'THOU')>THOU</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label class="col-form-label" for="hhbFsObservedSize">Observed Size</label>
                                                <div class="input-group">
                                                    <input type="number" step="0.001" min="0" class="form-control" id="hhbFsObservedSize"
                                                        name="fs_observed_size" value="{{ old('fs_observed_size', $workOrder->fs_observed_size) }}" required>
                                                    <select class="form-select" name="fs_observed_unit" aria-label="Observed finished material size unit" required>
                                                        <option value="MM" @selected(old('fs_observed_unit', $workOrder->fs_observed_unit) === 'MM')>MM</option>
                                                        <option value="THOU" @selected(old('fs_observed_unit', $workOrder->fs_observed_unit) === 'THOU')>THOU</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if ($currentOperation === 2)
                                        <div class="col-12 text-end mt-3">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            @if ($currentOperation >= 3)
                <div class="accordion-item shadow-sm border rounded mt-4">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $currentOperation === 3 ? '' : 'collapsed' }} bg-success text-white"
                            type="button" data-bs-toggle="collapse" data-bs-target="#hhbOperation3"
                            aria-expanded="{{ $currentOperation === 3 ? 'true' : 'false' }}" aria-controls="hhbOperation3">
                            <b>HEAT TREATMENT (Operation 3)</b>
                        </button>
                    </h2>
                    <div id="hhbOperation3" class="accordion-collapse collapse {{ $currentOperation === 3 ? 'show' : '' }}"
                        data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            <form method="post" action="{{ route('productions.operation', ['workOrder' => $workOrder->id, 'operation' => 3]) }}">
                                @csrf
                                <input type="hidden" name="workStatus" value="operation3">
                                <div class="row">
                                    <div class="col-md-3 mt-3">
                                        <label class="col-form-label" for="hhbBatchNo">Batch No.</label>
                                        <input type="text" class="form-control" id="hhbBatchNo" name="batch_no"
                                            value="{{ old('batch_no', $workOrder->batch_no) }}" maxlength="50" required>
                                    </div>
                                    <div class="col-md-3 mt-3">
                                        <label class="col-form-label" for="hhbHardnessSo">Hardness (Sale Order)</label>
                                        <div class="input-group">
                                            <input type="number" step="0.001" min="0" class="form-control" id="hhbHardnessSo"
                                                name="hardness_so" value="{{ old('hardness_so', $saleOrderItem->hardness ?? $workOrder->hardness_so) }}">
                                            <select class="form-select" name="hardness_so_unit" aria-label="Sale order hardness unit">
                                                <option value="BHN" @selected(old('hardness_so_unit', $saleOrderItem->hardness_unit ?? $workOrder->hardness_so_unit) === 'BHN')>BHN</option>
                                                <option value="HRC" @selected(old('hardness_so_unit', $saleOrderItem->hardness_unit ?? $workOrder->hardness_so_unit) === 'HRC')>HRC</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mt-3">
                                        <label class="col-form-label" for="hhbHardnessObserved">Hardness Observed</label>
                                        <div class="input-group">
                                            <input type="number" step="0.001" min="0" class="form-control" id="hhbHardnessObserved"
                                                name="hardness_observed" value="{{ old('hardness_observed', $workOrder->hardness_observed) }}" required>
                                            <select class="form-select" name="hardness_observed_unit" aria-label="Observed hardness unit" required>
                                                <option value="BHN" @selected(old('hardness_observed_unit', $workOrder->hardness_observed_unit) === 'BHN')>BHN</option>
                                                <option value="HRC" @selected(old('hardness_observed_unit', $workOrder->hardness_observed_unit) === 'HRC')>HRC</option>
                                            </select>
                                        </div>
                                    </div>
                                    @if ($currentOperation === 3)
                                        <div class="col-12 text-end mt-3">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            @if ($currentOperation >= 4)
                <div class="accordion-item shadow-sm border rounded mt-4">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $currentOperation === 4 ? '' : 'collapsed' }} bg-success text-white"
                            type="button" data-bs-toggle="collapse" data-bs-target="#hhbOperation4"
                            aria-expanded="{{ $currentOperation === 4 ? 'true' : 'false' }}" aria-controls="hhbOperation4">
                            <b>ACID PICKLING | PHOSPHATING (Operation 4)</b>
                        </button>
                    </h2>
                    <div id="hhbOperation4" class="accordion-collapse collapse {{ $currentOperation === 4 ? 'show' : '' }}"
                        data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            <form method="post" action="{{ route('productions.operation', ['workOrder' => $workOrder->id, 'operation' => 4]) }}">
                                @csrf
                                <input type="hidden" name="workStatus" value="operation4">
                                <input type="hidden" name="processtype" value="acid_pickling_phosphating">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="acid_pickling" id="hhbSecondAcidPickling"
                                                value="1" @checked(old('acid_pickling', $workOrder->second_acid_pickling ?? '') == '1')>
                                            <label class="form-check-label" for="hhbSecondAcidPickling">Acid Pickling</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="phosphating" id="hhbSecondPhosphating"
                                                value="1" @checked(old('phosphating', $workOrder->second_phosphating ?? '') == '1')>
                                            <label class="form-check-label" for="hhbSecondPhosphating">Phosphating</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mt-3">
                                        <label class="col-form-label" for="hhbPreviousBatch">Previous Batch No.</label>
                                        <input type="text" class="form-control" id="hhbPreviousBatch" name="batch_no_old"
                                            value="{{ old('batch_no_old', $workOrder->second_batch_no) }}" maxlength="50">
                                    </div>
                                    <div class="col-md-9 mt-3">
                                        <label class="col-form-label" for="hhbSecondAcidRemarks">Remarks</label>
                                        <textarea class="form-control" id="hhbSecondAcidRemarks" name="acid_remarks">{{ old('acid_remarks', $workOrder->second_acid_remarks) }}</textarea>
                                    </div>
                                    @if ($currentOperation === 4)
                                        <div class="col-12 text-end mt-3">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            @if ($currentOperation >= 5)
                <div class="accordion-item shadow-sm border rounded mt-4">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $currentOperation === 5 ? '' : 'collapsed' }} bg-success text-white"
                            type="button" data-bs-toggle="collapse" data-bs-target="#hhbOperation5"
                            aria-expanded="{{ $currentOperation === 5 ? 'true' : 'false' }}" aria-controls="hhbOperation5">
                            <b>WIRE DRAWING (Final Pass, Operation 5)</b>
                        </button>
                    </h2>
                    <div id="hhbOperation5" class="accordion-collapse collapse {{ $currentOperation === 5 ? 'show' : '' }}"
                        data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            <form method="post" action="{{ route('productions.operation', ['workOrder' => $workOrder->id, 'operation' => 5]) }}">
                                @csrf
                                <input type="hidden" name="workStatus" value="operation5">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="col-form-label" for="hhbFinalBundleWeight">Finished Bundle Weight (From SO)</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="hhbFinalBundleWeight"
                                                value="{{ $saleOrderItem->fs_bundle_weight ? _rz($saleOrderItem->fs_bundle_weight) : '' }}" readonly>
                                            <span class="input-group-text">{{ $bundleUnit }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label" for="hhbMiscBundleWeight">Other Misc. Bundle Weight (Kg.)</label>
                                        <input type="number" step="0.001" min="0" class="form-control" id="hhbMiscBundleWeight"
                                            name="misc_bundle_weight" value="{{ old('misc_bundle_weight', $workOrder->misc_bundle_weight) }}">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="col-form-label" for="hhbManageMiscScrap">Misc. Scrap (Kg.)</label>
                                        <input type="number" step="0.001" min="0" class="form-control" id="hhbManageMiscScrap"
                                            name="manage_misc_scrap" value="{{ old('manage_misc_scrap', $workOrder->misc_scrap) }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mt-3">
                                        <div class="fw-semibold border-bottom pb-1">First Draw</div>
                                        <div class="row">
                                            <div class="col-md-6 mt-3">
                                                <label class="col-form-label" for="hhbFirstDrawSize">First Draw Size</label>
                                                <input type="text" class="form-control" id="hhbFirstDrawSize"
                                                    value="{{ old('first_draw_size', $workOrder->fs_observed_size) }}" readonly>
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label class="col-form-label" for="hhbFirstDrawObservedSize">Observed Size</label>
                                                <div class="input-group">
                                                    <input type="number" step="0.001" min="0" class="form-control" id="hhbFirstDrawObservedSize"
                                                        name="first_draw_observed_size"
                                                        value="{{ old('first_draw_observed_size', $workOrder->final_first_draw_observed_size) }}" required>
                                                    <select class="form-select" name="first_draw_observed_unit" aria-label="First draw size unit" required>
                                                        <option value="MM" @selected(old('first_draw_observed_unit', $workOrder->final_first_draw_observed_unit) === 'MM')>MM</option>
                                                        <option value="THOU" @selected(old('first_draw_observed_unit', $workOrder->final_first_draw_observed_unit) === 'THOU')>THOU</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-3">
                                        <div class="fw-semibold border-bottom pb-1">Finished Material</div>
                                        <div class="row">
                                            <div class="col-md-6 mt-3">
                                                <label class="col-form-label" for="hhbFinalSize">FM Size (Sale)</label>
                                                <input type="text" class="form-control" id="hhbFinalSize"
                                                    value="{{ $saleOrderItem->size?->size_name }}" readonly>
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label class="col-form-label" for="hhbFinalObservedSize">Observed Size</label>
                                                <div class="input-group">
                                                    <input type="number" step="0.001" min="0" class="form-control" id="hhbFinalObservedSize"
                                                        name="final_observed_size"
                                                        value="{{ old('final_observed_size', $workOrder->final_observed_size) }}" required>
                                                    <select class="form-select" name="final_observed_unit" aria-label="Final size unit" required>
                                                        <option value="MM" @selected(old('final_observed_unit', $workOrder->final_observed_unit) === 'MM')>MM</option>
                                                        <option value="THOU" @selected(old('final_observed_unit', $workOrder->final_observed_unit) === 'THOU')>THOU</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if ($currentOperation === 5)
                                        <div class="col-12 text-end mt-3">
                                            <button type="submit" class="btn btn-primary">Submit and Generate Token</button>
                                        </div>
                                    @endif
                                </div>
                            </form>

                            <hr>
                            <div class="row bg-light mt-4 p-2">
                                <div class="col-12">
                                    <div class="fw-semibold border-bottom pb-1">Finish Material Info</div>
                                    <div class="row">
                                        <div class="col-md-3 mt-3">
                                            <label class="col-form-label">Send RM Qty (Kg.)</label>
                                            <input type="text" class="form-control" value="{{ _rz($materialSentKg) }}" readonly>
                                        </div>
                                        <div class="col-md-3 mt-3">
                                            <label class="col-form-label">Non-Recoverable Scrap (Kg.)</label>
                                            <input type="text" class="form-control" value="{{ _rz($nonRecoverableScrap) }}" readonly>
                                        </div>
                                        <div class="col-md-3 mt-3">
                                            <label class="col-form-label">Recoverable Scrap (Kg.)</label>
                                            <input type="text" class="form-control" value="{{ _rz($recoverableScrap) }}" readonly>
                                        </div>
                                        <div class="col-md-3 mt-3">
                                            <label class="col-form-label">Finish Qty (Kg.)</label>
                                            <input type="text" class="form-control" value="{{ _rz($finishedMaterialKg) }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('#accordionFlushExample form').forEach((form) => {
        form.addEventListener('submit', () => {
            const loader = document.getElementById('productionOperationLoader');
            const submitButton = form.querySelector('[type="submit"]');

            if (submitButton) {
                submitButton.disabled = true;
            }

            loader.classList.add('is-visible');
            loader.setAttribute('aria-hidden', 'false');
        });
    });
</script>
@endsection
