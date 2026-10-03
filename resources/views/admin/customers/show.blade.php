@extends('layouts.app')

@section('title', 'Customer | ' . ($customer->company_name ?: $customer->cust_code))

@section('content')
<div class="page-header">
    <div class="page-header__inner">
        <div class="page-header__content"><div class="page-header__title"><h6>Customer Details</h6></div></div>
        <div class="page-header__actions">
            <a href="{{ route('customers.edit', ['id' => $customer->id]) }}" class="btn btn-outline-primary"><i class="far fa-edit me-1"></i> Edit</a>
            <a href="{{ route('customers') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Back</a>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header"><h5 class="mb-0">Customer Information</h5></div>
        <div class="table-responsive">
            <table class="table mb-0">
                <tbody>
                    <tr><th>Customer Code</th><td>{{ $customer->cust_code ?: '-' }}</td><th>Party Name</th><td>{{ $customer->company_name ?: '-' }}</td></tr>
                    <tr><th>GST Number</th><td>{{ $customer->gst_no ?: '-' }}</td><th>Email</th><td>{{ $customer->email ?: '-' }}</td></tr>
                    <tr><th>Contact</th><td>{{ trim(($customer->country_code ? '+' . $customer->country_code . ' ' : '') . ($customer->mobile ?? '')) ?: '-' }}</td><th>Country</th><td>{{ $customer->country?->name ?: '-' }}</td></tr>
                    <tr><th>State</th><td>{{ $customer->state?->name ?: '-' }}</td><th>City</th><td>{{ $customer->city?->name ?: '-' }}</td></tr>
                    <tr><th>Pincode</th><td>{{ $customer->pincode ?: '-' }}</td><th>Party Type</th><td>{{ $customer->buyer === 1 ? 'Buyer' : '' }}{{ $customer->buyer === 1 && $customer->supplier === 1 ? ' / ' : '' }}{{ $customer->supplier === 1 ? 'Supplier' : '' }}{{ $customer->buyer !== 1 && $customer->supplier !== 1 ? '-' : '' }}</td></tr>
                    <tr><th>Address</th><td colspan="3">{{ $customer->address ?: '-' }}</td></tr>
                    <tr><th>Status</th><td colspan="3">{{ $customer->status === 1 ? 'Active' : 'Inactive' }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header"><h5 class="mb-0">GST Verification Details</h5></div>
        @if($customer->gst_details)
            <div class="table-responsive">
                <table class="table mb-0">
                    <tbody>
                        @foreach($customer->gst_details as $key => $value)
                            <tr>
                                <th>{{ \Illuminate\Support\Str::headline($key) }}</th>
                                <td>{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : ($value === null || $value === '' ? '-' : $value) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="card-body text-muted">GST details have not been verified.</div>
        @endif
    </div>
</div>
@endsection