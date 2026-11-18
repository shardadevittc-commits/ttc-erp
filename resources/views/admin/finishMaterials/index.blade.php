
@extends('layouts.app')

@section('title', 'Manage Finished Materials')

@section('content')

<style>
    .summary-card {
        border-radius: 12px;
        border: none;
        color: white;
        box-shadow: 0 3px 10px rgba(0,0,0,0.10);
    }

    .summary-card h6 {
        font-size: 14px;
        margin-bottom: 12px;
    }

    .summary-card h3 {
        font-size: 26px;
        font-weight: 700;
    }

    .filter-box {
        border-radius: 8px;
        height: 42px;
    }

    #finishedMaterialsTable th {
        white-space: nowrap;
    }

    @media print {
        @page {
            size: landscape;
            margin: 8mm;
        }

        .no-print,
        .main-header,
        .main-sidebar,
        .main-footer,
        nav,
        aside {
            display: none !important;
        }

        .container-fluid {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }

        .card-header {
            display: block !important;
            text-align: center;
            background: white !important;
            color: black !important;
        }

        .summary-card {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }

        .table-responsive {
            overflow: visible !important;
        }

        table {
            width: 100% !important;
            font-size: 9px !important;
            border-collapse: collapse !important;
        }

        table th,
        table td {
            padding: 5px !important;
            border: 1px solid #555 !important;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        .table-dark {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }
    }
</style>

<div class="container-fluid">

    @php
        $totalFullBundleCount = $data->sum('total_full_bundle_count');
        $totalLoseBundleWeight = $data->sum('lose_bundle_weight');
        $totalLoseBundleCount = $data->sum('lose_bundle_count');
    @endphp

    <div class="card">

        {{-- Header --}}
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Manage Finished Materials</h4>

            <button type="button" class="btn btn-dark no-print"
                onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>
        </div>

        <div class="card-body">

            {{-- Summary Cards --}}
            <div class="row mb-4">

                <div class="col-md-4 mb-3">
                    <div class="card summary-card"
                        style="background: #344B67;">
                        <div class="card-body">
                            <h6>Total Full Bundle Count</h6>
                            <h3>
                                {{ number_format($totalFullBundleCount, 2) }}
                            </h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card summary-card"
                        style="background: #087E8B;">
                        <div class="card-body">
                            <h6>Total Lose Bundle Weight</h6>
                            <h3>
                                {{ number_format($totalLoseBundleWeight, 2) }}
                            </h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card summary-card"
                        style="background: #6254A5;">
                        <div class="card-body">
                            <h6>Total Lose Bundle Count</h6>
                            <h3>
                                {{ number_format($totalLoseBundleCount, 2) }}
                            </h3>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Search Filters --}}
            <div class="row mb-3 no-print">

                <div class="col-md-4 mb-2">
                    <label>Search Customer</label>
                    <input type="text"
                        id="customerFilter"
                        class="form-control filter-box"
                        placeholder="Type customer name...">
                </div>

                <div class="col-md-4 mb-2">
                    <label>Search Grade</label>
                    <input type="text"
                        id="gradeFilter"
                        class="form-control filter-box"
                        placeholder="Type grade name...">
                </div>

                <div class="col-md-4 mb-2">
                    <label>Search Product</label>
                    <input type="text"
                        id="productFilter"
                        class="form-control filter-box"
                        placeholder="Type product name...">
                </div>

                <div class="col-md-12 mt-2">

                    <button type="button"
                        class="btn btn-secondary btn-sm"
                        onclick="clearFilters()">
                        <i class="fas fa-sync"></i> Clear Filters
                    </button>

                    <span class="ml-3">
                        Records:
                        <strong id="recordCount">{{ $data->count() }}</strong>
                    </span>

                </div>

            </div>

            {{-- Data Table --}}
            <div class="table-responsive">

                <table class="table table-bordered table-striped"
                    id="finishedMaterialsTable">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Order Qty</th>
                            <th>Basic Rate</th>
                            <th>Grade</th>
                            <th>Product</th>
                            <th>Size</th>
                            <th>Material Send For Work</th>
                            <th>FS Bundle Weight</th>
                            <th>FS Bundle Unit</th>
                            <th>Total Full Bundle Count</th>
                            <th>Lose Bundle Weight</th>
                            <th>Lose Bundle Count</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($data as $key => $row)

                            <tr class="data-row">

                                <td>{{ $key + 1 }}</td>

                                <td class="customer-name">
                                    {{ $row->company_name }}
                                </td>

                                <td>{{ $row->order_qty }}</td>

                                <td>{{ $row->basic_rate }}</td>

                                <td class="grade-name">
                                    {{ $row->grade_name }}
                                </td>

                                <td class="product-name">
                                    {{ $row->product_name }}
                                </td>

                                <td>{{ $row->size_name }}</td>

                                <td>{{ $row->material_send_for_work }}</td>

                                <td>{{ $row->fs_bundle_weight }}</td>

                                <td>{{ $row->fs_bundle_unit }}</td>

                                <td class="full-bundle">
                                    {{ $row->total_full_bundle_count }}
                                </td>

                                <td class="lose-weight">
                                    {{ $row->lose_bundle_weight }}
                                </td>

                                <td class="lose-count">
                                    {{ $row->lose_bundle_count }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="13" class="text-center text-danger">
                                    No Finished Materials Found
                                </td>
                            </tr>

                        @endforelse

                        {{-- Dynamic No Records Row --}}
                        <tr id="noRecordRow" style="display: none;">
                            <td colspan="13"
                                class="text-center text-danger font-weight-bold">
                                No Records Found
                            </td>
                        </tr>

                    </tbody>

                    {{-- Grand Total --}}
                    <tfoot>
                        <tr class="table-dark">
                            <th colspan="10" class="text-right">
                                Grand Total
                            </th>

                            <th id="grandFullBundle">
                                {{ number_format($totalFullBundleCount, 2) }}
                            </th>

                            <th id="grandLoseWeight">
                                {{ number_format($totalLoseBundleWeight, 2) }}
                            </th>

                            <th id="grandLoseCount">
                                {{ number_format($totalLoseBundleCount, 2) }}
                            </th>
                        </tr>
                    </tfoot>

                </table>

            </div>

        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const customerFilter = document.getElementById('customerFilter');
    const gradeFilter = document.getElementById('gradeFilter');
    const productFilter = document.getElementById('productFilter');

    const table = document.getElementById('finishedMaterialsTable');
    const tbody = table.querySelector('tbody');

    const noRecordRow = document.getElementById('noRecordRow');

    function applyFilters() {

        let customer = customerFilter.value.toLowerCase().trim();
        let grade = gradeFilter.value.toLowerCase().trim();
        let product = productFilter.value.toLowerCase().trim();

        let rows = tbody.querySelectorAll('tr.data-row');

        let count = 0;

        let fullBundleTotal = 0;
        let loseWeightTotal = 0;
        let loseCountTotal = 0;

        rows.forEach(function(row) {

            let customerName = row.querySelector('.customer-name').textContent.toLowerCase().trim();

            let gradeName = row.querySelector('.grade-name').textContent.toLowerCase().trim();

            let productName = row.querySelector('.product-name').textContent.toLowerCase().trim();

            let matchCustomer = customerName.includes(customer);
            let matchGrade = gradeName.includes(grade);
            let matchProduct = productName.includes(product);

            if (matchCustomer && matchGrade && matchProduct) {

                row.style.display = '';
                count++;

                fullBundleTotal += parseFloat(row.querySelector('.full-bundle').textContent.replace(/,/g, '')) || 0;

                loseWeightTotal += parseFloat(row.querySelector('.lose-weight').textContent.replace(/,/g, '')) || 0;

                loseCountTotal += parseFloat(row.querySelector('.lose-count').textContent.replace(/,/g, '')) || 0;

            } else {

                row.style.display = 'none';

            }

        });

        // Update Record Count
        document.getElementById('recordCount').textContent = count;

        // Update Grand Totals
        document.getElementById('grandFullBundle').textContent =
            fullBundleTotal.toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

        document.getElementById('grandLoseWeight').textContent =
            loseWeightTotal.toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

        document.getElementById('grandLoseCount').textContent =
            loseCountTotal.toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

        // Show / Hide No Records Found
        if (count === 0) {

            noRecordRow.style.display = '';

        } else {

            noRecordRow.style.display = 'none';

        }

    }

    // Live Search
    customerFilter.addEventListener('input', applyFilters);
    gradeFilter.addEventListener('input', applyFilters);
    productFilter.addEventListener('input', applyFilters);

    // Clear Filters
    window.clearFilters = function() {

        customerFilter.value = '';
        gradeFilter.value = '';
        productFilter.value = '';

        applyFilters();

    };

});
</script>

@endsection
