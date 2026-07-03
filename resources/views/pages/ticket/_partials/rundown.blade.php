<hr class="section-divider">
<section class="section">
    <div class="section-header">
        <span class="section-bar"></span>
        <h3 class="section-title">Susunan Acara</h3>
    </div>
    <div class="overflow-hidden rounded-2xl border border-mercury bg-white">
        <table class="w-full table-fixed text-left">
            <thead class="bg-skull text-[10px] font-bold uppercase tracking-wider text-onyx">
                <tr>
                    <th class="w-20 px-3 py-2.5">Waktu</th>
                    <th class="px-3 py-2.5">Acara</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-mercury text-[11px]">
                @foreach ($event['agenda'] as $item)
                <tr>
                    <td class="px-3 py-3 align-top font-black text-primary">{{ $item['time'] }}</td>
                    <td class="px-3 py-3 align-top">
                        <p class="font-bold text-foreground">{{ $item['title'] }}</p>
                        <p class="mt-1 leading-relaxed text-onyx">{{ $item['desc'] }}</p>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
