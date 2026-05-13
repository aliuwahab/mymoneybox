<x-layouts.app :title="__('Analytics')">
@php
    $sym = auth()->user()->country?->currency_symbol ?? '₵';
    $maxDaily = $daily->max('total') ?: 1;
@endphp

<div class="page-wrap max-w-[1280px]">

    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-6">
        <div>
            <h1 class="page-title">Analytics</h1>
            <p class="text-[13.5px] text-[#6B6862] mt-1.5">Trends and insights across your piggy boxes</p>
        </div>
        <div class="flex items-center gap-2">
            <select class="rounded-[6px] border border-[#E6E3DC] px-2.5 py-1.5 text-[13px] bg-white text-[#15140F] w-auto">
                <option>Last 14 days</option>
                <option>Last 30 days</option>
                <option>This year</option>
            </select>
        </div>
    </div>

    {{-- Stat grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 mb-6">
        <div class="stat-card">
            <div class="stat-label">Total raised</div>
            <div class="stat-value">{{ $sym }}{{ number_format($totalRaised, 2) }}</div>
            <div class="stat-delta text-primary-600">
                <svg viewBox="0 0 24 24" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5"/><path d="m5 12 7-7 7 7"/></svg>
                Across all boxes
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Contributors</div>
            <div class="stat-value tnum">{{ number_format($totalContributors) }}</div>
            <div class="stat-delta">Total contributions</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Avg. per day</div>
            <div class="stat-value">{{ $sym }}{{ number_format($daily->avg('total'), 2) }}</div>
            <div class="stat-delta">Last 14 days</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Best day</div>
            @php $bestDay = $daily->sortByDesc('total')->first(); @endphp
            <div class="stat-value">{{ $sym }}{{ number_format($bestDay['total'], 2) }}</div>
            <div class="stat-delta">{{ $bestDay['label'] }}</div>
        </div>
    </div>

    {{-- Charts row --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-4 mb-4">

        {{-- Daily contributions bar chart --}}
        <div class="card">
            <div class="card-head">
                <div>
                    <div class="card-title">Daily contributions</div>
                    <div class="tiny mt-0.5">Last 14 days</div>
                </div>
            </div>
            <div class="card-body">
                <div class="flex items-end gap-1.5" style="height: 200px; padding: 8px 0;">
                    @foreach($daily as $d)
                        <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end">
                            <div
                                class="w-full rounded-t {{ $loop->last ? 'bg-primary-600' : 'bg-primary-50' }}"
                                style="height: {{ $maxDaily > 0 ? max(4, ($d['total'] / $maxDaily) * 100) : 4 }}%;"
                                title="{{ $d['label'] }}: {{ $sym }}{{ number_format($d['total'], 2) }}"
                            ></div>
                            @if($loop->index % 2 === 0 || $loop->last)
                                <div class="tiny text-[10px]">{{ \Carbon\Carbon::parse($d['date'])->format('M j') }}</div>
                            @else
                                <div class="tiny text-[10px]">&nbsp;</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Top sources --}}
        <div class="card">
            <div class="card-head"><div class="card-title">Top sources</div></div>
            <div class="card-body flex flex-col gap-3">
                @foreach($topSources as $source)
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[13px]">{{ $source['source'] }}</span>
                            <span class="tnum tiny">{{ $source['percentage'] }}%</span>
                        </div>
                        <div class="progress-track">
                            <div class="progress-fill" style="width: {{ $source['percentage'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
</x-layouts.app>
