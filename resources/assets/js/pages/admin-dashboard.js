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
        delete: root?.dataset.deleteUrl,
    };

    const headers = { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' };
    const buildUrl = (template, orderId) => template.replace('__ORDER__', encodeURIComponent(orderId));

    const filters = {
        payment_type: '',
        status: '',
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
                d.filter_date_from = filters.date_from;
                d.filter_date_to = filters.date_to;
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
    const dateInput = filterRoot?.querySelector('[data-filter="date_range"]');
    const resetBtn = filterRoot?.querySelector('[data-filter-reset]');

    initSelect2([paymentSelect, statusSelect], { dropdownParent: filterRoot });

    const $ = window.jQuery;
    [paymentSelect, statusSelect].forEach((el) => {
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
        filters.date_from = '';
        filters.date_to = '';
        resetSelect2(paymentSelect);
        resetSelect2(statusSelect);
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
        if (filters.date_from) params.set('filter_date_from', filters.date_from);
        if (filters.date_to) params.set('filter_date_to', filters.date_to);
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
        const map = { detail: handleDetail, status: handleStatus, delete: handleDelete, 'sync-payment': handleSyncPayment, 'settlement-verify': handleSettlementVerify, shipping: handleShipping };
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
            const fd = new FormData();
            fd.append('amount_due', String(value));
            const res = await fetch(buildUrl(endpoints.syncPayment, syncState.orderId), { method: 'POST', headers, body: fd });
            let payload = null;
            try { payload = await res.json(); } catch (_) {}
            if (!res.ok || !payload?.ok) throw new Error(payload?.message || 'Gagal memperbarui pembayaran.');
            applyStats(payload.stats);
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
    const shippingOrderIdEl = shippingModal?.querySelector('[data-shipping-order-id]');
    const shippingState = { orderId: null };
    let shippingLastTrigger = null;

    const openShippingModal = (orderId, tracking, trigger) => {
        if (!shippingModal) return;
        shippingState.orderId = orderId;
        shippingLastTrigger = trigger || null;
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
        if (!endpoints.shipping) return;
        openShippingModal(btn.dataset.order, btn.dataset.tracking || '', btn);
    };

    shippingForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!shippingState.orderId || !endpoints.shipping) return;
        const value = (shippingInput?.value || '').trim();
        if (!value) {
            if (shippingError) { shippingError.textContent = 'Nomor resi wajib diisi.'; shippingError.classList.remove('hidden'); }
            return;
        }

        if (shippingSubmit) shippingSubmit.disabled = true;
        try {
            const fd = new FormData();
            fd.append('tracking', value);
            const res = await fetch(buildUrl(endpoints.shipping, shippingState.orderId), { method: 'POST', headers, body: fd });
            let payload = null;
            try { payload = await res.json(); } catch (_) {}
            if (!res.ok || !payload?.ok) throw new Error(payload?.message || 'Gagal menyimpan resi.');
            toast?.fire({ icon: 'success', title: 'Resi tersimpan', text: `Pesanan ${shippingState.orderId} diperbarui.` });
            dt.ajax.reload(null, false);
            await refreshOpenDetail(shippingState.orderId);
            closeShippingModal();
        } catch (err) {
            if (shippingError) { shippingError.textContent = err.message; shippingError.classList.remove('hidden'); }
            if (shippingSubmit) shippingSubmit.disabled = false;
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
            shipped: {
                title: 'Tandai pesanan dikirim?',
                text: 'Pesanan akan ditandai dikirim / siap diambil. Untuk pesanan kirim, pastikan nomor resi sudah diisi.',
                icon: 'question',
                confirmText: 'Ya, lanjut',
                toast: 'Pesanan ditandai dikirim.',
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
            const fd = new FormData();
            fd.append('status', status);
            const res = await fetch(buildUrl(endpoints.status, orderId), { method: 'POST', headers, body: fd });
            let payload = null;
            try { payload = await res.json(); } catch (_) {}
            if (!res.ok || !payload?.ok) {
                throw new Error(payload?.message || 'Terjadi kesalahan.');
            }
            applyStats(payload.stats);
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
            const res = await fetch(buildUrl(endpoints.settlementVerify, orderId), { method: 'POST', headers });
            let payload = null;
            try { payload = await res.json(); } catch (_) {}
            if (!res.ok || !payload?.ok) throw new Error(payload?.message || 'Gagal memverifikasi pelunasan.');
            applyStats(payload.stats);
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
                body: (() => { const fd = new FormData(); fd.append('_method', 'DELETE'); return fd; })(),
            });
            let payload = null;
            try { payload = await res.json(); } catch (_) {}
            if (!res.ok || !payload?.ok) throw new Error(payload?.message || 'Gagal membatalkan.');
            applyStats(payload.stats);
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
        const map = { detail: handleDetail, status: handleStatus, delete: handleDelete, 'sync-payment': handleSyncPayment, 'settlement-verify': handleSettlementVerify, shipping: handleShipping };
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
