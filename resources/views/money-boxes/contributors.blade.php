<x-layouts.app :title="__('Contributors')">
@php
    $sym = auth()->user()->country?->currency_symbol ?? '₵';
@endphp

<div class="page-wrap max-w-[1280px]">

    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-6">
        <div>
            <h1 class="page-title">Contributors</h1>
            <p class="text-[13.5px] text-[#6B6862] mt-1.5">Everyone who has contributed across your PiggyBoxes</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="btn">
                <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h16"/><path d="M7 12h10"/><path d="M10 19h4"/></svg>
                Filter
            </button>
            <button class="btn">
                <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v12"/><path d="m6 10 6 6 6-6"/><path d="M4 20h16"/></svg>
                Export CSV
            </button>
        </div>
    </div>

    {{-- Stat grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3.5 mb-6">
        <div class="stat-card">
            <div class="stat-label">Total contributors</div>
            <div class="stat-value tnum">{{ number_format($totalContributors) }}</div>
            <div class="stat-delta text-primary-600">
                <svg viewBox="0 0 24 24" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5"/><path d="m5 12 7-7 7 7"/></svg>
                Across all PiggyBoxes
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Repeat contributors</div>
            <div class="stat-value tnum">{{ number_format($repeatContributors) }}</div>
            <div class="stat-delta text-primary-600">
                @if($totalContributors > 0)
                    {{ number_format(($repeatContributors / $totalContributors) * 100, 1) }}% return rate
                @else
                    —
                @endif
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Largest gift</div>
            <div class="stat-value">{{ $largestGift ? $sym . number_format($largestGift->amount, 2) : '—' }}</div>
            <div class="stat-delta">
                @if($largestGift)
                    from {{ $largestGift->getDisplayName() }}
                @endif
            </div>
        </div>
    </div>

    {{-- Contributors table --}}
    <div class="card">
        @if($contributors->count() > 0)
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr>
                        <th>Contributor</th>
                        <th>PiggyBoxes</th>
                        <th class="num">Contributions</th>
                        <th class="num">Total</th>
                        <th>Last</th>
                        <th></th>
                    </tr></thead>
                    <tbody>
                        @foreach($contributors as $c)
                            @php
                                $initials = $c->name === 'Anonymous'
                                    ? '·'
                                    : collect(explode(' ', $c->name))->map(fn($p) => strtoupper($p[0] ?? ''))->take(2)->implode('');
                            @endphp
                            <tr>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="w-[22px] h-[22px] rounded-full bg-primary-600 text-white grid place-items-center text-[9.5px] font-semibold flex-none">{{ $initials }}</div>
                                        <span class="font-medium text-[#15140F]">
                                            {{ $c->name }}
                                            @if($c->name === 'Anonymous' && $c->contributions_count > 1)
                                                ({{ $c->contributions_count }})
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td class="muted">{{ $c->boxes_count }} {{ Str::plural('PiggyBox', $c->boxes_count) }}</td>
                                <td class="num tnum">{{ $c->contributions_count }}</td>
                                <td class="num tnum font-semibold text-[#15140F]">{{ $sym }}{{ number_format($c->total_amount, 2) }}</td>
                                <td class="muted text-[12px]">{{ \Carbon\Carbon::parse($c->last_contributed_at)->diffForHumans() }}</td>
                                <td>
                                    <button class="btn btn-ghost btn-icon btn-sm">
                                        <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="12" r="1.4"/><circle cx="12" cy="12" r="1.4"/><circle cx="19" cy="12" r="1.4"/></svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-16 text-center">
                <div class="w-12 h-12 rounded-xl bg-[#F3F1EB] grid place-items-center mx-auto mb-4">
                    <svg viewBox="0 0 24 24" class="w-6 h-6 text-[#9C998F]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6"/><path d="M16 4.5a3.5 3.5 0 0 1 0 7"/><path d="M21.5 20c0-3-1.7-5.2-4.5-5.8"/></svg>
                </div>
                <h3 class="text-[14px] font-semibold text-[#15140F] mb-1">No contributors yet</h3>
                <p class="tiny">Share your PiggyBoxes to start receiving contributions.</p>
            </div>
        @endif
    </div>

</div>
</x-layouts.app>
