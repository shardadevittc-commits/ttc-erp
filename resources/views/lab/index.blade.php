
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


let currentLabItem = null;

function safeText(value, fallback = '-') {
    if (value === null || value === undefined || value === '') {
        return fallback;
    }
    return String(value);
}

function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, char => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    }[char]));
}

function hasValue(value) {
    return value !== null &&
        value !== undefined &&
        String(value).trim() !== '';
}

/*
|--------------------------------------------------------------------------
| Determine Current Stage
|--------------------------------------------------------------------------
*/

function getLabStage(item) {

    const reactions = item.grade_reactions || [];

    const allActualSaved = reactions.length > 0 &&
        reactions.every(p => hasValue(p.actual_value));

    const allFinalSaved = reactions.length > 0 &&
        reactions.every(p => hasValue(p.final_value));

    const productionAvailable = hasValue(item.production_id);

    if (!allActualSaved) {
        return 'actual';
    }

    if (!productionAvailable) {
        return 'waiting';
    }

    if (allFinalSaved) {
        return 'completed';
    }

    return 'final';
}

/*
|--------------------------------------------------------------------------
| Render Properties
|--------------------------------------------------------------------------
*/

function renderDynamicGradeSections(item) {
    const container = document.getElementById('dynamicGradeSections');
    const reactions = item.grade_reactions || [];
    const stage = getLabStage(item);

    if (!reactions.length) {
        container.innerHTML = `
            <div class="alert alert-warning">
                No grade properties found.
            </div>`;
        updateSubmitButton('completed');
        return;
    }

    const categories = {};

    reactions.forEach(property => {
        const categoryId = property.category_id ?? 'other';

        if (!categories[categoryId]) {
            categories[categoryId] = {
                name: property.category_name || 'Other',
                properties: []
            };
        }

        categories[categoryId].properties.push(property);
    });

    container.innerHTML = Object.values(categories).map(category => {
        const propertiesHtml = category.properties.map(property => {
            const actualSaved = hasValue(property.actual_value);
            const finalSaved = hasValue(property.final_value);

            const actualReadonly =
                stage !== 'actual' || actualSaved;

            const finalReadonly =
                stage !== 'final' || finalSaved;

            const showActualInput = stage === 'actual' || actualSaved;
            const showFinalInput = stage === 'final' || finalSaved;
            return `
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="lab-property">

                        <div class="lab-property-label">
                            ${escapeHtml(property.property_name)}
                            ${property.unit ? '(' + escapeHtml(property.unit) + ')' : ''}
                        </div>

                        <div class="lab-property-values">

                            <span>
                                ${escapeHtml(safeText(property.min_value, 'Min'))}
                            </span>

                            <span>
                                ${escapeHtml(safeText(property.max_value, 'Max'))}
                            </span>

                            <span style="padding:4px;">
                               ${showActualInput ? `
                                    <input
                                        type="number"
                                        class="lab-actual-input"
                                        data-property-id="${Number(property.property_id)}"
                                        value="${actualSaved ? escapeHtml(property.actual_value) : ''}"
                                        placeholder="Actual"
                                        ${actualReadonly ? 'readonly' : ''}
                                    >
                                    ` : '-'}
                            </span>

                            <span style="padding:4px;">
                               ${showFinalInput ? `
                                    <input
                                        type="number"
                                        class="lab-final-input"
                                        data-property-id="${Number(property.property_id)}"
                                        value="${finalSaved ? escapeHtml(property.final_value) : ''}"
                                        placeholder="Final"
                                        ${finalReadonly ? 'readonly' : ''}
                                    >
                                    ` : '-'}
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

    container.insertAdjacentHTML('afterbegin', `
        {{-- <div class="row g-3 mt-2">
            <div class="col-12">
                <div class="lab-property-values fw-bold text-center">
                    <span>Min</span>
                    <span>Max</span>
                    <span>Actual</span>
                    <span>Final</span>
                </div>
            </div>
        </div> --}}
    `);

    updateSubmitButton(stage);
}

/*
|--------------------------------------------------------------------------
| Submit Button Status
|--------------------------------------------------------------------------
*/

function updateSubmitButton(stage) {
    const wrapper = document.getElementById('submitLabWrapper');
    const button = document.getElementById('submitLabBtn');

    if (stage === 'actual') {
        wrapper.style.display = 'block';
        button.style.display = 'inline-block';
        button.innerHTML = '<i class="fas fa-save me-1"></i> Save Actual Values';
    } else if (stage === 'final') {
        wrapper.style.display = 'block';
        button.style.display = 'inline-block';
        button.innerHTML = '<i class="fas fa-save me-1"></i> Save Final Values';
    } else {
        wrapper.style.display = 'none';
    }
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
        ['Production ID', 'production_id'],
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
        fields.map(([label, key]) => {
            let value = item[key];

            if (key === 'formlist' && typeof value === 'object' && value !== null) {
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
    } catch (error) {
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
| Save Actual / Final Values
|--------------------------------------------------------------------------
*/

async function submitLabDetails() {
    if (!currentLabItem) {
        alert('Please select a purchase order first.');
        return;
    }

    const stage = getLabStage(currentLabItem);

    if (!['actual', 'final'].includes(stage)) {
        alert('No values available for submission.');
        return;
    }

    if (stage === 'final' && !hasValue(currentLabItem.production_id)) {
        alert('Production ID is not available.');
        return;
    }

    const inputClass = stage === 'actual'
        ? '.lab-actual-input:not([readonly])'
        : '.lab-final-input:not([readonly])';

    const inputs = document.querySelectorAll(
        '#dynamicGradeSections ' + inputClass
    );

    const values = [];

    for (const input of inputs) {
        const rawValue = input.value.trim();

        if (rawValue === '') {
            alert('Please enter all ' + stage + ' values.');
            input.focus();
            return;
        }

        const value = Number(rawValue);
        const propertyId = Number(input.dataset.propertyId);

        if (!Number.isInteger(propertyId) || !Number.isFinite(value) || value < 0) {
            alert('Please enter valid non-negative values.');
            input.focus();
            return;
        }

        values.push({
            property_id: propertyId,
            value: value
        });
    }

    if (!values.length) {
        alert('No editable properties found.');
        return;
    }

    const button = document.getElementById('submitLabBtn');
    const originalHtml = button.innerHTML;

    button.disabled = true;
    button.innerHTML = `
        <span class="spinner-border spinner-border-sm me-1"></span>
        Saving...
    `;

    try {
        const response = await fetch(
            @json(route('lab-results.store')),
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.getElementById('lab_csrf_token').value
                },
                body: JSON.stringify({
                    stage: stage,
                    purchase_order_item_id: Number(currentLabItem.purchase_order_item_id),
                    values: values
                })
            }
        );

        const result = await response.json();

        if (!response.ok) {
            if (response.status === 422 && result.errors) {
                throw new Error(
                    Object.values(result.errors).flat().join('\n')
                );
            }

            throw new Error(result.message || 'Unable to save values.');
        }

        // Update local data after successful database save.
        values.forEach(saved => {
            const property = currentLabItem.grade_reactions.find(
                p => Number(p.property_id) === Number(saved.property_id)
            );

            if (property) {
                if (stage === 'actual') {
                    property.actual_value = saved.value;
                } else {
                    property.final_value = saved.value;
                }
            }
        });

        alert(result.message || 'Values saved successfully.');

        // Re-render based on updated stage.
        renderDynamicGradeSections(currentLabItem);

        // Update listing button data.
        document.querySelectorAll(
            'button[onclick="openLabDetails(this)"]'
        ).forEach(rowButton => {
            try {
                const rowItem = JSON.parse(rowButton.getAttribute('data-item'));

                if (
                    Number(rowItem.purchase_order_item_id) ===
                    Number(currentLabItem.purchase_order_item_id)
                ) {
                    rowItem.grade_reactions = currentLabItem.grade_reactions;

                    rowButton.setAttribute(
                        'data-item',
                        JSON.stringify(rowItem)
                    );
                }
            } catch (error) {
                // Ignore malformed listing data.
            }
        });

    } catch (error) {
        alert(error.message || 'Something went wrong.');
    } finally {
        button.disabled = false;
        button.innerHTML = originalHtml;
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
