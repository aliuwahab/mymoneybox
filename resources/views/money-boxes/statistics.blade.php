<x-layouts.app>
    @php
        $sym = auth()->user()->country?->currency_symbol ?? '₵';
        $avg = $stats['total_count'] > 0
            ? $stats['total_amount'] / $stats['total_count']
            : 0;
        $pct = $moneyBox->goal_amount > 0
            ? min(100, round(($stats['total_amount'] / $moneyBox->goal_amount) * 100, 1))
            : null;
    @endphp

    <div class="page-wrap max-w-[1280px]">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('money-boxes.show', $moneyBox) }}"
               class="w-8 h-8 rounded-full border border-[#E6E3DC] bg-white grid place-items-center text-[#6B6862] hover:text-[#15140F] hover:border-[#D9D6CE] transition-colors duration-100"
               wire:navigate>
                <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5m0 0 7 7m-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="page-title" style="font-size:1.75rem;">{{ $moneyBox->title }}</h1>
                <p class="tiny mt-0.5">Statistics & Analytics</p>
            </div>
        </div>

        {{-- Stat cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="stat-card">
                <div class="stat-label">Total raised</div>
                <div class="stat-value text-[22px]">{{ $sym }}{{ number_format($stats['total_amount'], 2) }}</div>
                @if($moneyBox->goal_amount)
                    <div class="stat-delta">of {{ $sym }}{{ number_format($moneyBox->goal_amount, 2) }} goal</div>
                @endif
            </div>

            <div class="stat-card">
                <div class="stat-label">Contributors</div>
                <div class="stat-value text-[22px]">{{ number_format($stats['total_count']) }}</div>
                <div class="stat-delta">{{ Str::plural('person', $stats['total_count']) }} contributed</div>
            </div>

            @if($moneyBox->goal_amount)
                <div class="stat-card">
                    <div class="stat-label">Progress</div>
                    <div class="stat-value text-[22px]">{{ number_format($stats['progress_percentage'], 1) }}%</div>
                    <div class="mt-2.5">
                        <div class="progress-track">
                            <div class="progress-fill" style="width: {{ min(100, $stats['progress_percentage']) }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Remaining</div>
                    <div class="stat-value text-[22px]">{{ $sym }}{{ number_format(max(0, $moneyBox->goal_amount - $stats['total_amount']), 2) }}</div>
                    <div class="stat-delta">to reach goal</div>
                </div>
            @else
                <div class="stat-card">
                    <div class="stat-label">Average gift</div>
                    <div class="stat-value text-[22px]">{{ $sym }}{{ number_format($avg, 2) }}</div>
                    <div class="stat-delta">per contributor</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Status</div>
                    <div class="mt-2">
                        <span class="pill {{ $moneyBox->is_active ? 'pill-ok' : 'pill-muted' }}">
                            <span class="pill-dot"></span>
                            {{ $moneyBox->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="stat-delta">No goal set · ongoing</div>
                </div>
            @endif
        </div>

        {{-- Contributions table --}}
        <div class="card">
            <div class="card-head">
                <span class="card-title">All Contributions</span>
                <span class="pill pill-muted">{{ $stats['total_count'] }} total</span>
            </div>

            @if($stats['recent_contributions']->count() > 0)
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Contributor</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th class="num">Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stats['recent_contributions'] as $contribution)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-primary-50 text-primary-600 grid place-items-center text-[11px] font-semibold flex-none">
                                            {{ substr($contribution->getDisplayName(), 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-[13px] font-medium text-[#15140F]">{{ $contribution->getDisplayName() }}</div>
                                            @if($contribution->contributor_email && !$contribution->is_anonymous)
                                                <div class="tiny">{{ $contribution->contributor_email }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($contribution->message)
                                        <span class="text-[12px] text-[#6B6862] italic line-clamp-2">"{{ $contribution->message }}"</span>
                                    @else
                                        <span class="tiny">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="tiny">{{ $contribution->created_at->format('M d, Y') }}</div>
                                    <div class="tiny">{{ $contribution->created_at->format('g:i A') }}</div>
                                </td>
                                <td class="num font-semibold text-[#15140F] tnum">
                                    {{ $sym }}{{ number_format($contribution->amount, 2) }}
                                </td>
                                <td>
                                    <span class="pill {{ $contribution->payment_status->value === 'completed' ? 'pill-ok' : 'pill-warn' }}">
                                        <span class="pill-dot"></span>
                                        {{ ucfirst($contribution->payment_status->value) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="py-16 text-center">
                    <div class="w-12 h-12 rounded-xl bg-[#F3F1EB] grid place-items-center mx-auto mb-4">
                        <svg viewBox="0 0 24 24" class="w-6 h-6 text-[#9C998F]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <h3 class="text-[14px] font-semibold text-[#15140F] mb-1">No contributions yet</h3>
                    <p class="tiny">Share your box to start receiving contributions.</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>