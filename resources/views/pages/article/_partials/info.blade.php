<section class="px-4 pb-2 pt-5">
    <span class="text-[11px] text-onyx">{{ \Carbon\Carbon::parse($article['published_at'])->translatedFormat('d M Y, H:i') }} WIB</span>
    <h1 class="mt-1 text-xl font-black leading-tight text-foreground">{{ $article['title'] }}</h1>
</section>
