@php
    $editing = isset($saleOrder);
    $itemRows = old('items');
    if ($itemRows === null && $editing) {
        $itemRows = $saleOrder->items->map(fn ($item) => $item->toArray())->all();
    }
    if (empty($itemRows)) {
        $itemRows = [[]];
    }
    $categories = $products->pluck('product_category')->filter()->unique()->sort()->values();
    $productTypeOptions = [1 => 'brightbar', 2 => 'hb_wire', 3 => 'hhb_wire', 4 => 'annealed_bright_bar', 5 => 'bold_rods'];
    $firstItem = $saleOrder->items->first();
    $selectedProductType = old('product_type', $productTypeOptions[$firstItem?->product_type] ?? $firstItem?->product?->product_category ?? '');
@endphp

<div class="page-header">
    <div class="page-header__inner">
        <div class="page-header__content">
            <div class="page-header__title">
                <h6>{{ $editing ? 'Edit' : 'Add' }} Sale Order</h6>
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
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct the following errors:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ $editing ? route('saleOrders.edit', ['id' => $saleOrder->id]) : route('saleOrders.add') }}" method="POST" id="saleOrderForm">
                @csrf

                <h6 class="fw-bold text-uppercase mb-3">Customer Details</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="customer_id" class="form-label">Customer <span class="text-danger">*</span></label>
                        <select class="form-select" id="customer_id" name="customer_id" required>
                            <option value="">Select Customer</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" @selected((string) old('customer_id', $saleOrder->customer_id ?? '') === (string) $customer->id)>
                                    {{ $customer->company_name }}{{ $customer->cust_code ? ' (' . $customer->cust_code . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="payment_term" class="form-label">Payment Term</label>
                        <input type="text" class="form-control" id="payment_term" name="payment_term" value="{{ old('payment_term', $saleOrder->payment_term ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="customer_po_no" class="form-label">Customer P.O. No.</label>
                        <input type="text" class="form-control" id="customer_po_no" name="customer_po_no" value="{{ old('customer_po_no', $saleOrder->customer_po_no ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="freight_basis" class="form-label">Freight Basis <span class="text-danger">*</span></label>
                        <select class="form-select" id="freight_basis" name="freight_basis" required>
                            <option value="1" @selected((string) old('freight_basis', $saleOrder->freight_basis ?? '') === '1')>EX</option>
                            <option value="2" @selected((string) old('freight_basis', $saleOrder->freight_basis ?? '') === '2')>FOR</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="order_qty" class="form-label">Order Qty</label>
                        <input type="number" min="0" step="0.001" class="form-control" id="order_qty" name="order_qty" value="{{ old('order_qty', $saleOrder->order_qty ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="basic_rate" class="form-label">Basic Rate</label>
                        <input type="number" min="0" step="0.001" class="form-control" id="basic_rate" name="basic_rate" value="{{ old('basic_rate', $saleOrder->basic_rate ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="product_type" class="form-label">Product Category <span class="text-danger">*</span></label>
                        <select class="form-select" id="product_type" name="product_type" required>
                            <option value="">Select Product Type</option>
                            @foreach (['brightbar' => 'Bright Bar', 'hb_wire' => 'HB Wire', 'hhb_wire' => 'HHB Wire', 'annealed_bright_bar' => 'Annealed Bar', 'bold_rods' => 'Bolt Rods'] as $value => $label)
                                <option value="{{ $value }}" @selected($selectedProductType === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="dispatch_date" class="form-label">Dispatch Date</label>
                        <input type="text" class="form-control" id="dispatch_date" name="dispatch_date" value="{{ old('dispatch_date', $saleOrder->dispatch_date ?? '') }}" placeholder="Enter dispatch date">
                    </div>
                    <div class="col-md-8">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="1">{{ old('remarks', $saleOrder->remarks ?? '') }}</textarea>
                    </div>
                </div>

                <hr class="my-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-uppercase mb-0">Sale Order Items</h6>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="addSaleOrderItem">
                        <i class="fa-solid fa-plus me-1"></i> Add More
                    </button>
                </div>

                <div class="itemsListing">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-sm align-middle" style="min-width: 1200px;">
                            <thead class="table-light">
                                <tr>
                                    <th data-field="item_name">Item Name</th>
                                    <th data-field="size">Size (Options / Text)</th>
                                    <th data-field="grade">Grade</th>
                                    <th data-field="qty">Qty</th>
                                    <th data-field="sale_rate">Sale Rate</th>
                                    <th data-field="straightening">Straightening</th>
                                    <th data-field="point_discard">Point Discard</th>
                                    <th data-field="phosphating">Phosphating</th>
                                    <th data-field="hardness">Hardness</th>
                                    <th data-field="length">Length</th>
                                    <th data-field="coating">Coating</th>
                                    <th data-field="fs_bundle_weight">FS Bundle Wt</th>
                                    <th data-field="remarks">Remarks / Length</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="saleOrderItems">
                                @foreach ($itemRows as $index => $item)
                                    @include('admin.saleOrders.item-row', compact('item', 'index', 'products', 'categories', 'grades', 'brands', 'sizes'))
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('saleOrders') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-check me-1"></i> {{ $editing ? 'Update' : 'Add' }} Sale Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<template id="saleOrderItemTemplate">
    @include('admin.saleOrders.item-row', ['item' => [], 'index' => '__INDEX__', 'products' => $products, 'categories' => $categories, 'grades' => $grades, 'brands' => $brands, 'sizes' => $sizes])
</template>

