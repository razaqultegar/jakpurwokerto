import { createSwal, createToast } from '../utils/swal-factory.js';
import { initSelect2, resetSelect2 } from '../utils/select2-init.js';

document.addEventListener('DOMContentLoaded', () => {
    const tableEl = document.getElementById('orders-table');
    if (!tableEl || typeof window.DataTable === 'undefined') return;

    const root = tableEl.closest('[data-orders-root]');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const endpoints = {
        data: root?.dataset.dataUrl,
        export: root?.dataset.exportUrl,
        detail: root?.dataset.detailUrl,
        status: root?.dataset.statusUrl,
        syncPayment: root?.dataset.syncPaymentUrl,
        settlementVerify: root?.dataset.settlementVerifyUrl,
        shipping: root?.dataset.shippingUrl,
        pickup: root?.dataset.pickupUrl,
        paymentProof: root?.dataset.paymentProofUrl,
        delete: root?.dataset.deleteUrl,
    };

    const headers = { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' };
    const buildUrl = (template, orderId) => template.replace('__ORDER__', encodeURIComponent(orderId));

    const filterCategory = root?.dataset.filterCategory || '';
    // Sertakan konteks kategori halaman (Tiket/Merchandise) di setiap aksi tulis,
    // agar kartu statistik yang dikembalikan server tetap sinkron dengan halaman aktif.
    const withCategory = (fd) => {
        if (filterCategory) fd.append('filter_category', filterCategory);
        return fd;
    };

    const filters = {
        payment_type: '',
        status: '',
        shipping_method: '',
        date_from: '',
        date_to: '',
    };

    const dt = new window.DataTable(tableEl, {
        processing: true,
        serverSide: true,
        ajax: {
            url: endpoints.data,
            type: 'GET',
            data: (d) => {
                d.filter_payment_type = filters.payment_type;
                d.filter_status = filters.status;
                d.filter_shipping_method = filters.shipping_method;
                d.filter_date_from = filters.date_from;
                d.filter_date_to = filters.date_to;
                if (filterCategory) d.filter_category = filterCategory;
            },
        },
        scrollX: true,
        autoWidth: false,
        order: [[1, 'desc']],
        language: {
            searchPlaceholder: 'Cari order, nama, email, telepon…',
        },
        columns: [
            { data: 'order_id', className: 'whitespace-nowrap' },
            { data: 'created_at', className: 'whitespace-nowrap' },
            { data: 'customer', className: 'whitespace-nowrap' },
            { data: 'item_count', orderable: false, className: 'whitespace-nowrap text-center' },
            { data: 'amount', className: 'whitespace-nowrap text-right' },
            { data: 'payment', className: 'whitespace-nowrap' },
            { data: 'status', className: 'whitespace-nowrap' },
            { data: 'actions', orderable: false, className: 'whitespace-nowrap text-right' },
        ],
    });

    const filterRoot = root;

    const paymentSelect = filterRoot?.querySelector('[data-filter="payment_type"]');
    const statusSelect = filterRoot?.querySelector('[data-filter="status"]');
    const shippingSelect = filterRoot?.querySelector('[data-filter="shipping_method"]');
    const dateInput = filterRoot?.querySelector('[data-filter="date_range"]');
    const resetBtn = filterRoot?.querySelector('[data-filter-reset]');

    initSelect2([paymentSelect, statusSelect, shippingSelect], { dropdownParent: filterRoot });

    const $ = window.jQuery;
    [paymentSelect, statusSelect, shippingSelect].forEach((el) => {
        if (!el) return;
        const onChange = () => {
            filters[el.dataset.filter] = el.value || '';
            dt.ajax.reload();
        };
        if ($) $(el).on('change', onChange);
        else el.addEventListener('change', onChange);
    });

    let datePicker = null;
    const dateClearBtn = filterRoot?.querySelector('[data-filter-date-clear]');
    const toggleDateClear = (visible) => {
        if (!dateClearBtn) return;
        if (visible) dateClearBtn.removeAttribute('hidden');
        else dateClearBtn.setAttribute('hidden', '');
    };

    if (dateInput && typeof window.flatpickr === 'function') {
        datePicker = window.flatpickr(dateInput, {
            mode: 'range',
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd M Y',
            altInputClass: 'jpw-flatpickr',
            allowInput: false,
            onChange: (selectedDates) => {
                if (selectedDates.length === 2) {
                    const fmt = (d) => d.toISOString().slice(0, 10);
                    filters.date_from = fmt(selectedDates[0]);
                    filters.date_to = fmt(selectedDates[1]);
                    toggleDateClear(true);
                    dt.ajax.reload();
                } else if (selectedDates.length === 0) {
                    filters.date_from = '';
                    filters.date_to = '';
                    toggleDateClear(false);
                    dt.ajax.reload();
                }
            },
        });
    }

    dateClearBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        datePicker?.clear();
    });

    resetBtn?.addEventListener('click', () => {
        filters.payment_type = '';
        filters.status = '';
        filters.shipping_method = '';
        filters.date_from = '';
        filters.date_to = '';
        resetSelect2(paymentSelect);
        resetSelect2(statusSelect);
        resetSelect2(shippingSelect);
        datePicker?.clear();
        toggleDateClear(false);
        dt.ajax.reload();
    });

    const exportBtn = filterRoot?.querySelector('[data-export-orders]');
    exportBtn?.addEventListener('click', () => {
        if (!endpoints.export) return;
        const params = new URLSearchParams();
        if (filters.payment_type) params.set('filter_payment_type', filters.payment_type);
        if (filters.status) params.set('filter_status', filters.status);
        if (filters.shipping_method) params.set('filter_shipping_method', filters.shipping_method);
        if (filters.date_from) params.set('filter_date_from', filters.date_from);
        if (filters.date_to) params.set('filter_date_to', filters.date_to);
        if (filterCategory) params.set('filter_category', filterCategory);
        const qs = params.toString();
        window.location.href = endpoints.export + (qs ? '?' + qs : '');
    });

    dt.on('processing.dt', (e, settings, processing) => {
        const wrap = tableEl.closest('.dt-table-wrap');
        if (wrap) wrap.classList.toggle('is-loading', processing);
    });

    const Swal = createSwal();

    const detailModal = document.getElementById('order-detail-modal');
    const detailModalContent = detailModal?.querySelector('[data-modal-content]');
    let lastTrigger = null;

    const openDetailModal = (html) => {
        if (!detailModal || !detailModalContent) return;
        detailModalContent.innerHTML = html;
        detailModal.hidden = false;
        detailModal.removeAttribute('aria-hidden');
        document.body.style.overflow = 'hidden';
        const closeBtn = detailModal.querySelector('.order-modal__close');
        closeBtn?.focus({ preventScroll: true });
    };

    const closeDetailModal = () => {
        if (!detailModal || detailModal.hidden) return;
        detailModal.hidden = true;
        detailModal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        if (detailModalContent) detailModalContent.innerHTML = '';
        lastTrigger?.focus({ preventScroll: true });
        lastTrigger = null;
    };

    detailModal?.querySelectorAll('[data-modal-close]').forEach((el) => {
        el.addEventListener('click', closeDetailModal);
    });

    detailModalContent?.addEventListener('click', (e) => {
        const btn = e.target.closest('button[data-action]');
        if (!btn) return;
        e.preventDefault();
        const map = { detail: handleDetail, status: handleStatus, delete: handleDelete, 'sync-payment': handleSyncPayment, 'settlement-verify': handleSettlementVerify, shipping: handleShipping, pickup: handlePickup, 'payment-proof': handlePaymentProof };
        map[btn.dataset.action]?.(btn);
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && detailModal && !detailModal.hidden) closeDetailModal();
    });

    // ===== Sync Payment Modal =====
    const syncModal = document.getElementById('sync-payment-modal');
    const syncForm = syncModal?.querySelector('[data-sync-form]');
    const syncInput = syncModal?.querySelector('[data-sync-input]');
    const syncError = syncModal?.querySelector('[data-sync-error]');
    const syncSubmit = syncModal?.querySelector('[data-sync-submit]');
    const syncOrderIdEl = syncModal?.querySelector('[data-sync-order-id]');
    const syncSubtotalEl = syncModal?.querySelector('[data-sync-subtotal]');
    const syncCurrentEl = syncModal?.querySelector('[data-sync-current]');
    const syncRemainingEl = syncModal?.querySelector('[data-sync-remaining]');
    const syncMaxEl = syncModal?.querySelector('[data-sync-max]');
    const syncProgressEl = syncModal?.querySelector('[data-sync-progress]');
    const syncPercentEl = syncModal?.querySelector('[data-sync-percent]');
    const syncPercentLabelEl = syncModal?.querySelector('[data-sync-percent-label]');
    const syncState = { orderId: null, subtotal: 0, current: 0 };
    let syncLastTrigger = null;

    const parseAmount = (str) => {
        const n = parseInt(String(str ?? '').replace(/\D+/g, ''), 10);
        return Number.isFinite(n) ? n : 0;
    };
    const formatThousand = (n) => formatNumber(n);

    const updateSyncRemaining = () => {
        const value = parseAmount(syncInput?.value);
        const remaining = Math.max(0, syncState.subtotal - value);
        if (syncRemainingEl) syncRemainingEl.textContent = formatRupiah(remaining);

        const pct = syncState.subtotal > 0
            ? Math.min(100, Math.max(0, Math.round((value / syncState.subtotal) * 100)))
            : 0;
        if (syncProgressEl) syncProgressEl.style.width = pct + '%';
        if (syncPercentEl) syncPercentEl.textContent = pct;
        if (syncPercentLabelEl) {
            let label = 'Belum dibayar';
            if (pct >= 100) label = 'Lunas';
            else if (pct >= 75) label = 'Hampir lunas';
            else if (pct > 50) label = 'Di atas DP';
            else if (pct === 50) label = 'Tepat DP';
            else if (pct > 0) label = 'Di bawah DP';
            syncPercentLabelEl.textContent = label;
            syncPercentLabelEl.classList.toggle('text-emerald-600', pct >= 100);
            syncPercentLabelEl.classList.toggle('text-amber-700', pct > 50 && pct < 100);
        }

        let err = '';
        if (!value) err = 'Masukkan nominal pembayaran.';
        else if (value > syncState.subtotal) err = 'Nominal melebihi subtotal.';

        if (syncError) {
            syncError.textContent = err;
            syncError.classList.toggle('hidden', !err);
        }
        if (syncSubmit) syncSubmit.disabled = !!err || value === syncState.current;
    };

    const openSyncModal = (orderId, subtotal, current, trigger) => {
        if (!syncModal) return;
        syncState.orderId = orderId;
        syncState.subtotal = subtotal;
        syncState.current = current;
        syncLastTrigger = trigger || null;
        if (syncOrderIdEl) syncOrderIdEl.textContent = orderId;
        if (syncSubtotalEl) syncSubtotalEl.textContent = formatRupiah(subtotal);
        if (syncCurrentEl) syncCurrentEl.textContent = formatRupiah(current);
        if (syncMaxEl) syncMaxEl.textContent = formatRupiah(subtotal);
        if (syncInput) syncInput.value = formatThousand(current);
        updateSyncRemaining();
        syncModal.hidden = false;
        syncModal.removeAttribute('aria-hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => syncInput?.focus({ preventScroll: true }), 30);
    };

    const closeSyncModal = () => {
        if (!syncModal || syncModal.hidden) return;
        syncModal.hidden = true;
        syncModal.setAttribute('aria-hidden', 'true');
        if (!detailModal || detailModal.hidden) document.body.style.overflow = '';
        syncLastTrigger?.focus({ preventScroll: true });
        syncLastTrigger = null;
    };

    syncModal?.querySelectorAll('[data-sync-close]').forEach((el) => {
        el.addEventListener('click', closeSyncModal);
    });

    syncInput?.addEventListener('input', () => {
        const value = parseAmount(syncInput.value);
        syncInput.value = value ? formatThousand(value) : '';
        updateSyncRemaining();
    });

    syncModal?.querySelectorAll('[data-sync-preset]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const pct = parseInt(btn.dataset.syncPreset, 10) || 0;
            const value = Math.round((syncState.subtotal * pct) / 100);
            if (syncInput) syncInput.value = formatThousand(value);
            updateSyncRemaining();
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && syncModal && !syncModal.hidden) closeSyncModal();
    });

    const handleSyncPayment = (btn) => {
        const orderId = btn.dataset.order;
        const subtotal = parseInt(btn.dataset.subtotal, 10) || 0;
        const current = parseInt(btn.dataset.amountDue, 10) || 0;
        openSyncModal(orderId, subtotal, current, btn);
    };

    syncForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!syncState.orderId || !endpoints.syncPayment) return;
        const value = parseAmount(syncInput?.value);
        if (!value || value > syncState.subtotal) { updateSyncRemaining(); return; }

        if (syncSubmit) syncSubmit.disabled = true;
        try {
            const fd = withCategory(new FormData());
            fd.append('amount_due', String(value));
            const res = await fetch(buildUrl(endpoints.syncPayment, syncState.orderId), { method: 'POST', headers, body: fd });
            let payload = null;
            try { payload = await res.json(); } catch (_) {}
            if (!res.ok || !payload?.ok) throw new Error(payload?.message || 'Gagal memperbarui pembayaran.');
            applyStats(payload.stats);
            applyStock(payload.stockHtml);
            toast?.fire({ icon: 'success', title: 'Pembayaran disinkronkan', text: `Pesanan ${syncState.orderId} diperbarui.` });
            dt.ajax.reload(null, false);
            const openOrder = detailModalContent?.querySelector('.detail-modal')?.dataset?.order;
            if (openOrder === syncState.orderId) {
                try {
                    const r = await fetch(buildUrl(endpoints.detail, openOrder), { headers });
                    const p = await r.json();
                    if (p?.ok && detailModalContent) detailModalContent.innerHTML = p.html;
                } catch (_) {}
            }
            closeSyncModal();
        } catch (err) {
            if (syncError) {
                syncError.textContent = err.message;
                syncError.classList.remove('hidden');
            }
            if (syncSubmit) syncSubmit.disabled = false;
        }
    });

    // ===== Shipping (Resi) Modal =====
    const shippingModal = document.getElementById('shipping-modal');
    const shippingForm = shippingModal?.querySelector('[data-shipping-form]');
    const shippingInput = shippingModal?.querySelector('[data-shipping-input]');
    const shippingError = shippingModal?.querySelector('[data-shipping-error]');
    const shippingSubmit = shippingModal?.querySelector('[data-shipping-submit]');
    const shippingTitle = shippingModal?.querySelector('[data-shipping-title]');
    const shippingSubtitle = shippingModal?.querySelector('[data-shipping-subtitle]');
    const shippingOrderIdEl = shippingModal?.querySelector('[data-shipping-order-id]');
    const shippingState = { orderId: null, mode: 'edit' };
    let shippingLastTrigger = null;

    // Tampilan modal menyesuaikan mode: "ship" = simpan resi + tandai dikirim sekaligus.
    const shippingCopy = {
        edit: {
            title: 'Nomor Resi Pengiriman',
            subtitle: 'Masukkan nomor resi JNT Express. Resi wajib diisi sebelum pesanan kirim bisa ditandai selesai.',
            submit: '<i class="ri-save-3-line"></i> Simpan Resi',
        },
        ship: {
            title: 'Tandai Dikirim',
            subtitle: 'Masukkan nomor resi JNT Express. Pesanan akan langsung ditandai dikirim dan email berisi resi dikirim ke pembeli.',
            submit: '<i class="ri-truck-line"></i> Kirim Pesanan',
        },
    };

    const openShippingModal = (orderId, tracking, trigger, mode = 'edit') => {
        if (!shippingModal) return;
        shippingState.orderId = orderId;
        shippingState.mode = shippingCopy[mode] ? mode : 'edit';
        shippingLastTrigger = trigger || null;
        const copy = shippingCopy[shippingState.mode];
        if (shippingTitle) shippingTitle.textContent = copy.title;
        if (shippingSubtitle) shippingSubtitle.textContent = copy.subtitle;
        if (shippingSubmit) shippingSubmit.innerHTML = copy.submit;
        if (shippingOrderIdEl) shippingOrderIdEl.textContent = orderId;
        if (shippingInput) shippingInput.value = tracking || '';
        if (shippingError) shippingError.classList.add('hidden');
        if (shippingSubmit) shippingSubmit.disabled = false;
        shippingModal.hidden = false;
        shippingModal.removeAttribute('aria-hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => shippingInput?.focus({ preventScroll: true }), 30);
    };

    const closeShippingModal = () => {
        if (!shippingModal || shippingModal.hidden) return;
        shippingModal.hidden = true;
        shippingModal.setAttribute('aria-hidden', 'true');
        if (!detailModal || detailModal.hidden) document.body.style.overflow = '';
        shippingLastTrigger?.focus({ preventScroll: true });
        shippingLastTrigger = null;
    };

    shippingModal?.querySelectorAll('[data-shipping-close]').forEach((el) => {
        el.addEventListener('click', closeShippingModal);
    });

    const handleShipping = (btn) => {
        const mode = btn.dataset.mode || 'edit';
        // Mode "ship" butuh endpoint status; mode "edit" butuh endpoint shipping.
        if (mode === 'ship' ? !endpoints.status : !endpoints.shipping) return;
        openShippingModal(btn.dataset.order, btn.dataset.tracking || '', btn, mode);
    };

    shippingForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!shippingState.orderId) return;
        const value = (shippingInput?.value || '').trim();
        if (!value) {
            if (shippingError) { shippingError.textContent = 'Nomor resi wajib diisi.'; shippingError.classList.remove('hidden'); }
            return;
        }

        const isShip = shippingState.mode === 'ship';
        const endpoint = isShip ? endpoints.status : endpoints.shipping;
        if (!endpoint) return;

        if (shippingSubmit) shippingSubmit.disabled = true;
        try {
            const fd = withCategory(new FormData());
            fd.append('tracking', value);
            if (isShip) fd.append('status', 'shipped');
            const res = await fetch(buildUrl(endpoint, shippingState.orderId), { method: 'POST', headers, body: fd });
            let payload = null;
            try { payload = await res.json(); } catch (_) {}
            if (!res.ok || !payload?.ok) throw new Error(payload?.message || (isShip ? 'Gagal menandai dikirim.' : 'Gagal menyimpan resi.'));
            if (isShip) {
                applyStats(payload.stats);
                applyStock(payload.stockHtml);
                toast?.fire({ icon: 'success', title: 'Pesanan dikirim', text: `Resi disimpan & ${shippingState.orderId} ditandai dikirim.` });
            } else {
                toast?.fire({ icon: 'success', title: 'Resi tersimpan', text: `Pesanan ${shippingState.orderId} diperbarui.` });
            }
            dt.ajax.reload(null, false);
            await refreshOpenDetail(shippingState.orderId);
            closeShippingModal();
        } catch (err) {
            if (shippingError) { shippingError.textContent = err.message; shippingError.classList.remove('hidden'); }
            if (shippingSubmit) shippingSubmit.disabled = false;
        }
    });

    // ===== Pickup (Titik Temu) Modal =====
    const pickupModal = document.getElementById('pickup-modal');
    const pickupForm = pickupModal?.querySelector('[data-pickup-form]');
    const pickupAddressInput = pickupModal?.querySelector('[data-pickup-address]');
    const pickupContactNameInput = pickupModal?.querySelector('[data-pickup-contact-name]');
    const pickupContactPhoneInput = pickupModal?.querySelector('[data-pickup-contact-phone]');
    const pickupError = pickupModal?.querySelector('[data-pickup-error]');
    const pickupSubmit = pickupModal?.querySelector('[data-pickup-submit]');
    const pickupTitle = pickupModal?.querySelector('[data-pickup-title]');
    const pickupSubtitle = pickupModal?.querySelector('[data-pickup-subtitle]');
    const pickupOrderIdEl = pickupModal?.querySelector('[data-pickup-order-id]');
    const pickupState = { orderId: null, mode: 'edit' };
    let pickupLastTrigger = null;

    const pickupCopy = {
        edit: {
            title: 'Ubah Info Pengambilan',
            subtitle: 'Perbarui alamat & kontak pengurus untuk pesanan ini.',
            submit: '<i class="ri-save-3-line"></i> Simpan',
        },
        ship: {
            title: 'Tandai Siap Diambil',
            subtitle: 'Tentukan alamat & kontak pengurus. Pesanan akan ditandai siap diambil dan info ini dikirim ke pembeli.',
            submit: '<i class="ri-store-2-line"></i> Siap Diambil',
        },
    };

    const openPickupModal = (btn, mode = 'edit') => {
        if (!pickupModal) return;
        pickupState.orderId = btn.dataset.order;
        pickupState.mode = pickupCopy[mode] ? mode : 'edit';
        pickupLastTrigger = btn || null;
        const copy = pickupCopy[pickupState.mode];
        if (pickupTitle) pickupTitle.textContent = copy.title;
        if (pickupSubtitle) pickupSubtitle.textContent = copy.subtitle;
        if (pickupSubmit) { pickupSubmit.innerHTML = copy.submit; pickupSubmit.disabled = false; }
        if (pickupOrderIdEl) pickupOrderIdEl.textContent = btn.dataset.order;
        if (pickupAddressInput) pickupAddressInput.value = btn.dataset.pickupAddress || '';
        if (pickupContactNameInput) pickupContactNameInput.value = btn.dataset.pickupContactName || '';
        if (pickupContactPhoneInput) pickupContactPhoneInput.value = btn.dataset.pickupContactPhone || '';
        if (pickupError) pickupError.classList.add('hidden');
        pickupModal.hidden = false;
        pickupModal.removeAttribute('aria-hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => pickupAddressInput?.focus({ preventScroll: true }), 30);
    };

    const closePickupModal = () => {
        if (!pickupModal || pickupModal.hidden) return;
        pickupModal.hidden = true;
        pickupModal.setAttribute('aria-hidden', 'true');
        if (!detailModal || detailModal.hidden) document.body.style.overflow = '';
        pickupLastTrigger?.focus({ preventScroll: true });
        pickupLastTrigger = null;
    };

    pickupModal?.querySelectorAll('[data-pickup-close]').forEach((el) => {
        el.addEventListener('click', closePickupModal);
    });

    const handlePickup = (btn) => {
        const mode = btn.dataset.mode || 'edit';
        if (mode === 'ship' ? !endpoints.status : !endpoints.pickup) return;
        openPickupModal(btn, mode);
    };

    pickupForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!pickupState.orderId) return;
        const address = (pickupAddressInput?.value || '').trim();
        const contactName = (pickupContactNameInput?.value || '').trim();
        const contactPhone = (pickupContactPhoneInput?.value || '').trim();

        const showErr = (msg) => { if (pickupError) { pickupError.textContent = msg; pickupError.classList.remove('hidden'); } };
        if (!address) return showErr('Alamat / titik temu wajib diisi.');
        if (!/^[0-9]{8,20}$/.test(contactPhone)) return showErr('Nomor kontak wajib diisi (hanya angka, mis. 6281234567890).');

        const isShip = pickupState.mode === 'ship';
        const endpoint = isShip ? endpoints.status : endpoints.pickup;
        if (!endpoint) return;

        if (pickupSubmit) pickupSubmit.disabled = true;
        try {
            const fd = withCategory(new FormData());
            fd.append('pickup_address', address);
            fd.append('pickup_contact_name', contactName);
            fd.append('pickup_contact_phone', contactPhone);
            if (isShip) fd.append('status', 'shipped');
            const res = await fetch(buildUrl(endpoint, pickupState.orderId), { method: 'POST', headers, body: fd });
            let payload = null;
            try { payload = await res.json(); } catch (_) {}
            if (!res.ok || !payload?.ok) throw new Error(payload?.message || (isShip ? 'Gagal menandai siap diambil.' : 'Gagal menyimpan info pengambilan.'));
            if (isShip) {
                applyStats(payload.stats);
                applyStock(payload.stockHtml);
                toast?.fire({ icon: 'success', title: 'Siap diambil', text: `${pickupState.orderId} ditandai siap diambil & info dikirim ke pembeli.` });
            } else {
                toast?.fire({ icon: 'success', title: 'Info tersimpan', text: `Info pengambilan ${pickupState.orderId} diperbarui.` });
            }
            dt.ajax.reload(null, false);
            await refreshOpenDetail(pickupState.orderId);
            closePickupModal();
        } catch (err) {
            showErr(err.message);
            if (pickupSubmit) pickupSubmit.disabled = false;
        }
    });

    // ===== Bukti Transfer (Payment Proof) Modal =====
    const proofModal = document.getElementById('payment-proof-modal');
    const proofForm = proofModal?.querySelector('[data-proof-form]');
    const proofInput = proofModal?.querySelector('[data-proof-input]');
    const proofFilename = proofModal?.querySelector('[data-proof-filename]');
    const proofError = proofModal?.querySelector('[data-proof-error]');
    const proofSubmit = proofModal?.querySelector('[data-proof-submit]');
    const proofTitle = proofModal?.querySelector('[data-proof-title]');
    const proofSubtitle = proofModal?.querySelector('[data-proof-subtitle]');
    const proofOrderIdEl = proofModal?.querySelector('[data-proof-order-id]');
    const proofState = { orderId: null, type: 'payment' };
    let proofLastTrigger = null;

    const proofCopy = {
        payment: {
            title: 'Bukti Transfer',
            add: 'Unggah bukti transfer pembayaran untuk pesanan ini.',
            replace: 'Unggah file baru untuk mengganti bukti transfer lama. File lama akan dihapus.',
        },
        settlement: {
            title: 'Bukti Pelunasan DP',
            add: 'Unggah bukti transfer pelunasan sisa DP untuk pesanan ini.',
            replace: 'Unggah file baru untuk mengganti bukti pelunasan lama. File lama akan dihapus.',
        },
    };

    const resetProofField = () => {
        if (proofInput) proofInput.value = '';
        if (proofFilename) proofFilename.textContent = 'Klik untuk pilih file';
        if (proofError) proofError.classList.add('hidden');
    };

    const openProofModal = (btn) => {
        if (!proofModal) return;
        proofState.orderId = btn.dataset.order;
        proofState.type = proofCopy[btn.dataset.type] ? btn.dataset.type : 'payment';
        proofLastTrigger = btn || null;
        const replacing = btn.dataset.hasProof === '1';
        const copy = proofCopy[proofState.type];
        if (proofTitle) proofTitle.textContent = (replacing ? 'Ganti ' : 'Tambah ') + copy.title;
        if (proofSubtitle) proofSubtitle.textContent = replacing ? copy.replace : copy.add;
        if (proofOrderIdEl) proofOrderIdEl.textContent = btn.dataset.order;
        if (proofSubmit) proofSubmit.disabled = false;
        resetProofField();
        proofModal.hidden = false;
        proofModal.removeAttribute('aria-hidden');
        document.body.style.overflow = 'hidden';
    };

    const closeProofModal = () => {
        if (!proofModal || proofModal.hidden) return;
        proofModal.hidden = true;
        proofModal.setAttribute('aria-hidden', 'true');
        if (!detailModal || detailModal.hidden) document.body.style.overflow = '';
        resetProofField();
        proofLastTrigger?.focus({ preventScroll: true });
        proofLastTrigger = null;
    };

    proofModal?.querySelectorAll('[data-proof-close]').forEach((el) => {
        el.addEventListener('click', closeProofModal);
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && proofModal && !proofModal.hidden) closeProofModal();
    });

    proofInput?.addEventListener('change', () => {
        const file = proofInput.files?.[0];
        if (proofFilename) proofFilename.textContent = file ? file.name : 'Klik untuk pilih file';
        if (file && proofError) proofError.classList.add('hidden');
    });

    const handlePaymentProof = (btn) => {
        if (!endpoints.paymentProof) return;
        openProofModal(btn);
    };

    proofForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!proofState.orderId || !endpoints.paymentProof) return;
        const file = proofInput?.files?.[0];
        const showErr = (msg) => { if (proofError) { proofError.textContent = msg; proofError.classList.remove('hidden'); } };
        if (!file) return showErr('Pilih file bukti transfer terlebih dahulu.');
        if (file.size > 5 * 1024 * 1024) return showErr('Ukuran file maksimal 5 MB.');

        if (proofSubmit) proofSubmit.disabled = true;
        try {
            const fd = withCategory(new FormData());
            fd.append('proof', file);
            fd.append('type', proofState.type);
            const res = await fetch(buildUrl(endpoints.paymentProof, proofState.orderId), { method: 'POST', headers, body: fd });
            let payload = null;
            try { payload = await res.json(); } catch (_) {}
            if (!res.ok || !payload?.ok) throw new Error(payload?.message || 'Gagal mengunggah bukti transfer.');
            applyStats(payload.stats);
            toast?.fire({ icon: 'success', title: 'Bukti tersimpan', text: payload.message || `Bukti transfer ${proofState.orderId} diperbarui.` });
            dt.ajax.reload(null, false);
            await refreshOpenDetail(proofState.orderId);
            closeProofModal();
        } catch (err) {
            showErr(err.message);
            if (proofSubmit) proofSubmit.disabled = false;
        }
    });

    const handleDetail = async (btn) => {
        const orderId = btn.dataset.order;
        lastTrigger = btn;
        try {
            const res = await fetch(buildUrl(endpoints.detail, orderId), { headers });
            const payload = await res.json();
            if (!payload?.ok) throw new Error(payload?.message || 'Gagal memuat detail.');
            openDetailModal(payload.html);
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Gagal', text: e.message });
        }
    };

    // Muat ulang isi modal detail bila pesanan yang sedang dibuka == orderId.
    const refreshOpenDetail = async (orderId) => {
        const openOrder = detailModalContent?.querySelector('.detail-modal')?.dataset?.order;
        if (!openOrder || openOrder !== orderId) return;
        try {
            const r = await fetch(buildUrl(endpoints.detail, openOrder), { headers });
            const p = await r.json();
            if (p?.ok && detailModalContent) detailModalContent.innerHTML = p.html;
        } catch (_) {}
    };

    const toast = createToast();

    const statsRoot = document.querySelector('[data-stats-root]');
    const formatNumber = (n) => new Intl.NumberFormat('id-ID').format(Number(n) || 0);
    const formatRupiah = (n) => 'Rp' + formatNumber(n);
    const applyStats = (stats) => {
        if (!stats || !statsRoot) return;
        statsRoot.querySelectorAll('[data-stat]').forEach((el) => {
            const key = el.dataset.stat;
            if (!(key in stats)) return;
            const next = key === 'revenue' ? formatRupiah(stats[key]) : formatNumber(stats[key]);
            if (el.textContent !== next) {
                el.textContent = next;
                el.classList.remove('stat-flash');
                void el.offsetWidth;
                el.classList.add('stat-flash');
            }
        });
    };

    // Render ulang kartu "Stok Terjual" tanpa refresh halaman.
    const stockRoot = document.querySelector('[data-stock-root]');
    const applyStock = (html) => {
        if (typeof html === 'string' && stockRoot) stockRoot.innerHTML = html;
    };

    const handleStatus = async (btn) => {
        const orderId = btn.dataset.order;
        const status = btn.dataset.status;
        const confirmMap = {
            verified: {
                title: 'Terima pembayaran?',
                text: 'Pembayaran pesanan akan ditandai sudah diterima dan terhitung ke stok dan pendapatan.',
                icon: 'question',
                confirmText: 'Ya, diterima',
                toast: 'Pembayaran berhasil diterima.',
            },
            paid: {
                title: 'Tandai pesanan lunas?',
                text: 'Sisa pembayaran DP dianggap sudah diterima (konfirmasi manual admin). Pesanan akan ditandai LUNAS.',
                icon: 'question',
                confirmText: 'Ya, lunas',
                toast: 'Pesanan ditandai lunas.',
            },
            completed: {
                title: 'Tandai pesanan selesai?',
                text: 'Pesanan akan ditandai SELESAI. Pastikan pembayaran lunas dan (untuk pesanan kirim) nomor resi sudah diisi.',
                icon: 'question',
                confirmText: 'Ya, selesai',
                toast: 'Pesanan ditandai selesai.',
            },
        };
        const cfg = confirmMap[status];
        if (!cfg) return;

        const result = await Swal.fire({
            title: cfg.title,
            text: cfg.text,
            icon: cfg.icon,
            showCancelButton: true,
            confirmButtonText: cfg.confirmText,
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d84315',
        });
        if (!result.isConfirmed) return;

        try {
            const fd = withCategory(new FormData());
            fd.append('status', status);
            const res = await fetch(buildUrl(endpoints.status, orderId), { method: 'POST', headers, body: fd });
            let payload = null;
            try { payload = await res.json(); } catch (_) {}
            if (!res.ok || !payload?.ok) {
                throw new Error(payload?.message || 'Terjadi kesalahan.');
            }
            applyStats(payload.stats);
            applyStock(payload.stockHtml);
            toast?.fire({ icon: 'success', title: 'Status diperbarui', text: cfg.toast });
            dt.ajax.reload(null, false);
            await refreshOpenDetail(orderId);
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Gagal', text: e.message });
        }
    };

    const handleSettlementVerify = async (btn) => {
        const orderId = btn.dataset.order;
        if (!endpoints.settlementVerify) return;
        const result = await Swal.fire({
            title: 'Verifikasi pelunasan?',
            html: `Pelunasan DP pesanan <b>${orderId}</b> akan ditandai lunas. Pastikan bukti pembayaran sudah benar.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, verifikasi',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#059669',
        });
        if (!result.isConfirmed) return;

        try {
            const res = await fetch(buildUrl(endpoints.settlementVerify, orderId), { method: 'POST', headers, body: withCategory(new FormData()) });
            let payload = null;
            try { payload = await res.json(); } catch (_) {}
            if (!res.ok || !payload?.ok) throw new Error(payload?.message || 'Gagal memverifikasi pelunasan.');
            applyStats(payload.stats);
            applyStock(payload.stockHtml);
            toast?.fire({ icon: 'success', title: 'Pelunasan terverifikasi', text: `Pesanan ${orderId} sudah lunas.` });
            dt.ajax.reload(null, false);
            const openOrder = detailModalContent?.querySelector('.detail-modal')?.dataset?.order;
            if (openOrder === orderId) {
                try {
                    const r = await fetch(buildUrl(endpoints.detail, openOrder), { headers });
                    const p = await r.json();
                    if (p?.ok && detailModalContent) detailModalContent.innerHTML = p.html;
                } catch (_) {}
            }
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Gagal', text: e.message });
        }
    };

    const handleDelete = async (btn) => {
        const orderId = btn.dataset.order;
        const result = await Swal.fire({
            title: 'Batalkan pesanan?',
            html: `Pesanan <b>${orderId}</b> akan ditandai sebagai dibatalkan. Data pesanan tetap tersimpan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, batalkan',
            cancelButtonText: 'Tutup',
            confirmButtonColor: '#dc2626',
        });
        if (!result.isConfirmed) return;

        try {
            const res = await fetch(buildUrl(endpoints.delete, orderId), {
                method: 'POST',
                headers,
                body: (() => { const fd = withCategory(new FormData()); fd.append('_method', 'DELETE'); return fd; })(),
            });
            let payload = null;
            try { payload = await res.json(); } catch (_) {}
            if (!res.ok || !payload?.ok) throw new Error(payload?.message || 'Gagal membatalkan.');
            applyStats(payload.stats);
            applyStock(payload.stockHtml);
            toast?.fire({ icon: 'success', title: 'Dibatalkan', text: `Pesanan ${orderId} dibatalkan.` });
            dt.ajax.reload(null, false);
            // If deleted order is the currently open one, close modal; else refresh detail to update duplicate list
            const openOrder = detailModalContent?.querySelector('.detail-modal')?.dataset?.order;
            if (openOrder === orderId) {
                closeDetailModal();
            } else if (openOrder) {
                try {
                    const r = await fetch(buildUrl(endpoints.detail, openOrder), { headers });
                    const p = await r.json();
                    if (p?.ok && detailModalContent) detailModalContent.innerHTML = p.html;
                } catch (_) {}
            }
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Gagal', text: e.message });
        }
    };

    const closeDropdowns = (except = null) => {
        document.querySelectorAll('.orders-dropdown.is-open').forEach((d) => {
            if (d !== except) {
                d.classList.remove('is-open');
                d.querySelector('[data-dropdown-toggle]')?.setAttribute('aria-expanded', 'false');
            }
        });
    };

    const positionDropdown = (dropdown) => {
        const trigger = dropdown.querySelector('[data-dropdown-toggle]');
        const menu = dropdown.querySelector('.dropdown-menu');
        if (!trigger || !menu) return;
        const rect = trigger.getBoundingClientRect();
        const menuWidth = 224;
        const gap = 6;
        let left = rect.right - menuWidth;
        if (left < 8) left = 8;
        if (left + menuWidth > window.innerWidth - 8) left = window.innerWidth - menuWidth - 8;
        menu.style.top = `${rect.bottom + gap}px`;
        menu.style.left = `${left}px`;
    };

    tableEl.addEventListener('click', (e) => {
        const toggle = e.target.closest('[data-dropdown-toggle]');
        if (toggle) {
            e.stopPropagation();
            const dropdown = toggle.closest('[data-dropdown]');
            const wasOpen = dropdown.classList.contains('is-open');
            closeDropdowns();
            if (!wasOpen) {
                positionDropdown(dropdown);
                dropdown.classList.add('is-open');
                toggle.setAttribute('aria-expanded', 'true');
            }
            return;
        }

        const btn = e.target.closest('button[data-action]');
        if (!btn) return;
        closeDropdowns();
        const map = { detail: handleDetail, status: handleStatus, delete: handleDelete, 'sync-payment': handleSyncPayment, 'settlement-verify': handleSettlementVerify, shipping: handleShipping, pickup: handlePickup, 'payment-proof': handlePaymentProof };
        map[btn.dataset.action]?.(btn);
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.orders-dropdown')) closeDropdowns();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeDropdowns();
    });
    window.addEventListener('scroll', closeDropdowns, true);
    window.addEventListener('resize', closeDropdowns);
    dt.on('draw.dt', () => closeDropdowns());
});
