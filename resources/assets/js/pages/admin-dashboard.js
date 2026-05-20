document.addEventListener('DOMContentLoaded', () => {
    const tableEl = document.getElementById('orders-table');
    if (!tableEl || typeof window.DataTable === 'undefined') return;

    const root = tableEl.closest('[data-orders-root]');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const endpoints = {
        data: root?.dataset.dataUrl,
        detail: root?.dataset.detailUrl,
        status: root?.dataset.statusUrl,
        shipping: root?.dataset.shippingUrl,
        dpProof: root?.dataset.dpProofUrl,
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
        order: [[5, 'desc']],
        language: {
            searchPlaceholder: 'Cari order, nama, email, telepon…',
        },
        columns: [
            { data: 'order_id' },
            { data: 'customer', orderable: false },
            { data: 'payment' },
            { data: 'amount', className: 'text-right' },
            { data: 'status' },
            { data: 'created_at' },
            { data: 'actions', orderable: false, className: 'text-right whitespace-nowrap' },
        ],
    });

    const $ = window.jQuery;
    const filterRoot = root;

    const paymentSelect = filterRoot?.querySelector('[data-filter="payment_type"]');
    const statusSelect = filterRoot?.querySelector('[data-filter="status"]');
    const dateInput = filterRoot?.querySelector('[data-filter="date_range"]');
    const resetBtn = filterRoot?.querySelector('[data-filter-reset]');

    if ($ && typeof $.fn?.select2 === 'function') {
        [paymentSelect, statusSelect].forEach((el) => {
            if (!el) return;
            $(el).select2({
                minimumResultsForSearch: Infinity,
                width: '100%',
                dropdownParent: $(filterRoot),
            });
        });
    }

    [paymentSelect, statusSelect].forEach((el) => {
        if (!el) return;
        el.addEventListener('change', () => {
            filters[el.dataset.filter] = el.value || '';
            dt.ajax.reload();
        });
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
        if (paymentSelect) {
            paymentSelect.value = '';
            if ($ && $.fn?.select2) $(paymentSelect).trigger('change.select2');
        }
        if (statusSelect) {
            statusSelect.value = '';
            if ($ && $.fn?.select2) $(statusSelect).trigger('change.select2');
        }
        datePicker?.clear();
        toggleDateClear(false);
        dt.ajax.reload();
    });

    dt.on('processing.dt', (e, settings, processing) => {
        const wrap = tableEl.closest('.dt-table-wrap');
        if (wrap) wrap.classList.toggle('is-loading', processing);
    });

    const Swal = window.Swal?.mixin({
        buttonsStyling: true,
        customClass: {
            popup: 'jpw-swal',
            container: 'jpw-swal-backdrop',
        },
    });

    const toast = window.Swal?.mixin({
        toast: true,
        position: 'top-end',
        timer: 3500,
        showConfirmButton: false,
        timerProgressBar: true,
        customClass: {
            popup: 'jpw-toast',
            container: 'jpw-toast-container',
        },
    });

    const notify = (icon, title, text) => toast?.fire({ icon, title, text });

    const postForm = async (url, formData) => {
        const res = await fetch(url, { method: 'POST', headers, body: formData });
        let payload = null;
        try { payload = await res.json(); } catch (_) {}
        if (!res.ok || !payload?.ok) {
            const msg = payload?.message || (payload?.errors ? Object.values(payload.errors).flat().join('\n') : 'Terjadi kesalahan.');
            throw new Error(msg);
        }
        return payload;
    };

    const reload = () => dt.ajax.reload(null, false);

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
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && detailModal && !detailModal.hidden) closeDetailModal();
    });

    const handleDetail = async (btn) => {
        const orderId = btn.dataset.order;
        try {
            const res = await fetch(buildUrl(endpoints.detail, orderId), { headers });
            const payload = await res.json();
            if (!payload?.ok) throw new Error(payload?.message || 'Gagal memuat detail.');
            lastTrigger = btn;
            openDetailModal(payload.html);
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Gagal', text: e.message });
        }
    };

    const handleStatus = async (btn) => {
        const orderId = btn.dataset.order;
        const status = btn.dataset.status;
        const confirmMap = {
            verified: { title: 'Verifikasi pesanan?', text: 'Pesanan akan ditandai sudah diverifikasi dan terhitung ke stok.', icon: 'question', confirmText: 'Ya, verifikasi' },
            completed: { title: 'Tandai selesai?', text: 'Pesanan ditandai selesai (sudah diterima/diambil pelanggan).', icon: 'question', confirmText: 'Ya, selesai' },
            cancelled: { title: 'Batalkan pesanan?', text: 'Pesanan akan dibatalkan dan tidak terhitung ke stok.', icon: 'warning', confirmText: 'Ya, batalkan' },
            pending: { title: 'Buka kembali pesanan?', text: 'Pesanan akan dikembalikan ke status menunggu.', icon: 'question', confirmText: 'Ya, buka' },
        };
        const cfg = confirmMap[status];
        if (!cfg) return;

        const result = await Swal.fire({
            title: cfg.title, text: cfg.text, icon: cfg.icon,
            showCancelButton: true, confirmButtonText: cfg.confirmText,
            cancelButtonText: 'Batal', confirmButtonColor: '#d84315',
        });
        if (!result.isConfirmed) return;

        try {
            const fd = new FormData();
            fd.append('status', status);
            const payload = await postForm(buildUrl(endpoints.status, orderId), fd);
            applyStats(payload.stats);
            notify('success', 'Status diperbarui', 'Selamat, status pesanan berhasil diperbarui.');
            reload();
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Gagal', text: e.message });
        }
    };

    const handleShipping = async (btn) => {
        const orderId = btn.dataset.order;
        const current = btn.dataset.tracking || '';
        const { value, isConfirmed } = await Swal.fire({
            title: 'Nomor Resi Pengiriman',
            input: 'text', inputValue: current,
            inputPlaceholder: 'Contoh: JX1234567890',
            inputAttributes: { autocapitalize: 'characters' },
            showCancelButton: true, confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal', confirmButtonColor: '#d84315',
            inputValidator: (v) => !v?.trim() ? 'Nomor resi wajib diisi' : undefined,
        });
        if (!isConfirmed) return;
        try {
            const fd = new FormData();
            fd.append('tracking', value.trim());
            await postForm(buildUrl(endpoints.shipping, orderId), fd);
            notify('success', 'Resi tersimpan', 'Nomor resi pengiriman berhasil disimpan.');
            reload();
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Gagal', text: e.message });
        }
    };

    const handleDpProof = async (btn) => {
        const orderId = btn.dataset.order;
        const { value: file, isConfirmed } = await Swal.fire({
            title: 'Upload Bukti Pelunasan DP',
            input: 'file',
            inputAttributes: { accept: 'image/*,application/pdf' },
            showCancelButton: true, confirmButtonText: 'Upload',
            cancelButtonText: 'Batal', confirmButtonColor: '#d84315',
            inputValidator: (v) => !v ? 'Pilih berkas terlebih dahulu' : undefined,
        });
        if (!isConfirmed) return;
        try {
            const fd = new FormData();
            fd.append('proof', file);
            await postForm(buildUrl(endpoints.dpProof, orderId), fd);
            notify('success', 'Bukti pelunasan diunggah', 'Bukti pelunasan DP berhasil diunggah.');
            reload();
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
        const menuWidth = 224; // w-56
        const gap = 6;
        let left = rect.right - menuWidth;
        if (left < 8) left = 8;
        if (left + menuWidth > window.innerWidth - 8) left = window.innerWidth - menuWidth - 8;
        const top = rect.bottom + gap;
        menu.style.top = `${top}px`;
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
        const map = { detail: handleDetail, status: handleStatus, shipping: handleShipping, 'dp-proof': handleDpProof };
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
