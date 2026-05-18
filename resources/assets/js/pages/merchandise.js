const Swiper = window.Swiper;

const pad = (n) => String(n).padStart(2, '0');

function initCountdown(root) {
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
    const timer = setInterval(tick, 1000);
}

function initHeroSwiper() {
    const el = document.querySelector('[data-hero-swiper]');
    if (!el) return;

    const counter = document.querySelector('[data-hero-counter]');
    const total = el.querySelectorAll('.swiper-slide').length;

    new Swiper(el, {
        loop: true,
        speed: 500,
        autoplay: { delay: 4000, disableOnInteraction: false },
        navigation: {
            nextEl: '.hero-swiper-next',
            prevEl: '.hero-swiper-prev',
        },
        pagination: {
            el: '.hero-swiper-pagination',
            clickable: true,
            bulletClass: 'hero-bullet',
            bulletActiveClass: 'hero-bullet-active',
            renderBullet: (_, className) =>
                `<span class="${className}"></span>`,
        },
        on: {
            slideChange(swiper) {
                if (counter) counter.textContent = `${swiper.realIndex + 1} / ${total}`;
            },
        },
    });
}

function initGallerySwiper() {
    const el = document.querySelector('[data-gallery-swiper]');
    if (!el) return;

    new Swiper(el, {
        slidesPerView: 'auto',
        spaceBetween: 10,
        freeMode: true,
        navigation: {
            nextEl: '.gallery-swiper-next',
            prevEl: '.gallery-swiper-prev',
            disabledClass: 'swiper-button-disabled',
        },
    });
}

function initCategoryPicker() {
    const buttons = document.querySelectorAll('[data-category]');
    buttons.forEach((btn) => {
        btn.addEventListener('click', () => {
            buttons.forEach((b) => b.setAttribute('aria-pressed', b === btn ? 'true' : 'false'));
        });
    });
}

function initSizePicker() {
    const buttons = document.querySelectorAll('[data-size]');
    const helper = document.querySelector('[data-size-helper]');
    buttons.forEach((btn) => {
        btn.addEventListener('click', () => {
            buttons.forEach((b) => b.setAttribute('aria-pressed', b === btn ? 'true' : 'false'));
            if (helper) {
                helper.innerHTML = `<i class="ri-check-line text-primary"></i> Ukuran <strong class="font-semibold text-foreground">${btn.dataset.size}</strong> dipilih.`;
            }
        });
    });
}

function initQuantity() {
    const root = document.querySelector('[data-merch-options]');
    if (!root) return;

    const dec = root.querySelector('[data-qty-decrement]');
    const inc = root.querySelector('[data-qty-increment]');
    const display = root.querySelector('[data-qty-value]');
    const min = 1;
    let qty = 1;

    const render = () => {
        display.textContent = qty;
        dec.disabled = qty <= min;
    };

    dec.addEventListener('click', () => {
        if (qty > min) {
            qty -= 1;
            render();
        }
    });

    inc.addEventListener('click', () => {
        qty += 1;
        render();
    });

    render();
}

document.querySelectorAll('[data-countdown]').forEach(initCountdown);
initHeroSwiper();
initGallerySwiper();
initCategoryPicker();
initSizePicker();
initQuantity();
