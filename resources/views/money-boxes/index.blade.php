<x-layouts.app>
    @php
        $sym    = auth()->user()->country?->currency_symbol ?? '₵';
        $covers = ['cover-emerald','cover-amber','cover-slate','cover-rose','cover-violet'];
    @endphp

    <div class="page-wrap max-w-[1280px]" x-data="{ filter: 'all' }">

        {{-- Page header --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 sm:gap-6 mb-6">
            <div>
                <h1 class="page-title">Your PiggyBoxes</h1>
                <p class="text-[13.5px] text-[#6B6862] mt-1.5">
                    {{ $moneyBoxes->total() }} total ·
                    {{ $moneyBoxes->getCollection()->where('is_active', true)->count() }} active
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('money-boxes.create') }}" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    New PiggyBox
                </a>
            </div>
        </div>

        {{-- Tabs / filter --}}
        <div class="tabs">
            @foreach([['all','All'],['active','Active'],['inactive','Inactive']] as [$val,$label])
                <button
                    class="tab"
                    :class="filter === '{{ $val }}' ? 'active' : ''"
                    @click="filter = '{{ $val }}'"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        @if($moneyBoxes->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($moneyBoxes as $box)
                    @php
                        $cover = $covers[$loop->index % count($covers)];
                        $pct   = $box->goal_amount > 0
                            ? min(100, round(($box->total_contributions / $box->goal_amount) * 100))
                            : 0;
                    @endphp
                    <div
                        x-show="filter === 'all' || (filter === 'active' && {{ $box->is_active ? 'true' : 'false' }}) || (filter === 'inactive' && {{ !$box->is_active ? 'true' : 'false' }})"
                    >
                        <a href="{{ route('money-boxes.show', $box) }}" wire:navigate
                           class="card block hover:shadow-[0_1px_0_rgba(20,18,12,.04),0_8px_24px_-8px_rgba(20,18,12,.10)] transition-shadow duration-150 overflow-hidden">

                            {{-- Cover --}}
                            <div class="p-3.5 pb-0">
                                <div class="{{ $cover }} h-[90px] rounded-[6px] relative">
                                    <div class="absolute inset-0 grid place-items-center text-white/90 font-serif text-[28px] tracking-wide">
                                        {{ substr($box->title, 0, 1) }}
                                    </div>
                                </div>
                            </div>

                            <div class="p-4">
                                {{-- Badges --}}
                                <div class="flex items-center gap-1.5 mb-2">
                                    <span class="pill {{ $box->visibility->value === 'public' ? 'pill-info' : 'pill-muted' }}">
                                        <span class="pill-dot"></span>
                                        {{ ucfirst($box->visibility->value) }}
                                    </span>
                                    <span class="pill {{ $box->is_active ? 'pill-ok' : 'pill-muted' }}">
                                        <span class="pill-dot"></span>
                                        {{ $box->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>

                                <div class="text-[15px] font-semibold text-[#15140F] tracking-tight mb-0.5 leading-snug">
                                    {{ Str::limit($box->title, 52) }}
                                </div>
                                <div class="tiny mb-3">
                                    {{ $box->category?->name ?? 'General' }} · {{ $box->contributions_count }} contributors
                                </div>

                                {{-- Progress --}}
                                @if($box->goal_amount)
                                    <div class="flex items-baseline justify-between mb-1.5">
                                        <div class="text-[13px] font-semibold text-[#15140F] tnum">
                                            {{ $sym }}{{ number_format($box->total_contributions, 2) }}
                                            <span class="muted font-normal text-[12px]">of {{ $sym }}{{ number_format($box->goal_amount, 2) }}</span>
                                        </div>
                                        <div class="tiny tnum">{{ $pct }}%</div>
                                    </div>
                                    <div class="progress-track">
                                        <div class="progress-fill" style="width: {{ $pct }}%"></div>
                                    </div>
                                @else
                                    <div class="text-[13px] font-semibold text-[#15140F] tnum">
                                        {{ $sym }}{{ number_format($box->total_contributions, 2) }}
                                        <span class="muted font-normal text-[12px]">raised · no goal set</span>
                                    </div>
                                @endif

                                <div class="flex items-center justify-between mt-3">
                                    <span class="tiny">
                                        @if($box->end_date)
                                            Ends {{ $box->end_date->format('M j, Y') }}
                                        @else
                                            Ongoing
                                        @endif
                                    </span>
                                    <span class="text-[12px] font-medium text-primary-600 flex items-center gap-1">
                                        Open
                                        <svg viewBox="0 0 24 24" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($moneyBoxes->hasPages())
                <div class="mt-8">{{ $moneyBoxes->links() }}</div>
            @endif
        @else
            <div class="border-2 border-dashed border-[#D9D6CE] rounded-[10px] p-12 text-center">
                <div class="w-12 h-12 rounded-xl bg-primary-50 text-primary-600 grid place-items-center mx-auto mb-4">
                    <svg viewBox="0 0 24 24" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7.5 12 3l9 4.5v9L12 21l-9-4.5v-9Z"/><path d="M3 7.5 12 12l9-4.5"/><path d="M12 12v9"/></svg>
                </div>
                <h3 class="text-[15px] font-semibold text-[#15140F] mb-1">No PiggyBoxes yet</h3>
                <p class="tiny mb-5">Get started by creating your first PiggyBox.</p>
                <a href="{{ route('money-boxes.create') }}" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    Create a PiggyBox
                </a>
            </div>
        @endif
    </div>
</x-layouts.app>