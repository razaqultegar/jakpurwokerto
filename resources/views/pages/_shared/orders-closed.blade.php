@php
    $title = $title ?? 'Pemesanan Ditutup Sementara';
    $message = $message ?? 'Saat ini pemesanan sedang ditutup oleh admin. Silakan cek kembali nanti atau hubungi admin untuk info lebih lanjut.';
    $waNumber = $waNumber ?? '628975851952';
    $waText = $waText ?? 'Halo Admin, saya ingin tanya kapan pemesanan dibuka kembali.';
@endphp

<section class="px-4 pb-8 pt-4">
    <div class="rounded-2xl border border-mercury bg-skull/60 p-5 text-center">
        <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-onyx text-white">
            <i class="ri-lock-line text-xl"></i>
        </span>
        <h3 class="mt-3 text-sm font-black text-foreground">{{ $title }}</h3>
        <p class="mx-auto mt-1.5 max-w-xs text-[11px] leading-relaxed text-onyx">{{ $message }}</p>
        <a href="https://wa.me/{{ $waNumber }}?text={{ rawurlencode($waText) }}" target="_blank" rel="noopener"
            class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-white px-4 py-2.5 text-[12px] font-bold text-foreground ring-1 ring-mercury transition active:scale-95">
            <i class="ri-customer-service-2-fill text-base text-primary"></i>
            Hubungi Admin
        </a>
    </div>
</section>
