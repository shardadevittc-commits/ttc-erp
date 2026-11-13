@extends('layouts.app')

@section('title', ($saleOrderItem->product?->product_name ?? 'Bolt Rods') . ' Work Order #' . $workOrder->id)

@section('content')
@php
    $sizeLabel = $saleOrderItem->size?->size_name ?? $saleOrderItem->size_unit ?? '-';
    $materialSentKg = (float) ($workOrder->material_send_for_work ?? 0) * 1000;

    // Operation 4: 0.75% sirf Bend Saw Cutting par lagta hai. Press Cutting mein user ka Scrap (Kg.) use hota hai.
    $percentage       = 0.75;
    $bendSawCut       = _rz(($materialSentKg * $percentage) / 100);
    $totalNonRecScrap = (float) ($totalNonRecScrap ?? 0);
    $baseRecoScrap    = (float) ($totalRecScrap ?? 0); // DB mein ab tak ka recoverable scrap

    $cuttingType   = old('cutting_type', $workOrder->cutting_type ?: 'press_cutting');
    $op4Done       = $currentOperation > 4;
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

    $finishQty     = max($materialSentKg - $totalNonRecScrap - $recoScrap, 0);
    $totalWeightKg = _rz(
        (float) old('finished_pcs', $workOrder->finished_pcs) * (float) old('per_pcs_weight', $workOrder->per_pcs_weight));
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
                        <b>Point Discard / Bend Saw Cut</b>
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
                                <div class="col-md-4 mt-3">
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
                                <div class="col-md-2 mt-3">
                                    <label class="col-form-label" for="perPcsWt">Per PCS. WT (Kg.)</label>
                                    <input type="text" class="form-control number_only" id="perPcsWt"
                                        name="per_pcs_weight" value="{{ old('per_pcs_weight', _rz($workOrder->per_pcs_weight)) }}" required>
                                </div>
                                <div class="col-md-2 mt-3">
                                    <label class="col-form-label" for="finishPcs">Finished (Pcs)</label>
                                    <input type="text" class="form-control number_only" id="finishPcs"
                                        name="finished_pcs" value="{{ old('finished_pcs', _rz($workOrder->finished_pcs)) }}" required>
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label class="col-form-label" for="total_weight">Total Weight (Kg.)</label>
                                    <input type="number" step="0.001" class="form-control pe-none bg-body-secondary" id="total_weight"
                                        name="total_weight" value="{{ number_format($finishQty, 3, '.', '') }}" readonly>
                                </div>
                                
                                <div class="col-md-2 mt-3">
                                    <input type="hidden" class="form-control {{ $scrapReadonly ? 'bg-body-secondary' : '' }} number_only" id="scrapWt" name="scrap_weight" value="{{ _rz($scrapKg) }}"
                                        @if ($scrapReadonly) readonly @else required @endif>
                                    
                                </div>
                                {{-- <div class="col-12">
                                    <div class="text-danger small mt-1" id="op4Error" role="alert"></div>
                                </div> --}}
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
                                            <input type="number" step="0.001" class="form-control" id="reco_scrap"
                                                name="recoverable_scrap" value="{{ round($recoScrap, 3) }}">
                                        </div>
                                        <div class="col-md-2 mt-2">
                                            <label class="col-form-label" for="finish_qty">Finish Qty (Kg.)</label>
                                            <input type="number" step="0.0001" class="form-control pe-none bg-body-secondary"
                                                id="finish_qty" name="finish_qty" value="{{ round($finishQty, 3) }}" readonly>
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
                                                name="rejected_pcs" value="{{ old('rejected_pcs', $workOrder->rejected_pcs) }}" required>
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
                                                value="{{ old('threading_rejected', $workOrder->threading_rejected ?: $workOrder->rejected_pcs) }}" readonly>
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
                                            <input type="number" class="form-control pe-none bg-body-secondary" id="autoBlackFinish" name="auto_black_finish"
                                                value="{{ old('auto_black_finish', (float) $workOrder->finished_pcs - (float) $workOrder->rejected_pcs) }}" required>
                                            <span class="input-group-text">Pcs</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mt-3">
                                        <label class="col-form-label" for="autoBlackRejected">Rejected</label>
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control pe-none bg-body-secondary" id="autoBlackRejected" name="auto_black_rejected"
                                                value="{{ old('auto_black_rejected', $workOrder->rejected_pcs) }}" required>
                                            <span class="input-group-text">Pcs</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mt-3">
                                        <label class="col-form-label" for="weightPerPcs">Weight/Pcs</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control pe-none bg-body-secondary" id="weightPerPcs" name="weight_per_pcs"
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

            @php
                $availablePcs  = (int) $workOrder->finished_pcs - $workOrder->rejected_pcs;
                $savedLoosePcs = (int) $workOrder->loss_box_pcs;
                $savedHandling = $savedLoosePcs > 0
                    ? ((int) $workOrder->loss_box_count > 0 ? 'extra_box' : 'adjust_last_box')
                    : null;
                $looseHandling = old('loose_handling', $savedHandling);
            @endphp

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
                            <form method="post" id="boltOp8Form" action="{{ route('productions.operation', ['workOrder' => $workOrder->id, 'operation' => 8]) }}" data-available-pcs="{{ $availablePcs }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3 mt-3">
                                        <label class="col-form-label">Finished Pcs (for packing)</label>
                                        <input type="text" class="form-control" value="{{ $availablePcs }}" disabled>
                                    </div>
                                    <div class="col-md-3 mt-3">
                                        <label class="col-form-label" for="noOfBoxes">No of Boxes</label>
                                        <input type="number" min="1" step="1" class="form-control" id="noOfBoxes" name="no_of_boxes"
                                            value="{{ old('no_of_boxes', $workOrder->no_of_boxes) }}" required>
                                    </div>
                                    <div class="col-md-3 mt-3">
                                        <label class="col-form-label" for="pcsPerBox">Pcs Per Box</label>
                                        <input type="number" min="1" step="1" class="form-control" id="pcsPerBox" name="pcs_per_box"
                                            value="{{ old('pcs_per_box', $workOrder->pcs_per_box) }}" required>
                                    </div>
                            
                                    <div class="col-12 mt-3">
                                        <div id="packInfo" class="alert alert-info d-none mb-2" role="status"></div>
                            
                                        <div id="looseOptions" class="d-none border rounded p-3 bg-light">
                                            <div class="fw-semibold mb-2">How do you want to pack the remaining <span id="loosePcsText"></span> pcs?</div>
                            
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="loose_handling" id="looseAdjust"
                                                    value="adjust_last_box" @checked($looseHandling === 'adjust_last_box')>
                                                <label class="form-check-label" for="looseAdjust">
                                                    Adjust in the last box (last box = <span id="lastBoxPcsText"></span> pcs)
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="loose_handling" id="looseExtra"
                                                    value="extra_box" @checked($looseHandling === 'extra_box')>
                                                <label class="form-check-label" for="looseExtra">
                                                    Pack in 1 extra box (<span id="extraBoxPcsText"></span> pcs)
                                                </label>
                                            </div>
                            
                                            <div class="small text-muted mt-2" id="looseNote"></div>
                                        </div>
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
     // Operation 4: Total Weight <-> Per PCS WT <-> Finished Pcs <-> Scrap <-> Recoverable (sab auto, bina error)
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('boltOp4Form');
        if (!form || form.dataset.opDone === '1') return; // Operation 4 ho chuka hai
 
        // Sab calculation milli-units (1 kg = 1000) mein, taaki float ki galti na aaye.
        const toMilli   = (v) => Math.round((parseFloat(v) || 0) * 1000);
        const fromMilli = (m) => (m / 1000).toFixed(3);
 
        const material  = toMilli(form.dataset.materialKg);
        const nonReco   = toMilli(form.dataset.nonRecoKg);
        const baseReco  = toMilli(form.dataset.baseRecoKg);
        const bendSaw   = toMilli(form.dataset.bendSawKg);
        const available = Math.max(material - nonReco - baseReco, 0); // Total = available - scrap
 
        const wInput      = document.getElementById('perPcsWt');
        const nInput      = document.getElementById('finishPcs');
        const totalWt     = document.getElementById('total_weight');
        const scrapInput  = document.getElementById('scrapWt');
        const scrapHint   = document.getElementById('scrapHint');
        const recoInput   = document.getElementById('reco_scrap');
        const finishInput = document.getElementById('finish_qty');
        const adjReco     = document.getElementById('adjustable_reco_scrap');
        const adjPct      = document.getElementById('adjustable_percentage');
        const misc        = document.getElementById('misc');
        const processType = form.querySelector('input[name="processtype"]');
 
        let totalMilli = 0;    // Total Weight (= Finish Qty), milli
        let originalReco = 0;  // kg (manual recoverable logic ke liye)
        let prevReco = 0;      // kg
        let pieceAnchor = null; // 'weight' | 'pcs': in dono mein se jo aakhir mein edit hua
 
        const isBendSaw = () => {
            const checked = form.querySelector('input[name="cutting_type"]:checked');
            return !!checked && checked.value === 'bend_saw_cutting';
        };
        const getWeight = () => toMilli(wInput.value);
        const getPcs    = () => parseInt(nInput.value, 10) || 0;
        const getScrap  = () => (isBendSaw() ? bendSaw : toMilli(scrapInput.value));
 
        function showTotal() {
            totalWt.value     = fromMilli(totalMilli);
            finishInput.value = fromMilli(totalMilli);
        }
 
        // Per PCS WT se Pcs nikalo (Per PCS WT, Total se bada ho to Total tak limit)
        function applyWeight(w) {
            if (w <= 0) { nInput.value = ''; return; }
            if (totalMilli <= 0) { nInput.value = 0; return; }
            if (w > totalMilli) {
                w = totalMilli;
                wInput.value = fromMilli(w);
            }
            nInput.value = Math.floor(totalMilli / w);
        }
 
        // Pcs se Per PCS WT nikalo (Pcs bahut zyada ho to sabse chhote weight 0.001 Kg tak limit)
        function applyPcs(n) {
            if (n <= 0) { wInput.value = ''; return; }
            if (totalMilli <= 0) { wInput.value = '0.000'; return; }
            if (n > totalMilli) {
                n = totalMilli;
                nInput.value = n;
            }
            wInput.value = fromMilli(Math.floor(totalMilli / n));
        }
 
        // Total badalne par (scrap / recoverable) jo aakhir mein edit hua tha use rakh kar doosra adjust
        function resyncPieces() {
            if (pieceAnchor === 'pcs') {
                applyPcs(getPcs());
            } else if (pieceAnchor === 'weight') {
                applyWeight(getWeight());
            }
        }
 
        // Scrap ke hisaab se Total / Recoverable / Finish set karo aur manual adjustments reset
        function setBase(scrapMilli) {
            const scrap = Math.min(Math.max(scrapMilli, 0), available);
 
            totalMilli = available - scrap;
 
            const reco = baseReco + scrap;
            originalReco = reco / 1000;
            prevReco = originalReco;
 
            recoInput.value = fromMilli(reco);
            showTotal();
 
            adjReco.value = '0.000'; adjReco.disabled = false;
            adjPct.value  = '0.00';  adjPct.disabled  = false;
            misc.value    = '0.000'; misc.disabled    = true;
            processType.disabled = true;
 
            return scrap;
        }
 
        // Cutting type badalne par bilkul fresh form
        function resetForm() {
            const bend = isBendSaw();
 
            pieceAnchor = null;
            wInput.value = '';
            nInput.value = '';
 
            scrapInput.readOnly = bend;
            scrapInput.required = !bend;
            scrapInput.classList.toggle('bg-body-secondary', bend);
            //scrapHint.textContent = bend ? 'Bend Saw: auto 0.75% of sent RM' : 'Press Cutting: enter scrap';
 
            const scrap = setBase(bend ? bendSaw : 0);
            scrapInput.value = bend ? fromMilli(scrap) : '';
        }
 
        form.querySelectorAll('input[name="cutting_type"]').forEach((radio) => {
            radio.addEventListener('change', resetForm);
        });
 
        wInput.addEventListener('input', function () {
            pieceAnchor = 'weight';
            applyWeight(getWeight());
        });
 
        nInput.addEventListener('input', function () {
            pieceAnchor = 'pcs';
            applyPcs(getPcs());
        });
 
        scrapInput.addEventListener('input', function () {
            if (isBendSaw()) return; // Bend Saw mein scrap fixed hai
 
            const typed = toMilli(this.value);
            if (typed > available) {
                this.value = fromMilli(available); // available se zyada nahi
            }
 
            setBase(typed);
            resyncPieces();
        });
 
        // Recoverable scrap manually badalne par: farak Total Weight mein add / minus, phir pieces auto adjust
        recoInput.addEventListener('input', function () {
            let current = Math.max(parseFloat(this.value) || 0, 0);
            let diffMilli = Math.round((current - prevReco) * 1000);
            let adjusted = parseFloat(this.value) < 0;
 
            // Recoverable badhane se Total 0 se neeche na jaye
            if (diffMilli > totalMilli) {
                diffMilli = totalMilli;
                current = prevReco + diffMilli / 1000;
                adjusted = true;
            }
 
            if (adjusted) {
                this.value = current.toFixed(3);
            }
 
            const diff = diffMilli / 1000; // kg
 
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
 
                adjReco.value = ((parseFloat(adjReco.value) || 0) + diff).toFixed(3);
                adjPct.value  = originalReco > 0
                    ? (((current - originalReco) / originalReco) * 100).toFixed(2)
                    : '0.00';
            }
 
            totalMilli = Math.max(totalMilli - diffMilli, 0);
            prevReco = current;
            showTotal();
            resyncPieces();
        });
 
        // Page load: jo values pehle se bharin hain unhe rehne do, sirf Total / Recoverable sync karo.
        if (getWeight() > 0) {
            pieceAnchor = 'weight';
        } else if (getPcs() > 0) {
            pieceAnchor = 'pcs';
        }
        scrapInput.required = !isBendSaw();
        setBase(getScrap());
    });

    // Operation 8: No of Boxes <-> Pcs Per Box (auto) + bache hue pcs ka choice
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('boltOp8Form');
        if (!form) return;
    
        const total = parseInt(form.dataset.availablePcs, 10) || 0;
        const $ = (id) => document.getElementById(id);
        const boxesEl = $('noOfBoxes'), perBoxEl = $('pcsPerBox');
        const info = $('packInfo'), options = $('looseOptions'), note = $('looseNote');
        const radios = form.querySelectorAll('input[name="loose_handling"]');
        const num = (el) => parseInt(el.value, 10) || 0;
    
        function render() {
            const boxes = num(boxesEl), perBox = num(perBoxEl), loose = total - boxes * perBox;
            const valid = boxes > 0 && perBox > 0 && loose >= 0;
    
            info.classList.toggle('d-none', !valid);
            options.classList.toggle('d-none', !valid || loose === 0);
            radios.forEach((r) => (r.required = valid && loose > 0));
            if (!valid) return;
    
            info.textContent = loose === 0
                ? `${total} pcs = ${boxes} boxes x ${perBox} pcs/box. Nothing is left over.`
                : `${perBox} pcs/box x ${boxes} boxes = ${boxes * perBox} pcs. ${loose} pcs are left over.`;
            if (loose === 0) return;
    
            $('loosePcsText').textContent = loose;
            $('lastBoxPcsText').textContent = perBox + loose;
            $('extraBoxPcsText').textContent = loose;
    
            const choice = form.querySelector('input[name="loose_handling"]:checked')?.value;
            note.textContent = {
                adjust_last_box: `Note: ${loose} pcs will be adjusted in the last box. Last box will have ${perBox + loose} pcs.`,
                extra_box: `Note: 1 extra box of ${loose} pcs will be added (loose box count: 1, loose box pcs: ${loose}).`,
            }[choice] ?? 'Please choose one option to continue.';
        }
    
        // from: jo field user ne badli, to: jo auto hoti hai (dono taraf ka formula same: total / value)
        function link(from, to) {
            from.addEventListener('input', () => {
                const v = Math.min(num(from), total); // total se zyada nahi
                if (v > 0 && num(from) !== v) from.value = v;
                to.value = v > 0 ? Math.floor(total / v) : '';
                render();
            });
        }
    
        link(boxesEl, perBoxEl); // Boxes -> Pcs/Box
        link(perBoxEl, boxesEl); // Pcs/Box -> Boxes
        radios.forEach((r) => r.addEventListener('change', render));
        render(); // Page load par bhi (saved / old values ke liye)
    });
</script>
@endsection