@extends('layouts.app')

@section('title', 'Sale Order #' . $saleOrder->id)

@section('content')
<div class="page-header">
    <div class="page-header__inner">
        <div class="page-header__content">
            <div class="page-header__title">
                <h6>Sale Order #{{ $saleOrder->id }}</h6>
            </div>
        </div>
        <div class="page-header__actions d-flex gap-2">
            <a href="{{ route('saleOrders.edit', ['id' => $saleOrder->id]) }}" class="btn btn-outline-primary">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('saleOrders') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>
</div>

<div class="content_area">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">Order Details</h5></div>
                    <div class="card-body p-0">
						<div class="table-responsive text-nowrap">
							<table class="table">
								<tbody>
									<tr>
										<th>Order No</th>
										<td>{{ $saleOrder->id ?? '-' }}</td>
									</tr>
                                    <tr>
										<th>Order Date</th>
										<td>{{ _d($saleOrder->created_at) ?? '-' }}</td>
									</tr>
                                    <tr>
										<th>Payment Term</th>
										<td>{{ $saleOrder->payment_term ?: '-' }}</td>
									</tr>
                                    <tr>
										<th>Total Order Qty</th>
										<td>{{  $saleOrder->order_qty ?? '-' }}</td>
									</tr>
									<tr>
										<th>Basic Rate</th>
										<td>{{ $saleOrder->basic_rate ?? '-' }}</td>
									</tr>
                                    <tr>
										<th>Remarks</th>
										<td>{{ $saleOrder->remarks ?: '-' }}</td>
									</tr>									
								</tbody>
							</table>
						</div>
					</div>
                </div>
            </div>
            <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
				<!-- ==== Party Details -->
				<div class="card">
					<div class="card-header view_header">
			        	<div class="heading">
							<h5 class="mb-0">Party Details</h5>
						</div>
					</div>
					<div class="card-body p-0">
                        <div class="article-content container-max p-2 small">
                            <h6>{{ $saleOrder->customer?->company_name ?? '-' }}</h6>
                            @if ($saleOrder->customer?->address)
                                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode(strip_tags($saleOrder->customer->address)) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-dark text-decoration-none">
                                    {!! $saleOrder->customer->address !!}
                                    <i class="bi bi-geo-alt"></i>
                                </a>
                            @endif
                        </div>
						<div class="table-responsive text-nowrap">
							<table class="table">
								<tbody>
									<tr>
										<th>Contact Person</th>
										<td>
											{{ _dt($saleOrder->created_at) }}
										</td>
									</tr>
									<tr>
										<th>Designation</th>
										<td>
											{{ $saleOrder->customer->updated_at }}
										</td>
									</tr>
									<tr>
										<th>Contact No.</th>
										<td>
											{{ isset($saleOrder->customer->owner) ? $saleOrder->customer->owner->first_name . ' ' . $saleOrder->customer->owner->last_name : "" }}
										</td>
									</tr>
                                    <tr>
										<th>Email</th>
										<th>
											{!! $saleOrder->customer->status ? '<span class="badge bg-success">Publish</span>' : '<span class="badge bg-danger">Unpublish</span>' !!}
										</th>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0">Sale Order Items</h5></div>
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Grade</th>
                            <th>Brand</th>
                            <th>Size</th>
                            <th>Size / Unit</th>
                            <th>Quantity</th>
                            <th>Size Extra</th>
                            <th>Rate</th>
                            <th>Dispatch Qty</th>
                            <th>Dispatched Qty</th>
                            <th>Pending Qty</th>
                            <th>Completion</th>
                            <th>Straightening</th>
                            <th>Point Discard</th>
                            <th>Phosphating</th>
                            <th>Hardness</th>
                            <th>Hardness Unit</th>
                            <th>Length Unit</th>
                            <th>Coating</th>
                            <th>FS Bundle Weight</th>
                            <th>FS Bundle Unit</th>
                            <th>Product Type</th>
                            <th>Item Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($saleOrder->items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->product?->product_name ?? '-' }}</td>
                                <td>{{ $item->product?->product_category ? ucfirst(str_replace('_', ' ', $item->product->product_category)) : '-' }}</td>
                                <td>{{ $item->grade?->grade_name ?? '-' }}</td>
                                <td>{{ $item->brand?->brand_name ?? '-' }}</td>
                                <td>{{ $item->size?->size_name ?? '-' }}</td>
                                <td>{{ $item->size_unit ?: '-' }}</td>
                                <td>{{ $item->item_qty ?? '-' }} {{ $item->qty_type === 1 ? 'Tons' : ($item->qty_type === 2 ? 'KGs' : '') }}</td>
                                <td>{{ $item->size_extra ?? '-' }}</td>
                                <td>{{ $item->sale_item_prices ?? '-' }}</td>
                                <td>{{ $item->dispatch_qty ?? '-' }}</td>
                                <td>{{ $item->dispatched_qty ?? '-' }}</td>
                                <td>{{ $item->pending_qty ?? '-' }}</td>
                                <td>{{ $item->s_marked_completed === 1 ? 'Not Completed' : ($item->s_marked_completed === 2 ? 'Completed' : '-') }}</td>
                                <td>{{ $item->straightening === 1 ? 'Yes' : ($item->straightening === 2 ? 'No' : '-') }}</td>
                                <td>{{ $item->point_discard === 1 ? 'Yes' : ($item->point_discard === 2 ? 'No' : '-') }}</td>
                                <td>{{ $item->phosphating === 1 ? 'Yes' : ($item->phosphating === 2 ? 'No' : '-') }}</td>
                                <td>{{ $item->hardness ?: '-' }}</td>
                                <td>{{ $item->hardness_unit ?: '-' }}</td>
                                <td>{{ $item->pcs_length_unit ?: '-' }}</td>
                                <td>{{ $item->coating ?: '-' }}</td>
                                <td>{{ $item->fs_bundle_weight ?? '-' }}</td>
                                <td>{{ $item->fs_bundle_unit === 1 ? 'KG' : ($item->fs_bundle_unit === 2 ? 'Tons' : '-') }}</td>
                                <td>{{ [1 => 'Bright Bar', 2 => 'HB Wire', 3 => 'HHB Wire', 4 => 'Annealed Bright Bar', 5 => 'Bolt Rods'][$item->product_type] ?? '-' }}</td>
                                <td>{{ $item->item_remarks ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="25" class="text-center py-3">No items on this sale order.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
