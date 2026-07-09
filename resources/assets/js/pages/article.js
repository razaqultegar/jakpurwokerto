import { initShare } from '../utils/share.js';

const Swiper = window.Swiper;

function initHeroSwiper() {
    const el = document.querySelector('[data-hero-swiper]');
    if (!el) return;

    const counter = document.querySelector('[data-hero-counter]');
    const total = el.querySelectorAll('.swiper-slide').length;

    new Swiper(el, {
        loop: total > 1,
        speed: 500,
        grabCursor: true,
        allowTouchMove: total > 1,
        autoplay: total > 1 ? { delay: 4000, disableOnInteraction: false } : false,
        navigation: total > 1 ? {
            nextEl: '.hero-swiper-next',
            prevEl: '.hero-swiper-prev',
        } : false,
        pagination: total > 1 ? {
            el: '.hero-swiper-pagination',
            clickable: true,
            bulletClass: 'hero-bullet',
            bulletActiveClass: 'hero-bullet-active',
            renderBullet: (_, className) =>
                `<span class="${className}"></span>`,
        } : false,
        on: {
            slideChange(swiper) {
                if (counter) counter.textContent = `${swiper.realIndex + 1} / ${total}`;
            },
        },
    });
}

function initHeroZoom() {
    const triggers = document.querySelectorAll('[data-hero-zoom]');
    if (!triggers.length) return;

    let overlay = document.querySelector('[data-article-zoom-overlay]');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.setAttribute('data-article-zoom-overlay', '');
        overlay.className = 'fixed inset-0 z-[100] hidden items-center justify-center bg-black/90 p-4';
        overlay.innerHTML = `
            <button type="button" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-white/15 text-white ring-1 ring-white/25 backdrop-blur-md transition hover:bg-white/30" data-article-zoom-close>
                <i class="ri-close-line text-2xl"></i>
            </button>
            <img class="max-h-[88vh] max-w-full object-contain" data-article-zoom-img alt="">
        `;
        document.body.appendChild(overlay);

        const close = () => {
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
            document.body.style.overflow = '';
        };
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay || e.target.closest('[data-article-zoom-close]')) close();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !overlay.classList.contains('hidden')) close();
        });
    }
    const img = overlay.querySelector('[data-article-zoom-img]');

    triggers.forEach((t) => {
        t.addEventListener('click', () => {
            img.src = t.getAttribute('data-hero-zoom');
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
            document.body.style.overflow = 'hidden';
        });
    });
}

function initArticleFilters() {
    const list = document.querySelector('[data-article-list]');
    if (!list) return;

    const items = Array.from(list.querySelectorAll('[data-article-item]'));
    const searchInput = document.querySelector('[data-article-search]');
    const emptyState = document.querySelector('[data-article-empty]');

    const applyFilters = () => {
        const keyword = (searchInput?.value || '').trim().toLowerCase();
        let visibleCount = 0;

        items.forEach((item) => {
            const visible = !keyword || item.dataset.articleTitle.includes(keyword);
            item.classList.toggle('hidden', !visible);
            if (visible) visibleCount += 1;
        });

        if (emptyState) {
            emptyState.classList.toggle('hidden', visibleCount > 0);
            emptyState.classList.toggle('flex', visibleCount === 0);
        }
    };

    if (searchInput) searchInput.addEventListener('input', applyFilters);
}

initArticleFilters();
initHeroSwiper();
initHeroZoom();
initShare();
