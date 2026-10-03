$(function () {
    const formSelector = '.customer-form';
    let statesRequest;
    let citiesRequest;

    function setOptions($select, options, placeholder, selectedValue) {
        $select.empty().append(new Option(placeholder, ''));
        options.forEach(function (option) {
            $select.append(new Option(option.name, option.id, false, String(option.id) === String(selectedValue || '')));
        });
    }

    function loadCities($form, stateId, selectedCityId) {
        const $city = $form.find('#city_id');
        setOptions($city, [], 'Select city');
        if (citiesRequest && citiesRequest.readyState !== 4) {
            citiesRequest.abort();
        }
        if (!stateId) {
            return;
        }

        citiesRequest = $.ajax({
            url: $form.data('cities-url'),
            type: 'GET',
            dataType: 'json',
            data: { state_id: stateId }
        }).done(function (response) {
            setOptions($city, response.options || [], 'Select city', selectedCityId);
        });
    }

    function loadStates($form, countryId, selectedStateId, selectedCityId) {
        const $state = $form.find('#state_id');
        setOptions($state, [], 'Select state');
        setOptions($form.find('#city_id'), [], 'Select city');
        if (statesRequest && statesRequest.readyState !== 4) {
            statesRequest.abort();
        }
        if (!countryId) {
            return;
        }

        statesRequest = $.ajax({
            url: $form.data('states-url'),
            type: 'GET',
            dataType: 'json',
            data: { country_id: countryId }
        }).done(function (response) {
            setOptions($state, response.options || [], 'Select state', selectedStateId);
            if (selectedStateId && $state.val()) {
                loadCities($form, selectedStateId, selectedCityId);
            }
        });
    }

    $(document).on('change', formSelector + ' #country_id', function () {
        const $form = $(this).closest(formSelector);
        loadStates($form, $(this).val());
    });

    $(document).on('change', formSelector + ' #state_id', function () {
        loadCities($(this).closest(formSelector), $(this).val());
    });

    $(document).on('input', formSelector + ' #gst_no', function () {
        $(this).val($(this).val().toUpperCase());
        $(this).closest(formSelector).find('#gst_details').val('');
        $(this).closest(formSelector).find('#gstStatus').text('').removeClass('text-danger text-success');
    });

    $(document).on('click', formSelector + ' #verifyGst', function () {
        const $button = $(this);
        const $form = $button.closest(formSelector);
        const $gst = $form.find('#gst_no');
        const gstin = $.trim($gst.val()).toUpperCase();
        const $status = $form.find('#gstStatus');

        $gst.val(gstin);
        if (!/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/.test(gstin)) {
            $status.text('Enter a valid 15-character GSTIN.').removeClass('text-success').addClass('text-danger');
            $gst.trigger('focus');
            return;
        }

        $button.prop('disabled', true);
        $status.text('Verifying GST number...').removeClass('text-danger text-success');
        $.ajax({
            url: $form.data('gst-url') + '/' + encodeURIComponent(gstin),
            // url: $form.data('https://sheet.gstincheck.co.in/check') + '/' + $form.data('18189796bdcab5783baf477710e2562f') + '/' + encodeURIComponent(gstin),
            type: 'GET',
            dataType: 'json'
        }).done(function (response) {
            const fields = response.fields || {};
            ['company_name', 'email', 'mobile', 'address', 'pincode'].forEach(function (field) {
                if (fields[field]) {
                    $form.find('[name="' + field + '"]').val(fields[field]);
                }
            });
            $form.find('#gst_details').val(JSON.stringify(response.details || {}));

            const countryId = fields.country_id || $form.find('#country_id').val();
            if (fields.country_id) {
                $form.find('#country_id').val(String(fields.country_id));
            }
            if (countryId && fields.state_id) {
                loadStates($form, countryId, fields.state_id, fields.city_id);
            } else if (fields.country_id) {
                loadStates($form, countryId);
            }

            $status.text('GST details verified and populated.').removeClass('text-danger').addClass('text-success');
        }).fail(function (xhr) {
            const message = xhr.responseJSON && xhr.responseJSON.message
                ? xhr.responseJSON.message
                : 'GST verification failed. Please try again.';
            $status.text(message).removeClass('text-success').addClass('text-danger');
        }).always(function () {
            $button.prop('disabled', false);
        });
    });
});
