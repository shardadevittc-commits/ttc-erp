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
                                {{-- <input type="hidden" name="processtype" value="acid_pickling_phosphating"> --}}
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

            @php
                $finishedMaterial = !empty($workOrder->material_send_for_work) ? _rz(($workOrder->material_send_for_work * 1000) - ($totalNonRecScrap ?? 0)): '0:00 Kgs';
                $finishedqty = !empty($workOrder->material_send_for_work) ? _rz(($workOrder->material_send_for_work * 1000) - (($totalNonRecScrap ?? 0) + ($totalRecScrap ?? 0))) : '0:00 Kgs';
                
                if($bundleUnit == 'KG'){
                    $fsBundleW8 = $saleOrderItem->fs_bundle_weight;
                }elseif($bundleUnit == 'Tons'){
                    $fsBundleW8 = $saleOrderItem->fs_bundle_weight*1000;
                }
                $fullBundle = $finishedMaterial / $fsBundleW8;
                
                $fullBundles = (int) floor($finishedMaterial / $fsBundleW8);
                $remainingWeight = fmod($finishedMaterial, $fsBundleW8);

                $bundleData = [
                    [
                        "bundleWeight" => $fsBundleW8,
                        "count" => $fullBundles
                    ]
                ];
                
                if ($remainingWeight > 0) {
                    $bundleData[] = [
                        "bundleWeight" => $remainingWeight,
                        "count" => 1
                    ];
                }
                
                $loose_bundle_count = json_encode($bundleData);
            @endphp
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
                                            <input type="text" class="form-control" id="hhbBundleWeight" value="{{ $saleOrderItem->fs_bundle_weight ? _rz($saleOrderItem->fs_bundle_weight) : '' }}" disabled>
                                            <span class="input-group-text">{{ $bundleUnit }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="col-form-label" for="hhbFinalBundleCount">Bundle</label>
                                        <input type="text" min="1" step="1" class="form-control number_only" id="hhbFinalBundleCount"
                                            name="final_bundle_count" placeholder="Enter Final bundle Count Ex {{ $fullBundles ?? 12 }}"
                                            value="{{ old('final_bundle_count', ($workOrder->total_full_bundle_count) + ($workOrder->lose_bundle_count)) }}"
                                            required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="col-form-label text-wrap text-size">Bundles * Weight = Prop Weight</label>
                                        <input type="text" class="form-control" id="fullBundleCount" value="{{ $fullBundles . ' * ' . _rz($fsBundleW8) . ' = ' . ($fullBundles * _rz($fsBundleW8)) }}" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label text-size">Bundles * loose Weight = loose Weight</label>
                                        <input type="text" class="form-control" value="{{' 1' . ' * ' . $remainingWeight . ' = ' . ($remainingWeight * 1) }}" disabled>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mt-3">
                                        <div class="fw-semibold border-bottom pb-1 text-danger"> <b> Raw Material </b></div>
                                        <div class="row">
                                            <div class="col-md-6 mt-3">
                                                <label class="col-form-label" for="hhbRmSize">RM Size (Purchase)</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control pe-none bg-body-secondary" id="hhbRmSize" name="rm_size" value="{{ old('rm_size', $saleOrderItem->size?->size_name) }}" readonly required>
                                                    <select class="form-select pe-none bg-body-secondary" name="rm_size_unit" aria-label="Raw material size unit" required readonly>
                                                        <option value="MM" @selected(old('rm_size_unit', $saleOrderItem->size_unit) === 'MM')>MM</option>
                                                        <option value="THOU" @selected(old('rm_size_unit', $saleOrderItem->size_unit) === 'THOU')>THOU</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label class="col-form-label" for="hhbRmObservedSize">Observed Size</label>
                                                <div class="input-group">
                                                    <input type="text" step="0.001" min="0" class="form-control" id="hhbRmObservedSize" name="rm_observed_size" value="{{ old('rm_observed_size', $workOrder->rm_observed_size) }}" required>
                                                    <select class="form-select" name="rm_observed_unit" aria-label="Observed raw material size unit" required>
                                                        <option value="MM" @selected(old('rm_observed_unit', $workOrder->rm_observed_unit) === 'MM')>MM</option>
                                                        <option value="THOU" @selected(old('rm_observed_unit', $workOrder->rm_observed_unit) === 'THOU')>THOU</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-3">
                                        <div class="fw-semibold border-bottom pb-1 text-danger"> <b> Finished Material </b></div>
                                        <div class="row">
                                            <div class="col-md-6 mt-3">
                                                <label class="col-form-label" for="hhbFsSize">FM Size (Sale)</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control pe-none bg-body-secondary" id="hhbFsSize" name="fs_size" value="{{ old('fs_size', $saleOrderItem->size?->size_name) }}" readonly required>
                                                    <select class="form-select pe-none bg-body-secondary" name="fs_size_unit" aria-label="Finished material size unit" readonly required>
                                                        <option value="MM" @selected(old('fs_size_unit', $saleOrderItem->size_unit) === 'MM')>MM</option>
                                                        <option value="THOU" @selected(old('fs_size_unit', $saleOrderItem->size_unit) === 'THOU')>THOU</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label class="col-form-label" for="hhbFsObservedSize">Observed Size</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="hhbFsObservedSize" name="fs_observed_size" value="{{ old('fs_observed_size', $workOrder->fs_observed_size) }}" required>
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
                                
                                <hr>
                                <!-- Finish Material Info (HB wire) -->
                                <div class="row bg-light mt-4 p-2">
                                    <div class="col-md-12 mt-3">
                                        <div class="fheading">Finish Material Info</div>
                                        <div class="row">
                                            <div class="col-md-3 mt-3">
                                                <label class="col-form-label text-size">Send RM Qty (Kg.)</label>
                                                <input type="text" class="form-control" value="{{ $workOrder->material_send_for_work ? _rz(($workOrder->material_send_for_work*1000)) : '' }}" disabled>
                                            </div>
                                            <div class="col-md-3 mt-3">
                                                <label class="col-form-label text-size">Non. Reco. Scrap Qty (Kg.)</label>
                                                <input type="text" class="form-control" value="{{ $totalNonRecScrap ? $totalNonRecScrap : '0' }}" disabled>
                                            </div>
                                            <div class="col-md-3 mt-3">
                                                <label class="col-form-label text-size">Misc. Scrap Qty (Kg.)</label>
                                                <input type="text" step="0.001" min="0" max="{{ $remainingWeight }}" name="misc" id="reco_scrap" value="{{ $totalRecScrap ? $totalRecScrap : '0'}}" class="form-control number_only">
                                            </div>
                                            <div class="col-md-3 mt-3">
                                                <label class="col-form-label text-size">Finish Qty (Kg.)</label>
                                                <input type="text" id="finish_qty" class="form-control" name="finish_qty" value="{{ $finishedqty ? $finishedqty  : '0' }}" disabled>
                                            </div>
                                        </div>
                                    </div>
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
                                        <label class="col-form-label" for="sizeFirstDraw">Size First Draw</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="sizeFirstDraw" value="{{ old('fs_observed_size', $workOrder->fs_observed_size) }}" disabled>
                                            <select class="form-select" name="fs_observed_unit" aria-label="Observed finished material size unit" disabled>
                                                <option value="MM" @selected(old('fs_observed_unit', $workOrder->fs_observed_unit) === 'MM')>MM</option>
                                                <option value="THOU" @selected(old('fs_observed_unit', $workOrder->fs_observed_unit) === 'THOU')>THOU</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mt-3">
                                        <label class="col-form-label" for="hhbHardnessSo">No of Bundles</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="hhbHardnessSo" value="{{ old('final_bundle_count', ($workOrder->total_full_bundle_count) + ($workOrder->lose_bundle_count)) }}" disabled>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-5 mt-3">
                                        <label class="col-form-label" for="hhbBatchNo">Batch No.</label>

                                        <div class="row g-0">
                                            <div class="col-6">
                                                <input type="text" class="form-control rounded-end-0" id="hhbBatchNo" name="batch_no" value="{{ old('batch_no', $workOrder->batch_no) }}" maxlength="50" required>
                                            </div>

                                            <div class="col-6">
                                                <button type="button" class="btn btn-secondary w-100 rounded-start-0" id="generateBatchNo">
                                                    Generate
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mt-3">
                                        <label class="col-form-label" for="hhbFsSize">FM Size (Sale)</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="hhbFsSize" name="fs_size" value="{{ old('fs_size', $saleOrderItem->size?->size_name) }}" disabled>
                                            <select class="form-select" name="fs_size_unit" aria-label="Finished material size unit" disabled>
                                                <option value="MM" @selected(old('fs_size_unit', $saleOrderItem->size_unit) === 'MM')>MM</option>
                                                <option value="THOU" @selected(old('fs_size_unit', $saleOrderItem->size_unit) === 'THOU')>THOU</option>
                                            </select>
                                        </div>
                                    </div>                                    
                                    <div class="col-md-3 mt-3">
                                        <label class="col-form-label" for="hhbHardnessSo">Hardness (Sale Order)</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control pe-none bg-body-secondary" id="hhbHardnessSo" name="hardness" value="{{ old('hardness', $saleOrderItem->hardness ?? $workOrder->hardness) }}" readonly>
                                            <select class="form-select pe-none bg-body-secondary" name="hardness_unit" aria-label="Sale order hardness unit" readonly>
                                                <option value="BHN" @selected(old('hardness_unit', $saleOrderItem->hardness_unit ?? '') === 'BHN') readonly>BHN</option>
                                                <option value="HRC" @selected(old('hardness_unit', $saleOrderItem->hardness_unit ?? '') === 'HRC') readonly>HRC</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mt-3">
                                        <label class="col-form-label" for="hhbHardnessObserved">Hardness Observed</label>
                                        <div class="input-group">
                                            <input type="text" step="0.001" min="0" class="form-control number_only" id="hhbHardnessObserved" name="hardness_observed" value="{{ old('hardness_observed', $workOrder->hardness_observed) }}" required>
                                            <select class="form-select" name="hardness_observed_unit" aria-label="Observed hardness unit">
                                                <option value="BHN" @selected(old('hardness_observed_unit', $workOrder->hardness_observed_unit) === 'BHN')>BHN</option>
                                                <option value="HRC" @selected(old('hardness_observed_unit', $workOrder->hardness_observed_unit) === 'HRC')>HRC</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mt-3">
                                        <label class="col-form-label" for="grade">Grade</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="grade" value="{{ $saleOrderItem->grade?->grade_name ?? '-' }}" disabled>
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
                                {{-- <input type="hidden" name="processtype" value="acid_pickling_phosphating"> --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="second_acid_pickling" id="hhbSecondAcidPickling"
                                                value="1" @checked(old('second_acid_pickling', $workOrder->second_acid_pickling ?? '') == '1')>
                                            <label class="form-check-label" for="hhbSecondAcidPickling">Acid Pickling</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="second_phosphating" id="hhbSecondPhosphating"
                                                value="1" @checked(old('second_phosphating', $workOrder->second_phosphating ?? '') == '1')>
                                            <label class="form-check-label" for="hhbSecondPhosphating">Phosphating</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mt-3">
                                        <label class="col-form-label" for="hhbPreviousBatch">Previous Batch No.</label>
                                        <input type="text" class="form-control" id="hhbPreviousBatch" name="batch_no" value="{{ old('batch_no', $workOrder->batch_no) }}" maxlength="50" disabled>
                                    </div>
                                    <div class="col-md-9 mt-3">
                                        <label class="col-form-label" for="hhbSecondAcidRemarks">Remarks</label>
                                        <textarea class="form-control" id="hhbSecondAcidRemarks" name="second_acid_remarks">{{ old('second_acid_remarks', $workOrder->second_acid_remarks) }}</textarea>
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
                                    <div class="col-md-4">
                                        <label class="col-form-label" for="hhbFinalBundleWeight">Finished Bundle Weight (From SO)</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="hhbFinalBundleWeight"
                                                value="{{ $saleOrderItem->fs_bundle_weight ? _rz($saleOrderItem->fs_bundle_weight) : '' }}" disabled>
                                            <span class="input-group-text">{{ $bundleUnit }}</span>
                                        </div>
                                    </div>
                                    @if ($currentOperation === 5)
                                    <div class="col-md-4">
                                        <label class="col-form-label" for="hhbMiscBundleWeight"
                                            title="Whatever weight you enter in 'Misc. Bundle Weight (Kgs)' will be deducted directly from the main bundle weight and added to Misc Scrap.">
                                            Other Misc. Bundle Weight (Kg.)
                                        </label>
                                        <span data-toggle="tooltip" data-placement="top" 
                                            title="Whatever weight you enter in 'Misc. Bundle Weight (Kgs)' will be deducted directly from the main bundle weight and added to Misc Scrap." style="cursor: pointer;">ⓘ
                                        </span>
                                        <input type="text" class="form-control number_only" id="hhbMiscBundleWeight" placeholder="Enter Misc. Bundle weight in Kg EX: 250"
                                            name="misc_bundle_weight" value="{{ old('misc_bundle_weight', $workOrder->misc_bundle_weight) }}">
                                    </div>
                                    @php
                                        $text = "Any weight deducted from 'Misc. Scrap (Kg.)' will be directly adjusted against your Recovered Scrap.";
                                    @endphp
                                    <div class="col-md-4">
                                        <label class="col-form-label" for="hhbManageMiscScrap" 
                                            data-bs-toggle="tooltip"
                                            data-bs-html="true" title="{{ $text }}">
                                            Misc. Scrap (Kg.)
                                        </label>
                                        <span 
                                            data-bs-toggle="tooltip"
                                            data-bs-html="true"
                                            title="{{ $text }}"
                                            style="cursor: pointer;">
                                            ⓘ
                                        </span>
                                        <input type="text" class="form-control number_only" id="hhbManageMiscScrap" placeholder="Enter Adjust Scrap Quantity in Kg EX: 250"
                                            name="manage_misc_scrap" value="{{ old('manage_misc_scrap', ($totalRecScrap ?? 0) - ($adjScrap ?? 0)) }}">
                                    </div>
                                    @else
                                        @if(isset($adjScrap) && $adjScrap)
                                        <div class="col-md-4">
                                            <label class="col-form-label" for="hhbManageMiscScrap" 
                                                data-bs-toggle="tooltip"
                                                data-bs-html="true" title="...">
                                                Adjustable Scrap
                                            </label>
                                            <span 
                                                data-bs-toggle="tooltip"
                                                data-bs-html="true"
                                                title="..."
                                                style="cursor: pointer;">
                                                ⓘ
                                            </span>
                                            <input type="text" class="form-control number_only" id="hhbManageMiscScrap" placeholder="Enter Adjust Scrap Quantity in Kg EX: 250"
                                                name="manage_misc_scrap" value="{{ $adjScrap ?? 0 }}">
                                        </div>
                                        @endif
                                    @endif
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mt-3">
                                        <div class="fw-semibold border-bottom pb-1 text-danger text-danger"> <b> First Draw </b> </div>
                                        <div class="row">
                                            <div class="col-md-6 mt-3">
                                                <label class="col-form-label" for="hhbFirstDrawSize">First Draw Size</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="hhbFsObservedSize" name="fs_observed_size" value="{{ old('fs_observed_size', $workOrder->fs_observed_size) }}" disabled>
                                                    <select class="form-select" name="fs_observed_unit" aria-label="Observed finished material size unit" disabled>
                                                        <option value="MM" @selected(old('fs_observed_unit', $workOrder->fs_observed_unit) === 'MM')>MM</option>
                                                        <option value="THOU" @selected(old('fs_observed_unit', $workOrder->fs_observed_unit) === 'THOU')>THOU</option>
                                                    </select>
                                                </div>
                                            </div>                                        

                                            <div class="col-md-6 mt-3">
                                                <label class="col-form-label" for="hhbFirstDrawObservedSize">Observed Size</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control number_only" id="hhbFirstDrawObservedSize" name="first_draw_observed_size" value="{{ old('fs_observed_size', $workOrder->fs_observed_size) }}" required>
                                                    <select class="form-select" name="fs_observed_unit" aria-label="First draw size unit" required>
                                                        <option value="MM" @selected(old('fs_observed_unit', $workOrder->fs_observed_unit) === 'MM')>MM</option>
                                                        <option value="THOU" @selected(old('fs_observed_unit', $workOrder->fs_observed_unit) === 'THOU')>THOU</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-3">
                                        <div class="fw-semibold border-bottom pb-1 text-danger"> <b> Finished Material </b></div>
                                        <div class="row">
                                            <div class="col-md-6 mt-3">
                                                <label class="col-form-label" for="hhbFsSize">FM Size (Sale)</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="hhbFsSize" name="fs_size" value="{{ old('fs_size', $saleOrderItem->size?->size_name) }}" disabled>
                                                    <select class="form-select" name="fs_size_unit" aria-label="Finished material size unit" disabled>
                                                        <option value="MM" @selected(old('fs_size_unit', $saleOrderItem->size_unit) === 'MM')>MM</option>
                                                        <option value="THOU" @selected(old('fs_size_unit', $saleOrderItem->size_unit) === 'THOU')>THOU</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label class="col-form-label" for="hhbFinalObservedSize">Observed Size</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="hhbFinalObservedSize"
                                                        name="final_observed_size"
                                                        value="{{ old('final_observed_size', $workOrder->final_fs_observed_size) }}" required>
                                                    <select class="form-select" name="final_observed_unit" aria-label="Final size unit" required>
                                                        <option value="MM" @selected(old('final_observed_unit', $workOrder->final_fs_observed_unit) === 'MM')>MM</option>
                                                        <option value="THOU" @selected(old('final_observed_unit', $workOrder->final_fs_observed_unit) === 'THOU')>THOU</option>
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
                                    <div class="fw-semibold border-bottom pb-1 text-danger">
                                        <b>Finish Material Info</b>
                                    </div>

                                    <div class="row flex-nowrap overflow-auto">
                                        <div class="col mt-3">
                                            <label class="col-form-label text-nowrap">Send RM Qty (Kg.)</label>
                                            <input type="text" class="form-control" value="{{ _rz($materialSentKg) }}" disabled>
                                        </div>

                                        <div class="col mt-3">
                                            <label class="col-form-label text-nowrap">Non-Recoverable Scrap (Kg.)</label>
                                            <input type="text" class="form-control" value="{{ _rz($nonRecoverableScrap) }}" disabled>
                                        </div>

                                        <div class="col mt-3">
                                            <label class="col-form-label text-nowrap">Recoverable Scrap (Kg.)</label>
                                            <input type="text" class="form-control" value="{{ _rz($recoverableScrap) }}" disabled>
                                        </div>

                                        <div class="col mt-3">
                                            <label class="col-form-label text-nowrap">Finish Qty (Kg.)</label>
                                            <input type="text" class="form-control" value="{{ _rz($finishedMaterialKg + ($adjScrap ?? 0)) }}" disabled>
                                        </div>

                                        <div class="col mt-3">
                                            <label class="col-form-label text-nowrap">Full Bundle Qty</label>
                                            <input type="text" class="form-control" value="{{ (int) ($workOrder->total_full_bundle_count ?? 0) }}" disabled>
                                        </div>

                                        <div class="col mt-3">
                                            <label class="col-form-label text-nowrap">Loose Bundle Weight ({{ $bundleUnit }})</label>
                                            <input type="text" class="form-control" value="{{ _rz($workOrder->lose_bundle_weight ?? 0) }}" disabled>
                                        </div>

                                        <div class="col mt-3">
                                            <label class="col-form-label text-nowrap">Loose Bundle Qty</label>
                                            <input type="text" class="form-control" value="{{ (int) ($workOrder->lose_bundle_count ?? 0) }}" disabled>
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

    let totalFinish = {{ $finishedMaterial ?? 0 }};
    let maxMisc = {{ $remainingWeight ?? 0 }};

    document.getElementById('reco_scrap').addEventListener('input', function () {
        let misc = parseFloat(this.value) || 0;
        misc = parseFloat(misc.toFixed(3));

        if (misc > maxMisc) misc = maxMisc;
        if (misc < 0) misc = 0;

        this.value = misc;

        document.getElementById('finish_qty').value = Math.max(0, totalFinish - misc).toFixed(3);
    });

    // generate batch num
    document.getElementById('generateBatchNo').addEventListener('click', function () {
        const input = document.getElementById('hhbBatchNo');
        const appPrefix = 'TTC';

        // Generate a unique-looking batch number
        const uniqueNumber = Date.now().toString(36).toUpperCase();
        input.value = `${appPrefix}-${uniqueNumber}`;
    });

    // Operation 5 scrap adjust
    document.addEventListener('DOMContentLoaded', function () {
        const miscBundleWeight = document.getElementById('hhbMiscBundleWeight');
        const manageMiscScrap = document.getElementById('hhbManageMiscScrap');

        if (!miscBundleWeight || !manageMiscScrap) return;

        const initialBundleWeight = miscBundleWeight.value;
        const initialMiscScrap = manageMiscScrap.value;

        function toggleMiscFields() {
            const bundleChanged = miscBundleWeight.value !== initialBundleWeight;
            const scrapChanged = manageMiscScrap.value !== initialMiscScrap;

            if (bundleChanged && !scrapChanged) {
                manageMiscScrap.disabled = true;
                miscBundleWeight.disabled = false;
            } else if (scrapChanged && !bundleChanged) {
                miscBundleWeight.disabled = true;
                manageMiscScrap.disabled = false;
            } else {
                miscBundleWeight.disabled = false;
                manageMiscScrap.disabled = false;
            }
        }

        miscBundleWeight.addEventListener('input', toggleMiscFields);
        manageMiscScrap.addEventListener('input', toggleMiscFields);
    });
</script>
@endsection
