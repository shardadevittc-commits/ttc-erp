import QRCode from 'qrcode';
import html2pdf from 'html2pdf.js';

const qrElements = Array.from(document.querySelectorAll('[data-invoice-qr]'));
const actionButtons = Array.from(document.querySelectorAll('[data-invoice-action]'));
const statusElement = document.querySelector('[data-invoice-status]');

function setStatus(message, isError = false) {
    if (!statusElement) {
        return;
    }

    statusElement.textContent = message;
    statusElement.classList.toggle('invoice-action-status--error', isError);
}

async function renderQrCode(element) {
    const url = element.dataset.invoiceUrl;
    const code = element.querySelector('.invoice-qr__code');

    if (!url || !code) {
        throw new Error('Invoice QR code is missing its URL or target element.');
    }

    code.innerHTML = await QRCode.toString(url, {
        type: 'svg',
        errorCorrectionLevel: 'H',
        margin: 2,
        width: 240,
        color: {
            dark: '#111111',
            light: '#ffffff',
        },
    });
}

async function waitForImages(root) {
    const images = Array.from(root.querySelectorAll('img'));
    await Promise.all(images.map(async (image) => {
        if (image.complete && image.naturalWidth > 0) {
            return;
        }

        if (typeof image.decode === 'function') {
            await image.decode();
            return;
        }

        await new Promise((resolve, reject) => {
            image.addEventListener('load', resolve, { once: true });
            image.addEventListener('error', () => reject(new Error(`Unable to load invoice image: ${image.src}`)), { once: true });
        });
    }));
}

const qrReady = Promise.all(qrElements.map(renderQrCode))
    .then(() => true)
    .catch((error) => {
        console.error('Unable to render invoice QR code.', error);
        qrElements.forEach((element) => element.classList.add('invoice-qr--error'));
        setStatus('QR code could not be generated. Refresh the invoice and try again.', true);
        return false;
    });

async function prepareInvoice() {
    const qrIsReady = await qrReady;
    if (!qrIsReady) {
        throw new Error('The invoice QR code is not available.');
    }

    const bill = document.querySelector('.bill');
    if (!bill) {
        throw new Error('The invoice content could not be found.');
    }

    await waitForImages(bill);
    return bill;
}

document.querySelectorAll('[data-invoice-action="print"]').forEach((button) => {
    button.addEventListener('click', async () => {
        try {
            setStatus('Preparing invoice for printing…');
            await prepareInvoice();
            setStatus('');
            window.print();
        } catch (error) {
            console.error('Unable to print invoice.', error);
            setStatus(error.message || 'Unable to print this invoice.', true);
        }
    });
});

document.querySelectorAll('[data-invoice-action="download"]').forEach((button) => {
    button.addEventListener('click', async () => {
        try {
            setStatus('Generating PDF…');
            const bill = await prepareInvoice();
            const fileName = `${bill.dataset.invoiceNumber || 'invoice'}.pdf`;

            await html2pdf()
                .set({
                    margin: [12, 12, 12, 12],
                    filename: fileName,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: {
                        scale: 2,
                        useCORS: true,
                        backgroundColor: '#ffffff',
                        scrollY: 0,
                    },
                    jsPDF: {
                        unit: 'mm',
                        format: 'a4',
                        orientation: 'portrait',
                    },
                    pagebreak: {
                        mode: ['css', 'legacy'],
                        avoid: ['tr', '.invoice-qr'],
                    },
                })
                .from(bill)
                .save();

            setStatus('PDF downloaded.');
        } catch (error) {
            console.error('Unable to download invoice PDF.', error);
            setStatus(error.message || 'Unable to generate the invoice PDF.', true);
        }
    });
});

if (actionButtons.length && qrElements.length) {
    qrReady.then((ready) => {
        if (!ready) {
            actionButtons.forEach((button) => {
                button.disabled = true;
            });
        }
    });
}
