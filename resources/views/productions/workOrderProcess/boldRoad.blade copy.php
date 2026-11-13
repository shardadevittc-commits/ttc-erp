@extends('layouts.app')

@section('title', ($saleOrderItem->product?->product_name ?? 'Bolt Rods') . ' Work Order #' . $workOrder->id)

@section('content')
@php
    $sizeLabel = $saleOrderItem->size?->size_name ?? $saleOrderItem->size_unit ?? '-';
    $materialSentKg = (float) ($workOrder->material_send_for_work ?? 0) * 1000;
 
    // Operation 4: 0.75% sirf Bend Saw Cutting par lagta hai. Press Cutting mein user ka Scrap (Kg.) use hota hai.
    $percentage       = 0.75;
    $bendSawCut       = round(($materialSentKg * $percentage) / 100, 3);
    $totalNonRecScrap = (float) ($totalNonRecScrap ?? 0);
    $baseRecoScrap    = (float) ($totalRecScrap ?? 0); // DB mein ab tak ka recoverable scrap
 
    $cuttingType  = old('cutting_type', $workOrder->cutting_type ?: 'press_cutting');
    $op4Done      = $currentOperation > 4;
    $scrapReadonly = !$op4Done && $cuttingType === 'bend_saw_cutting';
 
    if ($op4Done) {
        // Operation 4 ho chuka hai: DB ki values hi dikhao (dobara add mat karo).
        $scrapKg   = (float) $workOrder->scrap_weight;
        $recoScrap = $baseRecoScrap;
    } else {
        $scrapKg   = $cuttingType === 'bend_saw_cutting'
            ? $bendSawCut
            : (float) old('scrap_weight', $workOrder->scrap_weight ?? 0);
        $recoScrap = $baseRecoScrap + $scrapKg;
    }
 
    $finishQty = max($materialSentKg - $totalNonRecScrap - $recoScrap, 0);
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
                    <h4 class="mb-1">{{ $saleOrderItem->product?->product_name ?? 'Bolt Rods' }} Production</h4>
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
                            <td>{{ _rz($workOrder->material_send_for_work ?? 0) }}</td>
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
        <div class="accordion accordion-flush" id="boltRodsOperationAccordion">

            {{-- ===================== OPERATION 1 : Shot Blasting / Acid Pickling ===================== --}}
            @if ($currentOperation >= 1)
                <div class="accordion-item shadow-sm border rounded mt-4">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $currentOperation === 1 ? '' : 'collapsed' }} bg-success text-white"
                            type="button" data-bs-toggle="collapse" data-bs-target="#boltOperation1"
                            aria-expanded="{{ $currentOperation === 1 ? 'true' : 'false' }}" aria-controls="boltOperation1">
                            <b>Shot Blasting / Acid Pickling (Operation 1)</b>
                        </button>
                    </h2>
                    <div id="boltOperation1" class="accordion-collapse collapse {{ $currentOperation === 1 ? 'show' : '' }}"
                        data-bs-parent="#boltRodsOperationAccordion">
                        <div class="accordion-body">
                            <form method="post" action="{{ route('productions.operation', ['workOrder' => $workOrder->id, 'operation' => 1]) }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="surface_process" id="boltShotBlasting"
                                                value="shot_blasting"
                                                {{ ($workOrder->shot_blasting == 1 || (!$workOrder->shot_blasting && !$workOrder->acid_pickling)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="boltShotBlasting">Shot Blasting</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="surface_process" id="boltAcidPickling"
                                                value="acid_pickling" {{ $workOrder->acid_pickling == 1 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="boltAcidPickling">Acid Pickling</label>
                                        </div>
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

            {{-- ===================== OPERATION 2 : Pointing ===================== --}}
            @if ($currentOperation >= 2)
                <div class="accordion-item shadow-sm border rounded mt-4">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $currentOperation === 2 ? '' : 'collapsed' }} bg-success text-white"
                            type="button" data-bs-toggle="collapse" data-bs-target="#boltOperation2"
                            aria-expanded="{{ $currentOperation === 2 ? 'true' : 'false' }}" aria-controls="boltOperation2">
                            <b>Pointing (Operation 2)</b>
                        </button>
                    </h2>
                    <div id="boltOperation2" class="accordion-collapse collapse {{ $currentOperation === 2 ? 'show' : '' }}"
                        data-bs-parent="#boltRodsOperationAccordion">
                        <div class="accordion-body">
                            <form method="post" action="{{ route('productions.operation', ['workOrder' => $workOrder->id, 'operation' => 2]) }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="pointing" id="boltPointingYes"
                                                value="1" @checked(old('pointing', (string) ($workOrder->pointing ?? '')) === '1') required>
                                            <label class="form-check-label" for="boltPointingYes">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="pointing" id="boltPointingNo"
                                                value="2" @checked(old('pointing', (string) ($workOrder->pointing ?? '')) === '2') required>
                                            <label class="form-check-label" for="boltPointingNo">No</label>
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

            {{-- ===================== OPERATION 3 : Drawing ===================== --}}
            @if ($currentOperation >= 3)
                <div class="accordion-item shadow-sm border rounded mt-4">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $currentOperation === 3 ? '' : 'collapsed' }} bg-success text-white"
                            type="button" data-bs-toggle="collapse" data-bs-target="#boltOperation3"
                            aria-expanded="{{ $currentOperation === 3 ? 'true' : 'false' }}" aria-controls="boltOperation3">
                            <b>Drawing (Operation 3)</b>
                        </button>
                    </h2>
                    <div id="boltOperation3" class="accordion-collapse collapse {{ $currentOperation === 3 ? 'show' : '' }}"
                        data-bs-parent="#boltRodsOperationAccordion">
                        <div class="accordion-body">
                            <form method="post" action="{{ route('productions.operation', ['workOrder' => $workOrder->id, 'operation' => 3]) }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-5 mt-3">
                                        <label class="col-form-label" for="boltSaleSize">Required Size (Sale Order)</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control pe-none bg-body-secondary" id="boltSaleSize"
                                                name="fs_size" value="{{ old('fs_size', $sizeLabel) }}" required>
                                            <select class="form-select pe-none bg-body-secondary" name="fs_size_unit" aria-label="Required size unit" required>
                                                <option value="MM" @selected(old('fs_size_unit', $saleOrderItem->size_unit) === 'MM')>MM</option>
                                                <option value="THOU" @selected(old('fs_size_unit', $saleOrderItem->size_unit) === 'THOU')>THOU</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-5 mt-3">
                                        <label class="col-form-label" for="boltObservedSize">Observed Size</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="boltObservedSize"
                                                name="fs_observed_size" value="{{ old('fs_observed_size', $workOrder->fs_observed_size) }}" required>
                                            <select class="form-select" name="fs_observed_unit" aria-label="Observed size unit" required>
                                                <option value="MM" @selected(old('fs_observed_unit', $workOrder->fs_observed_unit) === 'MM')>MM</option>
                                                <option value="THOU" @selected(old('fs_observed_unit', $workOrder->fs_observed_unit) === 'THOU')>THOU</option>
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

            {{-- ===================== OPERATION 4 : Press Cutting / Bend Saw Cutting ===================== --}}
            @if ($currentOperation >= 4)
                <div class="accordion-item shadow-sm border rounded mt-4">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $currentOperation === 4 ? '' : 'collapsed' }} bg-success text-white"
                            type="button" data-bs-toggle="collapse" data-bs-target="#boltOperation4"
                            aria-expanded="{{ $currentOperation === 4 ? 'true' : 'false' }}" aria-controls="boltOperation4">
                            <b>Press Cutting / Bend Saw Cutting (Operation 4)</b>
                        </button>
                    </h2>
                    <div id="boltOperation4" class="accordion-collapse collapse {{ $currentOperation === 4 ? 'show' : '' }}"
                        data-bs-parent="#boltRodsOperationAccordion">
                        <div class="accordion-body">
                            <form method="post" id="boltOp4Form"
                                action="{{ route('productions.operation', ['workOrder' => $workOrder->id, 'operation' => 4]) }}"
                                data-op-done="{{ $op4Done ? 1 : 0 }}"
                                data-material-kg="{{ $materialSentKg }}"
                                data-non-reco-kg="{{ $totalNonRecScrap }}"
                                data-base-reco-kg="{{ $baseRecoScrap }}"
                                data-bend-saw-kg="{{ $bendSawCut }}">
                                @csrf
                                <input type="hidden" name="adjustable_reco_scrap" id="adjustable_reco_scrap" value="0">
                                <input type="hidden" name="adjustable_percentage" id="adjustable_percentage" value="0">
                                <input type="hidden" name="processtype" value="misc" disabled>
                                <input type="hidden" name="misc" id="misc" value="" disabled>

                                <div class="row">
                                    <div class="col-md-3 mt-3">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="cutting_type" id="boltPressCutting"
                                                value="press_cutting" @checked($cuttingType === 'press_cutting')>
                                            <label class="form-check-label" for="boltPressCutting">Press Cutting</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="cutting_type" id="boltBendSawCutting"
                                                value="bend_saw_cutting" @checked($cuttingType === 'bend_saw_cutting')>
                                            <label class="form-check-label" for="boltBendSawCutting">Bend Saw Cutting</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mt-3">
                                        <label class="col-form-label number_only" for="perPcsWt">Per PCS. WT (Kg.)</label>
                                        <input type="text" class="form-control" id="perPcsWt"
                                            name="per_pcs_weight" value="{{ old('per_pcs_weight', $workOrder->per_pcs_weight) }}" required>
                                    </div>
                                    <div class="col-md-2 mt-3">
                                        <label class="col-form-label number_only" for="finishPcs">Finished (Pcs)</label>
                                        <input type="text" min="1" class="form-control" id="finishPcs"
                                            name="finished_pcs" value="{{ old('finished_pcs', $workOrder->finished_pcs) }}" required>
                                    </div>
                                    <div class="col-md-2 mt-3">
                                        <label class="col-form-label number_only" for="total_weight">Total Weight (Kg.)</label>
                                        <input type="text" step="0.001" class="form-control" id="total_weight"
                                            name="total_weight" value="{{ old('total_weight') }}">
                                    </div>
                                    <div class="col-md-2 mt-3">
                                        <label class="col-form-label" for="scrapWt">Scrap (Kg.)</label>
                                        <input type="text" class="form-control {{ $scrapReadonly ? 'bg-body-secondary' : '' }} number_only" id="scrapWt" name="scrap_weight" value="{{ number_format($scrapKg, 3, '.', '') }}"
                                            @if ($scrapReadonly) readonly @endif>
                                        <div class="form-text" id="scrapHint">
                                            {{ $scrapReadonly ? 'Bend Saw: auto ' . $percentage . '% of sent RM' : 'Press Cutting: enter scrap' }}
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <div class="row bg-light mt-4 p-3">
                                    <div class="col-12">
                                        <h6 class="fw-semibold border-bottom pb-2 text-danger">Finish Material Info</h6>
                                        <div class="row">
                                            <div class="col-md-2 mt-2">
                                                <label class="col-form-label">Send RM Qty (Kg.)</label>
                                                <input type="text" class="form-control" value="{{ _rz($materialSentKg) }}" disabled>
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label class="col-form-label">Non-Recoverable Scrap (Kg.)</label>
                                                <input type="text" class="form-control" value="{{ _rz($totalNonRecScrap) }}" disabled>
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label class="col-form-label" for="reco_scrap">Recoverable Scrap (Kg.)</label>
                                                <input type="text" class="form-control number_only" id="reco_scrap"
                                                    name="recoverable_scrap" value="{{ _rz($recoScrap) }}">
                                            </div>
                                            <div class="col-md-2 mt-2">
                                                <label class="col-form-label" for="finish_qty">Finish Qty (Kg.)</label>
                                                <input type="number" step="0.0001" class="form-control pe-none bg-body-secondary"
                                                    id="finish_qty" name="finish_qty" value="{{ _rz($finishQty) }}" readonly>
                                            </div>
                                            <div class="col-md-2 mt-2">
                                                <label class="col-form-label">Total PCS</label>
                                                <input type="text" class="form-control" value="{{ $workOrder->finished_pcs ?? '00' }}" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if ($currentOperation === 4)
                                    <div class="col-12 text-end mt-3">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ===================== OPERATION 5 : Marking ===================== --}}
            @if ($currentOperation >= 5)
                <div class="accordion-item shadow-sm border rounded mt-4">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $currentOperation === 5 ? '' : 'collapsed' }} bg-success text-white"
                            type="button" data-bs-toggle="collapse" data-bs-target="#boltOperation5"
                            aria-expanded="{{ $currentOperation === 5 ? 'true' : 'false' }}" aria-controls="boltOperation5">
                            <b>Marking (Operation 5)</b>
                        </button>
                    </h2>
                    <div id="boltOperation5" class="accordion-collapse collapse {{ $currentOperation === 5 ? 'show' : '' }}"
                        data-bs-parent="#boltRodsOperationAccordion">
                        <div class="accordion-body">
                            <form method="post" action="{{ route('productions.operation', ['workOrder' => $workOrder->id, 'operation' => 5]) }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3 mt-3">
                                        <label class="col-form-label" for="markingFinish">Finish</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control pe-none bg-body-secondary" id="markingFinish"
                                                name="marking_finish" value="{{ old('marking_finish', $workOrder->finished_pcs) }}" readonly>
                                            <span class="input-group-text">Pcs</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mt-3">
                                        <label class="col-form-label" for="markingRejected">Rejected</label>
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control" id="markingRejected"
                                                name="marking_rejected" value="{{ old('marking_rejected', $workOrder->marking_rejected) }}" required>
                                            <span class="input-group-text">Pcs</span>
                                        </div>
                                    </div>
                                    @if ($currentOperation === 5)
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

            {{-- ===================== OPERATION 6 : Threading ===================== --}}
            @if ($currentOperation >= 6)
                <div class="accordion-item shadow-sm border rounded mt-4">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $currentOperation === 6 ? '' : 'collapsed' }} bg-success text-white"
                            type="button" data-bs-toggle="collapse" data-bs-target="#boltOperation6"
                            aria-expanded="{{ $currentOperation === 6 ? 'true' : 'false' }}" aria-controls="boltOperation6">
                            <b>Threading (Operation 6)</b>
                        </button>
                    </h2>
                    <div id="boltOperation6" class="accordion-collapse collapse {{ $currentOperation === 6 ? 'show' : '' }}"
                        data-bs-parent="#boltRodsOperationAccordion">
                        <div class="accordion-body">
                            <form method="post" action="{{ route('productions.operation', ['workOrder' => $workOrder->id, 'operation' => 6]) }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3 mt-3">
                                        <label class="col-form-label" for="threadingFinish">Finish</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control pe-none bg-body-secondary" id="threadingFinish"
                                                name="threading_finish"
                                                value="{{ old('threading_finish', $workOrder->threading_finish ?: $workOrder->finished_pcs) }}" readonly>
                                            <span class="input-group-text">Pcs</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mt-3">
                                        <label class="col-form-label" for="threadingRejected">Rejected</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control pe-none bg-body-secondary" id="threadingRejected"
                                                name="threading_rejected"
                                                value="{{ old('threading_rejected', $workOrder->threading_rejected ?: $workOrder->marking_rejected) }}" readonly>
                                            <span class="input-group-text">Pcs</span>
                                        </div>
                                    </div>
                                    @if ($currentOperation === 6)
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

            {{-- ===================== OPERATION 7 : Auto Black ===================== --}}
            @if ($currentOperation >= 7)
                <div class="accordion-item shadow-sm border rounded mt-4">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $currentOperation === 7 ? '' : 'collapsed' }} bg-success text-white"
                            type="button" data-bs-toggle="collapse" data-bs-target="#boltOperation7"
                            aria-expanded="{{ $currentOperation === 7 ? 'true' : 'false' }}" aria-controls="boltOperation7">
                            <b>Auto Black (Operation 7)</b>
                        </button>
                    </h2>
                    <div id="boltOperation7" class="accordion-collapse collapse {{ $currentOperation === 7 ? 'show' : '' }}"
                        data-bs-parent="#boltRodsOperationAccordion">
                        <div class="accordion-body">
                            <form method="post" action="{{ route('productions.operation', ['workOrder' => $workOrder->id, 'operation' => 7]) }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3 mt-3">
                                        <label class="col-form-label" for="autoBlackFinish">Finish</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="autoBlackFinish" name="auto_black_finish"
                                                value="{{ old('auto_black_finish', (float) $workOrder->threading_finish - (float) $workOrder->threading_rejected) }}" required>
                                            <span class="input-group-text">Pcs</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mt-3">
                                        <label class="col-form-label" for="autoBlackRejected">Rejected</label>
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control" id="autoBlackRejected" name="auto_black_rejected"
                                                value="{{ old('auto_black_rejected', $workOrder->threading_rejected) }}" required>
                                            <span class="input-group-text">Pcs</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mt-3">
                                        <label class="col-form-label" for="weightPerPcs">Weight/Pcs</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control" id="weightPerPcs" name="weight_per_pcs"
                                                value="{{ old('weight_per_pcs', $workOrder->per_pcs_weight) }}" required>
                                            <span class="input-group-text">Kg</span>
                                        </div>
                                    </div>
                                    @if ($currentOperation === 7)
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

            {{-- ===================== OPERATION 8 : Packing ===================== --}}
            @if ($currentOperation >= 8)
                <div class="accordion-item shadow-sm border rounded mt-4">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $currentOperation === 8 ? '' : 'collapsed' }} bg-success text-white"
                            type="button" data-bs-toggle="collapse" data-bs-target="#boltOperation8"
                            aria-expanded="{{ $currentOperation === 8 ? 'true' : 'false' }}" aria-controls="boltOperation8">
                            <b>Packing (Operation 8)</b>
                        </button>
                    </h2>
                    <div id="boltOperation8" class="accordion-collapse collapse {{ $currentOperation === 8 ? 'show' : '' }}"
                        data-bs-parent="#boltRodsOperationAccordion">
                        <div class="accordion-body">
                            <form method="post" action="{{ route('productions.operation', ['workOrder' => $workOrder->id, 'operation' => 8]) }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3 mt-3">
                                        <label class="col-form-label" for="noOfBoxes">No of Boxes</label>
                                        <input type="number" min="0" class="form-control" id="noOfBoxes" name="no_of_boxes"
                                            value="{{ old('no_of_boxes', $workOrder->no_of_boxes) }}" required>
                                    </div>
                                    <div class="col-md-3 mt-3">
                                        <label class="col-form-label" for="pcsPerBox">Pcs Per Box</label>
                                        <input type="number" min="0" class="form-control" id="pcsPerBox" name="pcs_per_box"
                                            value="{{ old('pcs_per_box', $workOrder->pcs_per_box) }}" required>
                                    </div>
                                    @if ($currentOperation === 8)
                                        <div class="col-12 text-end mt-3">
                                            <button type="submit" class="btn btn-primary">Complete Operation</button>
                                        </div>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

<script>
    // Operation 4: Cutting type -> Scrap -> Recoverable scrap -> Finish qty
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('boltOp4Form');
        if (!form || form.dataset.opDone === '1') return; // Operation 4 ho chuka hai

        const num = (v) => parseFloat(v) || 0;

        const material = num(form.dataset.materialKg);
        const nonReco  = num(form.dataset.nonRecoKg);
        const baseReco = num(form.dataset.baseRecoKg);
        const bendSaw  = num(form.dataset.bendSawKg);

        const scrapInput  = document.getElementById('scrapWt');
        const scrapHint   = document.getElementById('scrapHint');
        const recoInput   = document.getElementById('reco_scrap');
        const finishInput = document.getElementById('finish_qty');
        const totalWt     = document.getElementById('total_weight');
        const adjReco     = document.getElementById('adjustable_reco_scrap');
        const adjPct      = document.getElementById('adjustable_percentage');
        const misc        = document.getElementById('misc');
        const processType = form.querySelector('input[name="processtype"]');

        let originalReco = 0;
        let prevReco = 0;

        const isBendSaw = () => {
            const checked = form.querySelector('input[name="cutting_type"]:checked');
            return !!checked && checked.value === 'bend_saw_cutting';
        };

        // Cutting type ya Scrap badalne par sab dobara calculate aur reset
        function recalc() {
            const bend = isBendSaw();

            // Bend Saw: scrap auto (0.75%), Press: user enter karega
            scrapInput.readOnly = bend;
            scrapInput.classList.toggle('bg-body-secondary', bend);
            scrapHint.textContent = bend
                ? 'Bend Saw: auto 0.75% of sent RM'
                : 'Press Cutting: enter scrap';
            if (bend) {
                scrapInput.value = bendSaw.toFixed(3);
            }

            const scrap = bend ? bendSaw : Math.max(num(scrapInput.value), 0);
            originalReco = baseReco + scrap;
            const originalFinish = Math.max(material - nonReco - originalReco, 0);

            recoInput.value   = originalReco.toFixed(3);
            finishInput.value = originalFinish.toFixed(3);
            totalWt.value     = (bend ? Math.max(originalFinish - originalReco, 0) : originalFinish).toFixed(3);

            adjReco.value = '0.000'; adjReco.disabled = false;
            adjPct.value  = '0.00';  adjPct.disabled  = false;
            misc.value    = '0.000'; misc.disabled    = true;
            processType.disabled = true;

            prevReco = originalReco;
        }

        form.querySelectorAll('input[name="cutting_type"]').forEach((radio) => {
            radio.addEventListener('change', recalc);
        });

        scrapInput.addEventListener('input', function () {
            if (!isBendSaw()) {
                recalc();
            }
        });

        // Recoverable scrap manually badalne par (pehle jaisa)
        recoInput.addEventListener('input', function () {
            let current = num(this.value);
            let finish  = num(finishInput.value);
            let total   = num(totalWt.value);

            // Negative allowed nahi
            if (current < 0) {
                this.value = '0.000';
                finishInput.value = (finish + prevReco).toFixed(3);
                totalWt.value     = (total + prevReco).toFixed(3);
                prevReco = 0;
                return;
            }

            const diff = current - prevReco;

            if (current > originalReco) {
                // MISC mode
                misc.value = (current - originalReco).toFixed(3);
                misc.disabled = false;
                processType.disabled = false;
                adjReco.disabled = true;
                adjPct.disabled  = true;
            } else {
                // ADJUSTABLE mode
                misc.value = '0.000';
                misc.disabled = true;
                processType.disabled = true;
                adjReco.disabled = false;
                adjPct.disabled  = false;

                adjReco.value = (num(adjReco.value) + diff).toFixed(3);
                adjPct.value  = originalReco > 0
                    ? (((current - originalReco) / originalReco) * 100).toFixed(2)
                    : '0.00';
            }

            finishInput.value = Math.max(finish - diff, 0).toFixed(3);
            totalWt.value     = Math.max(total - diff, 0).toFixed(3);

            prevReco = current;
        });

        recalc(); // Page load par bhi sahi values set karo
    });
</script>
@endsection