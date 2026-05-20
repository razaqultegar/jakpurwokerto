import { initCountdown } from '../utils/countdown.js';

function initMenuDrawer() {
    const drawer = document.querySelector('[data-menu-drawer]');
    if (!drawer) return;

    const panel = drawer.querySelector('[data-menu-drawer-panel]');
    const backdrop = drawer.querySelector('[data-menu-drawer-backdrop]');
    const openBtns = document.querySelectorAll('[data-menu-more-open]');
    const closeBtn = drawer.querySelector('[data-menu-drawer-close]');

    const open = () => {
        drawer.setAttribute('aria-hidden', 'false');
        backdrop.classList.remove('opacity-0');
        backdrop.classList.add('opacity-100');
        panel.classList.remove('translate-y-full');
        document.body.style.overflow = 'hidden';
    };

    const close = () => {
        drawer.setAttribute('aria-hidden', 'true');
        backdrop.classList.add('opacity-0');
        backdrop.classList.remove('opacity-100');
        panel.classList.add('translate-y-full');
        document.body.style.overflow = '';
    };

    openBtns.forEach((btn) => btn.addEventListener('click', open));
    if (closeBtn) closeBtn.addEventListener('click', close);
    if (backdrop) backdrop.addEventListener('click', close);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && drawer.getAttribute('aria-hidden') === 'false') close();
    });
}

document.querySelectorAll('[data-countdown]').forEach(initCountdown);
initMenuDrawer();
