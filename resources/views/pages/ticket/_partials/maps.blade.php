<hr class="section-divider">
<section class="section">
    <div class="section-header">
        <span class="section-bar"></span>
        <h3 class="section-title">Lokasi Acara</h3>
    </div>
    <div class="overflow-hidden rounded-2xl border border-mercury bg-white">
        <iframe src="https://www.google.com/maps?q={{ rawurlencode($event['maps_query'] ?? $event['venue']) }}&output=embed" class="h-56 w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        <div class="flex items-start gap-2 border-t border-mercury px-3 py-3">
            <i class="ri-map-pin-2-fill mt-0.5 text-base text-primary"></i>
            <div class="min-w-0">
                <p class="text-xs font-bold text-foreground">{{ $event['venue'] }}</p>
                <a href="https://www.google.com/maps/search/?api=1&query={{ rawurlencode($event['maps_query'] ?? $event['venue']) }}" target="_blank" rel="noopener" class="mt-1 inline-flex items-center gap-1 text-[11px] font-semibold text-primary">
                    Buka di Google Maps
                    <i class="ri-external-link-line"></i>
                </a>
            </div>
        </div>
    </div>
</section>
