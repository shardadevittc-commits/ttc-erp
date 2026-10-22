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