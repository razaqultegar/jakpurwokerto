import ApexCharts from 'apexcharts';
import { initCountdown } from '../utils/countdown.js';

/* ── Membership Statistics ──────────────────────────────────── */

function initMemberStats() {
    const section = document.getElementById('member-stats-section');
    if (!section) return;

    const COLORS = {
        primary:   '#d84315',
        light:     '#f57c00',
        lighter:   '#ff7043',
        soft:      '#ffe4d1',
        emerald:   '#10b981',
        emeraldLt: '#34d399',
        yellow:    '#f59e0b',
        purple:    '#8b5cf6',
        sky:       '#0ea5e9',
        pink:      '#ec4899',
        slate:     '#94a3b8',
    };

    const genderPalette = [COLORS.primary, COLORS.sky, COLORS.slate];
    const statusPalette = [COLORS.primary, COLORS.light, COLORS.lighter, COLORS.emerald, COLORS.purple, COLORS.pink, COLORS.yellow, COLORS.sky, COLORS.slate];
    const sectorPalette = [COLORS.primary, COLORS.light, COLORS.lighter, COLORS.emerald, COLORS.purple, COLORS.pink, COLORS.yellow, COLORS.sky];

    const baseOpts = {
        chart: { fontFamily: 'Rubik, sans-serif', toolbar: { show: false }, animations: { speed: 600 } },
        dataLabels: { enabled: false },
        legend: { position: 'bottom', fontSize: '11px', fontWeight: 600, markers: { width: 10, height: 10, radius: 3 } },
        tooltip: { style: { fontSize: '11px' } },
    };

    /* ── Gender donut ── */
    const gEl = document.querySelector('#chart-gender');
    if (gEl?.dataset.values) {
        const raw = JSON.parse(gEl.dataset.values);
        const gLabels = { L: 'Laki-laki', P: 'Perempuan', '-': 'Lainnya' };
        const total = Object.values(raw).reduce((a, b) => a + b, 0);
        new ApexCharts(gEl, {
            ...baseOpts,
            chart: { ...baseOpts.chart, type: 'donut', height: 200 },
            series: Object.values(raw),
            labels: Object.keys(raw).map(k => gLabels[k] || k),
            colors: genderPalette,
            plotOptions: { pie: { donut: { size: '65%', labels: { show: true, total: { show: true, label: 'Total', formatter: () => total } } } } },
        }).render();
    }

    /* ── Status donut ── */
    const sEl = document.querySelector('#chart-status');
    if (sEl?.dataset.values) {
        const raw = JSON.parse(sEl.dataset.values);
        const total = Object.values(raw).reduce((a, b) => a + b, 0);
        new ApexCharts(sEl, {
            ...baseOpts,
            chart: { ...baseOpts.chart, type: 'donut', height: 200 },
            series: Object.values(raw),
            labels: Object.keys(raw),
            colors: statusPalette,
            plotOptions: { pie: { donut: { size: '60%', labels: { show: true, total: { show: true, label: 'Total', formatter: () => total } } } } },
        }).render();
    }

    /* ── Monthly bar ── */
    const mEl = document.querySelector('#chart-monthly');
    if (mEl?.dataset.values) {
        const raw = JSON.parse(mEl.dataset.values);
        new ApexCharts(mEl, {
            ...baseOpts,
            chart: { ...baseOpts.chart, type: 'bar', height: 200 },
            series: [{ name: 'Pendaftaran', data: raw.map(m => m.total) }],
            xaxis: { categories: raw.map(m => m.label), labels: { style: { fontSize: '10px' }, rotate: -45, rotateAlways: true } },
            yaxis: { labels: { style: { fontSize: '10px' }, formatter: v => Math.round(v) }, min: 0, forceNiceScale: true },
            colors: [COLORS.primary],
            plotOptions: { bar: { borderRadius: 6, columnWidth: '50%', distributed: true } },
            grid: { borderColor: '#f1f1f1', strokeDashArray: 4 },
            dataLabels: { enabled: true, style: { fontSize: '10px', fontWeight: 700 }, offsetY: -4, formatter: v => v || '' },
        }).render();
    }

    /* ── Age categories bar ── */
    const aEl = document.querySelector('#chart-age');
    if (aEl?.dataset.values) {
        const raw = JSON.parse(aEl.dataset.values);
        const entries = Object.entries(raw);
        new ApexCharts(aEl, {
            ...baseOpts,
            chart: { ...baseOpts.chart, type: 'bar', height: 200 },
            series: [{ name: 'Anggota', data: entries.map(([, v]) => v) }],
            xaxis: { categories: entries.map(([k]) => k), labels: { style: { fontSize: '10px' } } },
            yaxis: { labels: { style: { fontSize: '10px' }, formatter: v => Math.round(v) }, min: 0, forceNiceScale: true },
            colors: [COLORS.primary, COLORS.light, COLORS.lighter, COLORS.emerald, COLORS.purple],
            plotOptions: { bar: { borderRadius: 6, columnWidth: '50%', distributed: true } },
            grid: { borderColor: '#f1f1f1', strokeDashArray: 4 },
            dataLabels: { enabled: true, style: { fontSize: '10px', fontWeight: 700 }, offsetY: -4, formatter: v => v || '' },
        }).render();
    }

    /* ── Sector horizontal bar ── */
    const secEl = document.querySelector('#chart-sector');
    if (secEl?.dataset.values) {
        const raw = JSON.parse(secEl.dataset.values);
        const entries = Object.entries(raw);
        new ApexCharts(secEl, {
            ...baseOpts,
            chart: { ...baseOpts.chart, type: 'bar', height: 40 + entries.length * 40 },
            series: [{ name: 'Anggota', data: entries.map(([, v]) => v) }],
            xaxis: { categories: entries.map(([k]) => k), labels: { style: { fontSize: '10px' } } },
            yaxis: { labels: { style: { fontSize: '10px' }, formatter: v => Math.round(v) }, min: 0, forceNiceScale: true },
            colors: sectorPalette,
            plotOptions: { bar: { borderRadius: 6, horizontal: true, distributed: true, barHeight: '60%' } },
            grid: { borderColor: '#f1f1f1', strokeDashArray: 4, xaxis: { lines: { show: false } } },
            dataLabels: { enabled: true, style: { fontSize: '10px', fontWeight: 700 }, offsetX: 4, formatter: v => v || '' },
        }).render();
    }
}

function initKomunitasDrawer() {
    const drawer = document.querySelector('[data-komunitas-drawer]');
    if (!drawer) return;

    const panel = drawer.querySelector('[data-komunitas-drawer-panel]');
    const backdrop = drawer.querySelector('[data-komunitas-drawer-backdrop]');
    const openBtns = document.querySelectorAll('[data-komunitas-open]');
    const closeBtn = drawer.querySelector('[data-komunitas-drawer-close]');

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
initKomunitasDrawer();
initMemberStats();
