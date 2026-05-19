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

function initSizeGuide() {
    const root = document.querySelector('[data-size-guide]');
    if (!root) return;

    const panel = root.querySelector('[data-size-guide-panel]');
    const backdrop = root.querySelector('[data-size-guide-backdrop]');
    const openBtns = document.querySelectorAll('[data-size-guide-open]');
    const closeBtn = root.querySelector('[data-size-guide-close]');
    const swiperEl = root.querySelector('[data-size-guide-swiper]');
    let swiper = null;

    const open = () => {
        root.setAttribute('aria-hidden', 'false');
        root.classList.remove('pointer-events-none');
        backdrop.classList.remove('opacity-0');
        backdrop.classList.add('opacity-100');
        panel.classList.remove('scale-95', 'opacity-0');
        panel.classList.add('scale-100', 'opacity-100');
        document.body.style.overflow = 'hidden';
        if (swiper) swiper.update();
    };

    const close = () => {
        root.setAttribute('aria-hidden', 'true');
        backdrop.classList.add('opacity-0');
        backdrop.classList.remove('opacity-100');
        panel.classList.add('scale-95', 'opacity-0');
        panel.classList.remove('scale-100', 'opacity-100');
        document.body.style.overflow = '';
        setTimeout(() => root.classList.add('pointer-events-none'), 300);
    };

    if (swiperEl) {
        swiper = new Swiper(swiperEl, {
            slidesPerView: 1,
            spaceBetween: 12,
            loop: false,
            navigation: {
                nextEl: '.size-guide-swiper-next',
                prevEl: '.size-guide-swiper-prev',
                disabledClass: 'swiper-button-disabled',
            },
            pagination: {
                el: '.size-guide-swiper-pagination',
                clickable: true,
                bulletClass: 'h-1.5 w-1.5 rounded-full bg-mercury transition cursor-pointer',
                bulletActiveClass: '!w-4 !bg-primary',
            },
        });
    }

    openBtns.forEach((btn) => btn.addEventListener('click', open));
    if (closeBtn) closeBtn.addEventListener('click', close);
    if (backdrop) backdrop.addEventListener('click', close);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && root.getAttribute('aria-hidden') === 'false') close();
    });
}

function initShare() {
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
        root.classList.remove('pointer-events-none');
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
        setTimeout(() => root.classList.add('pointer-events-none'), 300);
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

function getBasePrice() {
    const sleeve = document.querySelector('[data-sleeve][aria-pressed="true"]');
    const cat = document.querySelector('[data-category][aria-pressed="true"]');
    const catKey = cat ? cat.dataset.category : null;
    if (sleeve && catKey) {
        try {
            const prices = JSON.parse(sleeve.dataset.sleevePrices || '{}');
            if (prices[catKey] != null) return parseInt(prices[catKey], 10);
        } catch (_) {}
    }
    return 0;
}

function initCategoryPicker() {
    const buttons = document.querySelectorAll('[data-category]');
    buttons.forEach((btn) => {
        btn.addEventListener('click', () => {
            buttons.forEach((b) => b.setAttribute('aria-pressed', b === btn ? 'true' : 'false'));
            refreshSelectedStrip();
        });
    });
}

function initSleevePicker() {
    const buttons = document.querySelectorAll('[data-sleeve]');
    buttons.forEach((btn) => {
        btn.addEventListener('click', () => {
            buttons.forEach((b) => b.setAttribute('aria-pressed', b === btn ? 'true' : 'false'));
            refreshSelectedStrip();
        });
    });
}

function initSizePicker() {
    const buttons = document.querySelectorAll('[data-size]');
    const helper = document.querySelector('[data-size-helper]');
    const customWrap = document.querySelector('[data-custom-size-wrap]');
    const customInput = document.querySelector('[data-custom-size-input]');

    const updateHelper = (btn) => {
        if (!helper) return;
        const fee = parseInt(btn.dataset.sizeFee || '0', 10);
        const isCustom = btn.dataset.size === 'Kustom';
        const customVal = customInput ? customInput.value.trim().toUpperCase() : '';
        const label = isCustom && customVal ? `Kustom (${customVal})` : btn.dataset.size;
        helper.innerHTML = `<i class="ri-check-line text-primary"></i> Ukuran <strong class="font-semibold text-foreground">${label}</strong> dipilih.`;
    };

    buttons.forEach((btn) => {
        btn.addEventListener('click', () => {
            buttons.forEach((b) => b.setAttribute('aria-pressed', b === btn ? 'true' : 'false'));
            const isCustom = btn.dataset.size === 'Kustom';
            if (customWrap) customWrap.classList.toggle('hidden', !isCustom);
            if (isCustom && customInput) setTimeout(() => customInput.focus(), 50);
            updateHelper(btn);
            refreshSelectedStrip();
        });
    });

    if (customInput) {
        customInput.addEventListener('input', () => {
            const active = document.querySelector('[data-size][aria-pressed="true"]');
            if (active && active.dataset.size === 'Kustom') updateHelper(active);
            refreshSelectedStrip();
        });
    }
}

function refreshSelectedStrip() {
    const strip = document.querySelector('[data-merch-selected]');
    if (!strip) return;
    const panel = strip.querySelector('[data-merch-selected-panel]');
    const variantEl = strip.querySelector('[data-merch-selected-variant]');
    const totalEl = strip.querySelector('[data-merch-selected-total]');
    const qtyEl = strip.querySelector('[data-merch-selected-qty]');

    const activeSize = document.querySelector('[data-size][aria-pressed="true"]');
    const cat = getSelectedCategory();
    const sleeve = getSelectedSleeve();
    const qty = getQuantity();
    const fee = getSelectedSizeFee();

    const hiddenClass = 'translate-y-[calc(100%+1rem)]';

    if (!activeSize || !cat || !sleeve) {
        panel.classList.add(hiddenClass);
        panel.classList.remove('translate-y-0');
        return;
    }

    let sizeLabel;
    if (activeSize.dataset.size === 'Kustom') {
        const customInput = document.querySelector('[data-custom-size-input]');
        const val = customInput ? customInput.value.trim().toUpperCase() : '';
        sizeLabel = val ? `Kustom (${val})` : 'Kustom (—)';
    } else {
        sizeLabel = activeSize.dataset.size;
    }

    const total = (getBasePrice() + fee) * qty;
    if (variantEl) variantEl.textContent = `${cat.name} · ${sleeve.name} · ${sizeLabel}`;
    if (totalEl) totalEl.textContent = formatRupiah(total);
    if (qtyEl) qtyEl.textContent = qty;
    panel.classList.remove(hiddenClass);
    panel.classList.add('translate-y-0');
}

function hideSelectedStrip() {
    const strip = document.querySelector('[data-merch-selected]');
    if (!strip) return;
    const panel = strip.querySelector('[data-merch-selected-panel]');
    panel.classList.add('translate-y-[calc(100%+1rem)]');
    panel.classList.remove('translate-y-0');
}

function getSelectedSize() {
    const active = document.querySelector('[data-size][aria-pressed="true"]');
    if (!active) return null;
    if (active.dataset.size === 'Kustom') {
        const customInput = document.querySelector('[data-custom-size-input]');
        const val = customInput ? customInput.value.trim().toUpperCase() : '';
        return val ? `Kustom (${val})` : null;
    }
    return active.dataset.size;
}

function getSelectedSizeFee() {
    const active = document.querySelector('[data-size][aria-pressed="true"]');
    return active ? parseInt(active.dataset.sizeFee || '0', 10) : 0;
}

function isCustomSizeSelected() {
    const active = document.querySelector('[data-size][aria-pressed="true"]');
    return !!(active && active.dataset.size === 'Kustom');
}

function getSelectedCategory() {
    const active = document.querySelector('[data-category][aria-pressed="true"]');
    return active ? { key: active.dataset.category, name: active.dataset.categoryName || active.dataset.category } : null;
}

function getSelectedSleeve() {
    const active = document.querySelector('[data-sleeve][aria-pressed="true"]');
    return active ? { key: active.dataset.sleeve, name: active.dataset.sleeveName || active.dataset.sleeve } : null;
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

const CART_STORAGE_KEY = 'jpw.cart.v1';

function loadCart() {
    try {
        const raw = localStorage.getItem(CART_STORAGE_KEY);
        if (!raw) return [];
        const parsed = JSON.parse(raw);
        return Array.isArray(parsed) ? parsed : [];
    } catch (_) {
        return [];
    }
}

function saveCart(items) {
    try {
        localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(items));
    } catch (_) {}
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

    const nameEl = document.querySelector('[data-merch-name]');
    const productName = nameEl ? nameEl.textContent.trim() : 'Jersey';
    const meta = document.querySelector('[data-merch-meta]');
    const productSlug = meta ? (meta.dataset.merchSlug || '') : '';
    const productImage = meta ? (meta.dataset.merchImage || '') : '';

    const items = loadCart();

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
                    <div class="mt-0.5 text-[10px] text-onyx">${it.category} · ${it.sleeve} · ${it.size}${it.fee > 0 ? ` <span class="text-primary">(+${formatRupiah(it.fee)} kustom)</span>` : ''}</div>
                    <div class="mt-1 text-[11px] font-semibold text-primary">${formatRupiah(it.price)} × ${it.qty}</div>
                </div>
                <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full bg-white text-foreground ring-1 ring-mercury" data-cart-remove="${i}">
                    <i class="ri-delete-bin-line text-sm"></i>
                </button>
            </li>
        `).join('');

        list.querySelectorAll('[data-cart-remove]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const idx = parseInt(btn.dataset.cartRemove, 10);
                items.splice(idx, 1);
                saveCart(items);
                render();
            });
        });
    };

    const addToCart = () => {
        if (isCustomSizeSelected()) {
            const customInput = document.querySelector('[data-custom-size-input]');
            const val = customInput ? customInput.value.trim() : '';
            if (!val) {
                alert.show('Tulis ukuran kustom yang kamu inginkan (misal 2XL, 3XL).', 'Ukuran kustom kosong');
                if (customInput) customInput.focus();
                return false;
            }
        }
        const size = getSelectedSize();
        if (!size) {
            alert.show('Silakan pilih ukuran jersey terlebih dahulu sebelum menambahkan ke keranjang.');
            return false;
        }
        const cat = getSelectedCategory();
        const sleeve = getSelectedSleeve();
        const qty = getQuantity();
        const fee = getSelectedSizeFee();
        const itemPrice = getBasePrice() + fee;
        const sleeveName = sleeve?.name || '-';
        const existing = items.find((it) => it.size === size && it.category === (cat?.name || '-') && it.sleeve === sleeveName && it.slug === productSlug);
        if (existing) {
            existing.qty += qty;
        } else {
            items.push({
                slug: productSlug,
                name: productName,
                image: productImage,
                size,
                category: cat?.name || '-',
                sleeve: sleeveName,
                qty,
                price: itemPrice,
                fee,
            });
        }
        saveCart(items);
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
            if (addToCart()) {
                hideSelectedStrip();
                open();
            }
        });
    }

    if (orderBtn) {
        orderBtn.addEventListener('click', () => {
            if (items.length > 0) {
                hideSelectedStrip();
                window.location.href = '/bayar';
                return;
            }
            const size = getSelectedSize();
            if (!size) {
                alert.show('Silakan pilih ukuran jersey terlebih dahulu sebelum memesan.');
                return;
            }
            if (addToCart()) {
                hideSelectedStrip();
                window.location.href = '/bayar';
            }
        });
    }

    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', () => {
            if (items.length === 0) return;
            window.location.href = '/bayar';
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
            refreshSelectedStrip();
        }
    });

    inc.addEventListener('click', () => {
        qty += 1;
        render();
        refreshSelectedStrip();
    });

    render();
}

document.querySelectorAll('[data-countdown]').forEach(initCountdown);
initHeroSwiper();
initCategoryPicker();
initSleevePicker();
initSizeGuide();
initShare();
initSizePicker();
initQuantity();
const merchAlert = initAlert();
initCart(merchAlert);
refreshSelectedStrip();
