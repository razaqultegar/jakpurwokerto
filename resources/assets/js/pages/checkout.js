const formatRupiah = (n) => 'Rp' + n.toLocaleString('id-ID');

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

function getTotals() {
    const sub = parseInt(document.querySelector('[data-subtotal]')?.dataset.value || '0', 10);
    return { subtotal: sub, total: sub };
}

function getSelectedPaymentType() {
    const el = document.querySelector('input[name="payment_type"]:checked');
    return el ? el.value : 'dp';
}

function initPaymentAmounts() {
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

function initForm(alert) {
    const form = document.querySelector('form');
    if (!form) return;

    ['name', 'email', 'phone'].forEach((f) => {
        const el = document.querySelector(`[data-field="${f}"]`);
        if (el) el.addEventListener('input', () => setFieldError(f, ''));
    });

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        if (!validateForm()) {
            alert.show('Mohon periksa kembali data pemesan yang ditandai merah.');
            const firstError = form.querySelector('.border-red-500');
            if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
        const type = getSelectedPaymentType();
        const method = document.querySelector('input[name="payment_method"]:checked')?.value || '';
        const methodLabel = method.startsWith('qris') ? 'QRIS DANA' : 'transfer bank';
        alert.show(`Pesanan diterima. Lanjut ke pembayaran ${type === 'dp' ? 'DP 50%' : 'lunas'} via ${methodLabel}.`, 'Berhasil');
    });
}

const alert = initAlert();
initPaymentAmounts();
updatePayCTA();
initPaymentType();
initNoteCounter();
initForm(alert);
