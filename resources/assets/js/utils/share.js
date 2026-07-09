export function initShare() {
    const root = document.querySelector('[data-share]');
    if (!root) return;

    const panel = root.querySelector('[data-share-panel]');
    const backdrop = root.querySelector('[data-share-backdrop]');
    const openBtns = document.querySelectorAll('[data-share-open]');
    const closeBtn = root.querySelector('[data-share-close]');
    const copyBtn = root.querySelector('[data-share-copy]');
    const copyIcon = root.querySelector('[data-share-copy-icon]');
    const copyLabel = root.querySelector('[data-share-copy-label]');
    const igBtn = root.querySelector('[data-share-instagram]');
    const urlEl = root.querySelector('[data-share-url]');
    const shareUrl = urlEl ? urlEl.textContent.trim() : window.location.href;

    const open = () => {
        root.setAttribute('aria-hidden', 'false');
        backdrop.classList.remove('opacity-0');
        backdrop.classList.add('opacity-100');
        panel.classList.remove('translate-y-full');
        document.body.style.overflow = 'hidden';
    };

    const close = () => {
        root.setAttribute('aria-hidden', 'true');
        backdrop.classList.add('opacity-0');
        backdrop.classList.remove('opacity-100');
        panel.classList.add('translate-y-full');
        document.body.style.overflow = '';
    };

    if (copyBtn) {
        copyBtn.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(shareUrl);
            } catch (_) {
                const ta = document.createElement('textarea');
                ta.value = shareUrl;
                document.body.appendChild(ta);
                ta.select();
                try { document.execCommand('copy'); } catch (_) {}
                document.body.removeChild(ta);
            }
            if (copyIcon) {
                copyIcon.classList.remove('ri-link');
                copyIcon.classList.add('ri-check-line');
            }
            if (copyLabel) copyLabel.textContent = 'Tersalin';
            setTimeout(() => {
                if (copyIcon) {
                    copyIcon.classList.add('ri-link');
                    copyIcon.classList.remove('ri-check-line');
                }
                if (copyLabel) copyLabel.textContent = 'Salin Link';
            }, 1800);
        });
    }

    if (igBtn) {
        igBtn.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(shareUrl);
            } catch (_) {}
            window.open('https://www.instagram.com/', '_blank', 'noopener');
        });
    }

    openBtns.forEach((btn) => btn.addEventListener('click', open));
    if (closeBtn) closeBtn.addEventListener('click', close);
    if (backdrop) backdrop.addEventListener('click', close);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && root.getAttribute('aria-hidden') === 'false') close();
    });
}
