$(function () {

    const formSelector = '.customer-form';
    /* LOCATION FUNCTIONS Country → State → City */
    let statesRequest;
    let citiesRequest;

    /**Set options in select dropdown*/
    function setOptions($select, options, placeholder, selectedValue) {

        $select.empty().append(
            new Option(placeholder, '')
        );

        options.forEach(function (option) {
            $select.append(
                new Option(option.name,option.id,false,String(option.id) === String(selectedValue || ''))
            );
        });
    }


    /**
     * Load cities based on state
     */
    function loadCities($form, stateId, selectedCityId) {
        const $city = $form.find('#city_id');
        // Clear city dropdown
        setOptions($city,[],'Select city');

        // Abort previous cities request
        if (citiesRequest && citiesRequest.readyState !== 4) {
            citiesRequest.abort();
        }

        // No state selected
        if (!stateId) {
            return;
        }

        // Get cities
        citiesRequest = $.ajax({
            url: $form.data('cities-url'),
            type: 'GET',
            dataType: 'json',
            data: {
                state_id: stateId
            }

        }).done(function (response) {
            setOptions($city, response.options || [], 'Select city', selectedCityId);
        });
    }


    /* LOCATION EVENTS */
    /** Country change */
    $(document).on('change', formSelector + ' #country_id',
        function () {
            const $form = $(this).closest(formSelector);
            loadStates($form, $(this).val());
        }
    );

    /** State change */
    $(document).on('change', formSelector + ' #state_id',
        function () {
            const $form = $(this).closest(formSelector);
            loadCities($form, $(this).val());
        }
    );

    /* GST FUNCTIONS */

    /** Handle GST input */
    function handleGstInput($gst) {
        const $form = $gst.closest(formSelector);

        // Convert GST number to uppercase
        $gst.val($gst.val().toUpperCase());

        // Clear old GST details
        $form.find('#gst_details').val('');

        // Clear GST status
        $form.find('#gstStatus').text('').removeClass('text-danger text-success');
    }


    /** Verify GST */
    function verifyGst($button) {
        const $form = $button.closest(formSelector);
        const $gst = $form.find('#gst_no');
        const $status = $form.find('#gstStatus');

        // Get GST number
        const gstin = $.trim($gst.val()).toUpperCase();

        // Set formatted GST back to input
        $gst.val(gstin);
        /* GSTIN VALIDATION */

        if (!/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/.test(gstin)) {
            $status.text('Enter a valid 15-character GSTIN.').removeClass('text-success').addClass('text-danger');
            $gst.trigger('focus');
            return;
        }


        /* Disable verify button */

        $button.prop('disabled',true);

        $status.text('Verifying GST number...').removeClass('text-danger text-success');

        /* GST API REQUEST */

        $.ajax({

            url: $form.data('gst-url') + '/' + encodeURIComponent(gstin),

            // Example if you want to use another API:
            //
            // url:
            //     $form.data('https://sheet.gstincheck.co.in/check') +
            //     '/' +
            //     $form.data('18189796bdcab5783baf477710e2562f') +
            //     '/' +
            //     encodeURIComponent(gstin),

            type: 'GET',
            dataType: 'json'
        })

        /* SUCCESS */
        .done(function (response) {
            console.log('GST API Response:', response);
            const fields = response.fields || {};
            const data = response.data || {};
            
           if (data) {
                $form.find('[name="company_name"]').val(data.tradeNam || '');
                $form.find('[name="customer_name"]').val(data.lgnm || '');
                $form.find('[name="cust_code"]').val(generateShortCode(data.tradeNam));
                $form.find('[name="address"]').val(data.pradr.adr || '');
                $form.find('[name="pincode"]').val(data.pradr.addr.pncd || '');
                $form.find('[name="city_name"]').val(data.pradr.addr.dst || '');
                $form.find('[name="state_id"]').val(data.pradr.addr.stcd || '');
                // $form.find('[name="state_data"]').val(JSON.stringify(data.pradr));
                $form.find('[name="gst_details"]').val(JSON.stringify(data));
            }

            function generateShortCode(name) {
                return name.trim().split(/\s+/).map(word => word.charAt(0).toUpperCase()).join('');
            }


            /* Populate GST fields  */

            [
                'company_name',
                'email',
                'mobile',
                'address',
                'pincode'
            ].forEach(function (field) {
                if (fields[field]) {
                    $form.find('[name="' +field +'"]').val(fields[field]);
                }
            });

            /* Save GST details */
            $form.find('#gst_details').val(JSON.stringify(response.details || {}));


            /* Country / State / City from GST response */

            const countryId = fields.country_id || $form.find('#country_id').val();


            /* Set country if API returned country */
            if (fields.country_id) {
                $form.find('#country_id').val(String(fields.country_id));
            }

            /* Load State & City */

            if (countryId && fields.state_id) {
                loadStates($form, countryId, fields.state_id, fields.city_id);
            } else if (fields.country_id) {
                loadStates($form,countryId);
            }

            /* Success message */

            $status.text('GST details verified and populated.').removeClass('text-danger').addClass('text-success');

        })

        /* ERROR */

        .fail(function (xhr) {
            const message =xhr.responseJSON &&xhr.responseJSON.message? xhr.responseJSON.message: 'GST verification failed. Please try again.';
            $status.text(message).removeClass('text-success').addClass('text-danger');
        })

        /* ALWAYS */
        .always(function () {
            $button.prop('disabled',false);
        });
    }

    /* GST EVENTS */

    /** GST input change */
    $(document).on('input', formSelector + ' #gst_no',
        function () {
            handleGstInput($(this));
        }
    );

    /** Verify GST button click */
    $(document).on('click',formSelector + ' #verifyGst',
        function () {
            verifyGst($(this));
        }
    );
});

// Select2
$(document).ready(function () {
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
        allowClear: true,
        minimumResultsForSearch: 0,

        placeholder: function () {
            return $(this).data('placeholder') || 'Select an option';
        }
    });
});

// Form Validation 08/10/2026
function set_validation(setValidation)
{
    if($(setValidation).length)
    {
        $(setValidation).validate({
            ignore: [],
            rules: {
                email: {
                    email: true,
                    required: true,
                    emailcheck: true
                }
            },
            messages: {
                email: {
                    required: "This field is required.",
                    email: "Please enter a valid email address",
                    emailcheck: "Please enter a valid email address"
                }
            }
        });
        $('body').on('submit', setValidation, function(){
            if($(setValidation).valid())
            {
                $(setValidation +" button[type='submit']").prepend('<i class="far fa-spin fa-spinner"></i> ').attr('disabled', true);
                return true;
            }
        });
    }
}

set_validation('.form-validation');
set_validation('.form-validation1');


/* Required field message */
$(document).on('input change blur', 'input, select, textarea', function () {
    let field = $(this);
    let error = field.next('.error-msg');

    if (field.prop('required') && !String(field.val() || '').trim()) {
        if (!error.length) {
            field.after(
                $('<span class="error-msg text-muted">This field is required</span>')
                .css({
                    'color': 'red',
                    'font-size': '12px',
                    'display': 'block'
                })
            );
        }
    } else {
        error.remove();
    }
});

// Common Model  Submit throught ajax
if($('.ajax-modal-form').length) {
    $(document).on('submit', '.ajax-modal-form', function (e) {
    e.preventDefault();

    const $form = $(this);
    const $modal = $form.closest('.modal');
    const $btn = $form.find('[type="submit"]');

    $btn.prop('disabled', true);

    $.ajax({
        url: $form.attr('action'),
        type: 'POST',
        data: $form.serialize(),

        success: function () {
            $modal.modal('hide');
            $form[0].reset();

            alert(response.message || 'Saved successfully.');

            location.reload();
        },

        error: function (xhr) {
            alert(xhr.status === 422 ? 'Please check the required fields.' : 'Something went wrong.');
        },

        complete: function () {
            $btn.prop('disabled', false);
        }
    });
});
}

/* SALE ORDER ADD MORE ITEM */
if ($('#saleOrderItems').length) {
    let handleSaleOrderItems = {
        body: null,
        addSaleOrderItem: null,
        removeSaleOrderItem: null,
        nextIndex: 0,

        init: function () {
            this.body = $('body');
            this.nextIndex = 0;
            $('#saleOrderItems [data-item-row]').each(function () {
                let name = $(this).find('[name^="items["]').first().attr('name') || '';
                let index = Number(name.match(/^items\[(\d+)\]/)?.[1] || 0);
                handleSaleOrderItems.nextIndex = Math.max(handleSaleOrderItems.nextIndex, index + 1);
            });

            this.addSaleOrderItem = '#addSaleOrderItem';
            this.removeSaleOrderItem = '.remove-sale-order-item';

            this.body.on('click', this.addSaleOrderItem, this.addRow);
            this.body.on('click', this.removeSaleOrderItem, this.removeRow);

            this.body.on('input change', '[name$="[size_id]"], [name$="[size_unit]"]', this.productTypeChange);
            this.body.on('change', '#product_type', this.productTypeChange);

            $('#saleOrderItems [data-item-row]').each(function () {
                handleSaleOrderItems.bindRow($(this));
            });
            this.productTypeChange();
        },

        productTypeChange: function () {
            let productType = $('#product_type').val();
            let fieldsByType = {
                brightbar: ['straightening', 'point_discard'],
                hb_wire: ['phosphating'],
                hhb_wire: ['straightening', 'hardness'],
                annealed_bright_bar: ['hardness'],
                bold_rods: ['length', 'coating']
            };
            let commonFields = ['item_name', 'size', 'grade', 'qty', 'sale_rate', 'fs_bundle_weight', 'remarks'];
            let typeFields = fieldsByType[productType] || [];
            let visibleFields = commonFields.concat(typeFields);
            let requiredByField = {
                item_name: ['product_id'],
                size: ['size_id', 'size_unit'],
                grade: ['grade_id'],
                qty: ['item_qty', 'qty_type'],
                sale_rate: ['sale_item_prices'],
                straightening: ['straightening'],
                point_discard: ['point_discard'],
                phosphating: ['phosphating'],
                hardness: ['hardness', 'hardness_unit'],
                length: ['pcs_length_unit'],
                coating: ['coating'],
                fs_bundle_weight: ['fs_bundle_weight', 'fs_bundle_unit'],
                remarks: ['item_remarks']
            };

            $('.itemsListing').toggleClass('d-none', !productType);
            $('#saleOrderItems').closest('table').find('[data-field]').each(function () {
                $(this).toggle(visibleFields.includes($(this).data('field')));
            });

            $('#saleOrderItems [data-item-row]').each(function () {
                let row = $(this);
                row.find('[data-field]').each(function () {
                    let field = $(this).data('field');
                    let visible = visibleFields.includes(field);
                    let requiredNames = requiredByField[field] || [];

                    $(this).toggle(visible);
                    $(this).find('input, select, textarea').each(function () {
                        let input = $(this);
                        let name = input.attr('name') || '';
                        let inputField = name.match(/\[([^\]]+)\]$/)?.[1];
                        input.prop('disabled', false).prop('required', visible && requiredNames.includes(inputField));

                        if (!visible) {
                            if (input.is(':radio, :checkbox')) {
                                input.prop('checked', false);
                            } else {
                                input.val('');
                            }
                        }
                    });
                });

                let sizeOption = row.find('[name$="[size_id]"]');
                let sizeText = row.find('[name$="[size_unit]"]');
                sizeOption.prop('required', false);
                sizeText.prop('required', !sizeOption.val() && !sizeText.val());

            });
        },

        bindRow: function (row) {
            this.productTypeChange();
        },

        addRow: function () {
            let template = $('#saleOrderItemTemplate').html();

            let html = template.replaceAll(
                '__INDEX__',
                String(handleSaleOrderItems.nextIndex++)
            );

            let row = $(html.trim());

            $('#saleOrderItems').append(row);

            handleSaleOrderItems.bindRow(row);
        },

        removeRow: function () {
            let row = $(this).closest('[data-item-row]');

            row.remove();

            if (!$('#saleOrderItems [data-item-row]').length) {
                handleSaleOrderItems.addRow();
            }
        }
    };

    handleSaleOrderItems.init();
}

function initializeInlineOptionCreate() {
    if (!$('#inlineOptionCreateModal').length) {
        $('body').append('<div class="modal fade" id="inlineOptionCreateModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><form id="inlineOptionCreateForm"><div class="modal-header"><h5 class="modal-title">Add option</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><div class="alert alert-danger d-none" data-inline-option-error role="alert"></div><label class="form-label" for="inlineOptionCreateName">Name</label><input type="text" class="form-control" id="inlineOptionCreateName" required maxlength="50"></div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Add</button></div></form></div></div></div>');
    }

    const modalElement = document.getElementById('inlineOptionCreateModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    $(document).on('focusin.inlineOptionCreate', 'select[data-inline-add]', function () {
        this.dataset.previousValue = this.value;
    });

    $(document).on('change.inlineOptionCreate', 'select[data-inline-add]', function () {
        if (this.value !== '__add__') {
            this.dataset.previousValue = this.value;
            return;
        }

        const select = this;
        const $form = $('#inlineOptionCreateForm');
        const label = select.dataset.addLabel;

        select.value = select.dataset.previousValue || '';
        $(select).trigger('change.select2');
        $form.attr('action', select.dataset.addUrl);
        $form.data('select', select).data('type', select.dataset.inlineAdd);
        $form.find('.modal-title').text(`Add ${label}`);
        $form.find('label[for="inlineOptionCreateName"]').text(`${label} name`);
        $('#inlineOptionCreateName').attr('name', select.dataset.addField).val('');
        $form.find('[data-inline-option-error]').addClass('d-none').text('');
        modal.show();
        $('#inlineOptionCreateName').trigger('focus');
    });

    $(document).on('submit.inlineOptionCreate', '#inlineOptionCreateForm', function (event) {
        event.preventDefault();

        const form = this;
        const $form = $(form);
        const select = $form.data('select');
        const type = $form.data('type');
        const $submit = $form.find('[type="submit"]');
        const $error = $form.find('[data-inline-option-error]');

        $submit.prop('disabled', true);
        $error.addClass('d-none').text('');

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (option) {
                $(`select[data-inline-add="${type}"]`).each(function () {
                    let existing = Array.from(this.options).find((item) => item.value === String(option.id));
                    if (!existing) {
                        existing = new Option(option.text, option.id);
                        const addOption = this.querySelector('option[value="__add__"]');
                        this.insertBefore(existing, addOption);
                    }

                    if (this === select) {
                        this.value = String(option.id);
                        this.dataset.previousValue = String(option.id);
                    }

                    $(this).trigger('change');
                });

                form.reset();
                modal.hide();
            },
            error: function (xhr) {
                const errors = xhr.responseJSON?.errors || {};
                const message = Object.values(errors).flat().join(' ') || 'Unable to add this option.';
                $error.removeClass('d-none').text(message);
            },
            complete: function () {
                $submit.prop('disabled', false);
            }
        });
    });
}

if ($('#saleOrderItems').length) {
    initializeInlineOptionCreate();
}

/* Required */
function asterisk()
{
    $('[required]').parents('.form-group').find('label.form-label').append('<span class="text-danger"> *</span>');
}
asterisk();

// Input type number validations
$('input[type="number"]').keydown(function(e){
    return event.keyCode !== 69 ? true : !isNaN(Number(event.key));
});

// Only numbers allowed
$(".number_only").keyup(function(e) {
    var regex = /^[0-9\.]+$/;
    if (regex.test(this.value) !== true)
    this.value = this.value.replace(/[^0-9]+/, '');
});

// Unloader select append 
if($('.item-select').length){
    $(document).on('change', '.item-select', function () {

        let selectedOption = $(this).find(':selected');
        let row = $(this).closest('tr');

        let itemId = $(this).val();
        let productName = selectedOption.data('product');
        let size = selectedOption.data('size');
        let gradeId = selectedOption.data('grade');
        let pendingQty = selectedOption.data('pending-qty');
        let rate = selectedOption.data('rate');

        row.find('.po-rate').val(rate ?? '');
        row.find('.pending-qty').val(pendingQty ?? '');
        row.find('.item-grade').val(gradeId ?? '');
        row.find('.item-size').val(size ?? '');

    });
}

if($('#returnMaterialModal').length){
const returnModalElement = document.getElementById('returnMaterialModal');

    if (returnModalElement) {
        const returnModal = bootstrap.Modal.getOrCreateInstance(returnModalElement);
        const $returnForm = $('#returnMaterialForm');

        $(document).on('click', '.js-return-material', function () {
            const $button = $(this);
            const $items = $('#returnReceivedItems');

            $returnForm[0].reset();
            $returnForm.attr('action', $button.data('submit-url'));
            $('#returnVehicleId').val('');
            $('#returnCustomerName, #returnVehicleNumber, #returnTokenNumber').text('---');
            $('#returnReason').removeClass('is-invalid');
            $('#returnReasonError, #returnFormError').text('');
            $('#submitMaterialReturn').prop('disabled', true);
            $items.html('<tr><td colspan="7" class="text-center">Loading received materials...</td></tr>');
            returnModal.show();

            $.ajax({
                url: $button.data('details-url'),
                type: 'GET',
                dataType: 'json'
            }).done(function (response) {
                $('#returnVehicleId').val(response.vehicle.id);
                $('#returnCustomerName').text(response.vehicle.customer_name || '---');
                $('#returnVehicleNumber').text(response.vehicle.truckno || '---');
                $('#returnTokenNumber').text(response.vehicle.id || '---');
                $items.empty();

                if (!response.items.length) {
                    $items.html('<tr><td colspan="7" class="text-center">No received materials found for this vehicle and customer.</td></tr>');
                    return;
                }

                response.items.forEach(function (item) {
                    const $row = $('<tr>');
                    [
                        item.product,
                        item.grade,
                        item.size,
                        item.received_qty,
                        item.received_price,
                        item.generated_heat_no,
                        item.furnace_heat_no
                    ].forEach(function (value) {
                        $('<td>').text(value || '---').appendTo($row);
                    });
                    $items.append($row);
                });

                $('#submitMaterialReturn').prop('disabled', false);
            }).fail(function () {
                $items.html('<tr><td colspan="7" class="text-center text-danger">Unable to load received materials. Please try again.</td></tr>');
                $('#returnFormError').text('Unable to load vehicle details.');
            });
        });

        $returnForm.on('submit', function (event) {
            event.preventDefault();

            const $button = $('#submitMaterialReturn');
            const $reason = $('#returnReason');
            const reason = $.trim($reason.val());

            $reason.removeClass('is-invalid');
            $('#returnReasonError, #returnFormError').text('');

            if (!reason) {
                $reason.addClass('is-invalid');
                $('#returnReasonError').text('Reason for Return is required.');
                $reason.trigger('focus');
                return;
            }

            $button.prop('disabled', true);
            $.ajax({
                url: $returnForm.attr('action'),
                type: 'POST',
                dataType: 'json',
                data: $returnForm.serialize()
            }).done(function (response) {
                returnModal.hide();
                window.location.reload();
            }).fail(function (xhr) {
                const response = xhr.responseJSON || {};
                const errors = response.errors || {};

                if (errors.return_reason) {
                    $reason.addClass('is-invalid');
                    $('#returnReasonError').text(errors.return_reason[0]);
                } else {
                    $('#returnFormError').text(response.message || 'Unable to submit the return. Please try again.');
                }
            }).always(function () {
                $button.prop('disabled', false);
            });
        });
    }
}
