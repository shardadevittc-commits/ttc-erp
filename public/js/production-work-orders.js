(() => {
    const modalElement = document.getElementById('workOrderModal');
    const modalBody = document.getElementById('workOrderModalBody');
    const successMessage = document.getElementById('productionSuccessMessage');

    if (!modalElement || !modalBody || !successMessage || !window.bootstrap) {
        return;
    }

    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    let activeWorkButton = null;

    const createSubmissionToken = () => {
        if (window.crypto?.randomUUID) {
            return window.crypto.randomUUID();
        }

        if (!window.crypto?.getRandomValues) {
            throw new Error('Secure token generation is unavailable in this browser.');
        }

        const bytes = window.crypto.getRandomValues(new Uint8Array(16));
        bytes[6] = (bytes[6] & 0x0f) | 0x40;
        bytes[8] = (bytes[8] & 0x3f) | 0x80;
        const hex = Array.from(bytes, (byte) => byte.toString(16).padStart(2, '0')).join('');

        return `${hex.slice(0, 8)}-${hex.slice(8, 12)}-${hex.slice(12, 16)}-${hex.slice(16, 20)}-${hex.slice(20)}`;
    };

    document.addEventListener('click', (event) => {
        const button = event.target.closest('.send-for-work');
        if (!button) {
            return;
        }

        // Prefill the shared form from the selected listing row before opening the modal.
        event.preventDefault();
        activeWorkButton = button;
        const form = document.getElementById('workOrderDetailsForm');
        const receivedQty = Number(button.dataset.receivedQty || 0);
        const availableQty = Number(button.dataset.availableQty || 0);
        const workQty = Math.min(Number(button.dataset.orderQty || 0), receivedQty);

        form.action = button.dataset.workOrderUrl;
        document.getElementById('workOrderSubmissionToken').value = createSubmissionToken();
        document.getElementById('workOrderParty').value = button.dataset.party || '';
        document.getElementById('workOrderProductDetails').value = button.dataset.productDetails || '';
        document.getElementById('workOrderGrade').value = button.dataset.grade || '-';
        document.getElementById('workOrderOperation').value = button.dataset.operation || '-';
        document.getElementById('workOrderHeatNo').value = button.dataset.rmHeatNo || '-';
        document.getElementById('workOrderVehicleId').value = button.dataset.vehicleId || '-';
        document.getElementById('workOrderInstruction').value = button.dataset.instruction || '';
        document.getElementById('workOrderReceivedQty').value = receivedQty.toFixed(3);
        document.getElementById('workOrderReceivedCount').textContent = `${button.dataset.receivedCount || 0} received entries for this grade`;
        document.getElementById('workOrderAvailableQty').value = availableQty.toFixed(3);

        const workQtyInput = document.getElementById('workOrderSendQty');
        workQtyInput.max = receivedQty.toFixed(3);
        workQtyInput.value = workQty.toFixed(3);

        const sendRmInput = document.getElementById('workOrderSendRmQty');
        sendRmInput.max = availableQty.toFixed(3);
        sendRmInput.value = Math.min(workQty, availableQty).toFixed(3);
        document.getElementById('workOrderBalanceRmQty').value = Math.max(0, availableQty - Number(sendRmInput.value)).toFixed(3);
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
        // Keep the balance preview synchronized with the requested raw-material quantity.
        if (event.target.id !== 'workOrderSendRmQty') {
            return;
        }

        const availableQty = Number(document.getElementById('workOrderAvailableQty').value || 0);
        const requestedQty = Number(event.target.value || 0);
        document.getElementById('workOrderBalanceRmQty').value = Math.max(0, availableQty - requestedQty).toFixed(3);
    });

    modalBody.addEventListener('submit', async (event) => {
        // Submit asynchronously so work-order creation does not navigate away from the listing.
        const form = event.target.closest('#workOrderDetailsForm');
        if (!form) {
            return;
        }

        event.preventDefault();
        const submitButton = form.querySelector('#submitWorkOrder');
        const errorSummary = form.querySelector('#workOrderFormError');
        form.querySelectorAll('.is-invalid').forEach((input) => input.classList.remove('is-invalid'));
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

            if (result.workflow_url) {
                window.location.assign(result.workflow_url);
                return;
            }

            if (activeWorkButton) {
                activeWorkButton.dataset.availableQty = result.available_qty;
                activeWorkButton.dataset.rmHeatNo = result.heat_no || '';
                activeWorkButton.dataset.vehicleId = result.vehicle_id || '';
                activeWorkButton.dataset.operation = result.total_operation_perfome || '';
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
