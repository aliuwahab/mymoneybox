@props(['contributions', 'moneyBox'])

@php $sym = $moneyBox->getCurrencySymbol(); @endphp

<ul class="divide-y divide-[#F0EDE6]">
    @foreach($contributions->take(10) as $c)
        @php
            $name     = $c->getDisplayName();
            $isAnon   = $name === 'Anonymous';
            $initials = $isAnon
                ? '·'
                : collect(explode(' ', $name))->map(fn($p) => strtoupper($p[0] ?? ''))->take(2)->implode('');
            $colors   = ['bg-[#1B6B4E] text-white', 'bg-[#15140F] text-white', 'bg-[#B8810D] text-white', 'bg-[#3F2A6E] text-white', 'bg-[#883647] text-white'];
            $color    = $isAnon ? 'bg-[#E6E3DC] text-[#9C998F]' : $colors[$loop->index % count($colors)];
        @endphp
        <li class="flex items-start gap-3 px-[18px] py-3">
            <div class="w-8 h-8 rounded-full {{ $color }} grid place-items-center text-[11.5px] font-semibold flex-none tracking-tight">
                {{ $initials }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[13px] font-medium text-[#15140F] truncate">{{ $name }}</span>
                    <span class="text-[13px] font-semibold text-[#15140F] tnum flex-none">{{ $sym }}{{ number_format($c->amount, 0) }}</span>
                </div>
                @if($c->message)
                    <div class="text-[11.5px] text-[#9C998F] italic mt-0.5 line-clamp-1">"{{ $c->message }}"</div>
                @endif
                <div class="text-[11px] text-[#C0BDB5] mt-0.5">{{ $c->created_at->diffForHumans() }}</div>
            </div>
        </li>
    @endforeach
</ul>

@if($contributions->count() > 10)
    <div class="px-[18px] py-3 border-t border-[#F0EDE6]">
        <p class="text-[12px] text-[#9C998F] text-center">+ {{ $contributions->count() - 10 }} more contributors</p>
    </div>
@endif
