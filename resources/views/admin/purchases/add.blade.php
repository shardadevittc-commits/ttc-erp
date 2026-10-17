@extends('layouts.app')

@section('title')
    {{ isset($purchaseOrder) ? 'Edit' : 'Add' }} Purchase Order | TTC Robotronics - Steel Industry ERP
@endsection

@section('content')

    @php
        /*
    |--------------------------------------------------------------------------
    | Default values for Add/Edit
    |--------------------------------------------------------------------------
    */

        $isEdit = isset($purchaseOrder);

        $paymentTerm = old('term', $purchaseOrder->term ?? '');

        $paymentParts = preg_split('/\s+/', trim($paymentTerm), 2);

        $defaultPayType = $paymentParts[0] ?? '';
        $defaultPayTypeUnit = $paymentParts[1] ?? '';

        $payType = old('payType', $defaultPayType);
        $payTypeUnit = old('payTypeUnit', $defaultPayTypeUnit);

        $itemRows = old('items');

        if (!is_array($itemRows)) {
            $itemRows = $isEdit
                ? $purchaseOrder->items
                    ->map(function ($item) {
                        return [
                            'item_id' => $item->product_id,
                            'size' => $item->size_id,
                            'size_unit' => $item->size_unit_id,
                            'grade' => $item->grade_id,
                            'length' => $item->length,
                            'rate' => $item->rate,
                            'qty' => $item->qty,
                            'qty_unit' => $item->qty_unit,
                            'qty_variation' => $item->qty_varaiate,
                            'remarks' => $item->remarks,
                        ];
                    })
                    ->toArray()
                : [];
        }

        if (empty($itemRows)) {
            $itemRows = [
                [
                    'item_id' => '',
                    'size' => '',
                    'size_unit' => '',
                    'grade' => '',
                    'length' => 'Standard',
                    'rate' => '',
                    'qty' => '',
                    'qty_unit' => '',
                    'qty_variation' => '±5%',
                    'remarks' => '',
                ],
            ];
        }
    @endphp

    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1" style="font-size: 0.8rem;">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">
                        Dashboard
                    </a>
                </li>

                <li class="breadcrumb-item">
                    <a href="{{ route('purchase') }}" class="text-decoration-none text-muted">
                        Purchase Orders
                    </a>
                </li>

                <li class="breadcrumb-item active text-danger fw-semibold" aria-current="page">
                    {{ $isEdit ? 'Edit' : 'Add' }} Purchase Order
                </li>
            </ol>
        </nav>
    </div>

    <div class="page-header">
        <div class="page-header__inner">
            <div class="page-header__content">
                <div class="page-header__title">
                    <h6>{{ $isEdit ? 'Edit' : 'Add' }} Purchase Order</h6>
                </div>
            </div>

            <div class="page-header__actions">
                <a href="{{ route('purchase') }}" class="btn btn-outline-secondary px-3 py-2 fw-semibold rounded-3">
                    <i class="fa-solid fa-arrow-left me-1"></i>
                    Back
                </a>
            </div>
        </div>
    </div>

    <div class="container-fluid">

        @if (session('error'))
            <div class="alert alert-danger">
                <strong>Error!</strong> {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Please correct the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">

                <form action="{{ route('purchase.add') }}" method="POST" id="purchaseOrderForm">

                    @csrf
                    <input type="hidden" name="purchase_order_id"
                        value="{{ old('purchase_order_id', $purchaseOrder->id ?? '') }}">
                    {{-- If your update route uses PUT/PATCH, uncomment and
                     configure this according to your routes:
                @if ($isEdit)
                    @method('PUT')
                @endif
                --}}

                    {{-- CUSTOMER DETAILS --}}

                    <h6 class="fw-bold text-uppercase mb-3">
                        Customer Details
                    </h6>

                    <div class="row">

                        {{-- Customer --}}
                        <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-12">
                            <div class="form-group mb-3">
                                <label for="customer" class="form-label"> Customer Name</label>
                                <select class="select2 form-select" id="customer" name="customer" data-placeholder="Select Customer" required>
                                    <option value="">Select Customer</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" @selected((string) old('customer', $purchaseOrder->cust_id ?? '') === (string) $customer->id)>
                                            {{ $customer->company_name }}
                                            {{ (isset($customer->cust_code) && $customer->cust_code ? " - (". $customer->cust_code . ")"  : '') }}
                                        </option>
                                    @endforeach

                                </select>

                                @error('customer')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Basic Rate --}}
                        <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-12">
                            <div class="form-group mb-3">
                                <label for="basicRate" class="form-label">
                                    Basic Rate
                                </label>

                                <input type="text" step="0.01" placeholder="Enter Basic Rate" name="basicRate" id="basicRate" class="form-control number_only"
                                    value="{{ old('basicRate', $purchaseOrder->b_rate ?? '') }}">
                            </div>
                        </div>

                        {{-- Reference Sale Order --}}
                        <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-12">
                            <div class="form-group mb-3">
                                <label for="cust_po" class="form-label">
                                    Ref. Sale Order
                                </label>

                                <select name="cust_po" id="cust_po" class="form-select" aria-label=".form-select-sm cust_po">

                                    <option value="">Select Ref. Sale Order</option>

                                    <option value="0" @selected((string) old('cust_po', $purchaseOrder->ref_saleorder_id ?? '') === '0')>
                                        Open Order
                                    </option>

                                    @foreach ($saleOrders as $saleOrder)
                                        <option value="{{ $saleOrder->id }}" @selected((string) old('cust_po', $purchaseOrder->ref_saleorder_id ?? '') === (string) $saleOrder->id)>
                                            {{ ($saleOrder->id ? "(". $saleOrder->id .") " : '') }}
                                            {{  ($saleOrder->created_at ? "(". _dtx($saleOrder->created_at) .") " : '')}} -
                                            {{ ($saleOrder->basic_rate ? "(". _rz($saleOrder->basic_rate) .") " : '') }}
                                            -
                                            {{ ($saleOrder->customer ? "(". optional($saleOrder->customer)->company_name .") " : '') }}
                                        </option>
                                            {{-- ({{ optional($saleOrder->customer)->cust_code }}) - --}}
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Freight Basis --}}
                        <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-12">
                            <div class="form-group mb-3">
                                <label class="form-label d-block">Freight Basis</label>
                                <div class="d-flex align-items-center gap-3 mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="frtBasis" id="frtBasisEx" value="EX" @checked(old('frtBasis', $purchaseOrder->basis ?? 'FOR') === 'EX')>
                                        <label class="form-check-label" for="frtBasisEx">EX</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="frtBasis" id="frtBasisFor" value="FOR" @checked(old('frtBasis', $purchaseOrder->basis ?? 'FOR') === 'FOR')>
                                        <label class="form-check-label" for="frtBasisFor"> FOR </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Payment Term --}}

                        <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-12">
                            <div class="form-group mb-3">
                                <label class="form-label">Payment Term</label>

                                <div class="input-group" style="display: flex; flex-wrap: nowrap;">
                                    <input type="text" name="payType" id="payType" class="form-control number_only" placeholder="Enter value" value="{{ $payType }}" style="width: 50%; flex: 0 0 50%; border-right: 0; border-radius: 6px 0 0 6px;">
                                    <select name="payTypeUnit" id="payTypeUnit" class="form-select" style="width: 50%; flex: 0 0 50%; border-radius: 0 6px 6px 0; cursor: pointer;">
                                        <option value="">Select</option>
                                        @foreach (['Days', 'Week', 'Month'] as $unit)
                                            <option value="{{ $unit }}"
                                                @selected(strcasecmp($payTypeUnit ?? '', $unit) === 0)>
                                                {{ $unit }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Remarks --}}
                        <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-12">
                            <div class="form-group mb-3">
                                <label for="remarks" class="form-label">
                                    Remarks
                                </label>

                                <input type="text" name="remarks" id="remarks" class="form-control"
                                    value="{{ old('remarks', $purchaseOrder->remarks ?? '') }}">
                            </div>
                        </div>

                    </div>

                    <hr class="my-4">

                    {{-- ITEMS --}}

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-bold text-uppercase mb-0">
                            Add Items
                        </h6>
                    </div>

                    <div class="table-responsive" id="itemTableWrapper">
                        <table class="table table-bordered table-med itemtable align-middle mb-0">

                            <thead>
                                <tr>
                                    <th style="min-width:60px;">S. No.</th>
                                    <th style="min-width:180px;">Item Name</th>
                                    <th style="min-width:250px;">Size</th>
                                    <th style="min-width:160px;">Grade</th>
                                    <th style="min-width:130px;">Length</th>
                                    <th style="min-width:130px;">Rate</th>
                                    <th style="min-width:230px;">Qty</th>
                                    <th style="min-width:120px;">Qty Variation</th>
                                    <th style="min-width:220px;">Remarks</th>
                                    <th style="width:60px;" class="text-center">
                                        <button type="button" class="btn btn-primary btn-sm rounded-2"
                                            id="addItemHeaderBtn" title="Add Item">
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </th>
                                </tr>
                            </thead>

                            <tbody id="itemTableBody">

                                @foreach ($itemRows as $index => $itemRow)
                                    <tr class="item-row">

                                        {{-- Serial Number --}}
                                        <td class="serial-no text-center fw-semibold">
                                            {{ $index + 1 }}
                                        </td>

                                        {{-- Item --}}
                                        <td>
                                            <select name="items[{{ $index }}][item_id]"
                                                class="form-select item-name">

                                                <option value="">Select Item</option>

                                                @foreach ($products ?? [] as $item)
                                                    <option value="{{ $item->id }}" @selected((string) ($itemRow['item_id'] ?? '') === (string) $item->id)>
                                                        {{ $item->product_name }}
                                                    </option>
                                                @endforeach

                                            </select>
                                        </td>

                                        {{-- Size + Size Unit --}}
                                        {{-- <td>
                                            <div class="row g-1">
                                                <div class="col-7">
                                                    <select name="items[{{ $index }}][size]" class="form-select item-size">
                                                        <option value="">Select Size</option>
                                                        @foreach ($sizes ?? [] as $size)
                                                            <option value="{{ $size->id }}"
                                                                @selected((string) ($itemRow['size'] ?? '') === (string) $size->id)>
                                                                {{ $size->size_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-5">
                                                    <select name="items[{{ $index }}][size_unit]" class="form-select">
                                                        <option value=""> Select Unit </option>
                                                        @foreach ($units ?? [] as $unit)
                                                            <option value="{{ $unit->id }}"
                                                                @selected((string) ($itemRow['size_unit'] ?? '') === (string) $unit->id)>
                                                                {{ $unit->unit_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </td> --}}
                                        <td>
                                            <div class="d-flex align-items-center border rounded"
                                                style="height: 38px; overflow: hidden;">

                                                <div style="width: 50%;">
                                                    <select name="items[{{ $index }}][size]"class="form-select item-size border-0 rounded-0 shadow-none" style="height: 36px;" data-inline-add="size" data-add-url="{{ route('sizes.add') }}" data-add-field="size_name" data-add-label="Size">
                                                        <option value="">Select Size</option>
                                                        <option value="__add__">+ Add Size</option>
                                                        @foreach ($sizes ?? [] as $size)
                                                            <option value="{{ $size->id }}"
                                                                @selected((string) ($itemRow['size'] ?? '') === (string) $size->id)>
                                                                {{ $size->size_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div style="width: 50%; border-left: 1px solid #dee2e6;">
                                                    <select name="items[{{ $index }}][size_unit]"
                                                            class="form-select border-0 rounded-0 shadow-none"
                                                            style="height: 36px;">
                                                        <option value="">Select Unit</option>
                                                        @foreach ($units ?? [] as $unit)
                                                            <option value="{{ $unit->id }}"
                                                                @selected((string) ($itemRow['size_unit'] ?? '') === (string) $unit->id)>
                                                                {{ $unit->unit_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                            </div>
                                        </td>


                                        {{-- Grade --}}
                                        <td>
                                            <select name="items[{{ $index }}][grade]" class="form-select item-grade" data-inline-add="grade" data-add-url="{{ route('grades.add') }}" data-add-field="grade_name" data-add-label="Grade">

                                                <option value="">Select Grade</option>
                                                <option value="__add__">+ Add Size</option>
                                                @foreach ($grades ?? [] as $grade)
                                                    <option value="{{ $grade->id }}" @selected((string) ($itemRow['grade'] ?? '') === (string) $grade->id)>
                                                        {{ $grade->grade_name }}
                                                    </option>
                                                @endforeach

                                            </select>
                                        </td>

                                        {{-- Length --}}
                                        <td>
                                            <input type="text" name="items[{{ $index }}][length]"
                                                class="form-control" value="{{ $itemRow['length'] ?? 'Standard' }}"
                                                readonly>
                                        </td>

                                        {{-- Rate --}}
                                        <td>
                                            <input type="number" step="0.01" min="0"
                                                name="items[{{ $index }}][rate]" class="form-control"
                                                placeholder="Rate" value="{{ $itemRow['rate'] ?? '' }}">
                                        </td>

                                        {{-- Quantity --}}
                                        <td>
                                            <div class="row g-1">

                                                <div class="col-6">
                                                    <input type="number" step="0.001" min="0"
                                                        name="items[{{ $index }}][qty]" class="form-control"
                                                        placeholder="Qty" value="{{ $itemRow['qty'] ?? '' }}">
                                                </div>

                                                <div class="col-6">
                                                    <select name="items[{{ $index }}][qty_unit]"
                                                        class="form-select">

                                                        <option value="">--Select Unit--</option>

                                                        @foreach (['TONS', 'PCS', 'Litre'] as $qtyUnit)
                                                            <option value="{{ $qtyUnit }}"
                                                                @selected((string) ($itemRow['qty_unit'] ?? '') === $qtyUnit)>
                                                                {{ $qtyUnit }}
                                                            </option>
                                                        @endforeach

                                                    </select>
                                                </div>

                                            </div>
                                        </td>

                                        {{-- Quantity Variation --}}
                                        <td>
                                            <input type="text" name="items[{{ $index }}][qty_variation]"
                                                class="form-control text-center"
                                                value="{{ $itemRow['qty_variation'] ?? '±5%' }}" readonly>
                                        </td>

                                        {{-- Remarks --}}
                                        <td>
                                            <input type="text" name="items[{{ $index }}][remarks]"
                                                class="form-control" placeholder="Remarks"
                                                value="{{ $itemRow['remarks'] ?? '' }}">
                                        </td>

                                        {{-- Delete --}}
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-danger btn-sm delete-item"
                                                title="Delete Item">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>

                    {{-- FORM BUTTONS --}}

                    <div class="mt-4 d-flex justify-content-end gap-2">

                        <a href="{{ route('purchase') }}" class="btn btn-outline-secondary px-4">
                            Cancel
                        </a>

                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa-solid fa-check me-1"></i>
                            {{ $isEdit ? 'Update' : 'Add' }} Purchase Order
                        </button>

                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- JAVASCRIPT --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const tableBody = document.getElementById('itemTableBody');
            const addItemHeaderBtn = document.getElementById('addItemHeaderBtn');

            /*
            |--------------------------------------------------------------------------
            | Reindex rows after add/delete
            |--------------------------------------------------------------------------
            */

            function updateRows() {
                const rows = tableBody.querySelectorAll('.item-row');

                rows.forEach(function(row, index) {

                    const serialNo = row.querySelector('.serial-no');

                    if (serialNo) {
                        serialNo.textContent = index + 1;
                    }

                    row.querySelectorAll('input, select, textarea').forEach(function(field) {

                        const name = field.getAttribute('name');

                        if (name) {
                            field.setAttribute(
                                'name',
                                name.replace(/items\[\d+\]/, 'items[' + index + ']')
                            );
                        }
                    });
                });
            }

            /*
            |--------------------------------------------------------------------------
            | Create a new blank row using the first row's available options
            |--------------------------------------------------------------------------
            */

            function createItemRow() {

                const firstRow = tableBody.querySelector('.item-row');
                const newRow = firstRow.cloneNode(true);

                newRow.querySelectorAll('input').forEach(function(field) {

                    if (field.name.includes('[length]')) {
                        field.value = 'Standard';
                    } else if (field.name.includes('[qty_variation]')) {
                        field.value = '±5%';
                    } else {
                        field.value = '';
                    }
                });

                newRow.querySelectorAll('select').forEach(function(field) {
                    field.selectedIndex = 0;
                });

                return newRow;
            }

            /*
            |--------------------------------------------------------------------------
            | Add item
            |--------------------------------------------------------------------------
            */

            addItemHeaderBtn.addEventListener('click', function() {

                tableBody.appendChild(createItemRow());

                updateRows();
            });

            /*
            |--------------------------------------------------------------------------
            | Delete item (keep at least one row)
            |--------------------------------------------------------------------------
            */

            tableBody.addEventListener('click', function(event) {

                const deleteButton = event.target.closest('.delete-item');

                if (!deleteButton) {
                    return;
                }

                const rows = tableBody.querySelectorAll('.item-row');

                if (rows.length === 1) {

                    const row = rows[0];

                    row.querySelectorAll('input').forEach(function(field) {

                        if (field.name.includes('[length]')) {
                            field.value = 'Standard';
                        } else if (field.name.includes('[qty_variation]')) {
                            field.value = '±5%';
                        } else {
                            field.value = '';
                        }
                    });

                    row.querySelectorAll('select').forEach(function(field) {
                        field.selectedIndex = 0;
                    });

                    return;
                }

                deleteButton.closest('.item-row').remove();

                updateRows();
            });

            /*
            |--------------------------------------------------------------------------
            | Initial setup
            |--------------------------------------------------------------------------
            */

            updateRows();

        });
    </script>

@endsection
