@extends('layouts.app')

@section('title', ($saleOrderItem->product?->product_name ?? 'HHB Wire') . ' Work Order #' . $workOrder->id)

@section('content')
@php
    $sizeLabel = $saleOrderItem->size?->size_name ?? $saleOrderItem->size_unit ?? '-';
    $bundleUnit = $workOrder->fs_bundle_unit ?: '-';
@endphp
    {{-- pr($saleOrderItem->toArray()); --}}

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
                            <th>SO No - Product - Sizes - Rate - Qty</th>
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
                                <strong>-</strong> {{ $saleOrderItem->product?->product_name }}
                                <strong>-</strong> {{ $sizeLabel }}
                                <strong>-</strong> {{ $saleOrderItem->sale_item_prices ?? '-' }}
                                <strong>-</strong> {{ _rz($saleOrderItem->item_qty) }}
                                {{ $saleOrderItem->qty_type === \App\Models\SaleOrderItem::QTY_KGS ? 'KG' : 'Tons' }}
                            </td>
                            <td>{{ $saleOrderItem->grade?->grade_name ?? '-' }}</td>
                            <td>{{ $workOrder->material_send_for_work ? _rz($workOrder->material_send_for_work) . 'Tons' : '' }}</td>
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
                    <h2 class="accordion-header rounded">
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
                                <input type="hidden" name="oprtion1" value="Operation 1">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-check ps-0">

                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="acid_pickling" id="hbAcid" value="1" @checked(old('acid_pickling') == '1')>
                                                <label class="form-check-label fs-6" for="hbAcid">
                                                    ACID Pickling
                                                </label>
                                            </div>

                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="phosphating" id="hbPhosphating" value="1" @checked(old('phosphating') == '1')>
                                                <label class="form-check-label fs-6" for="hbPhosphating">
                                                    For Phosphating
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-3">
                                        <label class="col-form-label">Remarks</label>
                                        <textarea class="form-control" name="acid_remarks">{{ old('acid_remarks') }}</textarea>
                                    </div>
                                    @if ($currentOperation === 1)
                                        <div class="col-12 text-end mt-3">
                                            <input type="submit" class="btn btn-primary" value="Submit">
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
                    <h2 class="accordion-header rounded">
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
                                <input type="hidden" name="oprtion1" value="Operation 1">
                                <input type="hidden" name="oprtion2" value="Operation 2">
                                <input type="hidden" name="processtype" value="wire_drawing">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="col-form-label">FS Bundle Weight (From SO)</label>
                                        <div class="input-group">
                                        <input type="number" step="0.001" id="fsbndle" class="form-control" name="fsbndle" value="{{ $saleOrderItem->fs_bundle_weight ? _rz($saleOrderItem->fs_bundle_weight) : '' }}" readonly>
                                        <select class="form-select" name="fsbndleUnit" style="max-width:100px;" disabled>
                                            <option value="KG" {{ $saleOrderItem->fs_bundle_weight == 1 ? 'selected' : '' }} >KG</option>
                                            <option value="Tons" {{ $saleOrderItem->fs_bundle_weight == 2 ? 'selected' : '' }} >Tons</option>
                                        </select>
                                        </div>
                                    </div>
                                        {{-- pr($saleOrderItem->toArray()); --}}
                                    @php
                                       $finishedMaterial = !empty($workOrder->material_send_for_work) ? _rz(($workOrder->material_send_for_work * 1000) - ($totalNonRecScrap ?? 0)): '0:00 Kgs';
                                       $fullBundle = $finishedMaterial / $saleOrderItem->fs_bundle_weight;
                                      

                                        $fullBundles = (int) floor($finishedMaterial / $saleOrderItem->fs_bundle_weight);
                                        $remainingWeight = fmod($finishedMaterial, $saleOrderItem->fs_bundle_weight);

                                        $bundleData = [
                                            [
                                                "bundleWeight" => $saleOrderItem->fs_bundle_weight,
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
                                    <div class="col-md-2">
                                        <label class="col-form-label">Bundle</label>
                                        <input type="number" class="form-control" id="pcsfsbndle" name="pcsfsbndle" placeholder="Enter Final bundle Count" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="col-form-label text-wrap text-size">Bundles * Weight = Prop Weight</label>
                                        <input type="text" class="form-control" id="fullBundleCount" value="{{ $fullBundles . ' * ' . _rz($saleOrderItem->fs_bundle_weight) . ' = ' . ($fullBundles * _rz($saleOrderItem->fs_bundle_weight)) }}" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label text-size">Bundles * loose Weight = loose Weight</label>
                                        <input type="text" class="form-control" value="{{' 1' . ' * ' . $remainingWeight . ' = ' . ($remainingWeight * 1) }}" disabled>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mt-3">
                                        <div class="fheading">Raw Material</div>
                                        <div class="row">
                                        <div class="col-md-6 mt-3">
                                            <label class="col-form-label">RM Size (Purchase)</label>
                                            <div class="input-group mb-3">
                                            <input type="text" class="form-control" value="{{ $saleOrderItem->size->size_name }}" name="rmSize" readonly>
                                            <select class="form-select" name="rmSizeUnit" style="max-width:100px;">
                                                <option value="MM">MM</option>
                                                <option value="THOU">THOU</option>
                                            </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mt-3">
                                            <label class="col-form-label">Observed Size</label>
                                            <div class="input-group mb-3">
                                            <input type="text" name="rmobservedSize" class="form-control">
                                            <select class="form-select" name="rmobservedSizeUnit" style="max-width:100px;">
                                                <option value="MM">MM</option>
                                                <option value="THOU">THOU</option>
                                            </select>
                                            </div>
                                        </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-3">
                                        <div class="fheading">Finished Material</div>
                                        <div class="row">
                                        <div class="col-md-6 mt-3">
                                            <label class="col-form-label">FM Size (Sale)</label>
                                            <div class="input-group mb-3">
                                            <input type="text" name="fsSize" value="{{ $saleOrderItem->size->size_name }}" class="form-control" readonly>
                                            <select class="form-select" name="fsSizeUnit" style="max-width:100px;">
                                                <option value="MM">MM</option>
                                                <option value="THOU">THOU</option>
                                            </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mt-3">
                                            <label class="col-form-label">Observed Size</label>
                                            <div class="input-group mb-3">
                                            <input type="text" class="form-control" name="fsobservedSize">
                                            <select class="form-select" name="fsObservedSizeUnit" style="max-width:100px;">
                                                <option value="MM">MM</option>
                                                <option value="THOU">THOU</option>
                                            </select>
                                            </div>
                                        </div>
                                        </div>
                                    </div>

                                    @if ($currentOperation === 2)
                                        <div class="col-12 text-end mt-3">
                                            <input type="submit" class="btn btn-primary" value="Submit">
                                        </div>
                                    @endif
                                </div>
                                <hr>
                                <!-- Finish Material Info (HB wire) -->
                                <div class="row bg-light mt-4 p-2">
                                    <div class="col-md-12 mt-3">
                                        <div class="fheading">Finish Material Info</div>
                                        <div class="row">
                                        <div class="col-md-2 mt-3">
                                            <label class="col-form-label text-size">Send RM Qty (Kg.)</label>
                                            <input type="text" class="form-control" value="{{ $workOrder->material_send_for_work ? _rz(($workOrder->material_send_for_work*1000)) . ' Kgs' : '' }}" disabled>
                                        </div>
                                        <div class="col-md-2 mt-3">
                                            <label class="col-form-label text-size">Non. Reco. Scrap Qty (Kg.)</label>
                                            <input type="text" class="form-control" value="{{ $totalNonRecScrap ? $totalNonRecScrap . ' Kgs' : '0' }}" disabled>
                                        </div>
                                        <div class="col-md-2 mt-3">
                                            <label class="col-form-label text-size">Misc. Scrap Qty (Kg.)</label>
                                            <input type="text" step="0.001" min="0" id="reco_scrap" value="{{ $totalRecScrap ? $totalRecScrap . ' Kgs' : '0'}}" class="form-control number_only">
                                        </div>
                                        <div class="col-md-3 mt-3">
                                            <label class="col-form-label text-size">Finish Qty (Kg.)</label>
                                            <input type="text" id="finish_qty" class="form-control" value="{{ $finishedMaterial ? $finishedMaterial . ' Kgs' : '0' }}" disabled>
                                        </div>
                                        <div class="col-md-3 mt-3">
                                            <label class="col-form-label text-size">Bundle Qty</label>
                                            <input type="text" class="form-control" value="0" disabled>
                                        </div>
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
