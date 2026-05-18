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

try {
    localStorage.removeItem('jpw.cart.v1');
} catch (_) {}

const showToast = initCopyToast();
initCopyButtons(showToast);
