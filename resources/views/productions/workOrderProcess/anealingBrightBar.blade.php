@extends('layouts.app')

@section('title', ($saleOrderItem->product?->product_name ?? 'Annealing Bright Bar') . ' Work Order #' . $workOrder->id)

@section('content')
@php
    $sizeLabel = $saleOrderItem->size?->size_name ?? $saleOrderItem->size_unit ?? '-';
    $bundleUnit = match (strtolower((string) $workOrder->fs_bundle_unit)) {
        '1', 'kg' => 'KG',
        '2', 'ton', 'tons' => 'Tons',
        default => '-',
    };
    $materialSentKg = (float) ($workOrder->material_send_for_work ?? 0) * 1000;
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
                    <h4 class="mb-1">{{ $saleOrderItem->product?->product_name ?? 'Annealing Bright Bar' }} Production</h4>
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
        <div class="accordion accordion-flush" id="annealingOperationAccordion">
            @if ($currentOperation >= 1)
                <div class="accordion-item shadow-sm border rounded mt-4">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $currentOperation === 1 ? '' : 'collapsed' }} bg-success text-white"
                            type="button" data-bs-toggle="collapse" data-bs-target="#annealingOperation1"
                            aria-expanded="{{ $currentOperation === 1 ? 'true' : 'false' }}" aria-controls="annealingOperation1">
                            <b>Process Annealing (Operation 1)</b>
                        </button>
                    </h2>
                    <div id="annealingOperation1" class="accordion-collapse collapse {{ $currentOperation === 1 ? 'show' : '' }}"
                        data-bs-parent="#annealingOperationAccordion">
                        <div class="accordion-body">
                            <form method="post" action="{{ route('productions.operation', ['workOrder' => $workOrder->id, 'operation' => 1]) }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-5 mt-3">
                                        <label class="col-form-label" for="annealingObservedHardness">Required Hardness (Sale Order)</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control pe-none bg-body-secondary " id="annealingObservedHardness"
                                                name="hardness" value="{{ old('hardness', $saleOrderItem->hardness) }}" required>
                                            <select class="form-select pe-none bg-body-secondary" name="hardness_unit" aria-label="Observed hardness unit" required>
                                                <option value="BHN" @selected(old('hardness_unit', $saleOrderItem->hardness_unit) === 'BHN')>BHN</option>
                                                <option value="HRC" @selected(old('hardness_unit', $saleOrderItem->hardness_unit) === 'HRC')>HRC</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-5 mt-3">
                                        <label class="col-form-label" for="annealingObservedHardness">Observed Hardness</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control number_only" id="annealingObservedHardness"
                                                name="hardness_observed" value="{{ old('hardness_observed', $workOrder->hardness_observed) }}" required>
                                            <select class="form-select" name="hardness_observed_unit" aria-label="Observed hardness unit" required>
                                                <option value="BHN" @selected(old('hardness_observed_unit', $workOrder->hardness_observed_unit) === 'BHN')>BHN</option>
                                                <option value="HRC" @selected(old('hardness_observed_unit', $workOrder->hardness_observed_unit) === 'HRC')>HRC</option>
                                            </select>
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
            
            @if ($currentOperation >= 2)
                <div class="accordion-item shadow-sm border rounded mt-4">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $currentOperation === 2 ? '' : 'collapsed' }} bg-success text-white"
                            type="button" data-bs-toggle="collapse" data-bs-target="#annealingOperation2"
                            aria-expanded="{{ $currentOperation === 2 ? 'true' : 'false' }}" aria-controls="annealingOperation2">
                            <b>SHOT BLASTING / ACID PICKLING (Operation 2)</b>
                        </button>
                    </h2>
                    <div id="annealingOperation2" class="accordion-collapse collapse {{ $currentOperation === 2 ? 'show' : '' }}"
                        data-bs-parent="#annealingOperationAccordion">
                        <div class="accordion-body">
                            <form method="post" action="{{ route('productions.operation', ['workOrder' => $workOrder->id, 'operation' => 2]) }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="surface_process" id="annealingShotBlasting"
                                                value="shot_blasting" {{ $workOrder->shot_blasting == 1 ? 'checked' : ''}} >
                                            <label class="form-check-label" for="annealingShotBlasting">Shot Blasting</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="surface_process" id="annealingAcidPickling"
                                                value="acid_pickling" {{$workOrder->acid_pickling == 1 ? 'checked' : '' }} >
                                            <label class="form-check-label" for="annealingAcidPickling">Acid Pickling</label>
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
                            type="button" data-bs-toggle="collapse" data-bs-target="#annealingOperation3"
                            aria-expanded="{{ $currentOperation === 3 ? 'true' : 'false' }}" aria-controls="annealingOperation3">
                            <b>POINTING (Operation 3)</b>
                        </button>
                    </h2>
                    <div id="annealingOperation3" class="accordion-collapse collapse {{ $currentOperation === 3 ? 'show' : '' }}"
                        data-bs-parent="#annealingOperationAccordion">
                        <div class="accordion-body">
                            <form method="post" action="{{ route('productions.operation', ['workOrder' => $workOrder->id, 'operation' => 3]) }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="pointing" id="annealingPointingYes"
                                                value="1" @checked(old('pointing', (string) ($workOrder->pointing ?? '')) === '1') required>
                                            <label class="form-check-label" for="annealingPointingYes">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="pointing" id="annealingPointingNo"
                                                value="2" @checked(old('pointing', (string) ($workOrder->pointing ?? '')) === '2') required>
                                            <label class="form-check-label" for="annealingPointingNo">No</label>
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
                            type="button" data-bs-toggle="collapse" data-bs-target="#annealingOperation4"
                            aria-expanded="{{ $currentOperation === 4 ? 'true' : 'false' }}" aria-controls="annealingOperation4">
                            <b>DRAWING (Operation 4)</b>
                        </button>
                    </h2>
                    <div id="annealingOperation4" class="accordion-collapse collapse {{ $currentOperation === 4 ? 'show' : '' }}"
                        data-bs-parent="#annealingOperationAccordion">
                        <div class="accordion-body">
                            <form method="post" action="{{ route('productions.operation', ['workOrder' => $workOrder->id, 'operation' => 4]) }}">
                                @csrf
                                <div class="row">
                                    {{-- <div class="col-md-4 mt-3">
                                        <label class="col-form-label" for="annealingSaleSize">Required Size (Sale Order)</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control pe-none bg-body-secondary" name="fs_size" id="annealingSaleSize" value="{{ $sizeLabel }}" readonly>
                                            <span class="input-group-text">{{ $saleOrderItem->size_unit ?? '-' }}</span>
                                        </div>
                                    </div> --}}
                                    <div class="col-md-5 mt-3">
                                        <label class="col-form-label" for="annealingSaleSize">Required Size (Sale Order)</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control pe-none bg-body-secondary" id="annealingSaleSize"
                                                name="fs_size" value="{{ old('fs_size', $sizeLabel) }}" required>
                                            <select class="form-select pe-none bg-body-secondary" name="fs_size_unit" aria-label="Observed size unit" required>
                                                <option value="MM" @selected(old('fs_size_unit', $saleOrderItem->size_unit) === 'MM')>MM</option>
                                                <option value="THOU" @selected(old('fs_size_unit', $saleOrderItem->size_unit) === 'THOU')>THOU</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-5 mt-3">
                                        <label class="col-form-label" for="annealingObservedSize">Observed Size</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="annealingObservedSize"
                                                name="fs_observed_size" value="{{ old('fs_observed_size', $workOrder->fs_observed_size) }}" required>
                                            <select class="form-select" name="fs_observed_unit" aria-label="Observed size unit" required>
                                                <option value="MM" @selected(old('fs_observed_unit', $workOrder->fs_observed_unit) === 'MM')>MM</option>
                                                <option value="THOU" @selected(old('fs_observed_unit', $workOrder->fs_observed_unit) === 'THOU')>THOU</option>
                                            </select>
                                        </div>
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
                            type="button" data-bs-toggle="collapse" data-bs-target="#annealingOperation5"
                            aria-expanded="{{ $currentOperation === 5 ? 'true' : 'false' }}" aria-controls="annealingOperation5">
                            <b>POINT DISCARD (Operation 5)</b>
                        </button>
                    </h2>
                    <div id="annealingOperation5" class="accordion-collapse collapse {{ $currentOperation === 5 ? 'show' : '' }}"
                        data-bs-parent="#annealingOperationAccordion">
                        <div class="accordion-body">
                            <form method="post" action="{{ route('productions.operation', ['workOrder' => $workOrder->id, 'operation' => 5]) }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="poin_discard" id="annealingPointDiscardYes"
                                                value="1" @checked(old('poin_discard', (string) ($workOrder->poin_discard ?? '')) === '1') required>
                                            <label class="form-check-label" for="annealingPointDiscardYes">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="poin_discard" id="annealingPointDiscardNo"
                                                value="2" @checked(old('poin_discard', (string) ($workOrder->poin_discard ?? '')) === '2') required>
                                            <label class="form-check-label" for="annealingPointDiscardNo">No</label>
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

            @if ($currentOperation >= 6)
                <div class="accordion-item shadow-sm border rounded mt-4">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $currentOperation === 6 ? '' : 'collapsed' }} bg-success text-white"
                            type="button" data-bs-toggle="collapse" data-bs-target="#annealingOperation6"
                            aria-expanded="{{ $currentOperation === 6 ? 'true' : 'false' }}" aria-controls="annealingOperation6">
                            <b>STRAIGHTENING (Operation 6)</b>
                        </button>
                    </h2>
                    <div id="annealingOperation6" class="accordion-collapse collapse {{ $currentOperation === 6 ? 'show' : '' }}"
                        data-bs-parent="#annealingOperationAccordion">
                        <div class="accordion-body">
                            <form method="post" action="{{ route('productions.operation', ['workOrder' => $workOrder->id, 'operation' => 6]) }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="straightening" id="annealingStraighteningYes"
                                                value="1" @checked(old('straightening', (string) ($workOrder->straightening ?? '')) === '1') required>
                                            <label class="form-check-label" for="annealingStraighteningYes">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="straightening" id="annealingStraighteningNo"
                                                value="2" @checked(old('straightening', (string) ($workOrder->straightening ?? '')) === '2') required>
                                            <label class="form-check-label" for="annealingStraighteningNo">No</label>
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

            @if ($currentOperation >= 7)
                <div class="accordion-item shadow-sm border rounded mt-4">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $currentOperation === 7 ? '' : 'collapsed' }} bg-success text-white"
                            type="button" data-bs-toggle="collapse" data-bs-target="#annealingOperation7"
                            aria-expanded="{{ $currentOperation === 7 ? 'true' : 'false' }}" aria-controls="annealingOperation7">
                            <b>Bend Saw Cut (Operation 7)</b>
                        </button>
                    </h2>
                    <div id="annealingOperation7" class="accordion-collapse collapse {{ $currentOperation === 7 ? 'show' : '' }}"
                        data-bs-parent="#annealingOperationAccordion">
                        <div class="accordion-body">
                            <form method="post" action="{{ route('productions.operation', ['workOrder' => $workOrder->id, 'operation' => 7]) }}">
                                @csrf
                                <div class="row">
                                    {{-- <div class="col-md-4 mt-3">
                                        <label class="col-form-label" for="annealingFinalSize">Final Size (Sale Order)</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="annealingFinalSize" value="{{ $sizeLabel }}" readonly>
                                            <span class="input-group-text">{{ $saleOrderItem->size_unit ?? '-' }}</span>
                                        </div>
                                    </div> --}}
                                    <div class="col-md-4 mt-3">
                                        <label class="col-form-label" for="annealingSaleSize">Required Size (Sale Order)</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control pe-none bg-body-secondary" id="annealingSaleSize"
                                                name="fs_size" value="{{ old('fs_size', $sizeLabel) }}" disabled>
                                            <select class="form-select pe-none bg-body-secondary" name="fs_size_unit" aria-label="Observed size unit" disabled>
                                                <option value="MM" @selected(old('fs_size_unit', $saleOrderItem->size_unit) === 'MM')>MM</option>
                                                <option value="THOU" @selected(old('fs_size_unit', $saleOrderItem->size_unit) === 'THOU')>THOU</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mt-3">
                                        <label class="col-form-label" for="annealingObservedSize">Observed Size</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="annealingObservedSize" name="fs_observed_size" value="{{ old('fs_observed_size', $workOrder->fs_observed_size) }}" disabled>
                                            <select class="form-select" name="fs_observed_unit" aria-label="Observed size unit" disabled>
                                                <option value="MM" @selected(old('fs_observed_unit', $workOrder->fs_observed_unit) === 'MM')>MM</option>
                                                <option value="THOU" @selected(old('fs_observed_unit', $workOrder->fs_observed_unit) === 'THOU')>THOU</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mt-3">
                                        <label class="col-form-label" for="annealingBundleWeight">FS Bundle Weight (From SO)</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="annealingBundleWeight"
                                                value="{{ $saleOrderItem->fs_bundle_weight ? _rz($saleOrderItem->fs_bundle_weight) : '' }}" readonly>
                                            <span class="input-group-text">{{ $bundleUnit }}</span>
                                        </div>
                                    </div>
                                    {{-- <div class="col-md-4 mt-3">
                                        <label class="col-form-label" for="annealingRecoScrap">Recoverable Scrap (Kg.)</label>
                                        <input type="number" min="0" step="0.0001" class="form-control" id="annealingRecoScrap"
                                            name="reco_scrap" value="{{ old('reco_scrap', $totalRecScrap ?? 0) }}">
                                    </div>
                                    <div class="col-md-4 mt-3">
                                        <label class="col-form-label">Sent Material (Kg.)</label>
                                        <input type="text" class="form-control" value="{{ _rz($materialSentKg) }}" readonly>
                                    </div> --}}

                                    @if ($currentOperation === 7)
                                        <div class="col-12 text-end mt-3">
                                            <button type="submit" class="btn btn-primary">Complete Operation &amp; Generate Token</button>
                                        </div>
                                    @endif
                                </div>

                                <hr>
                                @php
                                    $totalScrap = ($totalNonRecScrap ?? 0) + ($totalRecScrap ?? 0);
                                    $percentage = 0.75;
                                    $bendSawCutScrap = $workOrder->saleOrderItem->losses;
                                    if(isset($lossWeight) && $lossWeight){
                                        $bendSawCut = 0;
                                        $finishQty = $materialSentKg - $totalScrap;
                                    }else{
                                        $finishQty = $materialSentKg - ($totalScrap + $bendSawCut);
                                        $bendSawCut = ($materialSentKg * $percentage) / 100;
                                    }

                                @endphp
                                <div class="row bg-light mt-4 p-3">
                                    <div class="col-12">
                                        <h6 class="fw-semibold border-bottom pb-2 text-danger"> Finish Material Info </h6>
                                        <div class="row">
                                            <div class="col-md-3 mt-3">
                                            <label class="col-form-label">Sent Material (Kg.)</label>
                                            <input type="text" class="form-control" value="{{ _rz($materialSentKg) }}" disabled>
                                        </div>
                                            <div class="col-md-3 mt-2">
                                                <label class="col-form-label">Non-Recoverable Scrap (Kg.)</label>
                                                <input type="text" class="form-control" value="{{ _rz($totalNonRecScrap ?? 0) }}" disabled>
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label class="col-form-label">Recoverable Scrap (Kg.)</label>
                                                <input type="text" class="form-control" id="recoverable_scrap" name="recoverable_scrap" value="{{ _rz($totalRecScrap + $bendSawCut ?? 0) }}" >
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <label class="col-form-label">Finish Qty (Kg.)</label>
                                                <input type="text" class="form-control pe-none bg-body-secondary" id="finished_material" name="finished_material" value="{{ _rz($finishQty ?? 0) }}" >
                                            </div>
                                            @if(isset($workOrder->total_full_bundle_count) && $workOrder->total_full_bundle_count)
                                            <div class="col-md-4 mt-2">
                                                <label class="col-form-label">Full Bundle Count</label>
                                                <input type="text" class="form-control" value="{{ (int) ($workOrder->total_full_bundle_count ?? 0) }}" disabled>
                                            </div>
                                            <div class="col-md-4 mt-2">
                                                <label class="col-form-label">Loose Bundle Weight ({{ $bundleUnit }})</label>
                                                <input type="text" class="form-control" value="{{ _rz($workOrder->lose_bundle_weight ?? 0) }}" disabled>
                                            </div>
                                            <div class="col-md-4 mt-2">
                                                <label class="col-form-label">Loose Bundle Count</label>
                                                <input type="text" class="form-control" value="{{ (int) ($workOrder->lose_bundle_count ?? 0) }}" disabled>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
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
    document.querySelectorAll('#annealingOperationAccordion form').forEach((form) => {
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

    document.addEventListener('DOMContentLoaded', function () {

    let scrapInput = document.getElementById('recoverable_scrap');
    let finishInput = document.getElementById('finished_material');

    let initialScrap = parseFloat(scrapInput.value) || 0;
    let initialFinish = parseFloat(finishInput.value) || 0;

    let totalQty = initialScrap + initialFinish;

    scrapInput.addEventListener('input', function () {

        let scrap = parseFloat(this.value) || 0;

        // Scrap total quantity se jyada nahi hona chahiye
        if (scrap > totalQty) {
            scrap = totalQty;
            this.value = totalQty;
        }

        if (scrap < 0) {
            scrap = 0;
            this.value = 0;
        }

        // Finish Qty automatically calculate hogi
        let finish = totalQty - scrap;

        finishInput.value = finish.toFixed(3);

    });

});
</script>
@endsection
