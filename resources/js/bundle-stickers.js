import QRCode from 'qrcode';

const stickerGrid = document.getElementById('stickerGrid');

if (stickerGrid) {
    const printAllButton = document.getElementById('printAllStickers');
    const qrError = document.getElementById('stickerQrError');
    const cards = Array.from(stickerGrid.querySelectorAll('.bundle-sticker'));
    const companyData = JSON.parse(document.getElementById('sticker-company-data').textContent);
    const printStyles = `
        @page { size: 95mm 138mm; margin: 0; }
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; background: #fff; }
        .bundle-sticker {
            width: 95mm; height: 138mm; min-height: 138mm; overflow: hidden;
            border: 1px solid #182a38; background: #fff; color: #20252a;
            font-family: Arial, Helvetica, sans-serif; page-break-after: always;
            break-after: page; print-color-adjust: exact; -webkit-print-color-adjust: exact;
        }
        .sticker-header { display:flex; align-items:center; min-height:33mm; padding:3mm 5mm; background:#064d78; color:#fff; }
        .sticker-brand-mark { display:flex; flex:0 0 21mm; width:21mm; height:21mm; overflow:hidden; align-items:center; justify-content:center; margin-right:4mm; padding:1.5mm; border:1px solid #e6d600; border-radius:50%; background:#fff; color:#17668d; font-size:15pt; font-weight:800; letter-spacing:-1px; }
        .sticker-brand-logo { display:block; width:100%; height:100%; object-fit:contain; object-position:center; }
        .sticker-company { flex:1 1 0; min-width:0; overflow:hidden; font-size:8pt; font-weight:700; line-height:1.15; }
        .sticker-company-name { margin-bottom:1mm; font-size:clamp(7pt, 3vw, 11pt); font-weight:800; line-height:1.05; overflow-wrap:anywhere; }
        .sticker-company-tagline { margin-bottom:2mm; font-size:5.5pt; }
        .sticker-company-address { overflow-wrap:anywhere; }
        .sticker-yellow-rule { height:2.5mm; background:#e6d600; }
        .sticker-details { padding:4mm 5mm 1mm; }
        .sticker-detail-row { display:grid; grid-template-columns:1fr 1fr; min-height:9.5mm; align-items:center; border-bottom:1px solid #d5d5d5; font-size:8pt; }
        .sticker-detail-label { padding-left:1mm; }
        .sticker-detail-value { padding-left:2mm; font-weight:800; }
        .sticker-weight-row { background:#faf6d9; }
        .sticker-qr-area { display:flex; height:27mm; align-items:center; justify-content:space-between; margin:4mm 5mm 2mm; padding:1mm 2mm; border:1px solid #075b9d; border-radius:1mm; }
        .sticker-origin { max-width:48mm; color:#111; font-size:13pt; font-weight:900; line-height:1; text-align:center; }
        .sticker-origin small { display:block; margin-top:1.5mm; font-size:6pt; letter-spacing:1px; }
        .sticker-qr { display:block; width:25mm; height:25mm; }
        .sticker-footer { padding:0 3mm 2mm; color:#07507d; font-size:7pt; font-weight:700; text-align:center; }
        .sticker-qr-payload { display:none; }
        @media print { .bundle-sticker { margin:0; box-shadow:none; } }
        .bundle-sticker:last-child { page-break-after:auto; break-after:auto; }
    `;

    const escapeXml = (value) => String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&apos;');

    const payloadFor = (card) => JSON.parse(card.querySelector('.sticker-qr-payload').textContent);

    const showQrError = (message) => {
        qrError.textContent = message;
        qrError.style.display = 'block';
    };

    const imageAsDataUrl = async (imageUrl) => {
        const response = await fetch(imageUrl, { credentials: 'same-origin' });
        if (!response.ok) {
            throw new Error(`Unable to load the company logo (HTTP ${response.status}).`);
        }

        const blob = await response.blob();
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => resolve(reader.result);
            reader.onerror = () => reject(new Error('Unable to embed the company logo in the downloaded sticker.'));
            reader.readAsDataURL(blob);
        });
    };

    const wrapText = (text, maxLength) => {
        const words = String(text ?? '').trim().split(/\s+/).filter(Boolean);
        const lines = [];
        let line = '';

        words.forEach((word) => {
            while (word.length > maxLength) {
                if (line) {
                    lines.push(line);
                    line = '';
                }
                lines.push(word.slice(0, maxLength));
                word = word.slice(maxLength);
            }

            if (line && `${line} ${word}`.length > maxLength) {
                lines.push(line);
                line = word;
            } else {
                line = line ? `${line} ${word}` : word;
            }
        });

        if (line) {
            lines.push(line);
        }

        return lines;
    };

    const printCards = (selectedCards) => {
        const printWindow = window.open('', '_blank');
        if (!printWindow) {
            showQrError('Allow pop-ups to print stickers.');
            return;
        }

        printWindow.opener = null;
        printWindow.document.open();
        printWindow.document.write(`<!doctype html>
            <html lang="en"><head><meta charset="utf-8"><title>Bundle Stickers</title>
            <style>${printStyles}</style></head>
            <body>${selectedCards.map((card) => card.outerHTML).join('')}</body></html>`);
        printWindow.document.close();

        Promise.all(Array.from(printWindow.document.images).map((image) => image.decode().catch(() => undefined)))
            .then(() => {
                printWindow.focus();
                printWindow.print();
            });
    };

    const svgFor = (card, qrDataUrl, logoDataUrl) => {
        const payload = payloadFor(card);
        const bundle = payload.bundle;
        const workOrderId = payload.work_order.id;
        const bundleLabel = `${bundle.number} / ${bundle.total}`;
        const weightLabel = `${bundle.weight} ${bundle.unit}${bundle.type === 'loose' ? ' (L)' : ''}`;
        const companyName = companyData.company_name || '';
        const companyNameLines = wrapText(companyName, 30);
        const address = new DOMParser()
            .parseFromString(String(companyData.address || '').replace(/<br\s*\/?>/gi, ' '), 'text/html')
            .body.textContent.replace(/\s+/g, ' ').trim();
        const addressLines = wrapText(address, 57);
        const descriptionLines = wrapText(companyData.certificate_desc || '', 50);
        const companyPhone = companyData.phone || '';
        const headerRows = companyNameLines.length
            + descriptionLines.length
            + (companyPhone ? 1 : 0)
            + addressLines.length;
        const headerLineHeight = Math.min(32, 205 / Math.max(headerRows, 1));
        const nameFontSize = Math.min(34, headerLineHeight * 1.05);
        const detailFontSize = Math.min(21, headerLineHeight * 0.68);
        let headerY = 52;
        const companyHeader = companyNameLines.map((line) => {
            const y = headerY;
            headerY += headerLineHeight;
            return `<text x="285" y="${y}" fill="#fff" font-family="Arial, sans-serif" font-size="${nameFontSize}" font-weight="800">${escapeXml(line)}</text>`;
        }).join('');
        const companyDescriptionLines = descriptionLines.map((line) => {
            headerY += headerLineHeight;
            return `<text x="285" y="${headerY}" fill="#fff" font-family="Arial, sans-serif" font-size="${detailFontSize}" font-weight="700">${escapeXml(line)}</text>`;
        }).join('');
        const companyPhoneLine = companyPhone
            ? `<text x="285" y="${headerY += headerLineHeight}" fill="#fff" font-family="Arial, sans-serif" font-size="${detailFontSize}" font-weight="700">${escapeXml(companyPhone)}</text>`
            : '';
        const addressText = addressLines.map((line) => {
            headerY += headerLineHeight;
            return `<text x="285" y="${headerY}" fill="#fff" font-family="Arial, sans-serif" font-size="${detailFontSize}" font-weight="700">${escapeXml(line)}</text>`;
        }).join('');
        const logoMarkup = logoDataUrl
            ? `<image x="35" y="50" width="190" height="190" preserveAspectRatio="xMidYMid meet" href="${escapeXml(logoDataUrl)}"/>`
            : `<text x="130" y="160" fill="#17668d" font-family="Arial, sans-serif" font-size="60" font-weight="800" text-anchor="middle">${escapeXml(companyName.slice(0, 2).toUpperCase())}</text>`;

        return `<svg xmlns="http://www.w3.org/2000/svg" width="1122" height="1630" viewBox="0 0 950 1380">
            <rect x="1" y="1" width="948" height="1378" fill="#fff" stroke="#182a38" stroke-width="2"/>
            <rect x="1" y="1" width="948" height="270" fill="#064d78"/>
            <circle cx="130" cy="145" r="95" fill="#fff" stroke="#e6d600" stroke-width="5"/>
            ${logoMarkup}
            ${companyHeader}${companyDescriptionLines}${companyPhoneLine}${addressText}
            <rect x="1" y="270" width="948" height="25" fill="#e6d600"/>
            <text x="65" y="365" font-family="Arial, sans-serif" font-size="29">PRODUCT</text>
            <text x="510" y="365" font-family="Arial, sans-serif" font-size="31" font-weight="700">${escapeXml(payload.product.name)}</text>
            <line x1="50" y1="400" x2="900" y2="400" stroke="#d5d5d5"/>
            <text x="65" y="465" font-family="Arial, sans-serif" font-size="29">BUNDLE NO.</text>
            <text x="510" y="465" font-family="Arial, sans-serif" font-size="31" font-weight="700">${escapeXml(bundleLabel)}</text>
            <line x1="50" y1="500" x2="900" y2="500" stroke="#d5d5d5"/>
            <text x="65" y="565" font-family="Arial, sans-serif" font-size="29">HEAT NO.</text>
            <text x="510" y="565" font-family="Arial, sans-serif" font-size="31" font-weight="700">${escapeXml(bundle.heat_number || '-')}</text>
            <line x1="50" y1="600" x2="900" y2="600" stroke="#d5d5d5"/>
            <text x="65" y="665" font-family="Arial, sans-serif" font-size="29">GRADE</text>
            <text x="510" y="665" font-family="Arial, sans-serif" font-size="31" font-weight="700">${escapeXml(payload.product.grade)}</text>
            <line x1="50" y1="700" x2="900" y2="700" stroke="#d5d5d5"/>
            <text x="65" y="765" font-family="Arial, sans-serif" font-size="29">SIZE</text>
            <text x="510" y="765" font-family="Arial, sans-serif" font-size="31" font-weight="700">${escapeXml(payload.product.size)}</text>
            <rect x="50" y="800" width="850" height="90" fill="#faf6d9"/>
            <text x="65" y="855" font-family="Arial, sans-serif" font-size="29">WEIGHT</text>
            <text x="510" y="855" font-family="Arial, sans-serif" font-size="31" font-weight="700">${escapeXml(weightLabel)}</text>
            <rect x="50" y="970" width="850" height="270" rx="8" fill="#fff" stroke="#075b9d" stroke-width="3"/>
            <text x="345" y="1090" font-family="Arial, sans-serif" font-size="45" font-weight="900" text-anchor="middle">MAKE IN INDIA</text>
            <text x="345" y="1140" font-family="Arial, sans-serif" font-size="21" font-weight="700" letter-spacing="3" text-anchor="middle">${escapeXml(companyName)}</text>
            <image x="630" y="985" width="250" height="250" preserveAspectRatio="xMidYMid meet" href="${escapeXml(qrDataUrl)}"/>
            <text x="475" y="1320" fill="#07507d" font-family="Arial, sans-serif" font-size="25" font-weight="700" text-anchor="middle">WO: ${workOrderId}  |  Bundle: ${escapeXml(bundleLabel)}</text>
        </svg>`;
    };

    const jpegFor = async (svg) => {
        const svgUrl = URL.createObjectURL(new Blob([svg], { type: 'image/svg+xml;charset=utf-8' }));
        try {
            const image = new Image();
            image.src = svgUrl;
            await image.decode();

            const canvas = document.createElement('canvas');
            canvas.width = 1122;
            canvas.height = 1630;
            const context = canvas.getContext('2d');
            if (!context) {
                throw new Error('Unable to prepare the sticker image for download.');
            }

            context.fillStyle = '#fff';
            context.fillRect(0, 0, canvas.width, canvas.height);
            context.drawImage(image, 0, 0, canvas.width, canvas.height);

            return await new Promise((resolve, reject) => {
                canvas.toBlob((blob) => {
                    if (blob) {
                        resolve(blob);
                    } else {
                        reject(new Error('Unable to create the JPEG sticker image.'));
                    }
                }, 'image/jpeg', 0.96);
            });
        } finally {
            URL.revokeObjectURL(svgUrl);
        }
    };

    const initializeSticker = async (card) => {
        const payload = payloadFor(card);
        const brandLogo = card.querySelector('.sticker-brand-logo');
        if (brandLogo) {
            const showFallback = () => {
                const fallback = card.querySelector('.sticker-brand-fallback');
                if (fallback) {
                    fallback.style.display = 'block';
                }
                brandLogo.style.display = 'none';
            };

            brandLogo.addEventListener('error', showFallback, { once: true });
            if (brandLogo.complete && brandLogo.naturalWidth === 0) {
                showFallback();
            }
        }

        const qrDataUrl = await QRCode.toDataURL(payload.scan_url, {
            errorCorrectionLevel: 'M',
            margin: 4,
            width: 1000
        });
        const qrImage = card.querySelector('.sticker-qr');
        qrImage.src = qrDataUrl;

        card.closest('.sticker-item').querySelector('.print-sticker').disabled = false;
        card.closest('.sticker-item').querySelector('.download-sticker').disabled = false;

        card.closest('.sticker-item').querySelector('.print-sticker').addEventListener('click', () => {
            printCards([card]);
        });
        const downloadButton = card.closest('.sticker-item').querySelector('.download-sticker');
        downloadButton.addEventListener('click', async () => {
            const logoImage = card.querySelector('.sticker-brand-logo');
            const logoDataUrlPromise = logoImage?.naturalWidth
                ? imageAsDataUrl(logoImage.currentSrc || logoImage.src)
                : Promise.resolve(null);

            downloadButton.disabled = true;
            try {
                const logoDataUrl = await logoDataUrlPromise;
                const svg = svgFor(card, qrDataUrl, logoDataUrl);
                const jpeg = await jpegFor(svg);
                const url = URL.createObjectURL(jpeg);
                const link = document.createElement('a');
                link.href = url;
                link.download = `WO-${payload.work_order.id}-bundle-${payload.bundle.number}-of-${payload.bundle.total}.jpg`;
                document.body.appendChild(link);
                link.click();
                link.remove();
                window.setTimeout(() => URL.revokeObjectURL(url), 1000);
            } catch (error) {
                showQrError(error.message);
            } finally {
                downloadButton.disabled = false;
            }
        });
    };

    let failedCount = 0;
    const queue = [...cards];
    const generateStickerQueue = async () => {
        while (queue.length > 0) {
            const card = queue.shift();
            try {
                await initializeSticker(card);
            } catch (error) {
                failedCount++;
            }
        }
    };

    Promise.all(Array.from(
        { length: Math.min(4, cards.length) },
        () => generateStickerQueue()
    )).then(() => {
        if (failedCount > 0) {
            showQrError(`${failedCount} QR code(s) could not be generated. Reload the page and try again.`);
        }
        if (failedCount === 0) {
            printAllButton.disabled = false;
        }
    });

    printAllButton.addEventListener('click', () => printCards(cards));
}
