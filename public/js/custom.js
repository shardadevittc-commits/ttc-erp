$(function () {

    const formSelector = '.customer-form';

    /* =========================================================
       LOCATION FUNCTIONS
       Country → State → City
    ========================================================= */

    let statesRequest;
    let citiesRequest;


    /**
     * Set options in select dropdown
     */
    function setOptions($select, options, placeholder, selectedValue) {

        $select.empty().append(
            new Option(placeholder, '')
        );

        options.forEach(function (option) {

            $select.append(
                new Option(
                    option.name,
                    option.id,
                    false,
                    String(option.id) === String(selectedValue || '')
                )
            );

        });
    }


    /**
     * Load cities based on state
     */
    function loadCities($form, stateId, selectedCityId) {

        const $city = $form.find('#city_id');

        // Clear city dropdown
        setOptions(
            $city,
            [],
            'Select city'
        );


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


    /**
     * Load states based on country
     */
    /* function loadStates($form, countryId, selectedStateId, selectedCityId) {

        const $state = $form.find('#state_id');
        const $city = $form.find('#city_id');

        // Clear state and city dropdown
        setOptions($state,[],'Select state');

        setOptions( $city, [], 'Select city');

        // Abort previous states request
        if (statesRequest && statesRequest.readyState !== 4) {
            statesRequest.abort();
        }

        // No country selected
        // if (!countryId) {
        //     return;
        // }

        countryId = countryId || 19;

        // Get states
        statesRequest = $.ajax({
            url: $form.data('states-url'),
            type: 'GET',
            dataType: 'json',
            data: {
                country_id: countryId
            }

        }).done(function (response) {

            setOptions($state,response.options || [],'Select state',selectedStateId);
            // If state is already selected,
            // load its cities
            if (selectedStateId && $state.val()) {
                loadCities($form, selectedStateId, selectedCityId);
            }
        });
    } */


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

            loadCities(
                $form,
                $(this).val()
            );
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

            $status.text('Enter a valid 15-character GSTIN.')
                .removeClass('text-success')
                .addClass('text-danger');
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


// $(function () {
//     const formSelector = '.customer-form';
//     let statesRequest;
//     let citiesRequest;

//     function setOptions($select, options, placeholder, selectedValue) {
//         $select.empty().append(new Option(placeholder, ''));
//         options.forEach(function (option) {
//             $select.append(new Option(option.name, option.id, false, String(option.id) === String(selectedValue || '')));
//         });
//     }

//     function loadCities($form, stateId, selectedCityId) {
//         const $city = $form.find('#city_id');
//         setOptions($city, [], 'Select city');
//         if (citiesRequest && citiesRequest.readyState !== 4) {
//             citiesRequest.abort();
//         }
//         if (!stateId) {
//             return;
//         }

//         citiesRequest = $.ajax({
//             url: $form.data('cities-url'),
//             type: 'GET',
//             dataType: 'json',
//             data: { state_id: stateId }
//         }).done(function (response) {
//             setOptions($city, response.options || [], 'Select city', selectedCityId);
//         });
//     }

//     function loadStates($form, countryId, selectedStateId, selectedCityId) {
//         const $state = $form.find('#state_id');
//         setOptions($state, [], 'Select state');
//         setOptions($form.find('#city_id'), [], 'Select city');
//         if (statesRequest && statesRequest.readyState !== 4) {
//             statesRequest.abort();
//         }
//         if (!countryId) {
//             return;
//         }

//         statesRequest = $.ajax({
//             url: $form.data('states-url'),
//             type: 'GET',
//             dataType: 'json',
//             data: { country_id: countryId }
//         }).done(function (response) {
//             setOptions($state, response.options || [], 'Select state', selectedStateId);
//             if (selectedStateId && $state.val()) {
//                 loadCities($form, selectedStateId, selectedCityId);
//             }
//         });
//     }

//     $(document).on('change', formSelector + ' #country_id', function () {
//         const $form = $(this).closest(formSelector);
//         loadStates($form, $(this).val());
//     });

//     $(document).on('change', formSelector + ' #state_id', function () {
//         loadCities($(this).closest(formSelector), $(this).val());
//     });

//     $(document).on('input', formSelector + ' #gst_no', function () {
//         $(this).val($(this).val().toUpperCase());
//         $(this).closest(formSelector).find('#gst_details').val('');
//         $(this).closest(formSelector).find('#gstStatus').text('').removeClass('text-danger text-success');
//     });

//     $(document).on('click', formSelector + ' #verifyGst', function () {
//         const $button = $(this);
//         const $form = $button.closest(formSelector);
//         const $gst = $form.find('#gst_no');
//         const gstin = $.trim($gst.val()).toUpperCase();
//         const $status = $form.find('#gstStatus');

//         $gst.val(gstin);
//         if (!/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/.test(gstin)) {
//             $status.text('Enter a valid 15-character GSTIN.').removeClass('text-success').addClass('text-danger');
//             $gst.trigger('focus');
//             return;
//         }

//         $button.prop('disabled', true);
//         $status.text('Verifying GST number...').removeClass('text-danger text-success');
//         $.ajax({
//             url: $form.data('gst-url') + '/' + encodeURIComponent(gstin),
//             // url: $form.data('https://sheet.gstincheck.co.in/check') + '/' + $form.data('18189796bdcab5783baf477710e2562f') + '/' + encodeURIComponent(gstin),
//             type: 'GET',
//             dataType: 'json'
//         }).done(function (response) {
//             const fields = response.fields || {};
//             ['company_name', 'email', 'mobile', 'address', 'pincode'].forEach(function (field) {
//                 if (fields[field]) {
//                     $form.find('[name="' + field + '"]').val(fields[field]);
//                 }
//             });
//             $form.find('#gst_details').val(JSON.stringify(response.details || {}));

//             const countryId = fields.country_id || $form.find('#country_id').val();
//             if (fields.country_id) {
//                 $form.find('#country_id').val(String(fields.country_id));
//             }
//             if (countryId && fields.state_id) {
//                 loadStates($form, countryId, fields.state_id, fields.city_id);
//             } else if (fields.country_id) {
//                 loadStates($form, countryId);
//             }

//             $status.text('GST details verified and populated.').removeClass('text-danger').addClass('text-success');
//         }).fail(function (xhr) {
//             const message = xhr.responseJSON && xhr.responseJSON.message
//                 ? xhr.responseJSON.message
//                 : 'GST verification failed. Please try again.';
//             $status.text(message).removeClass('text-success').addClass('text-danger');
//         }).always(function () {
//             $button.prop('disabled', false);
//         });
//     });
// });
