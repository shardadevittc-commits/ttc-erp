@extends('layouts.app')

@section('title')
    {{ isset($purchaseOrder) ? 'Edit' : 'Add' }} Purchase Order | TTC Robotronics - Steel Industry ERP
@endsection

@section('content')

<div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1" style="font-size: 0.8rem;">

            <li class="breadcrumb-item">
                <a
                    href="{{ route('dashboard') }}"
                    class="text-decoration-none text-muted"
                >
                    Dashboard
                </a>
            </li>

            <li class="breadcrumb-item">
                <a
                    href="{{ route('purchase') }}"
                    class="text-decoration-none text-muted"
                >
                    Purchase Orders
                </a>
            </li>

            <li
                class="breadcrumb-item active text-danger fw-semibold"
                aria-current="page"
            >
                {{ isset($purchaseOrder) ? 'Edit' : 'Add' }} Purchase Order
            </li>

        </ol>
    </nav>
</div>


<!-- Page Header -->
<div class="page-header">

    <div class="page-header__inner">

        <div class="page-header__content">

            <div class="page-header__title">
                <h6>
                    {{ isset($purchaseOrder) ? 'Edit' : 'Add' }} Purchase Order
                </h6>
            </div>

        </div>

        <div class="page-header__actions">

            <a
                href="{{ route('purchase') }}"
                class="btn btn-outline-secondary px-3 py-2 fw-semibold rounded-3"
            >
                <i class="fa-solid fa-arrow-left me-1"></i>
                Back
            </a>

        </div>

    </div>

</div>


<div class="container-fluid">

    <!-- Error Message -->
    @if(session('error'))

        <div class="alert alert-danger">
            <strong>Oh snap!</strong>
            {{ session('error') }}
        </div>

    @endif


    <!-- Success Message -->
    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    <div class="card">

        <div class="card-body">

            <form
                action="{{ route('purchase.add') }}"
                method="POST"
                id="purchaseOrderForm"
            >

                @csrf


                <!-- ========================================= -->
                <!-- CUSTOMER DETAILS -->
                <!-- ========================================= -->

                <h6 class="fw-bold text-uppercase mb-3">
                    Customer Details
                </h6>


                <div class="row">


                    <!-- Customer -->
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12">

                        <div class="form-group">

                            <label
                                for="customer"
                                class="form-label"
                            >
                                Customer Name
                                <span class="text-danger">*</span>
                            </label>


                            <select
                                class="select2 form-select"
                                id="customer"
                                name="customer"
                                data-placeholder="Select Customer"
                                data-minimum-results-for-search="-1"
                            >

                                <option value="">
                                    --Select Customer--
                                </option>


                                @foreach($customers as $customer)

                                    <option
                                        value="{{ $customer->id }}"
                                        @if(old('customer', $purchaseOrder->customer_id ?? '') == $customer->id)
                                            selected
                                        @endif
                                    >
                                        ({{ $customer->cust_code }}) -
                                        {{ $customer->company_name }}
                                    </option>

                                @endforeach

                            </select>


                            <label
                                id="customer-error"
                                class="error"
                                for="customer"
                            >

                                @error('customer')
                                    {{ $message }}
                                @enderror

                            </label>

                        </div>

                    </div>


                    <!-- Basic Rate -->
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">

                        <div class="form-group">

                            <label
                                for="basicRate"
                                class="form-label"
                            >
                                Basic Rate
                            </label>


                            <input
                                type="text"
                                name="basicRate"
                                id="basicRate"
                                class="form-control"
                                value="{{ old('basicRate', $saleOrder->basicRate ?? '') }}"
                            >

                        </div>

                    </div>


                    <!-- Ref Sale Order -->
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">

                        <div class="form-group">

                            <label
                                for="cust_po"
                                class="form-label"
                            >
                                Ref. Sale Order
                            </label>


                            <input
                                type="text"
                                name="cust_po"
                                id="cust_po"
                                class="form-control"
                                value="{{ old('cust_po', $saleOrder->cust_po ?? '') }}"
                            >

                        </div>

                    </div>


                    <!-- Freight Basis -->
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">

                        <div class="form-group">

                            <label class="form-label d-block">
                                Freight Basis
                            </label>


                            <div class="d-flex align-items-center gap-3 mt-2">


                                <div class="form-check">

                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="frtBasis"
                                        id="frtBasisEx"
                                        value="EX"
                                        @checked(
                                            old(
                                                'frtBasis',
                                                $saleOrder->frtBasis ?? 'FOR'
                                            ) == 'EX'
                                        )
                                    >

                                    <label
                                        class="form-check-label"
                                        for="frtBasisEx"
                                    >
                                        EX
                                    </label>

                                </div>


                                <div class="form-check">

                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="frtBasis"
                                        id="frtBasisFor"
                                        value="FOR"
                                        @checked(
                                            old(
                                                'frtBasis',
                                                $saleOrder->frtBasis ?? 'FOR'
                                            ) == 'FOR'
                                        )
                                    >

                                    <label
                                        class="form-check-label"
                                        for="frtBasisFor"
                                    >
                                        FOR
                                    </label>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- Payment Term -->
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">

                        <div class="form-group">

                            <label class="form-label">
                                Payment Term
                            </label>


                            <div class="row">


                                <div class="col-6">

                                    <input
                                        type="text"
                                        name="payType"
                                        id="payType"
                                        class="form-control"
                                        placeholder="Enter value"
                                        value="{{ old('payType', $saleOrder->payType ?? '') }}"
                                    >

                                </div>


                                <div class="col-6">

                                    <select
                                        name="payTypeUnit"
                                        id="payTypeUnit"
                                        class="form-select"
                                    >

                                        <option value="">
                                            Select
                                        </option>

                                        <option
                                            value="Days"
                                            {{ old('payTypeUnit', $saleOrder->payTypeUnit ?? '') == 'Days' ? 'selected' : '' }}
                                        >
                                            Days
                                        </option>

                                        <option
                                            value="Week"
                                            {{ old('payTypeUnit', $saleOrder->payTypeUnit ?? '') == 'Week' ? 'selected' : '' }}
                                        >
                                            Week
                                        </option>

                                        <option
                                            value="Month"
                                            {{ old('payTypeUnit', $saleOrder->payTypeUnit ?? '') == 'Month' ? 'selected' : '' }}
                                        >
                                            Month
                                        </option>

                                    </select>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- Remarks -->
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">

                        <div class="form-group">

                            <label
                                for="remarks"
                                class="form-label"
                            >
                                Remarks
                            </label>


                            <input
                                type="text"
                                name="remarks"
                                id="remarks"
                                class="form-control"
                                value="{{ old('remarks', $saleOrder->remarks ?? '') }}"
                            >

                        </div>

                    </div>

                </div>


                <hr class="my-4">


                <!-- ========================================= -->
                <!-- ADD ITEMS -->
                <!-- ========================================= -->

                <div class="d-flex align-items-center justify-content-between mb-3">

                    <h6 class="fw-bold text-uppercase mb-0">
                        Add Items
                    </h6>


                    <!-- Add Item Button -->
                    <button
                        type="button"
                        class="btn btn-primary btn-sm rounded-2 d-none"
                        id="addItemBtn"
                        title="Add Item"
                    >
                        <i class="fa-solid fa-plus"></i>
                    </button>

                </div>


                <div
                    class="table-responsive"
                    id="itemTableWrapper"
                >

                    <table
                        class="table table-bordered table-med itemtable align-middle mb-0"
                    >

                        <thead>

                            <tr>

                                <th style="min-width:60px;">
                                    S. No.
                                </th>


                                <th style="min-width:180px;">
                                    Item Name
                                </th>


                                <th style="min-width:250px;">
                                    Size
                                </th>


                                <th style="min-width:160px;">
                                    Grade
                                </th>


                                <th style="min-width:130px;">
                                    Length
                                </th>


                                <th style="min-width:130px;">
                                    Rate
                                </th>


                                <th style="min-width:230px;">
                                    Qty
                                </th>


                                <th style="min-width:120px;">
                                    Qty Variation
                                </th>


                                <th style="min-width:220px;">
                                    Remarks
                                </th>


                                <th
                                    style="width:60px;"
                                    class="text-center"
                                >

                                    <button
                                        type="button"
                                        class="btn btn-primary btn-sm rounded-2"
                                        id="addItemHeaderBtn"
                                        title="Add Item"
                                    >
                                        <i class="fa-solid fa-plus"></i>
                                    </button>

                                </th>

                            </tr>

                        </thead>


                        <tbody id="itemTableBody">

                            <!-- ================================= -->
                            <!-- DEFAULT FIRST ROW -->
                            <!-- ================================= -->

                            <tr class="item-row">


                                <!-- S.No -->
                                <td class="serial-no text-center fw-semibold">
                                    1
                                </td>


                                <!-- Item Name -->
                                <td>

                                    <select
                                        name="items[0][item_id]"
                                        class="form-select item-name"
                                    >

                                        <option value="">
                                            Select Item
                                        </option>


                                        @foreach($products ?? [] as $item)

                                            <option value="{{ $item->id }}">
                                                {{ $item->product_name }}
                                            </option>

                                        @endforeach

                                    </select>

                                </td>


                                <!-- Size -->
                                <td>

                                    <div class="row g-1">


                                        <div class="col-7">

                                            <select
                                                name="items[0][size]"
                                                class="form-select item-size"
                                            >

                                                <option value="">
                                                    Select Size
                                                </option>


                                                @foreach($sizes ?? [] as $size)

                                                    <option value="{{ $size->id }}">
                                                        {{ $size->size_name }}
                                                    </option>

                                                @endforeach

                                            </select>

                                        </div>


                                        <div class="col-5">

                                            <select
                                                name="items[0][size_unit]"
                                                class="form-select"
                                            >

                                                <option value="">
                                                    --Select Unit--
                                                </option>

                                                @foreach($units ?? [] as $unit)

                                                    <option value="{{ $unit->id }}">
                                                        {{ $unit->unit_name }}
                                                    </option>

                                                @endforeach

                                            </select>

                                        </div>

                                    </div>

                                </td>


                                <!-- Grade -->
                                <td>

                                    <select
                                        name="items[0][grade]"
                                        class="form-select item-grade"
                                    >

                                        <option value="">
                                            Select Grade
                                        </option>


                                        @foreach($grades ?? [] as $grade)

                                            <option value="{{ $grade->id }}">
                                                {{ $grade->grade_name }}
                                            </option>

                                        @endforeach

                                    </select>

                                </td>


                                <!-- Length -->
                                <td>

                                    <input
                                        type="text"
                                        name="items[0][length]"
                                        class="form-control"
                                        value="Standard"
                                        readonly
                                    >

                                </td>


                                <!-- Rate -->
                                <td>

                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        name="items[0][rate]"
                                        class="form-control"
                                        placeholder="Rate"
                                    >

                                </td>


                                <!-- Qty -->
                                <td>

                                    <div class="row g-1">


                                        <div class="col-6">

                                            <input
                                                type="number"
                                                step="0.001"
                                                min="0"
                                                name="items[0][qty]"
                                                class="form-control"
                                                placeholder="Qty"
                                            >

                                        </div>


                                        <div class="col-6">

                                            <select
                                                name="items[0][qty_unit]"
                                                class="form-select"
                                            >

                                                <option value="">
                                                    --Select Unit--
                                                </option>

                                                <option value="TONS">
                                                    TONS
                                                </option>

                                                <option value="PCS">
                                                    PCS
                                                </option>

                                                <option value="Litre">
                                                    Litre
                                                </option>

                                            </select>

                                        </div>

                                    </div>

                                </td>


                                <!-- Qty Variation -->
                                <td>

                                    <input
                                        type="text"
                                        name="items[0][qty_variation]"
                                        class="form-control text-center"
                                        value="±5%"
                                        readonly
                                    >

                                </td>


                                <!-- Remarks -->
                                <td>

                                    <input
                                        type="text"
                                        name="items[0][remarks]"
                                        class="form-control"
                                        placeholder="Remarks"
                                    >

                                </td>


                                <!-- Delete -->
                                <td class="text-center">

                                    <button
                                        type="button"
                                        class="btn btn-outline-danger btn-sm delete-item"
                                        title="Delete Item"
                                    >

                                        <i class="fa-solid fa-trash"></i>

                                    </button>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>


                <!-- ========================================= -->
                <!-- FORM BUTTONS -->
                <!-- ========================================= -->

                <div class="mt-4 d-flex justify-content-end gap-2">


                    <a
                        href="{{ route('purchase') }}"
                        class="btn btn-outline-secondary px-4"
                    >
                        Cancel
                    </a>


                    <button
                        type="submit"
                        class="btn btn-primary px-4"
                    >

                        <i class="fa-solid fa-check me-1"></i>

                        {{ isset($purchaseOrder) ? 'Update' : 'Add' }}
                        Purchase Order

                    </button>

                </div>


            </form>

        </div>

    </div>

</div>


<!-- ========================================= -->
<!-- JAVASCRIPT -->
<!-- ========================================= -->

<script>

document.addEventListener('DOMContentLoaded', function () {


    const tableBody = document.getElementById('itemTableBody');

    const addItemBtn = document.getElementById('addItemBtn');

    const addItemHeaderBtn = document.getElementById('addItemHeaderBtn');


    /*
    |--------------------------------------------------------------------------
    | Update Serial Numbers + Input Names
    |--------------------------------------------------------------------------
    */

    function updateRows() {

        const rows = tableBody.querySelectorAll('.item-row');


        rows.forEach(function (row, index) {


            /*
            |--------------------------------------------------------------------------
            | Serial Number
            |--------------------------------------------------------------------------
            */

            const serialNo = row.querySelector('.serial-no');

            if (serialNo) {

                serialNo.textContent = index + 1;

            }


            /*
            |--------------------------------------------------------------------------
            | Update Field Names
            |--------------------------------------------------------------------------
            */

            row.querySelectorAll('input, select').forEach(function (field) {


                const name = field.getAttribute('name');


                if (!name) {

                    return;

                }


                field.setAttribute(
                    'name',
                    name.replace(
                        /items\[\d+\]/,
                        'items[' + index + ']'
                    )
                );

            });

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Create New Item Row
    |--------------------------------------------------------------------------
    */

    function createItemRow() {


        const row = document.createElement('tr');

        row.classList.add('item-row');


        row.innerHTML = `

            <!-- S.No -->
            <td class="serial-no text-center fw-semibold">
                1
            </td>


            <!-- Item Name -->
            <td>

                <select
                    name="items[0][item_id]"
                    class="form-select item-name"
                >

                    <option value="">
                        Select Item
                    </option>

                    @foreach($products ?? [] as $item)

                        <option value="{{ $item->id }}">
                            {{ $item->product_name }}
                        </option>

                    @endforeach

                </select>

            </td>


            <!-- Size -->
            <td>

                <div class="row g-1">

                    <div class="col-7">

                        <select
                            name="items[0][size]"
                            class="form-select item-size"
                        >

                            <option value="">
                                Select Size
                            </option>

                            @foreach($sizes ?? [] as $size)

                                <option value="{{ $size->id }}">
                                    {{ $size->size_name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-5">

                        <select
                            name="items[0][size_unit]"
                            class="form-select"
                        >

                            <option value="">
                                --Select Unit--
                            </option>

                            @foreach($units ?? [] as $unit)

                                <option value="{{ $unit->id }}">
                                    {{ $unit->unit_name }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

            </td>


            <!-- Grade -->
            <td>

                <select
                    name="items[0][grade]"
                    class="form-select item-grade"
                >

                    <option value="">
                        Select Grade
                    </option>

                    @foreach($grades ?? [] as $grade)

                        <option value="{{ $grade->id }}">
                            {{ $grade->grade_name }}
                        </option>

                    @endforeach

                </select>

            </td>


            <!-- Length -->
            <td>

                <input
                    type="text"
                    name="items[0][length]"
                    class="form-control"
                    value="Standard"
                    readonly
                >

            </td>


            <!-- Rate -->
            <td>

                <input
                    type="number"
                    step="0.01"
                    min="0"
                    name="items[0][rate]"
                    class="form-control"
                    placeholder="Rate"
                >

            </td>


            <!-- Qty -->
            <td>

                <div class="row g-1">

                    <div class="col-6">

                        <input
                            type="number"
                            step="0.001"
                            min="0"
                            name="items[0][qty]"
                            class="form-control"
                            placeholder="Qty"
                        >

                    </div>


                    <div class="col-6">

                        <select
                            name="items[0][qty_unit]"
                            class="form-select"
                        >

                            <option value="">
                                --Select Unit--
                            </option>

                            <option value="TONS">
                                TONS
                            </option>

                            <option value="PCS">
                                PCS
                            </option>

                            <option value="Litre">
                                Litre
                            </option>

                        </select>

                    </div>

                </div>

            </td>


            <!-- Qty Variation -->
            <td>

                <input
                    type="text"
                    name="items[0][qty_variation]"
                    class="form-control text-center"
                    value="±5%"
                    readonly
                >

            </td>


            <!-- Remarks -->
            <td>

                <input
                    type="text"
                    name="items[0][remarks]"
                    class="form-control"
                    placeholder="Remarks"
                >

            </td>


            <!-- Delete -->
            <td class="text-center">

                <button
                    type="button"
                    class="btn btn-outline-danger btn-sm delete-item"
                    title="Delete Item"
                >

                    <i class="fa-solid fa-trash"></i>

                </button>

            </td>

        `;


        return row;

    }


    /*
    |--------------------------------------------------------------------------
    | Add Item
    |--------------------------------------------------------------------------
    */

    function addItem() {


        const newRow = createItemRow();


        tableBody.appendChild(newRow);


        updateRows();


    }


    /*
    |--------------------------------------------------------------------------
    | Top Add Button
    |--------------------------------------------------------------------------
    */

    addItemBtn.addEventListener(
        'click',
        function () {

            addItem();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Header Add Button
    |--------------------------------------------------------------------------
    */

    addItemHeaderBtn.addEventListener(
        'click',
        function () {

            addItem();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Delete Item
    |--------------------------------------------------------------------------
    */

    tableBody.addEventListener(
        'click',
        function (event) {


            const deleteButton =
                event.target.closest('.delete-item');


            if (!deleteButton) {

                return;

            }


            const rows =
                tableBody.querySelectorAll('.item-row');


            /*
            |--------------------------------------------------------------------------
            | Keep At Least One Row
            |--------------------------------------------------------------------------
            */

            if (rows.length === 1) {


                rows[0]
                    .querySelectorAll('input, select')
                    .forEach(function (field) {


                        if (field.tagName === 'SELECT') {

                            field.selectedIndex = 0;

                        }

                        else if (
                            field.name.includes('[length]')
                        ) {

                            field.value = 'Standard';

                        }

                        else if (
                            field.name.includes('[qty_variation]')
                        ) {

                            field.value = '±5%';

                        }

                        else {

                            field.value = '';

                        }

                    });


                return;

            }


            /*
            |--------------------------------------------------------------------------
            | Delete Row
            |--------------------------------------------------------------------------
            */

            deleteButton
                .closest('.item-row')
                .remove();


            /*
            |--------------------------------------------------------------------------
            | Recalculate Serial Numbers
            |--------------------------------------------------------------------------
            */

            updateRows();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Initial Row Setup
    |--------------------------------------------------------------------------
    */

    updateRows();


});

</script>

@endsection
