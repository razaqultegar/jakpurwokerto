@if (empty($articles))
<div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-skull bg-white p-6 text-center">
    <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-skull/60 text-foreground/60">
        <i class="ri-newspaper-line text-2xl"></i>
    </div>
    <h4 class="text-sm font-semibold text-foreground">Belum ada berita</h4>
    <p class="mt-1 text-xs text-foreground/60">Belum ada berita yang tersedia saat ini. Pantau terus untuk update terbaru.</p>
</div>
@else
<div class="flex flex-col gap-2.5">
    @foreach ($articles as $article)
    <a href="{{ route('article.show', $article['slug']) }}" class="flex items-center gap-3 rounded-2xl border border-mercury bg-white p-3 transition active:scale-[0.99] hover:border-primary-soft hover:bg-primary-softer/40">
        <div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl">
            <img src="{{ asset('build/' . $article['image']) }}" alt="{{ $article['title'] }}" class="h-full w-full object-cover" loading="lazy">
        </div>
        <div class="min-w-0 flex-1">
            <span class="text-[10px] text-onyx">{{ \Carbon\Carbon::parse($article['published_at'])->translatedFormat('d M Y, H:i') }} WIB</span>
            <p class="mt-1 line-clamp-2 text-xs font-bold leading-snug text-foreground">{{ $article['title'] }}</p>
        </div>
        <i class="ri-arrow-right-s-line shrink-0 text-lg text-onyx"></i>
    </a>
    @endforeach
</div>
@endif
