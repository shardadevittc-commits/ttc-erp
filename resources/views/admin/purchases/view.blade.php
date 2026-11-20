@extends('layouts.app')

@section('title', 'View Purchase Orders | TTC Robotronics - Steel Industry ERP')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Purchase Order Details</h4>
            <p class="text-muted mb-0">View purchase order and reference sale order information</p>
        </div>

        <div>
            <a href="{{ route('purchase') }}"
               class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    {{-- Purchase Order Details --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Purchase Order Information</h5>
        </div>

        <div class="card-body">
            <div class="row">

                <div class="col-md-3 mb-3">
                    <label class="text-muted">Purchase Order ID</label>
                    <h6>#{{ $purchaseOrders->id }}</h6>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="text-muted">Reference Sale Order</label>
                    <h6>#{{ $saleOrder->id ?? '-' }}</h6>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="text-muted">Basic Rate</label>
                    <h6>₹{{ number_format($purchaseOrders->b_rate, 2) }}</h6>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="text-muted">Basis</label>
                    <h6>{{ $purchaseOrders->basis ?? '-' }}</h6>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="text-muted">Payment / Delivery Term</label>
                    <h6>{{ $purchaseOrders->term ?? '-' }}</h6>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="text-muted">Created Date</label>
                    <h6>
                        {{ $purchaseOrders->created_at
                            ? $purchaseOrders->created_at->format('d-m-Y')
                            : '-' }}
                    </h6>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="text-muted">Remarks</label>
                    <h6>{{ $purchaseOrders->remarks ?? '-' }}</h6>
                </div>

            </div>
        </div>
    </div>


    {{-- Customer Details --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Customer Details (From Sale Order)</h5>
        </div>

        <div class="card-body">

            @if($customer)

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label class="text-muted">Customer Code</label>
                        <h6>{{ $customer->cust_code ?? '-' }}</h6>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="text-muted">Company Name</label>
                        <h6>{{ $customer->company_name ?? '-' }}</h6>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="text-muted">Email</label>
                        <h6>{{ $customer->email ?? '-' }}</h6>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="text-muted">Mobile</label>
                        <h6>{{ $customer->mobile ?? '-' }}</h6>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="text-muted">GST Number</label>
                        <h6>{{ $customer->gst_no ?? '-' }}</h6>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="text-muted">Address</label>
                        <h6>{{ $customer->address ?? '-' }}</h6>
                    </div>

                </div>

            @else
                <p class="text-danger mb-0">Customer details not found.</p>
            @endif

        </div>
    </div>


    {{-- Purchase Order Items --}}
    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Purchase Order Items</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Grade</th>
                            <th>Size</th>
                            <th>Unit</th>
                            <th>Length</th>
                            <th>Rate (₹)</th>
                            <th>Quantity</th>
                            <th>Variation</th>
                            <th>Amount (₹)</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>

                    <tbody>

                        @php
                            $grandTotal = 0;
                        @endphp

                        @forelse($purchaseOrders->items as $key => $item)

                            @php
                                $amount = (float) $item->rate * (float) $item->qty;
                                $grandTotal += $amount;
                            @endphp

                            <tr>
                                <td>{{ $key + 1 }}</td>

                                <td>
                                    {{ $item->product->product_name ?? '-' }}
                                </td>

                                <td>
                                    {{ $item->grade->grade_name ?? '-' }}
                                </td>

                                <td>
                                    {{ $item->size->size_name ?? '-' }}
                                </td>

                                <td>
                                    {{ $item->sizeUnit->short_name ?? '-' }}
                                </td>

                                <td>
                                    {{ $item->length ?? '-' }}
                                </td>

                                <td>
                                    {{ number_format($item->rate, 2) }}
                                </td>

                                <td>
                                    {{ $item->qty }}
                                    {{ $item->qty_unit }}
                                </td>

                                <td>
                                    {{ $item->qty_varaiate ?? '-' }}
                                </td>

                                <td>
                                    {{ number_format($amount, 2) }}
                                </td>

                                <td>
                                    {{ $item->remarks ?? '-' }}
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="11" class="text-center">
                                    No items found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="9" class="text-end">
                                Grand Total
                            </th>

                            <th colspan="2">
                                ₹{{ number_format($grandTotal, 2) }}
                            </th>
                        </tr>
                    </tfoot>

                </table>

            </div>

        </div>
    </div>

</div>

@endsection
