<x-layouts.app :title="__('Overview')">
@php
    $user = auth()->user();
    $sym  = $user->country?->currency_symbol ?? '₵';
    $firstName = explode(' ', $user->name)[0];

    // Aggregate stats across all user's PiggyBoxes
    $totalRaised       = $user->moneyBoxes()->sum('total_contributions');
    $totalContributors = $user->moneyBoxes()->sum('contribution_count');
    $activeBoxes       = $user->moneyBoxes()->where('is_active', true)->count();
    $avgGift           = $totalContributors > 0 ? $totalRaised / $totalContributors : 0;

    // Recent contributions across all PiggyBoxes
    $recentContribs = \App\Models\Contribution::whereIn('money_box_id', $user->moneyBoxes->pluck('id'))
        ->with(['moneyBox'])
        ->latest()
        ->take(8)
        ->get();

    // Quick pick first active box for quick action link
    $firstActiveBox = $user->moneyBoxes()->where('is_active', true)->first();
    $availableBalance = $user->moneyBoxes()->get()->sum(fn($b) => $b->getAvailableBalance());

    // Weekly bar chart data (last 7 days)
    $boxIds = $user->moneyBoxes->pluck('id');
    $weeklyData = collect();
    for ($i = 6; $i >= 0; $i--) {
        $date = now()->subDays($i);
        $dayTotal = \App\Models\Contribution::whereIn('money_box_id', $boxIds)
            ->whereDate('created_at', $date->format('Y-m-d'))
            ->sum('amount');
        $weeklyData->push(['label' => $date->format('D'), 'total' => (float) $dayTotal]);
    }
    $weekMax = $weeklyData->max('total') ?: 1;
    $weekTotal = $weeklyData->sum('total');

    // Goal progress (combined)
    $totalGoal = $user->moneyBoxes()->where('goal_amount', '>', 0)->sum('goal_amount');
    $totalTowardGoal = $user->moneyBoxes()->where('goal_amount', '>', 0)->sum('total_contributions');
    $goalPct = $totalGoal > 0 ? min(100, round(($totalTowardGoal / $totalGoal) * 100)) : 0;
@endphp

<div class="page-wrap max-w-[1280px]">

    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-6">
        <div>
            <h1 class="page-title">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ $firstName }}.</h1>
            <p class="text-[13.5px] text-[#6B6862] mt-1.5">Here's what's happening across your PiggyBoxes this week.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="btn">
                <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v12"/><path d="m6 10 6 6 6-6"/><path d="M4 20h16"/></svg>
                Export
            </button>
            <a href="{{ route('money-boxes.create') }}" wire:navigate class="btn btn-primary">
                <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                New PiggyBox
            </a>
        </div>
    </div>

    {{-- Stat grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 mb-6">
        <div class="stat-card">
            <div class="stat-label">Total raised</div>
            <div class="stat-value">{{ $sym }}{{ number_format($totalRaised, 2) }}</div>
            <div class="stat-delta text-[#6B6862]">across all PiggyBoxes</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Contributors</div>
            <div class="stat-value tnum">{{ number_format($totalContributors) }}</div>
            <div class="stat-delta text-[#6B6862]">total contributions</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Active PiggyBoxes</div>
            <div class="stat-value tnum">{{ $activeBoxes }}</div>
            <div class="stat-delta text-[#6B6862]">{{ $user->moneyBoxes()->count() }} total created</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Avg. gift</div>
            <div class="stat-value tnum">{{ $avgGift > 0 ? $sym . number_format($avgGift, 2) : '—' }}</div>
            <div class="stat-delta text-[#6B6862]">per contributor</div>
        </div>
    </div>

    {{-- Charts row --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-4 mb-4">

        {{-- Contributions this week bar chart --}}
        <div class="card">
            <div class="card-head">
                <div>
                    <div class="card-title">Contributions this week</div>
                    <div class="tiny mt-0.5">{{ now()->subDays(6)->format('M j') }} – {{ now()->format('M j, Y') }}</div>
                </div>
            </div>
            <div class="card-body">
                <div class="flex items-end gap-6 mb-4">
                    <div>
                        <div class="tiny">This week</div>
                        <div class="text-[28px] font-semibold tracking-tight tnum leading-tight">{{ $sym }}{{ number_format($weekTotal, 2) }}</div>
                    </div>
                </div>
                <div class="flex items-end gap-2" style="height: 180px; padding: 8px 0;">
                    @foreach($weeklyData as $d)
                        <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end">
                            <div
                                class="w-full rounded-t {{ $loop->last ? 'bg-primary-600' : 'bg-primary-50' }}"
                                style="height: {{ max(4, ($d['total'] / $weekMax) * 100) }}%;"
                                title="{{ $d['label'] }}: {{ $sym }}{{ number_format($d['total'], 2) }}"
                            ></div>
                            <div class="tiny text-[10.5px]">{{ $d['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Goal progress donut --}}
        <div class="card">
            <div class="card-head">
                <div class="card-title">Goal progress</div>
            </div>
            <div class="card-body flex items-center gap-5">
                @if($totalGoal > 0)
                    <div class="donut flex-none" style="--p: {{ $goalPct }}">
                        <div class="donut-inner">
                            <div class="donut-num">{{ $goalPct }}%</div>
                            <div class="donut-sub">of combined goals</div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3.5 flex-1">
                        <div class="flex items-baseline justify-between">
                            <div class="tiny text-[12px]">Goal</div>
                            <div class="tnum font-semibold text-[14px]">{{ $sym }}{{ number_format($totalGoal) }}</div>
                        </div>
                        <div class="flex items-baseline justify-between">
                            <div class="tiny text-[12px]">Raised</div>
                            <div class="tnum font-semibold text-[14px] text-primary-600">{{ $sym }}{{ number_format($totalTowardGoal) }}</div>
                        </div>
                        <div class="flex items-baseline justify-between">
                            <div class="tiny text-[12px]">Remaining</div>
                            <div class="tnum font-semibold text-[14px] text-[#6B6862]">{{ $sym }}{{ number_format(max(0, $totalGoal - $totalTowardGoal)) }}</div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-6 w-full">
                        <div class="tiny">No goals set on any PiggyBoxes yet.</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Main 2-col grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-4 mb-4">

        {{-- Recent contributions --}}
        <div class="card">
            <div class="card-head">
                <div class="card-title">Recent contributions</div>
                <a href="{{ route('money-boxes.index') }}" wire:navigate class="btn btn-ghost btn-sm text-[#6B6862]">
                    View all
                    <svg viewBox="0 0 24 24" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>
            </div>
            @if($recentContribs->count() > 0)
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead><tr>
                            <th>Contributor</th>
                            <th>PiggyBox</th>
                            <th>Method</th>
                            <th class="text-right">Amount</th>
                            <th>When</th>
                        </tr></thead>
                        <tbody>
                            @foreach($recentContribs as $c)
                                @php
                                    $name = $c->getDisplayName();
                                    $initials = $name === 'Anonymous' ? '·' : collect(explode(' ', $name))->map(fn($p) => strtoupper($p[0] ?? ''))->take(2)->implode('');
                                @endphp
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div class="w-[22px] h-[22px] rounded-full bg-primary-600 text-white grid place-items-center text-[9.5px] font-semibold flex-none">{{ $initials }}</div>
                                            <div>
                                                <div class="font-medium text-[#15140F] text-[13px]">{{ $name }}</div>
                                                @if($c->message)
                                                    <div class="tiny italic">"{{ Str::limit($c->message, 32) }}"</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="muted text-[12.5px]">{{ Str::limit($c->moneyBox?->title ?? '—', 28) }}</td>
                                    <td><span class="pill pill-muted"><span class="pill-dot"></span>{{ $c->payment_method ?? 'Card' }}</span></td>
                                    <td class="text-right font-semibold text-[#15140F] tnum text-[13px]">{{ $sym }}{{ number_format($c->amount, 2) }}</td>
                                    <td class="muted text-[12px]">{{ $c->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="card-body text-center py-10">
                    <div class="w-10 h-10 rounded-xl bg-[#F3F1EB] grid place-items-center mx-auto mb-3">
                        <svg viewBox="0 0 24 24" class="w-5 h-5 text-[#9C998F]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6"/><path d="M16 4.5a3.5 3.5 0 0 1 0 7"/><path d="M21.5 20c0-3-1.7-5.2-4.5-5.8"/></svg>
                    </div>
                    <p class="text-[13px] text-[#6B6862]">No contributions yet. Share your PiggyBox to get started.</p>
                </div>
            @endif
        </div>

        {{-- Quick actions --}}
        <div class="card">
            <div class="card-head"><div class="card-title">Quick actions</div></div>
            <div class="card-body flex flex-col gap-2">
                <a href="{{ route('money-boxes.create') }}" wire:navigate
                   class="flex items-center gap-3 p-2.5 rounded-[8px] border border-[#E6E3DC] bg-white hover:bg-[#FBFAF6] transition-colors text-left">
                    <div class="w-8 h-8 rounded-[7px] bg-[#E6F1EB] text-[#1B6B4E] grid place-items-center flex-none">
                        <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-[13px] font-medium text-[#15140F]">Create a PiggyBox</div>
                        <div class="tiny">Wedding, medical, project — 60 seconds</div>
                    </div>
                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5 text-[#9C998F]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>

                @if($firstActiveBox)
                <a href="{{ route('money-boxes.share', $firstActiveBox) }}" wire:navigate
                   class="flex items-center gap-3 p-2.5 rounded-[8px] border border-[#E6E3DC] bg-white hover:bg-[#FBFAF6] transition-colors text-left">
                    <div class="w-8 h-8 rounded-[7px] bg-[#E6F1EB] text-[#1B6B4E] grid place-items-center flex-none">
                        <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="12" r="3"/><circle cx="18" cy="6" r="3"/><circle cx="18" cy="18" r="3"/><path d="M8.6 13.5 15.4 17"/><path d="M15.4 7 8.6 10.5"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-[13px] font-medium text-[#15140F]">Share your active PiggyBox</div>
                        <div class="tiny">QR code, WhatsApp, link</div>
                    </div>
                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5 text-[#9C998F]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>
                @endif

                @if($availableBalance >= config('withdrawal.min_amount', 10) && auth()->user()->isVerified() && auth()->user()->withdrawalAccounts()->active()->count() > 0)
                @php $withdrawBox = $user->moneyBoxes()->get()->first(fn($b) => $b->getAvailableBalance() > 0); @endphp
                @if($withdrawBox)
                <a href="{{ route('money-boxes.withdraw.create', $withdrawBox) }}" wire:navigate
                   class="flex items-center gap-3 p-2.5 rounded-[8px] border border-[#E6E3DC] bg-white hover:bg-[#FBFAF6] transition-colors text-left">
                    <div class="w-8 h-8 rounded-[7px] bg-[#E6F1EB] text-[#1B6B4E] grid place-items-center flex-none">
                        <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18"/><circle cx="17" cy="14.5" r="1"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-[13px] font-medium text-[#15140F]">Withdraw funds</div>
                        <div class="tiny">{{ $sym }}{{ number_format($availableBalance, 2) }} available</div>
                    </div>
                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5 text-[#9C998F]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>
                @endif
                @endif

                <a href="{{ route('browse') }}" wire:navigate
                   class="flex items-center gap-3 p-2.5 rounded-[8px] border border-[#E6E3DC] bg-white hover:bg-[#FBFAF6] transition-colors text-left">
                    <div class="w-8 h-8 rounded-[7px] bg-[#E6F1EB] text-[#1B6B4E] grid place-items-center flex-none">
                        <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3a13 13 0 0 1 0 18"/><path d="M12 3a13 13 0 0 0 0 18"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-[13px] font-medium text-[#15140F]">Browse PiggyBoxes</div>
                        <div class="tiny">Discover and support PiggyBoxes</div>
                    </div>
                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5 text-[#9C998F]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>

                @if(!auth()->user()->isVerified())
                <a href="{{ route('settings.verification') }}" wire:navigate
                   class="flex items-center gap-3 p-2.5 rounded-[8px] border border-amber-200 bg-amber-50 hover:bg-amber-100 transition-colors text-left">
                    <div class="w-8 h-8 rounded-[7px] bg-amber-100 text-amber-600 grid place-items-center flex-none">
                        <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-[13px] font-medium text-amber-800">Verify your identity</div>
                        <div class="text-[11.5px] text-amber-700">Required to withdraw funds</div>
                    </div>
                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Active PiggyBoxes preview --}}
    @if($user->moneyBoxes()->where('is_active', true)->count() > 0)
    <div class="card">
        <div class="card-head">
            <div class="card-title">Your active PiggyBoxes</div>
            <a href="{{ route('money-boxes.index') }}" wire:navigate class="btn btn-ghost btn-sm text-[#6B6862]">
                All PiggyBoxes <svg viewBox="0 0 24 24" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </a>
        </div>
        @php
            $activeBoxList = $user->moneyBoxes()->where('is_active', true)->latest()->take(4)->get();
            $covers = ['cover-emerald','cover-amber','cover-slate','cover-rose','cover-violet'];
        @endphp
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr>
                    <th>PiggyBox</th>
                    <th class="text-right">Raised</th>
                    <th class="text-right">Contributors</th>
                    <th>Progress</th>
                    <th>Status</th>
                    <th></th>
                </tr></thead>
                <tbody>
                    @foreach($activeBoxList as $i => $b)
                    @php $bPct = $b->goal_amount > 0 ? min(100, round(($b->total_contributions / $b->goal_amount) * 100)) : 0; @endphp
                    <tr>
                        <td>
                            <div class="flex items-center gap-2.5">
                                <div class="{{ $covers[$i % count($covers)] }} w-9 h-9 rounded-[7px] relative flex-none overflow-hidden">
                                    <div class="absolute inset-0 grid place-items-center text-white/90 font-serif text-[16px]">{{ substr($b->title,0,1) }}</div>
                                </div>
                                <div>
                                    <div class="text-[13px] font-medium text-[#15140F]">{{ Str::limit($b->title, 36) }}</div>
                                    <div class="tiny">{{ $b->category?->name ?? 'General' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-right font-semibold tnum text-[13px]">{{ $sym }}{{ number_format($b->total_contributions, 2) }}</td>
                        <td class="text-right tnum text-[13px]">{{ number_format($b->contribution_count) }}</td>
                        <td style="min-width:120px">
                            @if($b->goal_amount)
                                <div class="flex items-center gap-2">
                                    <div class="progress-track flex-1"><div class="progress-fill" style="width:{{ $bPct }}%"></div></div>
                                    <span class="tiny tnum">{{ $bPct }}%</span>
                                </div>
                            @else
                                <span class="tiny text-[#9C998F]">No goal</span>
                            @endif
                        </td>
                        <td><span class="pill pill-ok"><span class="pill-dot"></span>Active</span></td>
                        <td>
                            <a href="{{ route('money-boxes.show', $b) }}" wire:navigate class="btn btn-ghost btn-sm btn-icon">
                                <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
</x-layouts.app>
