(function () {
    document.querySelectorAll('[data-toggle-print-card]').forEach(function (button) {
        button.addEventListener('click', function () {
            const row = document.getElementById(button.getAttribute('aria-controls'));

            if (!row) {
                console.error('Unable to find the unload print card requested for display.');
                return;
            }

            const isExpanded = button.getAttribute('aria-expanded') === 'true';
            row.hidden = isExpanded;
            button.setAttribute('aria-expanded', String(!isExpanded));
            button.title = isExpanded ? 'Show Print Card' : 'Hide Print Card';
            button.setAttribute(
                'aria-label',
                (isExpanded ? 'Show' : 'Hide') + ' print card ' + button.dataset.togglePrintCard
            );
        });
    });

    const qrElements = document.querySelectorAll('.rm-card-qr[data-qr-value]');

    if (typeof window.QRCode !== 'function') {
        console.error('The QR code library did not load; unload print card QR codes are unavailable.');
        qrElements.forEach(function (element) {
            element.textContent = 'QR code unavailable';
        });
    } else {
        qrElements.forEach(function (element) {
            try {
                element.textContent = '';
                new window.QRCode(element, {
                    text: element.dataset.qrValue,
                    width: 76,
                    height: 76,
                    correctLevel: window.QRCode.CorrectLevel.M
                });
            } catch (error) {
                console.error('Unable to generate an unload print card QR code.', error);
                element.textContent = 'QR code unavailable';
            }
        });
    }

    let activeCard = null;

    function clearPrintTarget() {
        document.body.classList.remove('rm-printing-card');

        if (activeCard) {
            activeCard.classList.remove('is-print-target');
            activeCard = null;
        }
    }

    document.querySelectorAll('[data-print-card]').forEach(function (button) {
        button.addEventListener('click', function () {
            const card = document.getElementById(button.dataset.printCard);

            if (!card) {
                console.error('Unable to find the unload print card requested for printing.');
                return;
            }

            clearPrintTarget();
            activeCard = card;
            activeCard.classList.add('is-print-target');
            document.body.classList.add('rm-printing-card');
            window.addEventListener('afterprint', clearPrintTarget, { once: true });
            window.print();
        });
    });
})();
