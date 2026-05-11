<x-layouts.app>
    @php
        $sym           = auth()->user()->country?->currency_symbol ?? '₵';
        $totalRaised   = $moneyBoxes->sum('total_contributions');
        $totalContribs = $moneyBoxes->sum('contribution_count');
        $activeCount   = $moneyBoxes->where('is_active', true)->count();
        $avgGift       = $totalContribs > 0 ? round($totalRaised / $totalContribs, 2) : 0;
        $recent        = $moneyBoxes->pluck('contributions')->flatten()->sortByDesc('created_at')->take(6);
    @endphp

    <div class="px-7 py-7 max-w-[1280px]">

        {{-- Page header --}}
        <div class="flex items-end justify-between gap-6 mb-6">
            <div>
                <h1 class="page-title">Good morning, {{ explode(' ', auth()->user()->name)[0] }}.</h1>
                <p class="text-[13.5px] text-[#6B6862] mt-1.5">Here's what's happening across your money boxes.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('money-boxes.create') }}" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    New box
                </a>
            </div>
        </div>

        {{-- Stat grid --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 mb-6">
            <div class="stat-card">
                <div class="stat-label">Total raised</div>
                <div class="stat-value">{{ $sym }}{{ number_format($totalRaised, 2) }}</div>
                <div class="stat-delta text-primary-600">
                    <svg viewBox="0 0 24 24" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5"/><path d="m5 12 7-7 7 7"/></svg>
                    All campaigns
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Contributors</div>
                <div class="stat-value tnum">{{ number_format($totalContribs) }}</div>
                <div class="stat-delta">All time</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Active boxes</div>
                <div class="stat-value tnum">{{ $activeCount }}</div>
                <div class="stat-delta {{ $activeCount > 0 ? 'text-primary-600' : '' }}">
                    {{ $moneyBoxes->count() }} total
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Avg. contribution</div>
                <div class="stat-value">{{ $sym }}{{ number_format($avgGift, 2) }}</div>
                <div class="stat-delta">Per contributor</div>
            </div>
        </div>

        {{-- Main grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-4 mb-4">

            {{-- Recent contributions --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-title">Recent contributions</div>
                    <a href="{{ route('money-boxes.index') }}" class="btn btn-ghost btn-sm text-[#6B6862]">
                        View all
                        <svg viewBox="0 0 24 24" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </a>
                </div>

                @if($recent->count() > 0)
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Contributor</th>
                                <th>Box</th>
                                <th>Method</th>
                                <th class="num">Amount</th>
                                <th>When</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent as $c)
                                @php
                                    $name     = $c->getDisplayName();
                                    $initials = $name === 'Anonymous' ? '·' : collect(explode(' ', $name))->map(fn($p) => strtoupper($p[0] ?? ''))->take(2)->implode('');
                                    $box      = $c->moneyBox;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div class="w-[22px] h-[22px] rounded-full bg-primary-600 text-white grid place-items-center text-[9.5px] font-semibold flex-none">{{ $initials }}</div>
                                            <div>
                                                <div class="font-medium text-[#15140F]">{{ $name }}</div>
                                                @if($c->message)
                                                    <div class="tiny italic">"{{ Str::limit($c->message, 40) }}"</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="muted text-[12.5px]">{{ $box?->title ? Str::limit($box->title, 28) : '—' }}</td>
                                    <td>
                                        <span class="pill pill-muted">
                                            <span class="pill-dot"></span>
                                            {{ $c->payment_method ?? 'Card' }}
                                        </span>
                                    </td>
                                    <td class="num font-semibold text-[#15140F] tnum">{{ $sym }}{{ number_format($c->amount, 2) }}</td>
                                    <td class="muted text-[12px]">{{ $c->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="card-body text-center py-10">
                        <div class="text-[#9C998F] mb-1">No contributions yet</div>
                        <div class="tiny">Share your box to start receiving contributions.</div>
                    </div>
                @endif
            </div>

            {{-- Quick actions --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-title">Quick actions</div>
                </div>
                <div class="card-body flex flex-col gap-2.5">

                    <a href="{{ route('money-boxes.create') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-lg border border-[#E6E3DC] bg-white hover:bg-[#FBFAF6] transition text-left">
                        <div class="w-8 h-8 rounded-[7px] bg-primary-50 text-primary-600 grid place-items-center flex-none">
                            <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-[13px] font-medium text-[#15140F]">Create a money box</div>
                            <div class="tiny">Wedding, medical, project — under 60 seconds</div>
                        </div>
                        <svg viewBox="0 0 24 24" class="w-3.5 h-3.5 text-[#9C998F]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </a>

                    <a href="{{ route('money-boxes.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-lg border border-[#E6E3DC] bg-white hover:bg-[#FBFAF6] transition text-left">
                        <div class="w-8 h-8 rounded-[7px] bg-primary-50 text-primary-600 grid place-items-center flex-none">
                            <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="12" r="3"/><circle cx="18" cy="6" r="3"/><circle cx="18" cy="18" r="3"/><path d="M8.6 13.5 15.4 17"/><path d="M15.4 7 8.6 10.5"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-[13px] font-medium text-[#15140F]">Share your active box</div>
                            <div class="tiny">QR code, WhatsApp, link</div>
                        </div>
                        <svg viewBox="0 0 24 24" class="w-3.5 h-3.5 text-[#9C998F]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </a>

                    <div class="group flex items-center gap-3 px-3 py-2.5 rounded-lg border border-[#E6E3DC] bg-white hover:bg-[#FBFAF6] transition text-left">
                        <div class="w-8 h-8 rounded-[7px] bg-primary-50 text-primary-600 grid place-items-center flex-none">
                            <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18"/><circle cx="17" cy="14.5" r="1"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-[13px] font-medium text-[#15140F]">Withdraw funds</div>
                            <div class="tiny">{{ $sym }}{{ number_format($totalRaised, 2) }} across your boxes</div>
                        </div>
                        <svg viewBox="0 0 24 24" class="w-3.5 h-3.5 text-[#9C998F]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </div>

                    <a href="{{ route('money-boxes.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-lg border border-[#E6E3DC] bg-white hover:bg-[#FBFAF6] transition text-left">
                        <div class="w-8 h-8 rounded-[7px] bg-primary-50 text-primary-600 grid place-items-center flex-none">
                            <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V8"/><path d="M10 20V4"/><path d="M16 20v-8"/><path d="M22 20H2"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-[13px] font-medium text-[#15140F]">See analytics</div>
                            <div class="tiny">Trends, top contributors, retention</div>
                        </div>
                        <svg viewBox="0 0 24 24" class="w-3.5 h-3.5 text-[#9C998F]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </a>

                </div>
            </div>
        </div>

        {{-- Your boxes --}}
        @if($moneyBoxes->count() > 0)
            <div class="flex items-center justify-between mb-3">
                <div class="text-[13px] font-semibold text-[#15140F]">Your money boxes</div>
                <a href="{{ route('money-boxes.index') }}" class="btn btn-ghost btn-sm text-[#6B6862]">
                    See all
                    <svg viewBox="0 0 24 24" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($moneyBoxes->take(6) as $box)
                    @php
                        $covers  = ['cover-emerald','cover-amber','cover-slate','cover-rose','cover-violet'];
                        $cover   = $covers[$loop->index % count($covers)];
                        $pct     = $box->goal_amount > 0 ? min(100, round(($box->total_contributions / $box->goal_amount) * 100)) : 0;
                    @endphp
                    <a href="{{ route('money-boxes.show', $box) }}" wire:navigate
                       class="card block hover:shadow-[0_1px_0_rgba(20,18,12,.04),0_8px_24px_-8px_rgba(20,18,12,.10)] transition-shadow duration-150 overflow-hidden">

                        {{-- Cover --}}
                        <div class="{{ $cover }} h-[90px] relative">
                            <div class="absolute inset-0 grid place-items-center text-white/90 font-serif text-[28px] tracking-wide">
                                {{ substr($box->title, 0, 1) }}
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
                                {{ Str::limit($box->title, 48) }}
                            </div>
                            <div class="tiny mb-3">
                                {{ $box->category?->name ?? 'General' }} · {{ $box->contribution_count }} contributors
                            </div>

                            {{-- Progress --}}
                            <div class="flex items-baseline justify-between mb-1.5">
                                <div class="text-[13px] font-semibold text-[#15140F] tnum">
                                    {{ $sym }}{{ number_format($box->total_contributions, 2) }}
                                    <span class="muted font-normal text-[12px]">of {{ $sym }}{{ number_format($box->goal_amount ?? 0, 2) }}</span>
                                </div>
                                <div class="tiny tnum">{{ $pct }}%</div>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill" style="width: {{ $pct }}%"></div>
                            </div>

                            <div class="flex items-center justify-between mt-3">
                                <span class="tiny">
                                    @if($box->end_date)
                                        Ends {{ $box->end_date->format('M j, Y') }}
                                    @else
                                        Ongoing
                                    @endif
                                </span>
                                <span class="text-[12px] font-medium text-primary-600">
                                    Open
                                    <svg viewBox="0 0 24 24" class="w-3 h-3 inline" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="card p-12 text-center border-dashed border-2 border-[#D9D6CE]">
                <div class="w-12 h-12 rounded-xl bg-primary-50 text-primary-600 grid place-items-center mx-auto mb-4">
                    <svg viewBox="0 0 24 24" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7.5 12 3l9 4.5v9L12 21l-9-4.5v-9Z"/><path d="M3 7.5 12 12l9-4.5"/><path d="M12 12v9"/></svg>
                </div>
                <h3 class="text-[15px] font-semibold text-[#15140F] mb-1">No money boxes yet</h3>
                <p class="tiny mb-5 max-w-xs mx-auto">Create your first box to start collecting contributions for any cause.</p>
                <a href="{{ route('money-boxes.create') }}" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    Create your first box
                </a>
            </div>
        @endif
    </div>
</x-layouts.app>