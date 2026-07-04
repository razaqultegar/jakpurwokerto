<hr class="section-divider">
<section class="section">
    <div class="section-header">
        <span class="section-bar"></span>
        <h3 class="section-title">Susunan Acara</h3>
    </div>
    <div class="mb-3 flex items-center gap-2" data-rundown-tabs>
        @foreach ($event['agenda'] as $i => $day)
        <button type="button" class="rundown-tab{{ $i === 0 ? ' rundown-tab-active' : '' }}" data-rundown-tab data-rundown-index="{{ $i }}">
            {{ $day['day'] }}, {{ $day['date'] }}
        </button>
        @endforeach
    </div>
    <div class="swiper rundown-swiper" data-rundown-swiper>
        <div class="swiper-wrapper">
            @foreach ($event['agenda'] as $day)
            <div class="swiper-slide">
                @if (count($day['items']))
                <div class="flex flex-col gap-2.5">
                    @foreach ($day['items'] as $item)
                    <div class="flex items-center gap-3 rounded-2xl bg-skull p-3 ring-1 ring-mercury">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-softer text-primary">
                            <i class="{{ $item['icon'] }} text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-foreground">{{ $item['title'] }}</p>
                            <p class="mt-0.5 text-[11px] leading-relaxed text-onyx">{{ $item['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="rounded-2xl border border-mercury bg-white p-4 text-center text-[11px] text-onyx">
                    Susunan acara segera diumumkan.
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>
