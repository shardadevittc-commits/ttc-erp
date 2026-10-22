@extends('layouts.app')

@section('title', 'Bright Bar Work Order #' . $workOrder->id)

@section('content')
@php
    $sizeLabel = $saleOrderItem->size?->size_name ?? $saleOrderItem->size_unit ?? '-';
    $bundleUnit = $workOrder->fs_bundle_unit ?: '-';
    $operations = [
        1 => 'Shot Blasting / Acid Pickling',
        2 => 'Pointing',
        3 => 'Drawing',
        4 => 'Point Discard',
        5 => 'Straightening',
        6 => 'Bend Saw Cut (Final Stage)',
    ];
@endphp

<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h4 class="mb-1">{{ $saleOrderItem->product?->product_name ?? 'Bright Bar' }} Production</h4>
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
                        <th>SO No - Product - Sizes - Rate - Qty</th>
                        <th>Grade</th>
                        <th>Pending Qty. (Tons)</th>
                        <th class="no-print">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <td>1</td>
                        <td>{{$workOrder->id}} / <strong>{{ _dt($workOrder->created_at) }}</strong></td>
                        <td>{{ $saleOrderItem->saleOrder?->customer?->company_name ?? '-' }}</td>
                        <td><strong class="text-danger">{{ $saleOrderItem->saleOrder?->id ?? '-' }}</strong> <strong>-</strong> {{ $saleOrderItem->product?->product_name }} <strong>-</strong> 5.5 <strong>-</strong> {{ $saleOrderItem->sale_item_prices ?? '-' }} <strong>-</strong> {{ _rz($saleOrderItem->item_qty) . ($saleOrderItem->qty_type = 1 ? 'KG' : ($saleOrderItem->qty_type = 2 ? 'Ton' : '')) ?? '-' }}</td>
                        <td>{{ $saleOrderItem->grade?->grade_name ?? '-' }}</td>
                        <td><span id="pendingPrd">{{ isset($workOrder->material_send_for_work) && $workOrder->material_send_for_work ? _rz($workOrder->material_send_for_work) : '' }}</span> Tons</td>
                        <td class="text-success"><strong class="text-capitalize">{{ isset($workOrder->total_operation_perfome) && $workOrder->total_operation_perfome ? $workOrder->total_operation_perfome : ''}}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="accordion accordion-flush" id="accordionFlushExample">
            <!-- Operation 1 -->
            <div class="accordion-item shadow-sm border rounded mt-4">
                <h2 class="accordion-header rounded">
                    <button class="accordion-button bg-success text-white" type="button" data-bs-toggle="collapse" data-bs-target="#work2" aria-expanded="true" aria-controls="work2">
                    <b>Shot Blasting / Acid Pickling (Operation 1)</b>
                    </button>
                </h2>
                <div id="work2" class="accordion-collapse collapse show" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body">
                    <form method="post">
                        <input type="hidden" name="workStatus" value="operation1">
                        <input type="hidden" name="doAction" value="Addoprt">
                        <input type="hidden" name="oprtion1" value="Operation 1">
                        <input type="hidden" name="processtype" value="shotblastingAcidPickling">
                        <div class="row">
                        <div class="col-md-12">
                            <div class="form-check ps-0">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="acid" id="acid1" value="optionacid" checked>
                                <label class="form-check-label fs-6" for="acid1">Shot Blasting</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="acid" id="phos2" value="optionphos">
                                <label class="form-check-label fs-6" for="phos2">Acid Pickling</label>
                            </div>
                            </div>
                        </div>
                        <div class="col-12 text-end">
                            <input type="submit" class="btn btn-primary">
                        </div>
                        </div>
                    </form>
                    </div>
                </div>
            </div>

            <!-- Operation 2 -->
            <div class="accordion-item shadow-sm border rounded mt-4">
                <h2 class="accordion-header rounded">
                    <button class="accordion-button collapsed bg-success text-white" type="button" data-bs-toggle="collapse" data-bs-target="#work3" aria-expanded="false" aria-controls="work3">
                    <b>Pointing (Operation 2)</b>
                    </button>
                </h2>
                <div id="work3" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body">
                    <form method="post">
                        <input type="hidden" name="workStatus" value="operation2">
                        <input type="hidden" name="doAction" value="Addoprt">
                        <input type="hidden" name="oprtion1" value="Operation 1">
                        <input type="hidden" name="oprtion2" value="Operation 2">
                        <input type="hidden" name="processtype" value="pointing">
                        <div class="row">
                        <div class="col-md-8">
                            <div class="form-check ps-0 mt-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="pointyN" id="pointy" value="pointingOpY" checked>
                                <label class="form-check-label fs-6" for="pointy">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="pointyN" id="pointn" value="pointingOpN">
                                <label class="form-check-label fs-6" for="pointn">No</label>
                            </div>
                            </div>
                        </div>
                        <div class="col-12 text-end">
                            <input type="submit" class="btn btn-primary">
                        </div>
                        </div>
                    </form>
                    </div>
                </div>
            </div>

            <!-- Operation 3 -->
            <div class="accordion-item shadow-sm border rounded mt-4">
                <h2 class="accordion-header rounded">
                    <button class="accordion-button collapsed bg-success text-white" type="button" data-bs-toggle="collapse" data-bs-target="#work4" aria-expanded="false" aria-controls="work4">
                    <b>Drawing (Operation 3)</b>
                    </button>
                </h2>
                <div id="work4" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body">
                    <form method="post">
                        <input type="hidden" name="workStatus" value="operation3">
                        <input type="hidden" name="doAction" value="Addoprt">
                        <input type="hidden" name="oprtion1" value="Operation 1">
                        <input type="hidden" name="oprtion2" value="Operation 2">
                        <input type="hidden" name="oprtion3" value="Operation 3">
                        <div class="row">
                        <div class="col-md-6 mt-2">
                            <div class="row">
                            <div class="col-md-6 mt-3">
                                <label class="col-form-label">Required Size</label>
                                <div class="input-group mb-3">
                                <input type="text" name="fsSize" class="form-control" value="10" readonly>
                                <select class="form-select" name="fsSizeUnit" style="max-width:100px;">
                                    <option value="MM" selected>MM</option>
                                    <option value="THOU">THOU</option>
                                </select>
                                </div>
                            </div>
                            <div class="col-md-6 mt-3">
                                <label class="col-form-label">Observed Size</label>
                                <div class="input-group mb-3">
                                <input type="text" class="form-control" name="fsobservedSize" value="">
                                <select class="form-select" name="fsObservedSizeUnit" style="max-width:100px;">
                                    <option value="MM">MM</option>
                                    <option value="THOU">THOU</option>
                                </select>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="col-12 text-end">
                            <input type="submit" class="btn btn-primary">
                        </div>
                        </div>
                    </form>
                    </div>
                </div>
            </div>

            <!-- Point Discard (Operation 4) -->
            <div class="accordion-item shadow-sm border rounded mt-4">
                <h2 class="accordion-header rounded">
                    <button class="accordion-button collapsed bg-success text-white" type="button" data-bs-toggle="collapse" data-bs-target="#work5" aria-expanded="false" aria-controls="work5">
                    <b>Point Discard</b>
                    </button>
                </h2>
                <div id="work5" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body">
                    <form method="post">
                        <input type="hidden" name="workStatus" value="operation4">
                        <input type="hidden" name="doAction" value="Addoprt">
                        <input type="hidden" name="oprtion1" value="Operation 1">
                        <input type="hidden" name="oprtion2" value="Operation 2">
                        <input type="hidden" name="oprtion3" value="Operation 3">
                        <input type="hidden" name="oprtion4" value="Operation 4">
                        <input type="hidden" name="processtype" value="pointing_discard">
                        <div class="row">
                        <div class="col-md-8">
                            <div class="form-check ps-0 mt-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="pointdyN" id="pointdy" value="pointdy">
                                <label class="form-check-label fs-6" for="pointdy">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="pointdyN" id="pointdn" value="pointdn" checked>
                                <label class="form-check-label fs-6" for="pointdn">No</label>
                            </div>
                            </div>
                        </div>
                        <div class="col-12 text-end">
                            <input type="submit" class="btn btn-primary">
                        </div>
                        </div>
                    </form>
                    </div>
                </div>
            </div>

            <!-- Straightening (Operation 5) -->
            <div class="accordion-item shadow-sm border rounded mt-4">
                <h2 class="accordion-header rounded">
                    <button class="accordion-button collapsed bg-success text-white" type="button" data-bs-toggle="collapse" data-bs-target="#work6" aria-expanded="false" aria-controls="work6">
                    <b>Straightening</b>
                    </button>
                </h2>
                <div id="work6" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body">
                    <form method="post">
                        <input type="hidden" name="workStatus" value="operation5">
                        <input type="hidden" name="doAction" value="Addoprt">
                        <input type="hidden" name="oprtion1" value="Operation 1">
                        <input type="hidden" name="oprtion2" value="Operation 2">
                        <input type="hidden" name="oprtion3" value="Operation 3">
                        <input type="hidden" name="oprtion4" value="Operation 4">
                        <input type="hidden" name="oprtion5" value="Operation 5">
                        <div class="row">
                        <div class="col-md-8">
                            <div class="form-check ps-0 mt-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="pointsyN" id="pointsy" value="pointsy">
                                <label class="form-check-label fs-6" for="pointsy">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="pointsyN" id="pointsn" value="pointsn" checked>
                                <label class="form-check-label fs-6" for="pointsn">No</label>
                            </div>
                            </div>
                        </div>
                        <div class="col-12 text-end">
                            <input type="submit" class="btn btn-primary">
                        </div>
                        </div>
                    </form>
                    </div>
                </div>
            </div>

            <!-- Operation 6: Bend Saw Cut -->
            <div class="accordion-item shadow-sm border rounded mt-4">
                <h2 class="accordion-header rounded">
                    <button class="accordion-button collapsed bg-success text-white" type="button" data-bs-toggle="collapse" data-bs-target="#work7" aria-expanded="false" aria-controls="work7">
                    <b>Bend Saw Cut</b>
                    </button>
                </h2>
                <div id="work7" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body">
                    <form method="post">
                        <input type="hidden" name="workStatus" value="operation6">
                        <input type="hidden" name="doAction" value="Addoprt">
                        <input type="hidden" name="oprtion1" value="Operation 1">
                        <input type="hidden" name="oprtion2" value="Operation 2">
                        <input type="hidden" name="oprtion3" value="Operation 3">
                        <input type="hidden" name="oprtion4" value="Operation 4">
                        <input type="hidden" name="oprtion5" value="Operation 5">
                        <input type="hidden" name="oprtion6" value="Operation 6">
                        <input type="hidden" name="processtype" value="bendsawcut">
                        <input type="hidden" name="processtype2" value="misc" disabled>
                        <input type="hidden" class="form-control" name="misc" value="" disabled>
                        <input type="hidden" name="adjustable_reco_scrap" id="adjustable_reco_scrap" value="0">
                        <input type="hidden" name="adjustable_percentage" id="adjustable_percentage" value="0">
                        <input type="hidden" name="bundleCount" value="0">

                        <div class="row">
                        <div class="col-md-12 mt-2">
                            <div class="row">
                            <div class="col-md-3 mt-3">
                                <label class="col-form-label">Required Size</label>
                                <div class="input-group mb-3">
                                <input type="text" name="fsSize" class="form-control" value="10" readonly>
                                <select class="form-select" name="fsSizeUnit" style="max-width:100px;">
                                    <option value="MM" selected>MM</option>
                                    <option value="THOU">THOU</option>
                                </select>
                                </div>
                            </div>
                            <div class="col-md-3 mt-3">
                                <label class="col-form-label">Observed Size</label>
                                <div class="input-group mb-3">
                                <input type="text" class="form-control" name="fsobservedSize" value="">
                                <select class="form-select" name="fsObservedSizeUnit" style="max-width:100px;">
                                    <option value="MM">MM</option>
                                    <option value="THOU">THOU</option>
                                </select>
                                </div>
                            </div>
                            <div class="col-md-3 mt-3">
                                <label class="col-form-label">FS Bundle Weight (From SO)</label>
                                <div class="input-group">
                                <input type="number" step="0.001" class="form-control" name="fsbndle" value="500" readonly>
                                <select class="form-select" name="fsbndleUnit" style="max-width:100px;">
                                    <option value="KG" selected>KG</option>
                                    <option value="MT">TONS</option>
                                </select>
                                </div>
                            </div>
                            <div class="col-md-3 mt-3">
                                <label class="col-form-label">Pcs Per Bundle</label>
                                <div class="input-group">
                                <input type="number" class="form-control" name="pcsfsbndle" value="">
                                </div>
                            </div>
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <div class="fheading"><b>Raw Material</b></div>
                            <div class="row">
                            <div class="col-md-5 mt-3">
                                <label class="col-form-label">RM Size (Purchase)</label>
                                <div class="input-group mb-3">
                                <input type="text" class="form-control" name="rmSize" value="12" readonly>
                                <select class="form-select" name="rmSizeUnit" style="max-width:100px;">
                                    <option value="MM" selected>MM</option>
                                    <option value="THOU">THOU</option>
                                </select>
                                </div>
                            </div>
                            </div>
                        </div>

                        <div class="col-12 text-end">
                            <input type="submit" class="btn btn-primary" value="Complete Operation & Generate Token">
                        </div>
                        </div>

                        <hr>

                        <div class="row bg-light mt-4">
                        <div class="col-md-12 mt-3">
                            <div class="fheading"><b>Finish Material Info</b></div>
                            <div class="row">
                            <div class="col-md-2 mt-3">
                                <label class="col-form-label text-size">Send RM Qty (Kg.)</label>
                                <div class="input-group mb-3">
                                <input type="text" class="form-control" value="1000" disabled>
                                </div>
                            </div>
                            <div class="col-md-2 mt-3">
                                <label class="col-form-label text-size">Non. Reco. Scrap Qty (Kg.)</label>
                                <div class="input-group mb-3">
                                <input type="text" class="form-control" value="0.000" disabled>
                                </div>
                            </div>
                            <div class="col-md-2 mt-3">
                                <label class="col-form-label text-size">Reco. Scrap Qty (Kg.)</label>
                                <div class="input-group mb-3">
                                <input type="number" step="0.0001" min="0" id="reco_scrap" class="form-control" value="7.5">
                                </div>
                            </div>
                            <div class="col-md-2 mt-3">
                                <label class="col-form-label text-size">Finish Qty (Kg.)</label>
                                <div class="input-group mb-3">
                                <input type="number" id="finish_qty" name="finish_qty" step="0.0001" class="form-control" value="992.5" readonly>
                                </div>
                            </div>
                            <div class="col-md-2 mt-3">
                                <label class="col-form-label text-size">Bundle Qty</label>
                                <div class="input-group mb-3">
                                <input type="text" class="form-control" value="00" disabled>
                                </div>
                            </div>
                            <div class="col-md-2 mt-3">
                                <label class="col-form-label text-size">Total PCS</label>
                                <div class="input-group mb-3">
                                <input type="text" class="form-control" value="00" disabled>
                                </div>
                            </div>
                            </div>
                        </div>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($currentOperation === 6)
<script>
    $(document).ready(function () {
        // Original / baseline values
        const originalReco = parseFloat($("#reco_scrap").val()) || 0;
        const originalFinishQty = parseFloat($("#finish_qty").val()) || 0;

        $("#reco_scrap").on("keyup input change", function () {

            $('input[name="misc"]').val("").prop("disabled", true);
            $('input[name="processtype2"]').prop("disabled", true);
            let currentReco = parseFloat($(this).val());

            // Invalid / negative value
            if (isNaN(currentReco) || currentReco < 0) {
                currentReco = 0;
                $(this).val("0.0000");
            }

            // Finish Qty hamesha original values ke hisab se calculate hoti hai
            let newFinishQty = originalFinishQty + originalReco - currentReco;

            // Finish Qty negative nahi honi chahiye
            if (newFinishQty < 0) {
                newFinishQty = 0;
                currentReco = originalFinishQty + originalReco;
                $(this).val(currentReco.toFixed(3));
            }

            // CASE 1: Reco Scrap original se zyada -> extra misc mein jayega
            if (currentReco > originalReco) {
                $('input[name="misc"]').val("").prop("disabled", false);
                $('input[name="processtype2"]').prop("disabled", false);

                let miscValue = currentReco - originalReco;
                $('input[name="misc"]').val(miscValue.toFixed(3));

                $('input[name="adjustable_reco_scrap"]').val("").prop("disabled", true);
                $('input[name="adjustable_percentage"]').prop("disabled", true);
                $("#adjustable_reco_scrap").val("");
                $("#adjustable_percentage").val("");
            }

            // CASE 2: Reco Scrap original ke barabar
            else if (currentReco === originalReco) {
                $('input[name="misc"]').val("").prop("disabled", true);
                $('input[name="processtype2"]').prop("disabled", true);
                $('input[name="adjustable_reco_scrap"]').val("").prop("disabled", true);
                $('input[name="adjustable_percentage"]').prop("disabled", true);
                $("#adjustable_reco_scrap").val("");
                $("#adjustable_percentage").val("");
            }

            // CASE 3: Reco Scrap original se kam -> adjustment
            else {
                let adjustableReco = currentReco - originalReco;
                let percentage = 0;
                if (originalReco > 0) {
                    percentage = (adjustableReco / originalReco) * 100;
                }

                $('input[name="misc"]').val("").prop("disabled", true);
                $('input[name="processtype2"]').prop("disabled", true);
                $('input[name="adjustable_reco_scrap"]').prop("disabled", false);
                $('input[name="adjustable_percentage"]').prop("disabled", false);
                $("#adjustable_reco_scrap").val(adjustableReco.toFixed(3));
                $("#adjustable_percentage").val(percentage.toFixed(3));
            }

            $("#finish_qty").val(newFinishQty.toFixed(3));
        });
    });
</script>
@endif
@endsection