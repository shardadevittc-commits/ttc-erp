<form action="" method="POST" id="workOrderDetailsForm">
    @csrf
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label class="form-label">Party Name</label>
            <input type="text" class="form-control" id="workOrderParty" disabled>
        </div>
        <div class="col-md-5">
            <label class="form-label">SO - Product - Grade - Size - Price - QTY</label>
            <input type="text" class="form-control" id="workOrderProductDetails" disabled>
        </div>
        <div class="col-md-3">
            <label class="form-label">Grade</label>
            <input type="text" class="form-control" id="workOrderGrade" disabled>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label class="form-label">Purchase Rec. Qty (Tons)</label>
            <input type="number" step="0.01" class="form-control" id="workOrderReceivedQty" disabled>
            <small class="text-muted" id="workOrderReceivedCount"></small>
        </div>
        <div class="col-md-4">
            <label for="workOrderSendQty" class="form-label">Send Qty for Work (Tons)</label>
            <input type="number" min="0" step="0.001" class="form-control" name="send_work_qty" id="workOrderSendQty" required>
            <div class="invalid-feedback" data-error-for="send_work_qty"></div>
        </div>
        <div class="col-md-4">
            <label class="form-label">Available RM Qty (Tons)</label>
            <input type="number" step="0.01" class="form-control" id="workOrderAvailableQty" disabled>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label class="form-label">RM Heat No.</label>
            <input type="text" class="form-control" value="-" disabled>
        </div>
        <div class="col-md-4">
            <label for="workOrderSendRmQty" class="form-label">Send RM Qty (Tons)</label>
            <input type="number" min="0" step="0.01" class="form-control" name="send_rm_qty" id="workOrderSendRmQty" required>
            <div class="invalid-feedback" data-error-for="send_rm_qty"></div>
        </div>
        <div class="col-md-4">
            <label class="form-label">Balance RM Qty (Tons)</label>
            <input type="number" step="0.01" class="form-control" id="workOrderBalanceRmQty" disabled>
        </div>
    </div>

    <div class="mb-3">
        <label for="workOrderRemarks" class="form-label">Remarks for Operations</label>
        <textarea class="form-control" name="remarks" id="workOrderRemarks" rows="2" maxlength="255"
            placeholder="Add remarks for the operations"></textarea>
        <div class="invalid-feedback" data-error-for="remarks"></div>
    </div>

    <div class="mb-3">
        <label for="workOrderRemarks" class="form-label">Instruction (from Sale Order)</label>
        <textarea class="form-control" name="remarks" id="workOrderRemarks" rows="2" maxlength="255"
            placeholder="Instruction from Sale Order"></textarea>
        <div class="invalid-feedback" data-error-for="remarks"></div>
    </div>

    <div class="alert alert-danger d-none mt-3 mb-0" id="workOrderFormError" role="alert"></div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        {{-- <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button> --}}
        <button type="submit" class="btn btn-success" id="submitWorkOrder">Submit</button>
    </div>
</form>
