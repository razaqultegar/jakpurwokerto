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
        const map = { detail: handleDetail, delete: handleDelete };
        map[btn.dataset.action]?.(btn);
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && detailModal && !detailModal.hidden) closeDetailModal();
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
                title: 'Verifikasi pembayaran?',
                text: 'Pesanan akan ditandai sudah diverifikasi dan terhitung ke stok.',
                icon: 'question',
                confirmText: 'Ya, verifikasi',
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
            toast?.fire({ icon: 'success', title: 'Status diperbarui', text: 'Pembayaran berhasil diverifikasi.' });
            dt.ajax.reload(null, false);
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Gagal', text: e.message });
        }
    };

    const handleDelete = async (btn) => {
        const orderId = btn.dataset.order;
        const result = await Swal.fire({
            title: 'Hapus pesanan?',
            html: `Pesanan <b>${orderId}</b> akan dihapus permanen beserta bukti pembayarannya. Aksi ini tidak bisa dibatalkan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
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
            if (!res.ok || !payload?.ok) throw new Error(payload?.message || 'Gagal menghapus.');
            applyStats(payload.stats);
            toast?.fire({ icon: 'success', title: 'Dihapus', text: `Pesanan ${orderId} dihapus.` });
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
        const map = { detail: handleDetail, status: handleStatus, delete: handleDelete };
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
