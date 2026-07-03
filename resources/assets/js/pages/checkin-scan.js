import QrScanner from 'qr-scanner';

document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('[data-checkin-scan]');
    if (!root) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const headers = { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' };

    const lookupUrl = root.dataset.lookupUrl;
    const confirmUrlTpl = root.dataset.confirmUrl;
    const undoUrlTpl = root.dataset.undoUrl;
    const buildUrl = (tpl, code) => tpl.replace('__CODE__', encodeURIComponent(code));

    const video = document.getElementById('qr-video');
    const cameraError = root.querySelector('[data-camera-error]');
    const manualForm = root.querySelector('[data-manual-form]');
    const manualInput = manualForm?.querySelector('input[name="code"]');

    const resultEmpty = root.querySelector('[data-result-empty]');
    const resultCard = root.querySelector('[data-result-card]');
    const resultCode = root.querySelector('[data-result-code]');
    const resultName = root.querySelector('[data-result-name]');
    const resultItems = root.querySelector('[data-result-items]');
    const resultBadge = root.querySelector('[data-result-badge]');
    const resultIcon = root.querySelector('[data-result-icon]');
    const resultMessage = root.querySelector('[data-result-message]');
    const btnConfirm = root.querySelector('[data-action-confirm]');
    const btnUndo = root.querySelector('[data-action-undo]');
    const btnRescan = root.querySelector('[data-action-rescan]');

    const checkedInAlert = root.querySelector('[data-checkedin-alert]');
    const checkedInAlertName = root.querySelector('[data-checkedin-alert-name]');
    const checkedInAlertMessage = root.querySelector('[data-checkedin-alert-message]');
    const checkedInAlertContinue = root.querySelector('[data-checkedin-alert-continue]');

    const confirmAlert = root.querySelector('[data-confirm-alert]');
    const confirmAlertIconwrap = root.querySelector('[data-confirm-alert-iconwrap]');
    const confirmAlertIcon = root.querySelector('[data-confirm-alert-icon]');
    const confirmAlertTitle = root.querySelector('[data-confirm-alert-title]');
    const confirmAlertName = root.querySelector('[data-confirm-alert-name]');
    const confirmAlertMessage = root.querySelector('[data-confirm-alert-message]');
    const confirmAlertActions = root.querySelector('[data-confirm-alert-actions]');
    const confirmAlertConfirmBtn = root.querySelector('[data-confirm-alert-confirm]');
    const confirmAlertCancelBtn = root.querySelector('[data-confirm-alert-cancel]');
    const confirmAlertContinueBtn = root.querySelector('[data-confirm-alert-continue]');

    const badgeStyles = {
        valid: 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200 ri-shield-check-fill',
        checked_in: 'bg-amber-50 text-amber-800 ring-1 ring-amber-200 ri-checkbox-circle-fill',
        cancelled: 'bg-red-50 text-red-700 ring-1 ring-red-200 ri-close-circle-fill',
        not_found: 'bg-red-50 text-red-700 ring-1 ring-red-200 ri-error-warning-fill',
    };

    let currentCode = null;
    let busy = false;

    const renderResult = (payload) => {
        currentCode = payload.code;
        resultEmpty.hidden = true;
        resultCard.hidden = false;

        resultCode.textContent = payload.code;
        resultMessage.textContent = payload.message;

        const [badgeClass, icon] = (() => {
            const cfg = badgeStyles[payload.status] || badgeStyles.not_found;
            const parts = cfg.split(' ');
            return [parts.slice(0, -1).join(' '), parts[parts.length - 1]];
        })();
        resultBadge.className = 'mt-3 flex items-center gap-2 rounded-xl px-3 py-2 text-[12px] font-bold ' + badgeClass;
        resultIcon.className = 'text-base ' + icon;

        if (payload.order) {
            resultName.textContent = payload.order.customer_name;
            resultItems.textContent = `${payload.order.items_label} · Tiket ${payload.order.unit_index}/${payload.order.total_units}`;
            resultName.hidden = false;
            resultItems.hidden = false;
            btnConfirm.hidden = !payload.order.can_confirm;
            btnUndo.hidden = !payload.order.can_undo;
        } else {
            resultName.textContent = 'Tidak ditemukan';
            resultItems.textContent = '';
            btnConfirm.hidden = true;
            btnUndo.hidden = true;
        }
    };

    // Handles a *fresh* scan (camera decode, manual entry, preset code) — this is
    // where we decide whether to interrupt with a popup, as opposed to renderResult()
    // which just syncs the background card for any API response (including manual
    // confirm/undo clicks on that card).
    const handleScanResult = (payload) => {
        renderResult(payload);

        if (payload.status === 'checked_in') {
            showCheckedInAlert(payload);
        } else if (payload.status === 'valid') {
            showConfirmAlert(payload);
        }
    };

    const showCheckedInAlert = (payload) => {
        checkedInAlertName.textContent = payload.order?.customer_name ?? '';
        checkedInAlertMessage.textContent = payload.order?.checked_in_at
            ? `Sudah check-in pada ${payload.order.checked_in_at}`
            : payload.message;
        checkedInAlert.hidden = false;
        scanner?.pause();
    };

    const hideCheckedInAlert = () => {
        checkedInAlert.hidden = true;
        lastScanned = '';
        scanner?.start().catch(() => {});
    };

    const showConfirmAlert = (payload) => {
        confirmAlertIconwrap.className = 'mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200';
        confirmAlertIcon.className = 'ri-shield-check-fill text-3xl';
        confirmAlertTitle.textContent = 'Tiket Valid';
        confirmAlertName.textContent = payload.order?.customer_name ?? '';
        confirmAlertMessage.textContent = payload.order
            ? `${payload.order.items_label} · Tiket ${payload.order.unit_index}/${payload.order.total_units}`
            : payload.message;
        confirmAlertActions.hidden = false;
        confirmAlertContinueBtn.hidden = true;
        confirmAlert.hidden = false;
        scanner?.pause();
    };

    const showConfirmSuccess = (payload) => {
        confirmAlertIconwrap.className = 'mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200';
        confirmAlertIcon.className = 'ri-checkbox-circle-fill text-3xl';
        confirmAlertTitle.textContent = 'Check-in Berhasil';
        confirmAlertName.textContent = payload.order?.customer_name ?? '';
        confirmAlertMessage.textContent = payload.message;
        confirmAlertActions.hidden = true;
        confirmAlertContinueBtn.hidden = false;
    };

    const hideConfirmAlert = () => {
        confirmAlert.hidden = true;
        lastScanned = '';
        scanner?.start().catch(() => {});
    };

    const confirmFromAlert = () => withBusy(async () => {
        if (!currentCode) return;
        try {
            const res = await fetch(buildUrl(confirmUrlTpl, currentCode), { method: 'POST', headers });
            const payload = await res.json();
            if (payload?.ok) {
                renderResult(payload);
                showConfirmSuccess(payload);
            }
        } catch (_) {}
    });

    const resetResult = () => {
        currentCode = null;
        resultCard.hidden = true;
        resultEmpty.hidden = false;
    };

    const withBusy = async (fn) => {
        if (busy) return;
        busy = true;

        try {
            await fn();
        } finally {
            busy = false;
        }
    };

    const lookup = (code) => withBusy(async () => {
        try {
            const res = await fetch(lookupUrl, {
                method: 'POST',
                headers: { ...headers, 'Content-Type': 'application/json' },
                body: JSON.stringify({ code }),
            });

            const payload = await res.json();
            if (payload?.ok) handleScanResult(payload);
        } catch (_) {
            //
        }
    });

    const postAction = (tpl) => withBusy(async () => {
        if (!currentCode) return;
        try {
            const res = await fetch(buildUrl(tpl, currentCode), { method: 'POST', headers });
            const payload = await res.json();
            if (payload?.ok) renderResult(payload);
        } catch (_) {}
    });

    manualForm?.addEventListener('submit', (e) => {
        e.preventDefault();

        const code = (manualInput?.value || '').trim().toUpperCase();
        if (!code) return;

        manualInput.value = '';
        lookup(code);
    });

    btnConfirm?.addEventListener('click', () => postAction(confirmUrlTpl));
    btnUndo?.addEventListener('click', () => postAction(undoUrlTpl));
    btnRescan?.addEventListener('click', () => {
        resetResult();
        scanner?.start().catch(() => {});
    });
    checkedInAlertContinue?.addEventListener('click', () => {
        hideCheckedInAlert();
    });
    confirmAlertConfirmBtn?.addEventListener('click', () => confirmFromAlert());
    confirmAlertCancelBtn?.addEventListener('click', () => hideConfirmAlert());
    confirmAlertContinueBtn?.addEventListener('click', () => hideConfirmAlert());

    const presetCode = new URLSearchParams(window.location.search).get('code');
    if (presetCode) {
        lookup(presetCode.trim().toUpperCase());
        window.history.replaceState({}, '', window.location.pathname);
    }

    let scanner = null;
    let lastScanned = '';
    let lastScannedAt = 0;

    const onDecode = (result) => {
        if (checkedInAlert && !checkedInAlert.hidden) return;
        if (confirmAlert && !confirmAlert.hidden) return;

        const text = (result?.data ?? result ?? '').toString().trim();
        if (!text) return;

        const now = Date.now();
        if (text === lastScanned && now - lastScannedAt < 4000) return;

        lastScanned = text;
        lastScannedAt = now;
        lookup(text);
    };

    if (video && QrScanner.hasCamera) {
        QrScanner.hasCamera().then((hasCamera) => {
            if (!hasCamera) {
                cameraError.hidden = false;
                cameraError.textContent = 'Kamera tidak terdeteksi. Gunakan input kode manual di bawah.';
                return;
            }

            scanner = new QrScanner(video, onDecode, {
                preferredCamera: 'environment',
                highlightScanRegion: false,
                highlightCodeOutline: false,
                maxScansPerSecond: 5,
            });

            scanner.start().catch(() => {
                cameraError.hidden = false;
                cameraError.textContent = 'Tidak bisa mengakses kamera. Izinkan akses kamera pada browser, atau gunakan input kode manual.';
            });
        });
    }
});
