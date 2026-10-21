@extends('layouts.app')

@section('title', 'Production Listing')

@section('content')
<div class="page-header">
    <div class="page-header__inner">
        <div class="page-header__content">
            <div class="page-header__title">
                <h6>Production Listing</h6>
            </div>
        </div>
        <div class="page-header__actions">
            @include('productions.filters')
        </div>
    </div>
</div>

<div class="content_area">
    <div class="flex-grow-1 container-p-y">
        <div class="alert alert-success d-none" id="productionSuccessMessage" role="status"></div>
        <div class="card listing-block ajax-listing-loop">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Sale Order Items</h5>
                <div class="input-group input-group-merge" style="max-width: 320px;">
                    <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="search" class="form-control listing-search" placeholder="Search production..." value="{{ request('search', '') }}" aria-label="Search production">
                </div>
            </div>
            <div class="card-body p-0">
                <div class="listing-loading d-none px-3 py-2" role="status" aria-live="polite">
                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Loading production...
                </div>
                <div class="listing-error d-none alert alert-danger m-3" role="alert"></div>
                <div class="table-responsive">
                    <table class="table listing-table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>SR. NO</th>
                                <th>WO. No./Date</th>
                                <th>Party Name</th>
                                <th>SO No - Product - Sizes - Rate - Qty</th>
                                <th>Grade</th>
                                <th>Size</th>
                                <th>Order Qty.</th>
                                <th>Status</th>
                                <th>Action</th>
                                {{-- <th>Dispatch Qty.</th>
                                <th>Dispatched Qty.</th>
                                <th>Pending Qty.</th>
                                <th>Completion</th> --}}
                            </tr>
                        </thead>
                        <tbody class="list table_listing_body">
                            @include('productions.listingLoop')
                        </tbody>
                    </table>
                </div>
                <div class="listing-pagination px-3 py-2">
                    {{ $listing->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="workOrderModal" tabindex="-1" aria-labelledby="workOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="workOrderModalLabel">Send Material for Work</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="workOrderModalBody">
                @include('productions.workOrderDetails')
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const modalElement = document.getElementById('workOrderModal');
        const modalBody = document.getElementById('workOrderModalBody');
        const successMessage = document.getElementById('productionSuccessMessage');
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        let activeWorkButton = null;

        document.addEventListener('click', (event) => {
            const button = event.target.closest('.send-for-work');
            if (!button) {
                return;
            }

            event.preventDefault();
            activeWorkButton = button;
            const form = document.getElementById('workOrderDetailsForm');
            const receivedQty = Number(button.dataset.receivedQty || 0);
            const availableQty = Number(button.dataset.availableQty || 0);
            const workQty = Math.min(Number(button.dataset.orderQty || 0), receivedQty);

            form.action = button.dataset.workOrderUrl;
            document.getElementById('workOrderParty').value = button.dataset.party || '';
            document.getElementById('workOrderProductDetails').value = button.dataset.productDetails || '';
            document.getElementById('workOrderGrade').value = button.dataset.grade || '-';
            document.getElementById('workOrderReceivedQty').value = receivedQty.toFixed(2);
            document.getElementById('workOrderReceivedCount').textContent =
                `${button.dataset.receivedCount || 0} received entries for this grade`;
            document.getElementById('workOrderAvailableQty').value = availableQty.toFixed(2);

            const workQtyInput = document.getElementById('workOrderSendQty');
            workQtyInput.max = receivedQty.toFixed(2);
            workQtyInput.value = workQty.toFixed(3);

            const sendRmInput = document.getElementById('workOrderSendRmQty');
            sendRmInput.max = availableQty.toFixed(2);
            sendRmInput.value = Math.min(workQty, availableQty).toFixed(2);
            document.getElementById('workOrderBalanceRmQty').value =
                Math.max(0, availableQty - Number(sendRmInput.value)).toFixed(2);
            document.getElementById('workOrderRemarks').value = '';

            form.querySelectorAll('.is-invalid').forEach((input) => input.classList.remove('is-invalid'));
            form.querySelectorAll('[data-error-for]').forEach((error) => {
                error.textContent = '';
                error.classList.remove('d-block');
            });
            const errorSummary = document.getElementById('workOrderFormError');
            errorSummary.textContent = '';
            errorSummary.classList.add('d-none');
            modal.show();
        });

        modalBody.addEventListener('input', (event) => {
            if (event.target.id !== 'workOrderSendRmQty') {
                return;
            }

            const availableQty = Number(document.getElementById('workOrderAvailableQty').value || 0);
            const requestedQty = Number(event.target.value || 0);
            document.getElementById('workOrderBalanceRmQty').value =
                Math.max(0, availableQty - requestedQty).toFixed(2);
        });

        modalBody.addEventListener('submit', async (event) => {
            const form = event.target.closest('#workOrderDetailsForm');
            if (!form) {
                return;
            }

            event.preventDefault();
            const submitButton = form.querySelector('#submitWorkOrder');
            const errorSummary = form.querySelector('#workOrderFormError');
            const inputs = form.querySelectorAll('.is-invalid');
            inputs.forEach((input) => input.classList.remove('is-invalid'));
            form.querySelectorAll('[data-error-for]').forEach((error) => {
                error.textContent = '';
                error.classList.remove('d-block');
            });
            errorSummary.classList.add('d-none');
            submitButton.disabled = true;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new FormData(form)
                });
                const result = await response.json();

                if (response.status === 422 && result.errors) {
                    Object.entries(result.errors).forEach(([field, messages]) => {
                        const input = form.elements.namedItem(field);
                        const error = form.querySelector(`[data-error-for="${field}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                        }
                        if (error) {
                            error.textContent = messages[0];
                            error.classList.add('d-block');
                        }
                    });
                    errorSummary.textContent = 'Please correct the highlighted fields.';
                    errorSummary.classList.remove('d-none');
                    return;
                }

                if (!response.ok || !result.status) {
                    throw new Error(result.message || `Unable to create work order (HTTP ${response.status}).`);
                }

                if (activeWorkButton) {
                    activeWorkButton.dataset.availableQty = result.available_qty;
                }
                successMessage.textContent = result.message;
                successMessage.classList.remove('d-none');
                modal.hide();
            } catch (error) {
                errorSummary.textContent = error.message;
                errorSummary.classList.remove('d-none');
            } finally {
                submitButton.disabled = false;
            }
        });
    })();
</script>
@endpush
