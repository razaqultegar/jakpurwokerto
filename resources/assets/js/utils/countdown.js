const pad = (n) => String(n).padStart(2, '0');

export function initCountdown(root) {
    const startAt = new Date(root.dataset.start).getTime();
    const endAt = new Date(root.dataset.end).getTime();

    const els = {
        days: root.querySelector('[data-countdown-days]'),
        hours: root.querySelector('[data-countdown-hours]'),
        minutes: root.querySelector('[data-countdown-minutes]'),
        seconds: root.querySelector('[data-countdown-seconds]'),
        label: root.querySelector('[data-countdown-label]'),
        status: root.querySelector('[data-countdown-status]'),
    };

    const render = (ms, label, status) => {
        const total = Math.max(0, Math.floor(ms / 1000));
        els.days.textContent = pad(Math.floor(total / 86400));
        els.hours.textContent = pad(Math.floor((total % 86400) / 3600));
        els.minutes.textContent = pad(Math.floor((total % 3600) / 60));
        els.seconds.textContent = pad(total % 60);
        if (els.label) els.label.textContent = label;
        if (els.status) els.status.textContent = status;
    };

    let timer;
    const tick = () => {
        const now = Date.now();
        if (now < startAt) {
            render(startAt - now, 'PO dibuka dalam', 'Segera');
        } else if (now <= endAt) {
            render(endAt - now, 'PO berakhir dalam', 'Berlangsung');
        } else {
            render(0, 'Pre-Order telah ditutup', 'Ditutup');
            clearInterval(timer);
        }
    };

    tick();
    timer = setInterval(tick, 1000);
}
