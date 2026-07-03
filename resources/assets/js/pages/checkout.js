const CART_STORAGE_KEY = 'jpw.cart.v1';

const formatRupiah = (n) => 'Rp' + n.toLocaleString('id-ID');

const escapeHtml = (s) => String(s).replace(/[&<>"']/g, (c) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;',
}[c]));

function loadCart() {
    try {
        const raw = localStorage.getItem(CART_STORAGE_KEY);
        if (!raw) return [];
        const parsed = JSON.parse(raw);
        return Array.isArray(parsed) ? parsed.filter((it) => it && typeof it === 'object' && it.qty > 0 && it.price >= 0) : [];
    } catch (_) {
        return [];
    }
}

function saveCart(items) {
    try {
        localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(items));
    } catch (_) {}
}

function initAlert() {
    const root = document.querySelector('[data-checkout-alert]');
    if (!root) return { show: () => {}, hide: () => {} };

    const panel = root.querySelector('[data-checkout-alert-panel]');
    const titleEl = root.querySelector('[data-checkout-alert-title]');
    const msgEl = root.querySelector('[data-checkout-alert-message]');
    const closeBtn = root.querySelector('[data-checkout-alert-close]');
    const hiddenClass = 'translate-y-[calc(100%+1rem)]';
    let timer = null;

    const hide = () => {
        panel.classList.add(hiddenClass);
        panel.classList.remove('translate-y-0');
        root.classList.remove('is-open');
    };

    const show = (message, title = 'Data belum lengkap') => {
        if (titleEl) titleEl.textContent = title;
        if (msgEl) msgEl.textContent = message;
        root.classList.add('is-open');
        panel.classList.remove(hiddenClass);
        panel.classList.add('translate-y-0');
        if (timer) clearTimeout(timer);
        timer = setTimeout(hide, 3500);
    };

    if (closeBtn) closeBtn.addEventListener('click', hide);
    return { show, hide };
}

function renderCart(items) {
    const list = document.querySelector('[data-cart-items]');
    const empty = document.querySelector('[data-cart-empty]');
    const subtotalEl = document.querySelector('[data-subtotal]');
    const totalEl = document.querySelector('[data-total]');
    const countEl = document.querySelector('[data-cart-count]');

    const subtotal = items.reduce((s, it) => s + ((parseInt(it.price, 10) || 0) + (parseInt(it.fee, 10) || 0)) * (parseInt(it.qty, 10) || 0), 0);
    const totalQty = items.reduce((s, it) => s + (parseInt(it.qty, 10) || 0), 0);

    if (countEl) countEl.textContent = totalQty;
    if (subtotalEl) {
        subtotalEl.textContent = formatRupiah(subtotal);
        subtotalEl.dataset.value = String(subtotal);
    }
    if (totalEl) totalEl.textContent = formatRupiah(subtotal);

    if (!list || !empty) return subtotal;

    if (items.length === 0) {
        list.innerHTML = '';
        list.classList.add('hidden');
        empty.classList.remove('hidden');
        empty.classList.add('flex');
        return subtotal;
    }

    empty.classList.add('hidden');
    empty.classList.remove('flex');
    list.classList.remove('hidden');
    list.innerHTML = items.map((it, i) => {
        const isTicket = (it.category || '') === 'Tiket';
        const img = it.image
            ? `<img src="/build/${escapeHtml(it.image)}" alt="${escapeHtml(it.name)}" class="h-16 w-16 shrink-0 rounded-xl object-cover ring-1 ring-mercury">`
            : (isTicket
                ? `<span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl bg-white text-primary ring-1 ring-mercury"><i class="ri-ticket-2-fill text-2xl"></i></span>`
                : `<span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl bg-white text-primary ring-1 ring-mercury"><i class="ri-shirt-fill text-2xl"></i></span>`);
        const feeText = it.fee > 0 ? ` <span class="text-primary">(+${formatRupiah(it.fee)} kustom)</span>` : '';
        const detailLine = isTicket
            ? escapeHtml(it.name)
            : `${escapeHtml(it.category)} · ${escapeHtml(it.sleeve)} · Ukuran ${escapeHtml(it.size)}${feeText}`;
        return `
            <div class="relative flex items-center gap-3 rounded-2xl bg-skull p-3 pr-10 ring-1 ring-mercury">
                ${img}
                <div class="min-w-0 flex-1">
                    <div class="truncate text-xs font-bold text-foreground">${isTicket ? escapeHtml(it.category) : escapeHtml(it.name)}</div>
                    <div class="mt-0.5 text-[10px] text-onyx">${detailLine}</div>
                    <div class="mt-1 inline-flex items-center gap-2">
                        <span class="rounded-md bg-white px-1.5 py-0.5 text-[10px] font-semibold text-foreground ring-1 ring-mercury">x${it.qty}</span>
                        <span class="text-[11px] font-bold text-primary">${formatRupiah((parseInt(it.price, 10) || 0) + (parseInt(it.fee, 10) || 0))}</span>
                    </div>
                </div>
                <button type="button" class="absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-full bg-white text-red-500 ring-1 ring-mercury transition active:scale-95" data-cart-remove="${i}" aria-label="Hapus item">
                    <i class="ri-delete-bin-line text-sm"></i>
                </button>
            </div>
        `;
    }).join('');

    list.querySelectorAll('[data-cart-remove]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const idx = parseInt(btn.dataset.cartRemove, 10);
            if (Number.isNaN(idx)) return;
            removeCartItem(idx);
        });
    });

    return subtotal;
}

function syncHiddenInputs(items) {
    const wrap = document.querySelector('[data-cart-hidden-inputs]');
    if (!wrap) return;
    wrap.innerHTML = items.map((it, i) => {
        const fields = {
            slug: it.slug || '',
            name: it.name || '',
            image: it.image || '',
            category: it.category || '',
            sleeve: it.sleeve || '',
            size: it.size || '',
            qty: parseInt(it.qty, 10) || 0,
            price: parseInt(it.price, 10) || 0,
            fee: parseInt(it.fee, 10) || 0,
        };
        return Object.entries(fields).map(([k, v]) => (
            `<input type="hidden" name="items[${i}][${k}]" value="${escapeHtml(v)}">`
        )).join('');
    }).join('');
}

function getTotals() {
    const sub = parseInt(document.querySelector('[data-subtotal]')?.dataset.value || '0', 10);
    return { subtotal: sub, total: sub };
}

function isTicketOnlyCart(items) {
    return items.length > 0 && items.every((it) => (it.category || '') === 'Tiket');
}

function getSelectedPaymentType() {
    const el = document.querySelector('input[name="payment_type"]:checked');
    return el ? el.value : 'dp';
}

function updatePayCTA() {
    const { total } = getTotals();
    const type = getSelectedPaymentType();
    const amount = type === 'dp' ? Math.round(total * 0.5) : total;
    const label = type === 'dp' ? 'Bayar sekarang (DP 50%)' : 'Bayar lunas';
    const labelEl = document.querySelector('[data-pay-label]');
    const amountEl = document.querySelector('[data-pay-amount]');
    if (labelEl) labelEl.textContent = label;
    if (amountEl) amountEl.textContent = formatRupiah(amount);
}

function initPaymentType() {
    document.querySelectorAll('input[name="payment_type"]').forEach((el) => {
        el.addEventListener('change', updatePayCTA);
    });
}

function setPayButtonsDisabled(disabled) {
    const submitBtn = document.querySelector('form button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = disabled;
        submitBtn.classList.toggle('opacity-50', disabled);
        submitBtn.classList.toggle('pointer-events-none', disabled);
    }
}

function initAddressCounter() {
    const textarea = document.querySelector('[data-field="address"]');
    const counter = document.querySelector('[data-address-count]');
    if (!textarea || !counter) return;
    const update = () => { counter.textContent = textarea.value.length; };
    textarea.addEventListener('input', update);
    update();
}

const SHIPPING_DETAILS = {
    pickup: 'Pilih kota terdekat. Titik temu & jadwal ditentukan admin via WhatsApp.',
    kirim: 'Pengiriman menggunakan JNT Express dan untuk biaya ongkos kirim sepenuhnya di tanggung oleh pembeli.',
};

function getSelectedShipping() {
    const el = document.querySelector('input[name="shipping_method"]:checked');
    return el ? el.value : 'pickup';
}

function applyShippingState() {
    const method = getSelectedShipping();
    const detailText = document.querySelector('[data-shipping-detail-text]');
    if (detailText) detailText.textContent = SHIPPING_DETAILS[method] || '';

    const addrWrap = document.querySelector('[data-address-wrap]');
    const addr = document.querySelector('[data-field="address"]');
    if (addrWrap && addr) {
        const isKirim = method === 'kirim';
        addrWrap.classList.toggle('hidden', !isKirim);
        if (!isKirim) {
            addr.value = '';
            setFieldError('address', '');
            const counter = document.querySelector('[data-address-count]');
            if (counter) counter.textContent = '0';
        }
    }

    const pickupWrap = document.querySelector('[data-pickup-wrap]');
    const pickup = document.querySelector('[data-field="pickup_location"]');
    if (pickupWrap && pickup) {
        const isPickup = method === 'pickup';
        pickupWrap.classList.toggle('hidden', !isPickup);
        if (!isPickup) {
            pickup.value = '';
            setFieldError('pickup_location', '');
        }
    }
}

function initShipping() {
    document.querySelectorAll('input[name="shipping_method"]').forEach((el) => {
        el.addEventListener('change', applyShippingState);
    });
    applyShippingState();
}

function applyCartTypeUI(items) {
    const ticketOnly = isTicketOnlyCart(items);
    const shippingSection = document.querySelector('[data-shipping-section]');
    const paymentTypeSection = document.querySelector('[data-payment-type-section]');
    const shippingDivider = document.querySelector('[data-shipping-divider]');
    const paymentTypeDivider = document.querySelector('[data-payment-type-divider]');
    const bankTransferSection = document.querySelector('[data-bank-transfer-section]');
    if (shippingSection) shippingSection.classList.toggle('hidden', ticketOnly);
    if (paymentTypeSection) paymentTypeSection.classList.toggle('hidden', ticketOnly);
    if (shippingDivider) shippingDivider.classList.toggle('hidden', ticketOnly);
    if (paymentTypeDivider) paymentTypeDivider.classList.toggle('hidden', ticketOnly);
    if (bankTransferSection) bankTransferSection.classList.toggle('hidden', ticketOnly);

    if (ticketOnly) {
        const fullRadio = document.querySelector('input[name="payment_type"][value="full"]');
        if (fullRadio) fullRadio.checked = true;
        const pickupRadio = document.querySelector('input[name="shipping_method"][value="pickup"]');
        if (pickupRadio) pickupRadio.checked = true;
        const qrisRadio = document.querySelector('input[name="payment_method"][value^="qris"]');
        if (qrisRadio) qrisRadio.checked = true;
        setFieldError('pickup_location', '');
        setFieldError('address', '');
    }

    return ticketOnly;
}

function setFieldError(name, message) {
    const input = document.querySelector(`[data-field="${name}"]`);
    const err = document.querySelector(`[data-error="${name}"]`);
    if (!input || !err) return;
    if (message) {
        input.classList.remove('border-mercury');
        input.classList.add('border-red-500');
        err.textContent = message;
        err.classList.remove('hidden');
    } else {
        input.classList.add('border-mercury');
        input.classList.remove('border-red-500');
        err.textContent = '';
        err.classList.add('hidden');
    }
}

function validateForm(items) {
    const get = (n) => document.querySelector(`[data-field="${n}"]`)?.value.trim() || '';
    const errors = {};

    const name = get('name');
    if (!name) errors.name = 'Nama lengkap wajib diisi.';
    else if (name.length < 3) errors.name = 'Nama minimal 3 karakter.';

    const email = get('email');
    if (!email) errors.email = 'Email wajib diisi.';
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) errors.email = 'Format email tidak valid.';

    const phone = get('phone').replace(/^0+/, '');
    if (!phone) errors.phone = 'No. WhatsApp wajib diisi.';
    else if (!/^[0-9]{8,15}$/.test(phone)) errors.phone = 'Gunakan angka saja, 8–15 digit.';

    if (!isTicketOnlyCart(items)) {
        const shipping = getSelectedShipping();
        if (shipping === 'kirim') {
            const address = get('address');
            if (!address) errors.address = 'Alamat lengkap wajib diisi untuk pengiriman.';
            else if (address.length < 10) errors.address = 'Alamat terlalu singkat, mohon lebih detail.';
        } else if (shipping === 'pickup') {
            const loc = get('pickup_location');
            if (!loc) errors.pickup_location = 'Pilih kota pengambilan dulu.';
        }
    }

    ['name', 'email', 'phone', 'address', 'pickup_location'].forEach((f) => setFieldError(f, errors[f] || ''));
    return Object.keys(errors).length === 0;
}

function initForm(alert, getCart) {
    const form = document.querySelector('form');
    if (!form) return;

    ['name', 'email', 'phone', 'address', 'pickup_location'].forEach((f) => {
        const el = document.querySelector(`[data-field="${f}"]`);
        if (el) el.addEventListener(el.tagName === 'SELECT' ? 'change' : 'input', () => setFieldError(f, ''));
    });

    form.addEventListener('submit', (e) => {
        const items = getCart();
        if (items.length === 0) {
            e.preventDefault();
            alert.show('Keranjangmu masih kosong. Tambahkan merchandise dulu sebelum checkout.', 'Keranjang kosong');
            return;
        }
        if (!validateForm(items)) {
            e.preventDefault();
            alert.show('Mohon periksa kembali data pemesan yang ditandai merah.');
            const firstError = form.querySelector('.border-red-500');
            if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
        const phoneInput = document.querySelector('[data-field="phone"]');
        if (phoneInput) phoneInput.value = phoneInput.value.replace(/^0+/, '');
        syncHiddenInputs(items);
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-70', 'pointer-events-none');
            submitBtn.innerHTML = '<i class="ri-loader-4-line animate-spin text-base"></i> Memproses...';
        }
    });
}

let cart = loadCart();
const getCart = () => cart;

function removeCartItem(index) {
    if (index < 0 || index >= cart.length) return;
    cart.splice(index, 1);
    saveCart(cart);
    renderCart(cart);
    syncHiddenInputs(cart);
    applyCartTypeUI(cart);
    updatePayCTA();
    setPayButtonsDisabled(cart.length === 0);
    if (cart.length === 0) {
        alert.show('Keranjang sekarang kosong. Tambahkan merchandise dulu sebelum melanjutkan.', 'Keranjang kosong');
    }
}

const alert = initAlert();
renderCart(cart);
syncHiddenInputs(cart);
applyCartTypeUI(cart);
initShipping();
initPaymentType();
updatePayCTA();
setPayButtonsDisabled(cart.length === 0);
initAddressCounter();
initForm(alert, getCart);

if (cart.length === 0) {
    alert.show('Tambahkan merchandise dulu sebelum melanjutkan checkout.', 'Keranjang kosong');
}
