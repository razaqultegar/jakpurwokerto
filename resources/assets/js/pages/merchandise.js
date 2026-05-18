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

function getSelectedSize() {
    const active = document.querySelector('[data-size][aria-pressed="true"]');
    return active ? active.dataset.size : null;
}

function getSelectedCategory() {
    const active = document.querySelector('[data-category][aria-pressed="true"]');
    return active ? { key: active.dataset.category, name: active.querySelector('.block.text-xs')?.textContent?.trim() || active.dataset.category } : null;
}

function getQuantity() {
    const el = document.querySelector('[data-qty-value]');
    return el ? parseInt(el.textContent, 10) || 1 : 1;
}

function initAlert() {
    const root = document.querySelector('[data-merch-alert]');
    if (!root) return { show: () => {} };

    const panel = root.querySelector('[data-merch-alert-panel]');
    const titleEl = root.querySelector('[data-merch-alert-title]');
    const msgEl = root.querySelector('[data-merch-alert-message]');
    const closeBtn = root.querySelector('[data-merch-alert-close]');
    let timer = null;

    const hiddenClass = 'translate-y-[calc(100%+1rem)]';

    const hide = () => {
        panel.classList.add(hiddenClass);
        panel.classList.remove('translate-y-0');
    };

    const show = (message, title = 'Pilih ukuran dulu') => {
        if (titleEl) titleEl.textContent = title;
        if (msgEl) msgEl.textContent = message;
        panel.classList.remove(hiddenClass);
        panel.classList.add('translate-y-0');
        if (timer) clearTimeout(timer);
        timer = setTimeout(hide, 3200);
    };

    if (closeBtn) closeBtn.addEventListener('click', hide);
    return { show, hide };
}

function formatRupiah(n) {
    return 'Rp ' + n.toLocaleString('id-ID');
}

function initCart(alert) {
    const drawer = document.querySelector('[data-cart-drawer]');
    if (!drawer) return;

    const panel = drawer.querySelector('[data-cart-panel]');
    const backdrop = drawer.querySelector('[data-cart-backdrop]');
    const openBtn = document.querySelector('[data-cart-open]');
    const closeBtn = drawer.querySelector('[data-cart-close]');
    const addBtn = document.querySelector('[data-cart-add]');
    const orderBtn = document.querySelector('[data-cart-order]');
    const checkoutBtn = drawer.querySelector('[data-cart-checkout]');
    const list = drawer.querySelector('[data-cart-list]');
    const empty = drawer.querySelector('[data-cart-empty]');
    const totalEl = drawer.querySelector('[data-cart-total]');
    const summaryEl = drawer.querySelector('[data-cart-summary]');
    const countEl = document.querySelector('[data-cart-count]');

    const priceEl = document.querySelector('[data-merch-price]');
    const price = priceEl ? parseInt(priceEl.dataset.price || '0', 10) : 175000;
    const nameEl = document.querySelector('[data-merch-name]');
    const productName = nameEl ? nameEl.textContent.trim() : 'Jersey';

    const items = [];

    const open = () => {
        drawer.setAttribute('aria-hidden', 'false');
        drawer.classList.remove('pointer-events-none');
        backdrop.classList.remove('opacity-0');
        backdrop.classList.add('opacity-100');
        panel.classList.remove('translate-x-full');
        document.body.style.overflow = 'hidden';
    };

    const close = () => {
        drawer.setAttribute('aria-hidden', 'true');
        backdrop.classList.add('opacity-0');
        backdrop.classList.remove('opacity-100');
        panel.classList.add('translate-x-full');
        document.body.style.overflow = '';
        setTimeout(() => drawer.classList.add('pointer-events-none'), 300);
    };

    const render = () => {
        const totalQty = items.reduce((s, it) => s + it.qty, 0);
        const totalPrice = items.reduce((s, it) => s + it.qty * it.price, 0);

        if (countEl) {
            countEl.textContent = totalQty;
            countEl.classList.toggle('hidden', totalQty === 0);
            countEl.classList.toggle('flex', totalQty > 0);
        }
        if (summaryEl) summaryEl.textContent = totalQty;
        if (totalEl) totalEl.textContent = formatRupiah(totalPrice);
        if (checkoutBtn) checkoutBtn.disabled = totalQty === 0;

        if (items.length === 0) {
            empty.classList.remove('hidden');
            empty.classList.add('flex');
            list.classList.add('hidden');
            list.classList.remove('flex');
            list.innerHTML = '';
            return;
        }

        empty.classList.add('hidden');
        empty.classList.remove('flex');
        list.classList.remove('hidden');
        list.classList.add('flex');
        list.innerHTML = items.map((it, i) => `
            <li class="flex items-center gap-3 rounded-2xl bg-skull p-3 ring-1 ring-mercury">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary text-white">
                    <i class="ri-shirt-fill text-lg"></i>
                </span>
                <div class="flex-1">
                    <div class="text-xs font-bold text-foreground">${it.name}</div>
                    <div class="mt-0.5 text-[10px] text-onyx">${it.category} · Ukuran ${it.size}</div>
                    <div class="mt-1 text-[11px] font-semibold text-primary">${formatRupiah(it.price)} × ${it.qty}</div>
                </div>
                <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full bg-white text-foreground ring-1 ring-mercury" data-cart-remove="${i}" aria-label="Hapus item">
                    <i class="ri-delete-bin-line text-sm"></i>
                </button>
            </li>
        `).join('');

        list.querySelectorAll('[data-cart-remove]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const idx = parseInt(btn.dataset.cartRemove, 10);
                items.splice(idx, 1);
                render();
            });
        });
    };

    const addToCart = () => {
        const size = getSelectedSize();
        if (!size) {
            alert.show('Silakan pilih ukuran jersey terlebih dahulu sebelum menambahkan ke keranjang.');
            return false;
        }
        const cat = getSelectedCategory();
        const qty = getQuantity();
        const existing = items.find((it) => it.size === size && it.category === (cat?.name || '-'));
        if (existing) {
            existing.qty += qty;
        } else {
            items.push({
                name: productName,
                size,
                category: cat?.name || '-',
                qty,
                price,
            });
        }
        render();
        return true;
    };

    if (openBtn) openBtn.addEventListener('click', open);
    if (closeBtn) closeBtn.addEventListener('click', close);
    if (backdrop) backdrop.addEventListener('click', close);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && drawer.getAttribute('aria-hidden') === 'false') close();
    });

    if (addBtn) {
        addBtn.addEventListener('click', () => {
            if (addToCart()) open();
        });
    }

    if (orderBtn) {
        orderBtn.addEventListener('click', () => {
            const size = getSelectedSize();
            if (!size) {
                alert.show('Silakan pilih ukuran jersey terlebih dahulu sebelum memesan.');
                return;
            }
            addToCart();
            open();
        });
    }

    render();
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
const merchAlert = initAlert();
initCart(merchAlert);
