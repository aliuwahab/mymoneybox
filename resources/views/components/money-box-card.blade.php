@props(['moneyBox'])

@php
    $covers = ['cover-emerald','cover-amber','cover-slate','cover-rose','cover-violet'];
    $cover  = $covers[crc32($moneyBox->id) % count($covers)];
    $sym    = $moneyBox->user?->country?->currency_symbol ?? '₵';
    $pct    = $moneyBox->goal_amount > 0
        ? min(100, round(($moneyBox->total_contributions / $moneyBox->goal_amount) * 100))
        : 0;
@endphp

<a href="{{ route('box.show', $moneyBox->slug) }}"
   class="card block hover:shadow-[0_1px_0_rgba(20,18,12,.04),0_8px_24px_-8px_rgba(20,18,12,.10)] transition-shadow duration-150 overflow-hidden h-full">

    {{-- Cover --}}
    <div class="p-3.5 pb-0">
        @if($moneyBox->hasMedia('main'))
            <div class="rounded-[6px] overflow-hidden" style="aspect-ratio: 16/9;">
                <img src="{{ $moneyBox->getMainImageUrl() }}" alt="{{ $moneyBox->title }}"
                     class="w-full h-full object-cover"
                     style="object-position: center 30%;">
            </div>
        @else
            <div class="{{ $cover }} h-[90px] rounded-[6px] relative overflow-hidden">
                <div class="absolute inset-0 grid place-items-center text-white/90 font-serif text-[28px] tracking-wide">
                    {{ substr($moneyBox->title, 0, 1) }}
                </div>
            </div>
        @endif
    </div>

    <div class="p-4">
        {{-- Badges --}}
        <div class="flex items-center gap-1.5 mb-2">
            <span class="pill {{ $moneyBox->visibility->value === 'public' ? 'pill-info' : 'pill-muted' }}">
                <span class="pill-dot"></span>
                {{ ucfirst($moneyBox->visibility->value) }}
            </span>
            @if($moneyBox->category)
                <span class="pill pill-muted">
                    {{ $moneyBox->category->icon }} {{ $moneyBox->category->name }}
                </span>
            @endif
        </div>

        <div class="text-[15px] font-semibold text-[#15140F] tracking-tight mb-0.5 leading-snug line-clamp-2">
            {{ $moneyBox->title }}
        </div>
        <div class="tiny mb-3">
            by {{ $moneyBox->user->name }} · {{ $moneyBox->contribution_count }} {{ Str::plural('contributor', $moneyBox->contribution_count) }}
        </div>

        {{-- Progress --}}
        @if($moneyBox->goal_amount)
            <div class="flex items-baseline justify-between mb-1.5">
                <div class="text-[13px] font-semibold text-[#15140F] tnum">
                    {{ $sym }}{{ number_format($moneyBox->total_contributions, 2) }}
                    <span class="muted font-normal text-[12px]">of {{ $sym }}{{ number_format($moneyBox->goal_amount, 2) }}</span>
                </div>
                <div class="tiny tnum">{{ $pct }}%</div>
            </div>
            <div class="progress-track">
                <div class="progress-fill" style="width: {{ $pct }}%"></div>
            </div>
        @else
            <div class="text-[13px] font-semibold text-[#15140F] tnum">
                {{ $sym }}{{ number_format($moneyBox->total_contributions, 2) }}
                <span class="muted font-normal text-[12px]">raised</span>
            </div>
        @endif

        <div class="flex items-center justify-between mt-3">
            <span class="tiny">
                @if($moneyBox->end_date)
                    Ends {{ $moneyBox->end_date->format('M j, Y') }}
                @else
                    Ongoing
                @endif
            </span>
            <span class="text-[12px] font-medium text-primary-600 flex items-center gap-1">
                Contribute
                <svg viewBox="0 0 24 24" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </span>
        </div>
    </div>
</a>
