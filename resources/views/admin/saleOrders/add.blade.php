@extends('layouts.app')

@section('content')

<div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1" style="font-size: 0.8rem;">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('saleOrders') }}" class="text-decoration-none text-muted">Sale Orders</a></li>
            <li class="breadcrumb-item active text-danger fw-semibold" aria-current="page">{{ isset($saleOrder) ? 'Edit' : 'Add' }} Sale Order</li>
        </ol>
    </nav>
</div>

<div class="page-header">
    <div class="page-header__inner">
        <div class="page-header__content">
            <div class="page-header__title">
                <h6>{{ isset($saleOrder) ? 'Edit' : 'Add' }} Sale Order</h6>
            </div>
        </div>

        <div class="page-header__actions">
            <a href="{{ route('saleOrders') }}" class="btn btn-outline-secondary px-3 py-2 fw-semibold rounded-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>
</div>

<div class="container-fluid">

    @if(session('error'))
        <div class="alert alert-danger"><strong>Oh snap!</strong> {{ session('error') }}</div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">

            <form action="#" method="POST" id="saleOrderForm">
                @csrf
                <h6 class="fw-bold text-uppercase mb-3">Customer Details</h6>
                <div class="row">
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12">
                        <div class="form-group">
                            <label for="customer" class="form-label">Customer Name <span class="text-danger">*</span></label>
                            {{-- <a href="{{ route('customers.add') }}" class="btn btn-outline-primary btn-sm float-end" target="_blank">Add Customer</a> --}}
                            <select class="select2 form-select" id="customer" name="customer" data-placeholder="Select Customer" data-minimum-results-for-search="-1">
                                <option value=""></option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->cust_id }}" @if(old('customer', $saleOrder->cid ?? '') == $customer->cust_id) {{ 'selected' }} @endif>{{ $customer->name }} ({{ $customer->p_code }})</option>
                                @endforeach
                            </select>
                            <label id="customer-error" class="error" for="customer">@error('customer') {{ $message }} @enderror</label>
                        </div>
                    </div>

                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="form-group">
                            <label for="payType" class="form-label">Payment Term</label>
                            <input type="text" name="payType" id="payType" class="form-control" value="{{ old('payType', $saleOrder->payType ?? '') }}">
                        </div>
                    </div>

                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="form-group">
                            <label for="cust_po" class="form-label">Customer P.O. No.</label>
                            <input type="text" name="cust_po" id="cust_po" class="form-control" value="{{ old('cust_po', $saleOrder->cust_po ?? '') }}">
                        </div>
                    </div>

                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="form-group">
                            <label class="form-label d-block">Freight Basis</label>
                            <div class="d-flex align-items-center gap-3 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="frtBasis" id="frtBasisEx" value="EX" @checked(old('frtBasis', $saleOrder->frtBasis ?? 'FOR') == 'EX')>
                                    <label class="form-check-label" for="frtBasisEx">EX</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="frtBasis" id="frtBasisFor" value="FOR" @checked(old('frtBasis', $saleOrder->frtBasis ?? 'FOR') == 'FOR')>
                                    <label class="form-check-label" for="frtBasisFor">FOR</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="form-group">
                            <label for="orderqty" class="form-label">Order Qty (Tons)</label>
                            <input type="number" step="0.001" name="orderqty" id="orderqty" class="form-control" value="{{ old('orderqty', $saleOrder->orderqty ?? '') }}">
                        </div>
                    </div>

                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="form-group">
                            <label for="bprice" class="form-label">Basic Rate</label>
                            <input type="number" min="0" step="0.01" name="bprice" id="bprice" class="form-control" value="{{ old('bprice', $saleOrder->bprice ?? '') }}">
                        </div>
                    </div>

                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="form-group">
                            <label for="orderType" class="form-label">Order Type</label>
                            <select class="form-control" name="orderType" id="orderType">
                                <option value="">Select Order Type</option>
                                <option value="NULL" @selected(old('orderType', $saleOrder->orderType ?? 'NULL') == 'NULL')>General Order</option>
                                <option value="1" @selected(old('orderType', $saleOrder->orderType ?? '') == '1')>Special Order</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="form-group">
                            <label for="product_type" class="form-label">Product Category <span class="text-danger">*</span></label>
                            <select name="product_type" id="product_type" class="form-control" required>
                                <option value="">Select Product Type</option>
                                <option value="brightbar" @selected(old('product_type', $saleOrder->product_type ?? '') == 'brightbar')>Bright Bar</option>
                                <option value="hb_wire" @selected(old('product_type', $saleOrder->product_type ?? '') == 'hb_wire')>HB Wire</option>
                                <option value="hhb_wire" @selected(old('product_type', $saleOrder->product_type ?? '') == 'hhb_wire')>HHB Wire</option>
                                <option value="annealed_bright_bar" @selected(old('product_type', $saleOrder->product_type ?? '') == 'annealed_bright_bar')>Annealed Bar</option>
                                <option value="bold_rods" @selected(old('product_type', $saleOrder->product_type ?? '') == 'bold_rods')>Bolt Rods</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                        <label for="remarks" class="form-label">Dispatch Date / Remarks</label>
                        <input type="text" name="remarks" id="remarks" class="form-control" value="{{ old('remarks', $saleOrder->remarks ?? '') }}">
                    </div>

                </div>

                <hr class="my-4">

                <h6 class="fw-bold text-uppercase mb-3">Inspection</h6>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <tbody>
                            <tr id="row-spectro">
                                <th style="width: 30%;">SPECTRO / XRF</th>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" id="spectroCheck" name="spectroCheck" value="1" class="form-check-input inspection-check" @checked(old('spectroCheck', $saleOrder->spectroCheck ?? 0) == 1)>
                                        <label class="form-check-label" for="spectroCheck"><span class="status-text">Unchecked</span></label>
                                    </div>
                                </td>
                            </tr>
                            <tr id="row-xrf">
                                <th>Mechanical Properties</th>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" id="xrfCheck" name="xrfCheck" value="1" class="form-check-input inspection-check" @checked(old('xrfCheck', $saleOrder->xrfCheck ?? 0) == 1)>
                                        <label class="form-check-label" for="xrfCheck"><span class="status-text">Unchecked</span></label>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr class="my-4">

                <h6 class="fw-bold text-uppercase mb-3">Add Items</h6>

                <div class="table-responsive {{ empty($saleOrder) ? 'd-none' : '' }}" id="itemTableWrapper">
                    <table class="table table-bordered table-med itemtable align-middle">
                        <thead>
                            <tr>
                                <th>S. No.</th>
                                <th>Item Name</th>
                                <th><span class="text-label">Size (Options / Text)</span></th>
                                <th>Grade</th>
                                <th>Qty</th>
                                <th>Sale Rate</th>
                                <th>Length</th>
                                <th>Size Extra /Ton</th>
                                <th>Edge Type</th>
                                <th>Straightening</th>
                                <th>Point Discard</th>
                                <th>Phosphating</th>
                                <th>Hardness</th>
                                <th>Coating</th>
                                <th>FS Bundle Wt</th>
                                <th>Remarks / Length</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @forelse($saleOrderItems ?? [null] as $index => $saleItem)
                                @include('saleOrders.partials.item-row', ['item' => $saleItem, 'index' => $index, 'products' => $products, 'grades' => $grades])
                            @empty
                                @include('saleOrders.partials.item-row', ['item' => null, 'index' => 0, 'products' => $products, 'grades' => $grades])
                            @endforelse --}}
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('saleOrders') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-check me-1"></i> {{ isset($saleOrder) ? 'Update' : 'Add' }} Sale Order
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection
