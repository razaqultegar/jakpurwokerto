function initCopyToast() {
    const root = document.querySelector('[data-copy-toast]');
    if (!root) return () => {};

    const panel = root.querySelector('[data-copy-toast-panel]');
    const msgEl = root.querySelector('[data-copy-toast-message]');
    const hiddenClass = 'translate-y-[calc(100%+1rem)]';
    let timer = null;

    return (message) => {
        if (msgEl && message) msgEl.textContent = message;
        panel.classList.remove(hiddenClass);
        panel.classList.add('translate-y-0');
        if (timer) clearTimeout(timer);
        timer = setTimeout(() => {
            panel.classList.add(hiddenClass);
            panel.classList.remove('translate-y-0');
        }, 2200);
    };
}

async function copyToClipboard(text) {
    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(text);
            return true;
        }
    } catch (_) {}
    try {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        const ok = document.execCommand('copy');
        document.body.removeChild(ta);
        return ok;
    } catch (_) {
        return false;
    }
}

function initCopyButtons(showToast) {
    document.querySelectorAll('[data-copy]').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const value = btn.getAttribute('data-copy') || '';
            if (!value) return;
            const ok = await copyToClipboard(value);
            showToast(ok ? `Disalin: ${value}` : 'Gagal menyalin, salin manual ya.');
        });
    });
}

function formatBytes(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1024 / 1024).toFixed(2) + ' MB';
}

function initProofUpload(showToast) {
    const form = document.querySelector('[data-proof-form]');
    if (!form) return;

    const input = form.querySelector('[data-proof-input]');
    const dropzone = form.querySelector('[data-proof-dropzone]');
    const label = form.querySelector('[data-proof-label]');
    const preview = form.querySelector('[data-proof-preview]');
    const previewIcon = form.querySelector('[data-proof-preview-icon]');
    const previewImage = form.querySelector('[data-proof-preview-image]');
    const previewName = form.querySelector('[data-proof-preview-name]');
    const previewSize = form.querySelector('[data-proof-preview-size]');
    const clearBtn = form.querySelector('[data-proof-clear]');
    const submitBtn = form.querySelector('[data-proof-submit]');
    const submitLabel = form.querySelector('[data-proof-submit-label]');

    const MAX_SIZE = 5 * 1024 * 1024;
    const ALLOWED = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];

    const reset = () => {
        input.value = '';
        preview.classList.add('hidden');
        preview.classList.remove('flex');
        previewImage.classList.add('hidden');
        previewImage.removeAttribute('src');
        previewIcon.classList.remove('hidden');
        submitBtn.disabled = true;
    };

    const onChange = () => {
        const file = input.files && input.files[0];
        if (!file) {
            reset();
            return;
        }
        if (!ALLOWED.includes(file.type) && !file.name.match(/\.(jpe?g|png|webp|pdf)$/i)) {
            showToast('Format file harus JPG, PNG, WEBP, atau PDF.');
            reset();
            return;
        }
        if (file.size > MAX_SIZE) {
            showToast('Ukuran file maksimal 5MB.');
            reset();
            return;
        }

        previewName.textContent = file.name;
        previewSize.textContent = formatBytes(file.size);
        preview.classList.remove('hidden');
        preview.classList.add('flex');

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImage.src = e.target.result;
                previewImage.classList.remove('hidden');
                previewIcon.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            previewImage.classList.add('hidden');
            previewIcon.classList.remove('hidden');
            previewIcon.className = 'ri-file-pdf-2-line text-xl text-red-500';
        }

        submitBtn.disabled = false;
    };

    input.addEventListener('change', onChange);
    if (clearBtn) clearBtn.addEventListener('click', reset);

    ['dragenter', 'dragover'].forEach((ev) => {
        dropzone.addEventListener(ev, (e) => {
            e.preventDefault();
            dropzone.classList.add('ring-2', 'ring-primary');
        });
    });
    ['dragleave', 'drop'].forEach((ev) => {
        dropzone.addEventListener(ev, (e) => {
            e.preventDefault();
            dropzone.classList.remove('ring-2', 'ring-primary');
        });
    });
    dropzone.addEventListener('drop', (e) => {
        if (e.dataTransfer && e.dataTransfer.files.length > 0) {
            input.files = e.dataTransfer.files;
            onChange();
        }
    });

    form.addEventListener('submit', () => {
        submitBtn.disabled = true;
        submitBtn.classList.add('pointer-events-none');
        if (submitLabel) submitLabel.textContent = 'Mengunggah...';
    });
}

try {
    localStorage.removeItem('jpw.cart.v1');
} catch (_) {}

const showToast = initCopyToast();
initCopyButtons(showToast);
initProofUpload(showToast);
