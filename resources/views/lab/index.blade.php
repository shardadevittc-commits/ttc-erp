
@extends('layouts.app')

@section('title', 'Manage Lab Management | TTC Robotronics')

@section('content')

<div class="page-header">
    <div class="page-header__inner">
        <div class="page-header__content">
            <div class="page-header__title">
                <h6>Manage Lab Management</h6>
            </div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h5 class="mb-0">Lab Management Listing</h5>
    </div>

    <div class="card-body">
        <div class="table-responsive">

            <table class="table table-bordered table-striped table-hover">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Purchase Order ID</th>
                        <th>Size</th>
                        <th>Grade</th>
                        <th>PO Item Qty</th>
                        <th>Received Qty</th>
                        <th>Order Price</th>
                        <th>Received Price</th>
                        <th>Type</th>
                        <th>Lab Test</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($unloaderData as $key => $item)

                        <tr>
                            <td>{{ $key + 1 }}</td>

                            <td>{{ $item['customer_name'] ?? '-' }}</td>

                            <td>{{ $item['purchase_order_item_id'] ?? '-' }}</td>

                            <td>{{ $item['size_name'] ?? '-' }}</td>

                            <td>{{ $item['grade_name'] ?? '-' }}</td>

                            <td>
                                {{ number_format((float)($item['po_item_qty'] ?? 0), 2) }}
                            </td>

                            <td>
                                {{ number_format((float)($item['received_qty'] ?? 0), 2) }}
                            </td>

                            <td>
                                {{ number_format((float)($item['order_price'] ?? 0), 2) }}
                            </td>

                            <td>
                                {{ number_format((float)($item['received_price'] ?? 0), 2) }}
                            </td>

                            <td>
                                @php
                                    $formlist = $item['formlist'] ?? null;
                                @endphp

                                @if(is_array($formlist))
                                    {{ json_encode($formlist) }}
                                @elseif(is_string($formlist) && $formlist !== '')
                                    {{ $formlist }}
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                @if(($item['lab_test'] ?? '') == '1')
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-warning text-dark">No</span>
                                @endif
                            </td>

                            <td>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-primary"
                                    onclick="openLabDetails(this)"
                                    data-item="{{ json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}">

                                    <i class="fas fa-eye"></i> View

                                </button>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="12" class="text-center">
                                No lab management records found.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>
    </div>
</div>


{{-- Drawer Overlay --}}

<div id="labOverlay"
     class="lab-overlay"
     onclick="closeLabDetails()">
</div>


{{-- Right Side Drawer --}}

<div id="labDrawer" class="lab-drawer">

    <div class="lab-header">

        <div>
            <h5 class="mb-1">Lab Test Report</h5>
            <small class="text-muted">
                Laboratory Inspection Details
            </small>
        </div>

        <button type="button"
                class="lab-close"
                onclick="closeLabDetails()"
                aria-label="Close">

            &times;

        </button>

    </div>


    <div class="lab-body">

        <input type="hidden"
               id="lab_csrf_token"
               value="{{ csrf_token() }}">


        {{-- Order Information --}}

        <div class="table-responsive mb-4">

            <table class="table table-bordered text-center align-middle lab-order-table">

                <thead>
                    <tr>
                        <th>Order No. &amp; Date</th>
                        <th>Section (Nominal Size)</th>
                        <th>Grade</th>
                        <th>Qty (MT)</th>
                        <th>Remarks</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td id="labOrderNo">-</td>
                        <td id="labSize">-</td>
                        <td id="labGrade">-</td>
                        <td id="labQty">-</td>
                        <td id="labRemarks">-</td>
                    </tr>
                </tbody>

            </table>

        </div>


        {{-- Dynamic Grade Properties --}}

        <div id="dynamicGradeSections"></div>


        {{-- Submit Button --}}

        <div class="text-end mt-4 mb-3" id="submitLabWrapper">

            <button
                type="button"
                id="submitLabBtn"
                class="btn btn-primary px-4"
                onclick="submitLabDetails()">

                <i class="fas fa-save me-1"></i>
                Update Final Values

            </button>

        </div>


        {{-- Additional Details --}}

        <div class="lab-section-title mt-4">
            Additional Details
        </div>

        <div class="row g-3 mt-1"
             id="additionalFields">
        </div>

    </div>

</div>


<style>

    .lab-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.38);
        z-index: 9998;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .lab-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .lab-drawer {
        position: fixed;
        top: 0;
        right: 0;
        width: min(1150px, 95vw);
        height: 100vh;
        background: #f1f5f7;
        z-index: 9999;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        display: flex;
        flex-direction: column;
        box-shadow: -5px 0 25px rgba(0, 0, 0, 0.2);
    }

    .lab-drawer.active {
        transform: translateX(0);
    }

    .lab-header {
        flex-shrink: 0;
        padding: 18px 24px;
        background: #fff;
        border-bottom: 1px solid #ddd;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .lab-header h5 {
        font-size: 19px;
        font-weight: 600;
        color: #263445;
    }

    .lab-close {
        width: 36px;
        height: 36px;
        border: 0;
        border-radius: 50%;
        font-size: 28px;
        line-height: 1;
        background: #eee;
        color: #333;
        cursor: pointer;
    }

    .lab-close:hover {
        background: #ddd;
    }

    .lab-body {
        padding: 22px;
        overflow-y: auto;
        flex: 1;
    }

    .lab-order-table {
        background: #fff;
        margin-bottom: 0;
        font-size: 13px;
    }

    .lab-order-table thead th {
        background: #d0d0d0;
        color: #222;
        white-space: nowrap;
    }

    .lab-order-table tbody td {
        background: #f8fafb;
        overflow-wrap: anywhere;
    }

    .lab-section-title {
        background: #fff;
        padding: 12px 14px;
        font-size: 17px;
        font-weight: 500;
        color: #202832;
        border-left: 3px solid #0d6efd;
    }

    .lab-property {
        margin-bottom: 4px;
    }

    .lab-property-label {
        font-size: 13px;
        font-weight: 500;
        color: #303b47;
        margin-bottom: 8px;
    }

    .lab-property-values {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        background: #e3e7ea;
        min-height: 42px;
    }

    .lab-property-values span {
        min-width: 0;
        padding: 10px 5px;
        font-size: 12px;
        color: #596572;
        border-right: 1px solid #d3d8dc;
        overflow-wrap: anywhere;
        text-align: center;
    }

    .lab-property-values span:last-child {
        border-right: none;
    }

    .lab-property-values .has-value {
        color: #198754;
        font-weight: 600;
    }

    .lab-actual-input,
    .lab-final-input {
        width: 100%;
        min-width: 0;
        padding: 5px 3px;
        font-size: 12px;
        text-align: center;
        border: 1px solid #ced4da;
        border-radius: 3px;
        background: #fff;
    }

    .lab-actual-input {
        background: #e9ecef;
        color: #198754;
        font-weight: 600;
        cursor: not-allowed;
    }

    .lab-final-input:focus {
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.15);
    }

    .lab-info-box {
        padding: 12px;
        background: #fff;
        border: 1px solid #e0e5e9;
        border-radius: 4px;
        height: 100%;
        overflow-wrap: anywhere;
    }

    .lab-info-box small {
        display: block;
        color: #687582;
        margin-bottom: 6px;
    }

    .lab-info-box strong {
        color: #263445;
    }

    @media (max-width: 767px) {

        .lab-drawer {
            width: 100%;
        }

        .lab-body {
            padding: 12px;
        }

        .lab-header {
            padding: 14px;
        }

        .lab-order-table {
            min-width: 700px;
        }

        .lab-property-values {
            grid-template-columns: repeat(4, minmax(65px, 1fr));
        }

    }

</style>


<script>

    // Current selected order
    let currentLabItem = null;


    /*
    |--------------------------------------------------------------------------
    | Helper Functions
    |--------------------------------------------------------------------------
    */

    function safeText(value, fallback = '-') {

        if (value === null || value === undefined || value === '') {
            return fallback;
        }

        return String(value);
    }


    function escapeHtml(value) {

        return String(value).replace(/[&<>"']/g, function(char) {

            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[char];

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Check Final Value Status
    |--------------------------------------------------------------------------
    */

    function checkFinalValueStatus(item) {

        const buttonWrapper = document.getElementById('submitLabWrapper');

        const reactions = Array.isArray(item.grade_reactions)
            ? item.grade_reactions
            : [];

        // Properties with saved Actual Values
        const actualProperties = reactions.filter(function(property) {

            return property.actual_value !== null &&
                property.actual_value !== undefined &&
                property.actual_value !== '';

        });


        // No Actual Values found
        if (actualProperties.length === 0) {

            buttonWrapper.style.display = 'none';

            return;

        }


        // Check all Final Values
        const allFinalSaved = actualProperties.every(function(property) {

            return property.final_value !== null &&
                property.final_value !== undefined &&
                String(property.final_value).trim() !== '';

        });


        // Hide button if all Final Values are saved
        buttonWrapper.style.display = allFinalSaved ? 'none' : 'block';

    }


    /*
    |--------------------------------------------------------------------------
    | Render Grade Properties
    |--------------------------------------------------------------------------
    */

    function renderDynamicGradeSections(item) {

        const container = document.getElementById('dynamicGradeSections');

        const reactions = Array.isArray(item.grade_reactions)
            ? item.grade_reactions
            : [];


        if (reactions.length === 0) {

            container.innerHTML = `
                <div class="alert alert-warning">
                    No composition/property records found for this grade.
                </div>
            `;

            return;

        }


        const categories = {};


        reactions.forEach(function(reaction) {

            const categoryId = reaction.category_id ?? 'other';

            if (!categories[categoryId]) {

                categories[categoryId] = {

                    id: reaction.category_id ?? null,

                    name: reaction.category_name || 'Other',

                    properties: []

                };

            }

            categories[categoryId].properties.push(reaction);

        });


        container.innerHTML = Object.values(categories).map(function(category) {

            const propertiesHtml = category.properties.map(function(property) {

                const propertyId = Number(property.property_id);

                const minValue = property.min_value;

                const maxValue = property.max_value;

                const propertyLabel =
                    safeText(property.property_name, 'Property') +
                    (property.unit ? ' (' + property.unit + ')' : '');


                return `

                    <div class="col-12 col-sm-6 col-xl-3">

                        <div class="lab-property">

                            <div class="lab-property-label">

                                ${escapeHtml(propertyLabel)}

                            </div>


                            <div class="lab-property-values">

                                <!-- MIN VALUE -->

                                <span class="${minValue !== null && minValue !== undefined ? 'has-value' : ''}">

                                    ${escapeHtml(safeText(minValue, 'Min'))}

                                </span>


                                <!-- MAX VALUE -->

                                <span class="${maxValue !== null && maxValue !== undefined ? 'has-value' : ''}">

                                    ${escapeHtml(safeText(maxValue, 'Max'))}

                                </span>


                                <!-- ACTUAL VALUE READONLY -->

                                <span style="padding:4px;">

                                    <input
                                        type="number"
                                        class="lab-actual-input"
                                        value="${escapeHtml(safeText(property.actual_value, ''))}"
                                        placeholder="Actual"
                                        readonly
                                        tabindex="-1"
                                    >

                                </span>


                                <!-- FINAL VALUE EDITABLE -->

                                <span style="padding:4px;">

                                    <input
                                        type="number"
                                        class="lab-final-input"
                                        data-property-id="${propertyId}"
                                        min="0"
                                        step="any"
                                        inputmode="decimal"
                                        placeholder="Final"
                                        value="${escapeHtml(safeText(property.final_value, ''))}"
                                        onkeydown="return !['e','E','+','-'].includes(event.key)"
                                    >

                                </span>

                            </div>

                        </div>

                    </div>

                `;

            }).join('');


            return `

                <div class="lab-section-title mt-4">

                    ${escapeHtml(category.name)}

                </div>

                <div class="row g-3 mt-1">

                    ${propertiesHtml}

                </div>

            `;

        }).join('');


        // Column Headings

        container.insertAdjacentHTML('afterbegin', `

            <div class="row g-3 mt-2">

                <div class="col-12">

                    <div class="lab-property-values fw-bold text-center">

                        <span>Min</span>

                        <span>Max</span>

                        <span>Actual</span>

                        <span>Final</span>

                    </div>

                </div>

            </div>

        `);

    }


    /*
    |--------------------------------------------------------------------------
    | Additional Details
    |--------------------------------------------------------------------------
    */

    function renderAdditionalDetails(item) {

        const fields = [

            ['Customer', 'customer_name'],

            ['Purchase Order Item ID', 'purchase_order_item_id'],

            ['PO Item Qty', 'po_item_qty'],

            ['Received Qty', 'received_qty'],

            ['Order Price', 'order_price'],

            ['Received Price', 'received_price'],

            ['Furnace Heat No.', 'furnance_heat_no'],

            ['Generated Heat No.', 'genrated_heat_no'],

            ['Type', 'formlist'],

            ['Lab Test', 'lab_test']

        ];


        document.getElementById('additionalFields').innerHTML =

            fields.map(function([label, key]) {

                let value = item[key];

                if (
                    key === 'formlist' &&
                    value !== null &&
                    typeof value === 'object'
                ) {

                    value = JSON.stringify(value);

                }

                if (key === 'lab_test') {

                    value = value == '1' ? 'Yes' : 'No';

                }


                return `

                    <div class="col-12 col-sm-6 col-xl-4">

                        <div class="lab-info-box">

                            <small>${escapeHtml(label)}</small>

                            <strong>${escapeHtml(safeText(value))}</strong>

                        </div>

                    </div>

                `;

            }).join('');

    }


    /*
    |--------------------------------------------------------------------------
    | Open Drawer
    |--------------------------------------------------------------------------
    */

    function openLabDetails(button) {

        let item;

        try {

            item = JSON.parse(button.getAttribute('data-item'));

        } catch(error) {

            alert('Unable to load lab details.');

            return;

        }


        currentLabItem = item;


        document.getElementById('labOrderNo').textContent =
            safeText(item.order_no ?? item.purchase_order_item_id);

        document.getElementById('labSize').textContent =
            safeText(item.size_name);

        document.getElementById('labGrade').textContent =
            safeText(item.grade_name);

        document.getElementById('labQty').textContent =
            safeText(item.received_qty ?? item.po_item_qty);

        document.getElementById('labRemarks').textContent =
            safeText(item.item_remarks);


        renderDynamicGradeSections(item);

        renderAdditionalDetails(item);


        // Check button status every time drawer opens
        checkFinalValueStatus(item);


        document.getElementById('labOverlay').classList.add('active');

        document.getElementById('labDrawer').classList.add('active');

        document.body.style.overflow = 'hidden';

    }


    /*
    |--------------------------------------------------------------------------
    | Close Drawer
    |--------------------------------------------------------------------------
    */

    function closeLabDetails() {

        document.getElementById('labOverlay').classList.remove('active');

        document.getElementById('labDrawer').classList.remove('active');

        document.body.style.overflow = '';

    }


    /*
    |--------------------------------------------------------------------------
    | Submit Final Values
    |--------------------------------------------------------------------------
    */

    async function submitLabDetails() {

        if (!currentLabItem) {

            alert('Please select a purchase order first.');

            return;

        }


        const purchaseOrderItemId = Number(
            currentLabItem.purchase_order_item_id
        );


        if (
            !Number.isInteger(purchaseOrderItemId) ||
            purchaseOrderItemId <= 0
        ) {

            alert('Invalid Purchase Order Item ID.');

            return;

        }


        const inputs = document.querySelectorAll(
            '#dynamicGradeSections .lab-final-input'
        );


        const finalValues = [];


        for (const input of inputs) {

            const rawValue = input.value.trim();


            // Skip empty Final Values
            if (rawValue === '') {

                continue;

            }


            const propertyId = Number(input.dataset.propertyId);


            if (!Number.isInteger(propertyId) || propertyId <= 0) {

                alert('Invalid property ID found.');

                return;

            }


            const finalValue = Number(rawValue);


            if (!Number.isFinite(finalValue) || finalValue < 0) {

                alert('Please enter a valid non-negative Final Value.');

                input.focus();

                return;

            }


            finalValues.push({

                property_id: propertyId,

                final_value: finalValue

            });

        }


        if (finalValues.length === 0) {

            alert('Please enter at least one Final Value.');

            return;

        }


        const button = document.getElementById('submitLabBtn');

        const originalButtonHtml = button.innerHTML;


        button.disabled = true;

        button.innerHTML =
            '<span class="spinner-border spinner-border-sm me-1"></span> Updating...';


        try {

            const response = await fetch(

                @json(route('lab-results.store')),

                {

                    method: 'POST',

                    headers: {

                        'Content-Type': 'application/json',

                        'Accept': 'application/json',

                        'X-CSRF-TOKEN':
                            document.getElementById('lab_csrf_token').value

                    },

                    body: JSON.stringify({

                        purchase_order_item_id: purchaseOrderItemId,

                        final_values: finalValues

                    })

                }

            );


            const result = await response.json();


            if (!response.ok) {

                if (response.status === 422 && result.errors) {

                    const messages = Object.values(result.errors).flat();

                    throw new Error(messages.join('\n'));

                }

                throw new Error(
                    result.message || 'Unable to update Final Values.'
                );

            }


            alert(result.message || 'Final Values updated successfully.');


            /*
             * Update Final Values in current drawer data.
             * Actual Values remain unchanged.
             */

            finalValues.forEach(function(saved) {

                const property = currentLabItem.grade_reactions?.find(
                    function(reaction) {

                        return Number(reaction.property_id) ===
                            saved.property_id;

                    }
                );


                if (property) {

                    property.final_value = saved.final_value;

                }

            });


            /*
             * Update Listing Button Data
             */

            document.querySelectorAll(
                'button[onclick="openLabDetails(this)"]'
            ).forEach(function(rowButton) {

                try {

                    const rowItem = JSON.parse(
                        rowButton.getAttribute('data-item')
                    );


                    if (
                        Number(rowItem.purchase_order_item_id) ===
                        purchaseOrderItemId
                    ) {

                        finalValues.forEach(function(saved) {

                            const property = rowItem.grade_reactions?.find(
                                function(reaction) {

                                    return Number(reaction.property_id) ===
                                        saved.property_id;

                                }
                            );


                            if (property) {

                                property.final_value = saved.final_value;

                            }

                        });


                        rowButton.setAttribute(
                            'data-item',
                            JSON.stringify(rowItem)
                        );

                    }

                } catch(error) {

                    // Ignore malformed listing data

                }

            });


            /*
             * Hide Save Button if all Final Values are saved.
             */

            checkFinalValueStatus(currentLabItem);


        } catch(error) {

            alert(error.message || 'Something went wrong while updating.');

        } finally {

            button.disabled = false;

            button.innerHTML = originalButtonHtml;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Escape Key
    |--------------------------------------------------------------------------
    */

    document.addEventListener('keydown', function(event) {

        if (event.key === 'Escape') {

            closeLabDetails();

        }

    });

</script>

@endsection
