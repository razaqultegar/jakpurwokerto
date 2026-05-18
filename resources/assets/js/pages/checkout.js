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
    };

    const show = (message, title = 'Data belum lengkap') => {
        if (titleEl) titleEl.textContent = title;
        if (msgEl) msgEl.textContent = message;
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

    const subtotal = items.reduce((s, it) => s + (parseInt(it.price, 10) || 0) * (parseInt(it.qty, 10) || 0), 0);
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
    list.innerHTML = items.map((it) => {
        const img = it.image
            ? `<img src="/build/${escapeHtml(it.image)}" alt="${escapeHtml(it.name)}" class="h-16 w-16 shrink-0 rounded-xl object-cover ring-1 ring-mercury">`
            : `<span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl bg-white text-primary ring-1 ring-mercury"><i class="ri-shirt-fill text-2xl"></i></span>`;
        const feeText = it.fee > 0 ? ` <span class="text-primary">(+${formatRupiah(it.fee)} kustom)</span>` : '';
        return `
            <div class="flex items-center gap-3 rounded-2xl bg-skull p-3 ring-1 ring-mercury">
                ${img}
                <div class="min-w-0 flex-1">
                    <div class="truncate text-xs font-bold text-foreground">${escapeHtml(it.name)}</div>
                    <div class="mt-0.5 text-[10px] text-onyx">${escapeHtml(it.category)} · ${escapeHtml(it.sleeve)} · Ukuran ${escapeHtml(it.size)}${feeText}</div>
                    <div class="mt-1 inline-flex items-center gap-2">
                        <span class="rounded-md bg-white px-1.5 py-0.5 text-[10px] font-semibold text-foreground ring-1 ring-mercury">x${it.qty}</span>
                        <span class="text-[11px] font-bold text-primary">${formatRupiah(it.price)}</span>
                    </div>
                </div>
            </div>
        `;
    }).join('');

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

function getSelectedPaymentType() {
    const el = document.querySelector('input[name="payment_type"]:checked');
    return el ? el.value : 'dp';
}

function updatePaymentAmounts() {
    const { total } = getTotals();
    const dpEl = document.querySelector('[data-payment-amount="dp"]');
    const fullEl = document.querySelector('[data-payment-amount="full"]');
    if (dpEl) dpEl.textContent = formatRupiah(Math.round(total * 0.5));
    if (fullEl) fullEl.textContent = formatRupiah(total);
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

function setPayButtonsDisabled(disabled) {
    const submitBtn = document.querySelector('form button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = disabled;
        submitBtn.classList.toggle('opacity-50', disabled);
        submitBtn.classList.toggle('pointer-events-none', disabled);
    }
}

function initPaymentType() {
    document.querySelectorAll('input[name="payment_type"]').forEach((el) => {
        el.addEventListener('change', updatePayCTA);
    });
}

function initNoteCounter() {
    const textarea = document.querySelector('[data-field="note"]');
    const counter = document.querySelector('[data-note-count]');
    if (!textarea || !counter) return;
    const update = () => { counter.textContent = textarea.value.length; };
    textarea.addEventListener('input', update);
    update();
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

function validateForm() {
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

    ['name', 'email', 'phone'].forEach((f) => setFieldError(f, errors[f] || ''));
    return Object.keys(errors).length === 0;
}

function initForm(alert, getCart) {
    const form = document.querySelector('form');
    if (!form) return;

    ['name', 'email', 'phone'].forEach((f) => {
        const el = document.querySelector(`[data-field="${f}"]`);
        if (el) el.addEventListener('input', () => setFieldError(f, ''));
    });

    form.addEventListener('submit', (e) => {
        const items = getCart();
        if (items.length === 0) {
            e.preventDefault();
            alert.show('Keranjangmu masih kosong. Tambahkan merchandise dulu sebelum checkout.', 'Keranjang kosong');
            return;
        }
        if (!validateForm()) {
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

const alert = initAlert();
renderCart(cart);
syncHiddenInputs(cart);
updatePaymentAmounts();
updatePayCTA();
setPayButtonsDisabled(cart.length === 0);
initPaymentType();
initNoteCounter();
initForm(alert, getCart);

if (cart.length === 0) {
    alert.show('Tambahkan merchandise dulu sebelum melanjutkan checkout.', 'Keranjang kosong');
}
