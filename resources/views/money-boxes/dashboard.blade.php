<x-layouts.app>
    @php
        $sym           = auth()->user()->country?->currency_symbol ?? '₵';
        $totalRaised   = $moneyBoxes->sum('total_contributions');
        $totalContribs = $moneyBoxes->sum('contribution_count');
        $activeCount   = $moneyBoxes->where('is_active', true)->count();
        $avgGift       = $totalContribs > 0 ? round($totalRaised / $totalContribs, 2) : 0;
        $recent        = $moneyBoxes->pluck('contributions')->flatten()->sortByDesc('created_at')->take(6);
        $withdrawBox   = $moneyBoxes->first(fn($box) => $box->getAvailableBalance() > 0) ?? $moneyBoxes->first();

        // Piggy Wallet
        $piggyBox      = auth()->user()->piggyBox;
        $piggyBalance  = $piggyBox ? $piggyBox->getAvailableBalance() : 0;
    @endphp

    <div class="page-wrap max-w-[1280px]">

        {{-- Page header --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 sm:gap-6 mb-6">
            <div>
                <h1 class="page-title">Good morning, {{ explode(' ', auth()->user()->name)[0] }}.</h1>
                <p class="text-[13.5px] text-[#6B6862] mt-1.5">Here's your fundraising campaigns overview for this week.</p>
            </div>
            <div class="flex items-center gap-2 flex-none">
                <a href="{{ route('money-boxes.create') }}" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    New PiggyBox
                </a>
            </div>
        </div>

        {{-- Piggy Wallet banner --}}
        <div class="rounded-[10px] border border-amber-200 bg-gradient-to-br from-[#FFFBF0] to-[#FFF8E6] p-4 sm:p-5 mb-6"
             x-data="{ codeCopied: false }">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                {{-- Left: icon + info --}}
                <div class="flex items-start gap-3 flex-1 min-w-0">
                    <div class="w-10 h-10 rounded-[9px] bg-amber-100 text-amber-700 grid place-items-center flex-none mt-0.5">
                        <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><circle cx="17" cy="15" r="1"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[14.5px] font-semibold text-[#15140F]">Piggy Wallet</span>
                            <span class="text-[10px] font-semibold bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded-full tracking-wide uppercase">Always on</span>
                        </div>
                        <p class="text-[12.5px] text-[#6B6862] leading-relaxed">
                            Your permanent payment link — share it to receive money for services, appreciation, or anything. No campaign needed, no deadline.
                        </p>
                        <div class="flex items-center gap-2.5 mt-3 flex-wrap">
                            <span class="text-[11.5px] text-[#9C998F]">Your code</span>
                            <code class="font-mono text-[13.5px] font-bold text-amber-800 bg-amber-100 border border-amber-200 px-2.5 py-0.5 rounded-[6px] tracking-widest">{{ auth()->user()->piggy_code }}</code>
                            <button @click="navigator.clipboard.writeText('{{ url('/piggy/' . auth()->user()->piggy_code) }}').then(() => { codeCopied = true; setTimeout(() => codeCopied = false, 2000) })"
                                    class="text-[11.5px] font-medium text-amber-600 hover:text-amber-900 transition-colors">
                                <span x-show="!codeCopied">Copy link</span>
                                <span x-show="codeCopied" x-cloak>✓ Copied!</span>
                            </button>
                        </div>
                    </div>
                </div>
                {{-- Right: balance + actions --}}
                <div class="flex flex-col items-start sm:items-end gap-3 flex-none">
                    <div class="text-left sm:text-right">
                        <div class="text-[11px] text-[#9C998F] mb-0.5">Available balance</div>
                        <div class="text-[22px] font-bold tnum text-[#15140F] leading-none">{{ $sym }}{{ number_format($piggyBalance, 2) }}</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ url('/piggy/' . auth()->user()->piggy_code) }}" target="_blank" rel="noopener"
                           class="btn btn-sm" style="border-color:#D97706;color:#92400E;">
                            <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="12" r="3"/><circle cx="18" cy="6" r="3"/><circle cx="18" cy="18" r="3"/><path d="M8.6 13.5 15.4 17"/><path d="M15.4 7 8.6 10.5"/></svg>
                            Share wallet
                        </a>
                        <a href="{{ route('piggy.my-piggy-box') }}" wire:navigate
                           class="btn btn-sm" style="background:#B45309;border-color:#B45309;color:#fff;">
                            Open wallet
                            <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                        </a>
                    </div>
                </div>
            </div>
            {{-- Distinction note --}}
            <div class="border-t border-amber-200/70 mt-4 pt-3.5 flex flex-col sm:flex-row gap-2.5 sm:gap-8">
                <div class="flex items-start gap-2">
                    <div class="w-5 h-5 rounded-[5px] bg-amber-100 text-amber-600 grid place-items-center flex-none mt-px">
                        <svg viewBox="0 0 24 24" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><circle cx="17" cy="15" r="1"/></svg>
                    </div>
                    <p class="text-[12px] text-[#6B6862] leading-relaxed">
                        <span class="font-semibold text-[#15140F]">Piggy Wallet</span> — receiving payment for a service, gift, or appreciation? Share your wallet link. It's always on, no setup needed.
                    </p>
                </div>
                <div class="flex items-start gap-2">
                    <div class="w-5 h-5 rounded-[5px] bg-primary-50 text-primary-600 grid place-items-center flex-none mt-px">
                        <svg viewBox="0 0 24 24" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8h14M5 8a2 2 0 1 0-4 0v12a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8m-4 0V4a2 2 0 0 0-4 0v4"/></svg>
                    </div>
                    <p class="text-[12px] text-[#6B6862] leading-relaxed">
                        <span class="font-semibold text-[#15140F]">PiggyBox</span> — fundraising for a cause, event, or project? <a href="{{ route('money-boxes.create') }}" wire:navigate class="text-primary-600 font-medium hover:underline">Create Campaign (PiggyBox) →</a>
                    </p>
                </div>
            </div>
        </div>

        {{-- Fundraising campaigns section label --}}
        <div class="text-[10.5px] font-medium uppercase tracking-[0.08em] text-[#9C998F] mb-3">Fundraising Campaigns</div>

        {{-- Stat grid --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 mb-6">
            <div class="stat-card">
                <div class="stat-label">Total raised</div>
                <div class="stat-value">{{ $sym }}{{ number_format($totalRaised, 2) }}</div>
                <div class="stat-delta text-primary-600">
                    <svg viewBox="0 0 24 24" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5"/><path d="m5 12 7-7 7 7"/></svg>
                    All PiggyBoxes
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Contributors</div>
                <div class="stat-value tnum">{{ number_format($totalContribs) }}</div>
                <div class="stat-delta">All time</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Active PiggyBoxes</div>
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
                                <th>PiggyBox</th>
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
                        <div class="tiny">Share your PiggyBox to start receiving contributions.</div>
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
                            <div class="text-[13px] font-medium text-[#15140F]">Create a PiggyBox</div>
                            <div class="tiny">Wedding, medical, project — under 60 seconds</div>
                        </div>
                        <svg viewBox="0 0 24 24" class="w-3.5 h-3.5 text-[#9C998F]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </a>

                    <a href="{{ route('money-boxes.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-lg border border-[#E6E3DC] bg-white hover:bg-[#FBFAF6] transition text-left">
                        <div class="w-8 h-8 rounded-[7px] bg-primary-50 text-primary-600 grid place-items-center flex-none">
                            <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="12" r="3"/><circle cx="18" cy="6" r="3"/><circle cx="18" cy="18" r="3"/><path d="M8.6 13.5 15.4 17"/><path d="M15.4 7 8.6 10.5"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-[13px] font-medium text-[#15140F]">Share your active PiggyBox</div>
                            <div class="tiny">QR code, WhatsApp, link</div>
                        </div>
                        <svg viewBox="0 0 24 24" class="w-3.5 h-3.5 text-[#9C998F]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </a>

                    @if($withdrawBox)
                    <a href="{{ route('money-boxes.withdraw.create', $withdrawBox) }}" wire:navigate
                       class="group flex items-center gap-3 px-3 py-2.5 rounded-lg border border-[#E6E3DC] bg-white hover:bg-[#FBFAF6] transition text-left">
                        <div class="w-8 h-8 rounded-[7px] bg-primary-50 text-primary-600 grid place-items-center flex-none">
                            <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18"/><circle cx="17" cy="14.5" r="1"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-[13px] font-medium text-[#15140F]">Withdraw funds</div>
                            <div class="tiny">{{ $sym }}{{ number_format($totalRaised, 2) }} across your PiggyBoxes</div>
                        </div>
                        <svg viewBox="0 0 24 24" class="w-3.5 h-3.5 text-[#9C998F]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </a>
                    @endif

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

        {{-- Your PiggyBoxes --}}
        @if($moneyBoxes->count() > 0)
            <div class="flex items-center justify-between mb-3">
                <div class="text-[13px] font-semibold text-[#15140F]">Your PiggyBoxes</div>
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
                <h3 class="text-[15px] font-semibold text-[#15140F] mb-1">No PiggyBoxes yet</h3>
                <p class="tiny mb-5 max-w-xs mx-auto">Create your first PiggyBox to start collecting contributions for any cause.</p>
                <a href="{{ route('money-boxes.create') }}" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    Create your first PiggyBox
                </a>
            </div>
        @endif
    </div>
</x-layouts.app>
