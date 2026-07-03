<hr class="section-divider">
<section class="section">
    <div class="section-header mb-4">
        <span class="section-bar"></span>
        <h3 class="section-title">FAQ</h3>
    </div>
    <div class="flex flex-col gap-3">
        @foreach ($event['faqs'] as $faq)
        <div class="rounded-2xl border border-mercury bg-white p-3.5">
            <p class="text-[11px] font-bold leading-snug text-foreground">{{ $faq['question'] }}</p>
            <p class="mt-2 text-[11px] leading-relaxed text-onyx">{{ $faq['answer'] }}</p>
        </div>
        @endforeach
    </div>
</section>
