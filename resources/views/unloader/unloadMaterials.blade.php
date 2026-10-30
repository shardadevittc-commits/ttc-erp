@extends('layouts.app')
@section('title', 'Unload Raw Material | TTC Robotronics - Steel Industry ERP')
@section('content')
<style>
    .blink-blue {
        background-color: #dc3545 !important;
        color: #fff !important;
        border-color: #dc3545 !important;
        animation: blinkRed 1s infinite;
    }

    @keyframes blinkRed {
        50% {
            opacity: 0.5;
        }
    }
</style>
<div class="page-header">
    <div class="page-header__inner">
        <div class="page-header__content">
            <div class="page-header__title">
                <h6>Manage Unload materials</h6>
            </div>
        </div>

        @if($poItemsReceived && count($poItemsReceived) > 0)
            <div class="page-header__actions">

                @php
                    $item = $poItemsReceived[0];
                    $weight = $item['weight'] ?? [];
                @endphp

                @if(($weight['second_weight'] ?? '') === 'pending')
                    <a href="{{ route('material_weight', ['id' => $item['weight_id'], 'cust_id' => $item['customer_id'], 'vehicle_id' => $item['vehical_id'], 'formlist' => $weight['formlist'],]) }}" class="header-btn header-btn--primary blink-blue"> Add Material Weight </a>
                @endif
            </div>
        @endif

        <div class="page-header__actions">
            <a href="{{ route('unloader.index') }}" class="header-btn header-btn--primary"> Back </a>
        </div>
    </div>
</div>

<div class="content_area">
    <div class="flex-grow-1 container-p-y">
    	<!--!! FLAST MESSAGES !!-->
    	@include('admin.partials.flash_messages')
    	<div class="row">
            <!-- ==== Size Information -->
            <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
				<div class="card">
					<div class="card-header view_header">
			        	<div class="heading">
							<h5 class="mb-0">Vehicle Weigh Record</h5>
						</div>
					</div>
					<div class="card-body p-0">
						<div class="row">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">Token No.</th>
                                        <th scope="col">Party Name</th>
                                        <th scope="col">Vehicle No.</th>
                                        <th scope="col">Entry Type</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {{-- @php
                                    pr($vehicalDetails->toArray()); die;
                                    @endphp --}}
                                        <tr>
                                            <td>{{ $vehicalDetails->rstno }}</td>
                                            <td>{{ $vehicalDetails->customer->company_name }}</td>
                                            <td>{{ $vehicalDetails->truckno }}</td>
                                            <td>{{ $vehicalDetails->formlist }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
					</div>
				</div>
			</div>

            {{-- @php
            pr($poItemsReceived); die;
            @endphp --}}
            @if($poItemsReceived && count($poItemsReceived) > 0)
            <!-- ==== Unloaded Material -->
            <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
				<div class="card">
					<div class="card-header view_header">
			        	<div class="heading">
							<h5 class="mb-0">Unloaded Material</h5>
						</div>
					</div>
					<div class="card-body p-0">
						<div class="row">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">Sizes</th>
                                        <th scope="col">Grade</th>
                                        <th scope="col">Heat No.</th>
                                        <th scope="col">Furnace Heat No.</th>
                                        <th scope="col">Rate</th>
                                        <th scope="col">Order Qty</th>
                                        <th scope="col">Lab Require</th>
                                        <th scope="col">Print Card</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($poItemsReceived as $k => $recItems)

                                        <tr>
                                            <td>
                                                @if(@$recItems->purchaseOrderItem->product->product_name)
                                                    <b>{{ $recItems->purchaseOrderItem->product->product_name }}</b>
                                                    {{ $recItems->size->size_name }}
                                                @else
                                                    ---
                                                @endif
                                            </td>
                                            <td>{{ @$recItems->grade?->grade_name ? $recItems->grade->grade_name : '---' }}</td>
                                            <td>{{ @$recItems->genrated_heat_no ? $recItems->genrated_heat_no : '---' }}</td>
                                            <td>{{ @$recItems->furnance_heat_no ? $recItems->furnance_heat_no : '---' }}</td>
                                            <td>{{ @$recItems->received_price ? $recItems->received_price : '---' }}</td>
                                            <td>{{ @$recItems->received_qty ? $recItems->received_qty : '---' }}</td>
                                            <td>{{ @$recItems->lab_test == 1 ? 'Yes' : 'No' }}</td>
                                            <td></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
					</div>
				</div>
			</div>
            @endif

    		<div class="col-12">
                <div class="card">
                    <h5 class="card-header">Unload Material</h5>
                    <hr class="my-0">

                    <div class="card-body p-2">
                        <form action="{{ route('unloader.unload-materials') }}" method="POST" class="form-validation">
                            {{ csrf_field() }}
                            <div class="table-responsive">
                                <table class="table table-bordered {{-- align-middle --}}" id="materialTable">

                                    <thead style="background-color: #fcebd5;">
                                        <tr>
                                            <th>S. NO.</th>
                                            <th>ITEM NAME<span class="text-danger">*</span></th>
                                            <th>GRADE</th>
                                            <th>PO.RATE</th>
                                            <th>RECD.RATE <span class="text-danger">*</span></th>
                                            <th class="text-success">PENDING QTY ( TONS )</th>
                                            <th>QTY ( TONS )<span class="text-danger">*</span></th>
                                            <th>FURNACE HEAT</th>
                                            <th>ITEM REMARKS</th>
                                            <th>PARENT HEAT</th>
                                            <th>LAB TEST</th>
                                            <th>
                                                <button type="button" class="btn btn-warning btn-sm" onclick="addMaterialRow()"><i class="fa fa-plus"></i> </button>
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody id="materialTableBody">
                                        <tr>
                                            <input type="hidden" name="weight_id[]" value="{{ $vehicalDetails->id  }}">
                                            <input type="hidden" name="vehical_id[]" value="{{ $vehicalDetails->vehicle_id }}">
                                            <input type="hidden" name="customer_id[]" value="{{ $vehicalDetails->cust_id }}">
                                            <input type="hidden" name="size_id[]" class="item-size">


                                            <td class="serial-no">1</td>
                                            <td>
                                                <select name="item_name[]" class="form-select item-select text-nowrap" required>
                                                    <option value="">Select From List</option>
                                                    @foreach($purchaseOrderItems as $k => $item)
                                                        @php
                                                            $product = $item->product->product_name ? $item->product->product_name : '';
                                                            $size = $item->size->size_name ? " - ". $item->size->size_name : '';
                                                            $grade = $item->grade->grade_name ? $item->grade->grade_name  : '' ;
                                                        @endphp
                                                        <option value="{{ $item->id }}"
                                                                data-product="{{ $item->product->product_name ?? '' }}"
                                                                data-size="{{ $item->size_id ?? '' }}"
                                                                data-grade="{{ $item->grade_id ?? '' }}"
                                                                data-pending-qty="{{ $item->qty }}"
                                                                data-rate="{{ $item->rate }}"
                                                        >
                                                        {{ $item->id ." | ". $product.$size ." | ".  $grade ." | ". $item->size->size_name ." | Pending Qty ". $item->qty }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select name="grade[]" class="form-select text-nowrap item-grade">
                                                    <option value="">Select</option>
                                                    @foreach($grades as $grade)
                                                        <option value="{{ $grade->id }}"> {{ $grade->grade_name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="text" name="po_rate[]" class="form-control number_only po-rate" step="0.01" readonly></td>
                                            <td><input type="text" name="recd_rate[]" class="form-control number_only" step="0.01" required></td>
                                            <td><input type="text" name="pending_qty[]" class="form-control number_only pending-qty" step="0.001" readonly></td>
                                            <td><input type="text" name="qty[]" class="form-control number_only" step="0.001" min="0.001" required></td>
                                            <td><input type="text" name="furnace_heat[]" class="form-control"></td>
                                            <td><input type="text" name="item_remarks[]" class="form-control"></td>
                                            <td class="text-center">
                                                <select name="parent_heat[]" class="form-select item-select text-nowrap">
                                                    <option value="2" selected>No</option>
                                                    <option value="1">Yes</option>

                                                </select>
                                            </td>

                                            <td>
                                                <select name="lab_test[]" class="form-select">
                                                    <option value="">Select</option>
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>
                                            </td>
                                            <td><button type="button" class="btn btn-danger btn-sm" onclick="removeMaterialRow(this)"> <i class="fa fa-trash"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="form-group mt-3 clearfix">
                                <button type="submit" class="btn btn-primary float-end"> Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- <style>
                #materialTable {
                    min-width: 1200px;
                    font-size: 13px;
                    margin-bottom: 0;
                }

                #materialTable th {
                    background-color: #fcebd5;
                    color: #111;
                    font-weight: 500;
                    vertical-align: middle;
                    white-space: normal;
                    min-width: 80px;
                }

                #materialTable td {
                    vertical-align: middle;
                    padding: 6px;
                }

                #materialTable .form-control,
                #materialTable .form-select {
                    min-width: 75px;
                    font-size: 12px;
                    border-radius: 0;
                    background-color: #e9ecef;
                }

                #materialTable .form-check-input {
                    width: 20px;
                    height: 20px;
                    margin-right: 5px;
                }
            </style> --}}

            <script>
                function addMaterialRow() {
                    const tbody = document.getElementById('materialTableBody');
                    const firstRow = tbody.querySelector('tr');
                    const newRow = firstRow.cloneNode(true);

                    newRow.querySelectorAll('input').forEach(function (input) {
                        if (input.type === 'checkbox') {
                            input.checked = true;
                        } else if (input.type === 'hidden')
                        if (input.name === 'weight_id[]' || input.name === 'vehical_id[]' || input.name === 'customer_id[]')
                        {
                            // Value change nahi karni

                        }else if (input.name === 'parent_heat[]'){
                            input.value = '2';
                        } else {
                            input.value = '';
                        }
                    });

                    newRow.querySelectorAll('select').forEach(function (select) {
                        select.selectedIndex = 0;
                    });

                    tbody.appendChild(newRow);
                    updateSerialNumbers();
                }

                function removeMaterialRow(button) {
                    const tbody = document.getElementById('materialTableBody');

                    if (tbody.querySelectorAll('tr').length > 1) {
                        button.closest('tr').remove();
                        updateSerialNumbers();
                    } else {
                        alert('At least one material row is required.');
                    }
                }

                function updateSerialNumbers() {
                    document.querySelectorAll(
                        '#materialTableBody .serial-no'
                    ).forEach(function (cell, index) {
                        cell.textContent = index + 1;
                    });
                }
            </script>

    	</div>
    </div>
</div>
@endsection
