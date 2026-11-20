
@extends('layouts.app')

@section('title', 'Dispatch Report | TTC Robotronics')

@section('content')

@php
    // Get Dynamic Company / Branch Details
    $branch = \App\Models\Branch::latest()->first();

    $branchLogo = ($branch && $branch->logo)
        ? asset('storage/' . $branch->logo)
        : '';

    $branchData = [
        'company_name' => $branch->company_name ?? 'Company Name',
        'logo' => $branchLogo,
        'gst' => $branch->gst ?? '',
        'pan' => $branch->pan ?? '',
        'address' => $branch->address ?? '',
        'certificate_desc' => $branch->certificate_desc ?? '',
        'email' => $branch->email ?? '',
        'website' => $branch->website ?? '',
        'phone' => $branch->phone ?? '',
    ];
@endphp


<div class="container-fluid">

    <div class="card shadow-sm">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h4 class="mb-0">Dispatch Report</h4>

            <div>

                <button type="button"
                    class="btn btn-dark btn-sm"
                    style="padding: 2px 5px; font-size: 10px;"
                    onclick="printReport()">

                    <i class="fas fa-print"></i>

                </button>

                <span class="badge bg-primary">

                    Total Records:

                    <span id="totalRecords">
                        {{ $dispatches->count() }}
                    </span>

                </span>

            </div>

        </div>


        <div class="card-body">

            {{-- SEARCH --}}

            <div class="row mb-3">

                <div class="col-md-6 mb-2">

                    <input type="text"
                        id="globalSearch"
                        class="form-control"
                        placeholder="Search Company, Mobile, GST, Truck, Product...">

                </div>

                <div class="col-md-2 mb-2">

                    <button type="button"
                        class="btn btn-secondary w-100"
                        id="resetFilters">

                        Reset

                    </button>

                </div>

            </div>


            {{-- DISPATCH TABLE --}}

            <div class="table-responsive">

                <table class="table table-bordered table-striped table-hover"
                    id="dispatchTable">

                    <thead class="table-dark">

                        <tr>

                            <th>Sr.</th>
                            <th>Company Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>GST No.</th>
                            <th>Truck No.</th>
                            <th>Product</th>
                            <th>Size</th>
                            <th>Grade</th>
                            <th>Item Qty</th>
                            <th>Dispatch Qty</th>
                            <th>Status</th>
                            <th class="no-print">Action</th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($dispatches as $key => $row)

                            <tr>

                                <td>{{ $key + 1 }}</td>

                                <td>{{ $row->company_name }}</td>

                                <td>{{ $row->email }}</td>

                                <td>{{ $row->mobile }}</td>

                                <td>{{ $row->gst_no }}</td>

                                <td>{{ $row->truckno }}</td>

                                <td>{{ $row->product_name }}</td>

                                <td>{{ $row->size_name }}</td>

                                <td>{{ $row->grade_name }}</td>

                                <td class="item-qty">
                                    {{ $row->item_qty }}
                                </td>

                                <td class="dispatch-qty">
                                    {{ $row->dispatch_qty }}
                                </td>

                                <td>

                                    @if($row->status == 1)

                                        <span class="badge bg-success">
                                            Completed
                                        </span>

                                    @elseif($row->status == 0)

                                        <span class="badge bg-warning text-dark">
                                            Pending
                                        </span>

                                    @else

                                        <span class="badge bg-secondary">
                                            {{ $row->status }}
                                        </span>

                                    @endif

                                </td>


                                <td class="no-print">

                                    {{-- DISPATCH SLIP --}}

                                    <button type="button"
                                        class="btn btn-success btn-sm"
                                        style="padding: 3px 7px; font-size: 11px;"
                                        onclick="printSlipwithItems(this)"
                                        title="Print Dispatch Slip">

                                        <i class="fas fa-boxes"></i>
                                        Print

                                    </button>


                                    {{-- ESTIMATE SLIP --}}

                                    <button type="button"
                                        class="btn btn-primary btn-sm"
                                        style="padding: 3px 7px; font-size: 11px;"
                                        onclick="printSlip(this)"
                                        title="Print Estimate Slip">

                                        <i class="fas fa-file-invoice"></i>
                                        Estimate

                                    </button>

                                </td>

                            </tr>

                        @empty

                            <tr class="no-record">

                                <td colspan="13" class="text-center">
                                    No Dispatch Records Found
                                </td>

                            </tr>

                        @endforelse

                    </tbody>


                    <tfoot class="table-dark">

                        <tr>

                            <th colspan="9" class="text-end">
                                Grand Total:
                            </th>

                            <th id="totalItemQty">0</th>

                            <th id="totalDispatchQty">0</th>

                            <th colspan="2"></th>

                        </tr>

                    </tfoot>

                </table>

            </div>

        </div>

    </div>

</div>


{{-- HIDDEN PRINT AREA --}}

<div id="printArea" style="display:none;"></div>



{{-- =========================================================
     CSS
========================================================= --}}

<style>

/* =========================================
   PRINT HEADER
========================================= */

.slip-container {

    width: 100%;
    padding: 10px;
    font-family: Arial, sans-serif;
    color: #000;
    box-sizing: border-box;

}


/* HEADER MAIN */

.slip-header {

    width: 100%;
    position: relative;
    font-family: Arial, sans-serif;
    color: #10233f;
    padding: 48px 5px 5px;
    margin-bottom: 15px;
    box-sizing: border-box;

}


/* GST PAN ROW */

.slip-tax-row {

    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 11px;
    margin-bottom: 25px;
    padding: 0 0 0 0;

}


/* TOP ORANGE LINE */

.slip-top-lines {

    position: relative;
    height: 28px;
    border-top: 2px solid #f79400;
    margin-bottom: 29px;

}


/* GREEN BAR */

.slip-top-lines::after {

    content: "";
    position: absolute;
    left: 0;
    right: 0;
    top: 5px;
    height: 20px;
    background: #064b43;

}


/* CENTER LOGO */

.slip-logo {
    position: absolute;
    width: 80px;
    height: 80px;

    border: 1px solid #25364d;
    border-radius: 50%;

    background: #fff;

    top: 61px;
    left: 50%;

    transform: translateX(-50%);

    z-index: 10;

    display: flex;
    align-items: center;
    justify-content: center;

    overflow: hidden;
}

.slip-logo img {
    width: 90%;
    height: 90%;
    object-fit: contain;
}


/* HEADER CONTENT */

.slip-header-content {

    display: grid;
    grid-template-columns: 30% 40% 30%;
    align-items: center;
    min-height: 78px;
    margin-top: 0;

}


/* LEFT ADDRESS */

.slip-header-left {

    text-align: center;
    font-size: 11px;
    padding: 5px;
    line-height: 1.6;
    overflow-wrap: anywhere;

}


/* CENTER COMPANY */

.slip-header-center {

    text-align: center;
    border-left: 2px solid #f79400;
    border-right: 2px solid #f79400;
    min-height: 75px;
    padding: 5px;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    box-sizing: border-box;

}


.slip-header-center h2 {

    font-size: 17px;
    font-weight: bold;
    color: #064b43;
    margin: 0 0 5px;

}


.slip-header-center p {

    font-size: 10px;
    margin: 2px 0;
    line-height: 1.4;

}


/* RIGHT CONTACT DETAILS */

.slip-header-right {

    text-align: right;
    font-size: 11px;
    padding: 5px;
    line-height: 2;
    overflow-wrap: anywhere;

}


/* BOTTOM LINES */

.slip-bottom-lines {

    margin-top: 5px;
    height: 10px;
    border-top: 3px solid #f79400;
    border-bottom: 3px solid #064b43;
    position: relative;

}


/* =========================================
   SLIP TITLE
========================================= */

.slip-title {

    text-align: center;
    margin: 20px 0;
    font-size: 19px;
    font-weight: bold;
    text-decoration: underline;

}


/* =========================================
   SLIP TABLE
========================================= */

.slip-table {

    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;

}


.slip-table th,
.slip-table td {

    border: 1px solid #000;
    padding: 9px;
    font-size: 12px;
    text-align: left;

}


.slip-table th {

    background: #eee !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;

}


.slip-section {

    margin-top: 20px;
    font-size: 13px;

}


/* SIGNATURE */

.signature-section {

    display: flex;
    justify-content: space-between;
    margin-top: 90px;

}


.signature-section div {

    width: 35%;
    text-align: center;
    border-top: 1px solid #000;
    padding-top: 8px;

}


/* =========================================
   PRINT SETTINGS
========================================= */

@media print {

    @page {

        size: A4 portrait;
        margin: 10mm;

    }

    body * {

        visibility: hidden;

    }

    #printArea,
    #printArea * {

        visibility: visible;

    }

    #printArea {

        display: block !important;
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        background: white;

    }

    .slip-container {

        width: 100%;
        padding: 0;

    }

    .slip-header,
    .slip-header * {

        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;

    }

    .slip-top-lines::after {

        background: #064b43 !important;

    }

    .slip-header-center {

        border-left: 2px solid #f79400 !important;
        border-right: 2px solid #f79400 !important;

    }

    .slip-bottom-lines {

        border-top: 3px solid #f79400 !important;
        border-bottom: 3px solid #064b43 !important;

    }

    .slip-table th {

        background: #eee !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;

    }

    table {

        page-break-inside: auto;

    }

    tr {

        page-break-inside: avoid;

    }

}

</style>



{{-- =========================================================
     JAVASCRIPT
========================================================= --}}

<script>


// ==========================================
// DYNAMIC BRANCH DATA
// ==========================================

const branchData = @json($branchData);


// ==========================================
// ESCAPE HTML
// ==========================================

function escapeHTML(value) {

    return String(value ?? '').replace(/[&<>"']/g, function(char) {

        return {

            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'

        }[char];

    });

}


// ==========================================
// DYNAMIC PRINT HEADER
// ==========================================

function getPrintHeader() {

    const company = branchData;

    let logo = '';

    if (company.logo) {

        logo = `
            <img
                src="${escapeHTML(company.logo)}"
                alt="Company Logo"
            >
        `;

    } else {

        logo = `
            <span style="font-size:25px;font-weight:bold;">
                ${escapeHTML((company.company_name || 'C').substring(0, 1))}
            </span>
        `;

    }


    return `

        <div class="slip-header">


            <!-- GST AND PAN -->

            <div class="slip-tax-row">

                <div>

                    <strong>GSTIN:</strong>

                    ${escapeHTML(company.gst)}

                </div>


                <div>

                    <strong>PAN:</strong>

                    ${escapeHTML(company.pan)}

                </div>

            </div>



            <!-- TOP LINES -->

            <div class="slip-top-lines"></div>



            <!-- CENTER LOGO -->

            <div class="slip-logo">

                ${logo}

            </div>



            <!-- COMPANY INFORMATION -->

            <div class="slip-header-content">


                <!-- LEFT ADDRESS -->

                <div class="slip-header-left">

                    ${escapeHTML(company.address)}

                </div>



                <!-- CENTER COMPANY NAME -->

                <div class="slip-header-center">

                    <h2>

                        ${escapeHTML(company.company_name)}

                    </h2>


                    <p>

                        ${escapeHTML(company.certificate_desc)}

                    </p>

                </div>



                <!-- RIGHT CONTACT DETAILS -->

                <div class="slip-header-right">


                    ${company.email ? `
                        <div>
                            &#9993;
                            ${escapeHTML(company.email)}
                        </div>
                    ` : ''}


                    ${company.website ? `
                        <div>
                            &#127760;
                            ${escapeHTML(company.website)}
                        </div>
                    ` : ''}


                    ${company.phone ? `
                        <div>
                            &#9742;
                            ${escapeHTML(company.phone)}
                        </div>
                    ` : ''}


                </div>


            </div>



            <!-- BOTTOM LINES -->

            <div class="slip-bottom-lines"></div>


        </div>

    `;

}



// ==========================================
// WAIT FOR IMAGES AND PRINT
// ==========================================

function executePrint() {

    const printArea = document.getElementById('printArea');

    const images = Array.from(
        printArea.querySelectorAll('img')
    );

    Promise.all(

        images.map(img => {

            if (img.complete) {

                return Promise.resolve();

            }

            return new Promise(resolve => {

                img.onload = resolve;

                img.onerror = resolve;

            });

        })

    ).then(() => {

        window.print();

    });

}



// ==========================================
// GET SAFE TEXT FROM TABLE CELL
// ==========================================

function getCell(row, index) {

    return row.cells[index].textContent.trim();

}



// ==========================================
// SEARCH AND FILTER
// ==========================================

document.addEventListener('DOMContentLoaded', function () {


    const search = document.getElementById('globalSearch');

    const table = document.getElementById('dispatchTable');

    const tbody = table.querySelector('tbody');


    const rows = Array.from(

        tbody.querySelectorAll('tr')

    ).filter(row => !row.classList.contains('no-record'));



    function updateTable() {


        let itemTotal = 0;

        let dispatchTotal = 0;

        let visibleCount = 0;


        const searchValue = search.value.toLowerCase().trim();


        const searchColumns = [1, 2, 3, 4, 5, 6, 7, 8];


        rows.forEach(row => {


            const match = searchColumns.some(index => {


                const cellValue = row.cells[index].textContent
                    .toLowerCase()
                    .trim();


                return cellValue.includes(searchValue);


            });


            row.style.display = match ? '' : 'none';



            if (match) {


                visibleCount++;


                itemTotal += parseFloat(

                    row.querySelector('.item-qty')
                        .textContent.replace(/,/g, '').trim()

                ) || 0;



                dispatchTotal += parseFloat(

                    row.querySelector('.dispatch-qty')
                        .textContent.replace(/,/g, '').trim()

                ) || 0;


            }


        });



        document.getElementById('totalItemQty').textContent = itemTotal;

        document.getElementById('totalDispatchQty').textContent = dispatchTotal;

        document.getElementById('totalRecords').textContent = visibleCount;



        let noRecord = tbody.querySelector('.no-record');


        if (visibleCount === 0) {


            if (!noRecord) {


                noRecord = document.createElement('tr');

                noRecord.classList.add('no-record');


                noRecord.innerHTML = `

                    <td colspan="13" class="text-center">

                        No Dispatch Records Found

                    </td>

                `;


                tbody.appendChild(noRecord);


            }


        } else {


            if (noRecord) {

                noRecord.remove();

            }


        }


    }



    search.addEventListener('input', updateTable);



    document.getElementById('resetFilters')
        .addEventListener('click', function () {


            search.value = '';

            updateTable();


        });



    updateTable();


});



// ==========================================
// PRINT INDIVIDUAL ESTIMATE SLIP
// ==========================================

function printSlip(button) {


    const row = button.closest('tr');


    const company = getCell(row, 1);

    const email = getCell(row, 2);

    const mobile = getCell(row, 3);

    const gst = getCell(row, 4);

    const truck = getCell(row, 5);

    const product = getCell(row, 6);

    const size = getCell(row, 7);

    const grade = getCell(row, 8);

    const itemQty = getCell(row, 9);

    const dispatchQty = getCell(row, 10);

    const status = getCell(row, 11);



    const printArea = document.getElementById('printArea');


    printArea.innerHTML = `


        <div class="slip-container">


            ${getPrintHeader()}



            <div class="slip-title">

                ESTIMATE / DISPATCH SLIP

            </div>



            <table class="slip-table">


                <tr>

                    <th>Company Name</th>

                    <td>${escapeHTML(company)}</td>

                </tr>


                <tr>

                    <th>Email</th>

                    <td>${escapeHTML(email)}</td>

                </tr>


                <tr>

                    <th>Mobile</th>

                    <td>${escapeHTML(mobile)}</td>

                </tr>


                <tr>

                    <th>GST Number</th>

                    <td>${escapeHTML(gst)}</td>

                </tr>


                <tr>

                    <th>Truck Number</th>

                    <td>${escapeHTML(truck)}</td>

                </tr>


            </table>



            <h4 style="margin-top:25px;">

                Product Details

            </h4>



            <table class="slip-table">


                <thead>

                    <tr>

                        <th>Product</th>

                        <th>Size</th>

                        <th>Grade</th>

                        <th>Item Qty</th>

                        <th>Dispatch Qty</th>

                    </tr>

                </thead>



                <tbody>

                    <tr>

                        <td>${escapeHTML(product)}</td>

                        <td>${escapeHTML(size)}</td>

                        <td>${escapeHTML(grade)}</td>

                        <td>${escapeHTML(itemQty)}</td>

                        <td>${escapeHTML(dispatchQty)}</td>

                    </tr>

                </tbody>


            </table>



            <div class="slip-section">


                <p>

                    <strong>Dispatch Status:</strong>

                    ${escapeHTML(status)}

                </p>


                <p>

                    <strong>Date:</strong>

                    ${new Date().toLocaleDateString('en-IN')}

                </p>


            </div>



            <div class="signature-section">


                <div>

                    Prepared By

                </div>


                <div>

                    Authorized Signature

                </div>


            </div>


        </div>


    `;


    executePrint();


}



// ==========================================
// PRINT COMPLETE FILTERED REPORT
// ==========================================

function printReport() {


    const rows = Array.from(

        document.querySelectorAll('#dispatchTable tbody tr')

    ).filter(row =>

        !row.classList.contains('no-record') &&

        row.style.display !== 'none'

    );



    if (rows.length === 0) {

        alert('No records available to print!');

        return;

    }



    let itemTotal = 0;

    let dispatchTotal = 0;

    let tableRows = '';



    rows.forEach((row, index) => {


        const itemQty = parseFloat(

            getCell(row, 9).replace(/,/g, '')

        ) || 0;



        const dispatchQty = parseFloat(

            getCell(row, 10).replace(/,/g, '')

        ) || 0;



        itemTotal += itemQty;

        dispatchTotal += dispatchQty;



        tableRows += `


            <tr>

                <td>${index + 1}</td>

                <td>${escapeHTML(getCell(row, 1))}</td>

                <td>${escapeHTML(getCell(row, 3))}</td>

                <td>${escapeHTML(getCell(row, 5))}</td>

                <td>${escapeHTML(getCell(row, 6))}</td>

                <td>${escapeHTML(getCell(row, 7))}</td>

                <td>${escapeHTML(getCell(row, 8))}</td>

                <td>${escapeHTML(getCell(row, 9))}</td>

                <td>${escapeHTML(getCell(row, 10))}</td>

                <td>${escapeHTML(getCell(row, 11))}</td>

            </tr>


        `;


    });



    const printArea = document.getElementById('printArea');



    printArea.innerHTML = `


        <div class="slip-container">


            ${getPrintHeader()}



            <div class="slip-title">

                DISPATCH REPORT

            </div>



            <p>

                <strong>Report Date:</strong>

                ${new Date().toLocaleDateString('en-IN')}

            </p>



            <table class="slip-table">


                <thead>

                    <tr>

                        <th>Sr.</th>

                        <th>Company</th>

                        <th>Mobile</th>

                        <th>Truck</th>

                        <th>Product</th>

                        <th>Size</th>

                        <th>Grade</th>

                        <th>Item Qty</th>

                        <th>Dispatch Qty</th>

                        <th>Status</th>

                    </tr>

                </thead>



                <tbody>

                    ${tableRows}

                </tbody>



                <tfoot>

                    <tr>

                        <th colspan="7">Grand Total</th>

                        <th>${itemTotal}</th>

                        <th>${dispatchTotal}</th>

                        <th></th>

                    </tr>

                </tfoot>


            </table>


        </div>


    `;


    executePrint();


}



// ==========================================
// PRINT COMPLETE DISPATCH SLIP
// ==========================================

function printSlipwithItems(button) {


    const row = button.closest('tr');


    const company = getCell(row, 1);

    const email = getCell(row, 2);

    const mobile = getCell(row, 3);

    const gst = getCell(row, 4);

    const truck = getCell(row, 5);

    const product = getCell(row, 6);

    const size = getCell(row, 7);

    const grade = getCell(row, 8);

    const itemQty = getCell(row, 9);

    const dispatchQty = getCell(row, 10);

    const status = getCell(row, 11);



    const printArea = document.getElementById('printArea');



    printArea.innerHTML = `


        <div class="slip-container">


            ${getPrintHeader()}



            <div class="slip-title">

                DISPATCH SLIP

            </div>



            <p>

                <strong>Date:</strong>

                ${new Date().toLocaleDateString('en-IN')}

            </p>



            <table class="slip-table">


                <tr>

                    <th>Company Name</th>

                    <td>${escapeHTML(company)}</td>

                </tr>


                <tr>

                    <th>Email</th>

                    <td>${escapeHTML(email)}</td>

                </tr>


                <tr>

                    <th>Mobile</th>

                    <td>${escapeHTML(mobile)}</td>

                </tr>


                <tr>

                    <th>GST Number</th>

                    <td>${escapeHTML(gst)}</td>

                </tr>


                <tr>

                    <th>Truck Number</th>

                    <td>${escapeHTML(truck)}</td>

                </tr>


            </table>



            <h4 style="margin-top:25px;">

                Product Details

            </h4>



            <table class="slip-table">


                <thead>

                    <tr>

                        <th>Product</th>

                        <th>Size</th>

                        <th>Grade</th>

                        <th>Item Qty</th>

                        <th>Dispatch Qty</th>

                    </tr>

                </thead>



                <tbody>

                    <tr>

                        <td>${escapeHTML(product)}</td>

                        <td>${escapeHTML(size)}</td>

                        <td>${escapeHTML(grade)}</td>

                        <td>${escapeHTML(itemQty)}</td>

                        <td>${escapeHTML(dispatchQty)}</td>

                    </tr>

                </tbody>


            </table>



            <div class="slip-section">


                <p>

                    <strong>Dispatch Status:</strong>

                    ${escapeHTML(status)}

                </p>


            </div>



            <div class="signature-section">


                <div>

                    Prepared By

                </div>


                <div>

                    Received By

                </div>


                <div>

                    Authorized Signature

                </div>


            </div>


        </div>


    `;


    executePrint();


}


</script>


@endsection
