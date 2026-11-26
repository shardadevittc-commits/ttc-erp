
@extends('layouts.app')

@section('title', 'Raw Materials Print View')

@section('content')

<style>
    * {
        box-sizing: border-box;
    }

    .report-wrapper {
        max-width: 1100px;
        margin: 15px auto;
        font-family: Arial, sans-serif;
    }

    .report-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .report-toolbar h4 {
        font-size: 20px;
        font-weight: 700;
        color: #183653;
        margin: 0;
    }

    .print-btn {
        background: #16834a;
        color: #fff;
        border: none;
        padding: 10px 22px;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
    }

    .report-card {
        background: #fff;
        border: 1px solid #dce3eb;
        border-radius: 7px;
        overflow: hidden;
    }

    /* Header */

    .report-header {
        background: #183653;
        color: white;
        padding: 20px 28px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .report-header h2 {
        font-size: 21px;
        font-weight: 800;
        margin: 0 0 5px;
        letter-spacing: .5px;
    }

    .report-header p {
        font-size: 11px;
        color: #d6e4f1;
        margin: 0;
    }

    .report-label {
        border: 1px solid #a9c2d9;
        padding: 7px 13px;
        font-size: 11px;
        font-weight: 700;
        border-radius: 4px;
    }

    .report-body {
        padding: 22px 28px;
    }

    /* Details */

    .section-title {
        font-size: 14px;
        font-weight: 800;
        color: #183653;
        border-bottom: 2px solid #e5ebf1;
        padding-bottom: 8px;
        margin-bottom: 13px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-bottom: 18px;
    }

    .info-item {
        background: #f5f8fb;
        border: 1px solid #e4eaf0;
        border-left: 3px solid #3578bd;
        padding: 10px 13px;
        border-radius: 4px;
    }

    .info-item label {
        display: block;
        font-size: 10px;
        color: #748398;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .info-item .value {
        color: #24364b;
        font-size: 13px;
        font-weight: 700;
        overflow-wrap: anywhere;
    }

    /* Quantity */

    .quantity-box {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #edf7f1;
        border: 1px solid #cce8d6;
        border-left: 4px solid #16834a;
        border-radius: 5px;
        padding: 11px 16px;
        margin-bottom: 20px;
    }

    .quantity-box span {
        font-size: 12px;
        color: #537361;
        font-weight: 700;
        text-transform: uppercase;
    }

    .quantity-box strong {
        font-size: 19px;
        color: #16834a;
    }

    /* Composition */

    .composition-heading {
        font-size: 14px;
        font-weight: 800;
        color: #183653;
        border-bottom: 2px solid #e5ebf1;
        padding-bottom: 8px;
        margin-bottom: 12px;
    }

    .composition-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        margin-bottom: 15px;
    }

    .composition-table thead {
        display: table-header-group;
    }

    .composition-table thead th {
        background: #183653;
        color: #fff;
        padding: 9px 10px;
        font-size: 11px;
        text-transform: uppercase;
        border: 1px solid #183653;
        text-align: left;
        overflow-wrap: anywhere;
    }

    .composition-table tbody td {
        padding: 8px 10px;
        border: 1px solid #dfe6ed;
        font-size: 12px;
        color: #35465a;
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .composition-table tbody tr:nth-child(even) {
        background: #f3f7fb;
    }

    .sr-badge {
        background: #e8f1fa;
        color: #183653;
        padding: 3px 7px;
        border-radius: 3px;
        font-weight: 700;
    }

    .property-value {
        color: #16834a;
        font-weight: 800;
    }

    .no-data {
        padding: 18px;
        text-align: center;
        background: #f8fafc;
        border: 1px dashed #ccd5df;
        color: #8290a0;
        font-size: 13px;
        margin-bottom: 15px;
    }

    /* Signatures */

    .signature-section {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 35px;
        margin-top: 45px;
        padding: 20px 28px 25px;
        page-break-inside: avoid;
        break-inside: avoid;
    }

    .signature-box {
        text-align: center;
        font-size: 12px;
        color: #34445a;
        font-weight: 700;
    }

    .signature-line {
        border-top: 1px solid #8793a0;
        margin-top: 45px;
        margin-bottom: 8px;
    }

    .report-footer {
        text-align: center;
        background: #f4f7fa;
        border-top: 1px solid #e1e7ee;
        padding: 9px;
        font-size: 10px;
        color: #8190a0;
    }

    @media(max-width: 600px) {
        .info-grid {
            grid-template-columns: 1fr;
        }

        .report-body {
            padding: 15px;
        }

        .report-label {
            display: none;
        }
    }

    /* PRINT */

    @media print {

        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        html, body {
            width: 100%;
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
        }

        .no-print,
        header,
        nav,
        .sidebar,
        .main-sidebar,
        .main-header,
        footer {
            display: none !important;
        }

        .container-fluid {
            padding: 0 !important;
            margin: 0 !important;
        }

        .report-wrapper {
            width: 100%;
            max-width: 100%;
            margin: 0;
        }

        .report-card {
            border: none;
            border-radius: 0;
            overflow: visible;
            box-shadow: none;
        }

        .report-header,
        .composition-table thead th,
        .info-item,
        .quantity-box,
        .report-footer {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .report-body {
            padding: 15px 10px;
        }

        .composition-table {
            width: 100%;
            page-break-inside: auto;
        }

        .composition-table thead {
            display: table-header-group;
        }

        .composition-table tbody tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .info-grid,
        .quantity-box {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .signature-section {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .report-footer {
            page-break-inside: avoid;
        }
    }

</style>

<div class="container-fluid">

    <div class="report-wrapper">

        {{-- Toolbar --}}

        <div class="report-toolbar no-print">

            <h4>Raw Material Composition</h4>

            <button type="button"
                    class="print-btn"
                    onclick="window.print()">

                <i class="fa fa-print"></i> Print Report

            </button>

        </div>

        <div class="report-card">

            {{-- Header --}}

            <div class="report-header">

                <div>
                    <h2>RAW MATERIAL REPORT</h2>
                    <p>Material Details & Composition Analysis</p>
                </div>

                <div class="report-label">
                    QUALITY REPORT
                </div>

            </div>

            {{-- Report Body --}}

            <div class="report-body">

                {{-- Material Details --}}

                <div class="section-title">
                    <i class="fa fa-cubes"></i>
                    Raw Material Details
                </div>

                <div class="info-grid">

                    <div class="info-item">
                        <label>Company Name</label>
                        <div class="value">
                            {{ $unloadWeight->company_name ?? '-' }}
                        </div>
                    </div>

                    <div class="info-item">
                        <label>Product Name</label>
                        <div class="value">
                            {{ $unloadWeight->product_name ?? '-' }}
                        </div>
                    </div>

                    <div class="info-item">
                        <label>Grade Name</label>
                        <div class="value">
                            {{ $unloadWeight->grade_name ?? '-' }}
                        </div>
                    </div>

                    <div class="info-item">
                        <label>Size Name</label>
                        <div class="value">
                            {{ $unloadWeight->size_name ?? '-' }}
                        </div>
                    </div>

                    {{-- TRUCK NUMBER ADDED --}}

                    <div class="info-item">
                        <label>Truck Number</label>
                        <div class="value">
                            {{ $unloadWeight->truckno ?? '-' }}
                        </div>
                    </div>

                </div>

                {{-- Received Quantity --}}

                <div class="quantity-box">

                    <span>
                        <i class="fa fa-balance-scale"></i>
                        Received Quantity
                    </span>

                    <strong>
                        {{ $unloadWeight->received_qty ?? '0' }}
                    </strong>

                </div>

                {{-- Composition / Lab Results --}}

                <div class="composition-heading">
                    <i class="fa fa-flask"></i>
                    Composition / Lab Results
                </div>

                @if($labResults->count() > 0)

                    <table class="composition-table">

                        <thead>
                            <tr>
                                <th width="12%">Sr. No.</th>
                                <th width="43%">Property Name</th>
                                <th width="20%">Unit</th>
                                <th width="25%">Actual Value</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($labResults as $key => $result)

                                <tr>

                                    <td>
                                        <span class="sr-badge">
                                            {{ $key + 1 }}
                                        </span>
                                    </td>

                                    <td>
                                        <strong>
                                            {{ $result->property_name ?? '-' }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ $result->unit ?? '-' }}
                                    </td>

                                    <td>
                                        <span class="property-value">
                                            {{ $result->actual_value ?? '-' }}
                                        </span>
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                @else

                    <div class="no-data">
                        <i class="fa fa-info-circle"></i>
                        No Composition / Lab Results Found.
                    </div>

                @endif

            </div>

            {{-- SIGNATURE SECTION --}}

            <div class="signature-section">

                <div class="signature-box">
                    <div class="signature-line"></div>
                    Prepared By
                </div>

                <div class="signature-box">
                    <div class="signature-line"></div>
                    Checked By
                </div>

                <div class="signature-box">
                    <div class="signature-line"></div>
                    Approved By
                </div>

            </div>

            {{-- Footer --}}

            <div class="report-footer">
                Raw Material Quality & Composition Report
            </div>

        </div>

    </div>

</div>

@endsection
