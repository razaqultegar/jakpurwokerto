import jQuery from 'jquery';

window.jQuery = window.jQuery ?? jQuery;
window.$ = window.$ ?? jQuery;

document.addEventListener('DOMContentLoaded', () => {
    const tableEl = document.getElementById('members-table');
    if (tableEl || typeof window.DataTable !== 'undefined') {
        initTable(tableEl);
    }
    initImportModal();
});

function initTable(tableEl) {
    if (!tableEl || typeof window.DataTable === 'undefined') return;

    const root = tableEl.closest('[data-members-root]');
    const dataUrl = root?.dataset.dataUrl ?? '/admin/anggota/data';

    const dataTable = new window.DataTable(tableEl, {
        processing: true,
        serverSide: true,
        scrollX: true,
        autoWidth: false,
        order: [[1, 'asc']],
        pageLength: 10,
        ajax: {
            url: dataUrl,
            dataSrc: 'data',
        },
        columns: [
            { data: 'card_number', className: 'whitespace-nowrap' },
            { data: 'name', className: 'whitespace-nowrap' },
            { data: 'gender', className: 'whitespace-nowrap text-center' },
            { data: 'dob', className: 'whitespace-nowrap' },
            { data: 'age', orderable: false, className: 'whitespace-nowrap text-center' },
            { data: 'address', className: 'whitespace-nowrap' },
            { data: 'valid_until', className: 'whitespace-nowrap' },
        ],
        language: {
            searchPlaceholder: 'Cari nama, No KTA, alamat…',
        },
    });

    tableEl.__dataTable = dataTable;
}

function initImportModal() {
    const modal    = document.querySelector('[data-import-modal]');
    const root     = document.querySelector('[data-members-root]');
    if (!modal || !root) return;

    const importUrl    = root.dataset.importUrl;
    const openBtn      = document.querySelector('[data-import-open]');
    const closeBtns    = document.querySelectorAll('[data-import-close]');
    const form         = modal.querySelector('[data-import-form]');
    const input        = modal.querySelector('[data-import-input]');
    const filename     = modal.querySelector('[data-import-filename]');
    const submitBtn    = modal.querySelector('[data-import-submit]');
    const progressWrap = modal.querySelector('[data-import-progress-wrap]');
    const bar          = modal.querySelector('[data-import-bar]');
    const percentEl    = modal.querySelector('[data-import-percent]');
    const statusEl     = modal.querySelector('[data-import-status]');
    const resultEl     = modal.querySelector('[data-import-result]');
    const dropzone     = modal.querySelector('[data-import-dropzone]');

    const openModal  = () => { modal.classList.remove('hidden'); modal.classList.add('flex'); };
    const closeModal = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        resetModal();
    };

    openBtn?.addEventListener('click', openModal);
    closeBtns.forEach((btn) => btn.addEventListener('click', closeModal));
    modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

    const handleFile = (file) => {
        if (!file) return;
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        filename.textContent = file.name;
        submitBtn.disabled = false;
        hide(resultEl);
        hide(progressWrap);
    };

    input.addEventListener('change', () => {
        const file = input.files?.[0];
        filename.textContent = file ? file.name : 'Belum ada file dipilih';
        submitBtn.disabled = !file;
        hide(resultEl);
        hide(progressWrap);
    });

    if (dropzone) {
        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('border-primary', 'bg-primary-soft/30');
        });
        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('border-primary', 'bg-primary-soft/30');
        });
        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('border-primary', 'bg-primary-soft/30');
            const file = e.dataTransfer?.files?.[0];
            handleFile(file);
        });
    }

    submitBtn.addEventListener('click', async () => {
        const file = input.files?.[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);

        const csrf = form.querySelector('input[name="_token"]')?.value;
        submitBtn.disabled = true;
        show(progressWrap);
        hide(resultEl);
        setProgress(10, 'Memvalidasi file…');

        try {
            setProgress(30, 'Mengunggah…');
            const res = await fetch(importUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: formData,
            });

            setProgress(70, 'Memproses data…');
            const json = await res.json().catch(() => ({}));

            if (!res.ok) {
                throw new Error(json.message || 'Gagal memproses impor.');
            }

            setProgress(100, 'Selesai');
            showResult(json.message || 'Impor berhasil.', json.errors || []);

            input.value = '';
            filename.textContent = 'Belum ada file dipilih';

            const tableEl = document.getElementById('members-table');
            if (tableEl?.__dataTable?.ajax) {
                tableEl.__dataTable.ajax.reload();
            }
        } catch (err) {
            hide(progressWrap);
            showResult(err.message || 'Terjadi kesalahan.', []);
        } finally {
            submitBtn.disabled = false;
        }
    });

    function setProgress(pct, status) {
        bar.style.width = pct + '%';
        percentEl.textContent = pct + '%';
        if (status) statusEl.textContent = status;
    }

    function showResult(message, errors) {
        const errorList = errors.length
            ? `<ul class="mt-2 list-disc space-y-0.5 pl-4 text-[11px] text-rose-600">${errors.slice(0, 5).map((e) => `<li>${escapeHtml(e)}</li>`).join('')}</ul>`
            : '';
        resultEl.innerHTML = `<p class="font-medium text-foreground">${escapeHtml(message)}</p>${errorList}`;
        show(resultEl);
    }

    function resetModal() {
        hide(progressWrap);
        hide(resultEl);
        bar.style.width = '0%';
        percentEl.textContent = '0%';
        input.value = '';
        filename.textContent = 'Belum ada file dipilih';
        submitBtn.disabled = true;
    }

    function show(el) { el?.classList.remove('hidden'); }
    function hide(el) { el?.classList.add('hidden'); }
    function escapeHtml(str) {
        return String(str).replace(/[&<>"']/g, (c) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
        }[c]));
    }
}
