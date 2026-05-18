@php
    $shareUrl = url()->current();
    $shareText = ($merch['name'] ?? 'Merchandise') . ' - ' . ($merch['tagline'] ?? '');
@endphp
<div class="pointer-events-none fixed inset-0 z-50" data-share aria-hidden="true">
    <div class="absolute inset-0 bg-black/60 opacity-0 transition-opacity duration-300" data-share-backdrop></div>
    <div class="absolute inset-x-0 bottom-0 mx-auto flex w-full max-w-screen-sm translate-y-full flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition-transform duration-300 ease-out" data-share-panel role="dialog" aria-modal="true" aria-label="Bagikan">
        <div class="flex justify-center pt-2.5">
            <span class="h-1.5 w-10 rounded-full bg-mercury"></span>
        </div>
        <header class="flex items-center justify-between px-4 pb-3 pt-2">
            <div class="flex items-center gap-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-softer text-primary ring-1 ring-primary-soft">
                    <i class="ri-share-forward-line text-lg"></i>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-foreground">Bagikan</h2>
                    <p class="text-[10px] text-onyx">Ajak teman lihat merchandise ini</p>
                </div>
            </div>
            <button type="button" class="flex h-9 w-9 items-center justify-center rounded-full bg-skull text-foreground ring-1 ring-mercury" data-share-close aria-label="Tutup">
                <i class="ri-close-line text-lg"></i>
            </button>
        </header>
        <div class="px-4 pb-6">
            <div class="grid grid-cols-4 gap-2.5">
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank" rel="noopener" class="flex flex-col items-center gap-1.5 rounded-2xl bg-skull p-2.5 ring-1 ring-mercury transition active:scale-95">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#1877F2] text-white">
                        <i class="ri-facebook-fill text-lg"></i>
                    </span>
                    <span class="text-[10px] font-semibold text-foreground">Facebook</span>
                </a>
                <button type="button" class="flex flex-col items-center gap-1.5 rounded-2xl bg-skull p-2.5 ring-1 ring-mercury transition active:scale-95" data-share-instagram>
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-linear-to-br from-[#F58529] via-[#DD2A7B] to-[#8134AF] text-white">
                        <i class="ri-instagram-line text-lg"></i>
                    </span>
                    <span class="text-[10px] font-semibold text-foreground">Instagram</span>
                </button>
                <a href="https://wa.me/?text={{ urlencode($shareText . ' ' . $shareUrl) }}" target="_blank" rel="noopener" class="flex flex-col items-center gap-1.5 rounded-2xl bg-skull p-2.5 ring-1 ring-mercury transition active:scale-95">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#25D366] text-white">
                        <i class="ri-whatsapp-line text-lg"></i>
                    </span>
                    <span class="text-[10px] font-semibold text-foreground">WhatsApp</span>
                </a>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ urlencode($shareText) }}" target="_blank" rel="noopener" class="flex flex-col items-center gap-1.5 rounded-2xl bg-skull p-2.5 ring-1 ring-mercury transition active:scale-95">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-black text-white">
                        <i class="ri-twitter-x-line text-lg"></i>
                    </span>
                    <span class="text-[10px] font-semibold text-foreground">X</span>
                </a>
            </div>
            <div class="mt-3 flex items-center gap-2 rounded-2xl bg-skull p-2 pl-3 ring-1 ring-mercury">
                <i class="ri-link-m shrink-0 text-base text-onyx"></i>
                <span class="flex-1 truncate text-[11px] text-onyx" data-share-url>{{ $shareUrl }}</span>
                <button type="button" class="inline-flex shrink-0 items-center gap-1 rounded-xl bg-primary px-2.5 py-1.5 text-[11px] font-bold text-white shadow-sm transition active:scale-95" data-share-copy aria-label="Salin link">
                    <i class="ri-link text-sm" data-share-copy-icon></i>
                    <span data-share-copy-label>Salin</span>
                </button>
            </div>
        </div>
    </div>
</div>
