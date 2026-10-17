@extends('layouts.app')

@section('title', 'Vehicles Outside Yard | TTC Robotronics - Steel Industry ERP')

@push('styles')
<style>
    .vehicle-card {
        background-color: #daddfa;
        border: none;
        border-radius: 22px;
        height: 100%;
        transition: all 0.2s ease-in-out;
    }

    .vehicle-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.10);
    }

    .vehicle-card .card-body {
        padding: 24px;
    }

    .token-info {
        font-size: 13px;
        line-height: 1.6;
        color: #222;
    }

    .party-name {
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        margin-top: 22px;
        margin-bottom: 16px;
        overflow-wrap: anywhere;
    }

    .detail-label {
        font-size: 12px;
        color: #333;
        margin-bottom: 3px;
    }

    .detail-value {
        font-size: 13px;
        color: #222;
        font-weight: 500;
        overflow-wrap: anywhere;
    }

    .vehicle-status {
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-success {
        color: #198754;
    }

    .status-warning {
        color: #e5a500;
    }

    .status-danger {
        color: #ff334f;
    }

    .vehicle-action {
        width: 32px;
        height: 30px;
        padding: 3px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
    }

    .vehicles-container {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 20px;
    }

    .vehicles-heading {
        font-size: 17px;
        font-weight: 600;
        padding-bottom: 15px;
        margin-bottom: 24px;
        border-bottom: 1px solid #dee2e6;
    }
</style>
@endpush

@section('content')

<div class="container-fluid py-3">

    {{-- Page Header --}}
    <div class="page-header mb-3">
        <div class="page-header__inner d-flex flex-wrap justify-content-between align-items-center gap-3">

            <div class="page-header__content">
                <div class="page-header__title">
                    <h6 class="mb-0">Unloader Vehicle Listing</h6>
                </div>
            </div>

            <div class="page-header__actions d-flex align-items-center gap-2">
                {{-- Add buttons or filters here if required --}}
            </div>

        </div>
    </div>

    {{-- Vehicles Cards --}}
    <div class="content_area">
        <div class="vehicles-container">

            <h6 class="vehicles-heading">Vehicles Outside Yard</h6>

            <div class="row g-4">
                @foreach ($listing as $k => $data)
                {{-- Card 1 --}}
                {{-- @php
                pr($data->toArray()); die;
                @endphp --}}
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card vehicle-card">
                        <div class="card-body">
                            <div class="token-info d-flex justify-content-between align-items-center">
                                <span>Token Generated</span>
                                <span>{{ @$data->created_at ? _dt($data->created_at) : '---' }}</span>
                            </div>
                            
                            <div class="d-flex {{-- justify-content-end --}} align-items-start gap-3 mb-3 mt-1">
                                <div class="d-flex flex-column align-items-center text-center">
                                    <button type="button"
                                            class="btn btn-danger btn-sm vehicle-action js-return-material"
                                            title="Return"
                                            data-details-url="{{ route('unloader.return-details', ['vid' => $data->id]) }}"
                                            data-submit-url="{{ route('unloader.return') }}">
                                        <i class="fa-solid fa-arrow-rotate-left"></i>
                                    </button>
                                    <div class="vehicle-status status-danger mt-1">
                                        Return
                                    </div>
                                </div>

                                <div class="d-flex flex-column align-items-center text-center">
                                    <a href="{{ route('unloader.unload-materials', ['vid' => $data->id]) }}" class="btn btn-primary btn-sm vehicle-action" title="Unloading">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <div class="vehicle-status status-warning mt-1">
                                        Unloading
                                    </div>
                                </div>

                            </div>
                            

                            <h6 class="party-name">{{ @$data->customer ? $data->customer->company_name : '---' }}</h6>

                            <div class="row">
                                <div class="col-8">
                                    <div class="detail-label">Vehicle No.</div>
                                    <div class="detail-value">{{ @$data->truckno ? $data->truckno : '---' }}</div>
                                </div>

                                <div class="col-4">
                                    <div class="detail-label">Token No.</div>
                                    <div class="detail-value">{{ @$data->id ? $data->id : '---' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                {{-- Card 2 --}}
                {{-- <div class="col-12 col-md-6 col-xl-4">
                    <div class="card vehicle-card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-start gap-2">

                                <div class="token-info">
                                    Token Generated<br>
                                    2026-09-25 11:57:30
                                </div>

                                <div class="text-center">
                                    <a href="#"
                                       class="btn btn-primary btn-sm vehicle-action"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <div class="vehicle-status status-warning mt-1">
                                        Unloading
                                    </div>
                                </div>

                            </div>

                            <h6 class="party-name">A.M. ENGINEERING WORKS</h6>

                            <div class="row">
                                <div class="col-8">
                                    <div class="detail-label">Vehicle No.</div>
                                    <div class="detail-value">PB 10 HB5630</div>
                                </div>

                                <div class="col-4">
                                    <div class="detail-label">Token No.</div>
                                    <div class="detail-value">3</div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div> --}}

                {{-- Card 3 --}}
                {{-- <div class="col-12 col-md-6 col-xl-4">
                    <div class="card vehicle-card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-start gap-2">

                                <div class="token-info">
                                    Token Generated<br>
                                    2026-10-14 11:23:05
                                </div>

                                <div class="text-center">
                                    <a href="#"
                                       class="btn btn-primary btn-sm vehicle-action"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <div class="vehicle-status status-danger mt-1">
                                        Unload Pending
                                    </div>
                                </div>

                            </div>

                            <h6 class="party-name">HARYANA ISPAT UDYOG</h6>

                            <div class="row">
                                <div class="col-8">
                                    <div class="detail-label">Vehicle No.</div>
                                    <div class="detail-value">HR21B6164</div>
                                </div>

                                <div class="col-4">
                                    <div class="detail-label">Token No.</div>
                                    <div class="detail-value">1</div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div> --}}

            </div>
        </div>
    </div>

</div>

<div class="modal fade" id="returnMaterialModal" tabindex="-1" aria-labelledby="returnMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <form id="returnMaterialForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="returnMaterialModalLabel">Return Material to Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="vehicle_id" id="returnVehicleId">

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="detail-label">Customer</div>
                            <div class="detail-value" id="returnCustomerName">---</div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-label">Vehicle No.</div>
                            <div class="detail-value" id="returnVehicleNumber">---</div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-label">Token No.</div>
                            <div class="detail-value" id="returnTokenNumber">---</div>
                        </div>
                    </div>

                    {{-- <h6>Received Materials</h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Grade</th>
                                    <th>Size</th>
                                    <th>Received Qty</th>
                                    <th>Received Rate</th>
                                    <th>Generated Heat No.</th>
                                    <th>Furnace Heat No.</th>
                                </tr>
                            </thead>
                            <tbody id="returnReceivedItems">
                                <tr><td colspan="7" class="text-center">Loading received materials...</td></tr>
                            </tbody>
                        </table>
                    </div> --}}

                    <label for="returnReason" class="form-label">Reason for Return <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="returnReason" name="return_reason" rows="4" maxlength="2000" required></textarea>
                    <div class="invalid-feedback" id="returnReasonError"></div>
                    <div class="text-danger small mt-2" id="returnFormError" role="alert"></div>
                </div>
                <div class="modal-footer mb-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" id="submitMaterialReturn">Submit Return</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection