<x-layouts.app>
    @php
        $sym    = auth()->user()->country?->currency_symbol ?? '₵';
        $covers = ['cover-emerald','cover-amber','cover-slate','cover-rose','cover-violet'];
        $cover  = $covers[crc32($moneyBox->id) % count($covers)];
        $pct    = $moneyBox->goal_amount > 0
            ? min(100, round(($moneyBox->total_contributions / $moneyBox->goal_amount) * 100))
            : 0;
    @endphp

    <div
        class="page-wrap max-w-[1280px]"
        x-data="{
            tab: 'overview',
            showToast: false,
            toastMsg: '',
            toast(msg) { this.toastMsg = msg; this.showToast = true; setTimeout(() => this.showToast = false, 3500); }
        }"
        x-init="
            @if(session('success'))
                toast('{{ session('success') }}');
            @endif
        "
    >
        {{-- Toast --}}
        <div
            x-show="showToast"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed top-4 right-4 z-50 bg-[#15140F] text-white px-4 py-2.5 rounded-[8px] shadow-lg flex items-center gap-2 text-[13px]"
        >
            <svg viewBox="0 0 24 24" class="w-4 h-4 text-primary-400 flex-none" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
            <span x-text="toastMsg"></span>
        </div>

        {{-- Back --}}
        <div class="mb-3.5">
            <a href="{{ route('money-boxes.index') }}" wire:navigate class="btn btn-ghost btn-sm text-[#6B6862]">
                <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                All piggy boxes
            </a>
        </div>

        {{-- Page header --}}
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
            <div class="flex items-center gap-4 min-w-0">
                <div class="{{ $cover }} w-16 h-16 rounded-[12px] relative flex-none overflow-hidden">
                    <div class="absolute inset-0 grid place-items-center text-white/90 font-serif text-[28px]">
                        {{ substr($moneyBox->title, 0, 1) }}
                    </div>
                </div>
                <div>
                    <div class="flex items-center flex-wrap gap-1.5 mb-1.5">
                        <span class="pill {{ $moneyBox->visibility->value === 'public' ? 'pill-info' : 'pill-muted' }}">
                            <span class="pill-dot"></span>{{ ucfirst($moneyBox->visibility->value) }}
                        </span>
                        <span class="pill {{ $moneyBox->is_active ? 'pill-ok' : 'pill-muted' }}">
                            <span class="pill-dot"></span>{{ $moneyBox->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if($moneyBox->category)
                            <span class="tiny">{{ $moneyBox->category->name }}</span>
                        @endif
                    </div>
                    <h1 class="page-title" style="font-size:2rem">{{ $moneyBox->title }}</h1>
                    <p class="text-[13px] text-[#6B6862] mt-1">
                        Created {{ $moneyBox->created_at->format('M j, Y') }} ·
                        @if($moneyBox->end_date)
                            Ends {{ $moneyBox->end_date->format('M j, Y') }}
                        @else
                            Ongoing
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('money-boxes.edit', $moneyBox) }}" wire:navigate class="btn">
                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 4l6 6L8 22H2v-6L14 4Z"/></svg>
                    Edit
                </a>
                <a href="{{ route('money-boxes.share', $moneyBox) }}" wire:navigate class="btn">
                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="12" r="3"/><circle cx="18" cy="6" r="3"/><circle cx="18" cy="18" r="3"/><path d="M8.6 13.5 15.4 17"/><path d="M15.4 7 8.6 10.5"/></svg>
                    Share
                </a>
                <a href="{{ route('box.show', $moneyBox->slug) }}" target="_blank" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                    Public page
                </a>
            </div>
        </div>

        {{-- Stat grid --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 mb-6">
            <div class="stat-card">
                <div class="stat-label">Raised</div>
                <div class="stat-value">{{ $sym }}{{ number_format($moneyBox->total_contributions, 2) }}</div>
                @if($moneyBox->goal_amount)
                    <div class="stat-delta text-primary-600">
                        <svg viewBox="0 0 24 24" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5"/><path d="m5 12 7-7 7 7"/></svg>
                        {{ $pct }}% of goal
                    </div>
                @endif
            </div>

            <div class="stat-card">
                <div class="stat-label">Available</div>
                <div class="stat-value text-primary-600">{{ $sym }}{{ number_format($moneyBox->getAvailableBalance(), 2) }}</div>
                <div class="stat-delta">Ready to withdraw</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Contributors</div>
                <div class="stat-value tnum">{{ number_format($moneyBox->contribution_count) }}</div>
                <div class="stat-delta">{{ Str::plural('person', $moneyBox->contribution_count) }}</div>
            </div>

            @if($moneyBox->goal_amount)
                <div class="stat-card">
                    <div class="stat-label">Goal</div>
                    <div class="stat-value">{{ $sym }}{{ number_format($moneyBox->goal_amount, 2) }}</div>
                    <div class="stat-delta">
                        {{ $sym }}{{ number_format(max(0, $moneyBox->goal_amount - $moneyBox->total_contributions), 2) }} remaining
                    </div>
                </div>
            @else
                <div class="stat-card">
                    <div class="stat-label">Avg. gift</div>
                    <div class="stat-value">
                        {{ $moneyBox->contribution_count > 0
                            ? $sym . number_format($moneyBox->total_contributions / $moneyBox->contribution_count, 2)
                            : '—' }}
                    </div>
                    <div class="stat-delta">Per contributor</div>
                </div>
            @endif
        </div>

        {{-- Withdraw banner --}}
        @if(auth()->user()->isVerified() && $moneyBox->getAvailableBalance() >= config('withdrawal.min_amount', 10) && auth()->user()->withdrawalAccounts()->active()->count() > 0)
            <div class="flex items-center justify-between gap-4 px-4 py-3 rounded-[8px] bg-primary-50 border border-primary-200 mb-5">
                <div class="flex items-center gap-2 text-primary-700 text-[13px]">
                    <svg viewBox="0 0 24 24" class="w-4 h-4 flex-none" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18"/><circle cx="17" cy="14.5" r="1"/></svg>
                    <span>{{ $sym }}{{ number_format($moneyBox->getAvailableBalance(), 2) }} available to withdraw</span>
                </div>
                <a href="{{ route('money-boxes.withdraw.create', $moneyBox) }}" class="btn btn-primary btn-sm">
                    Withdraw funds
                </a>
            </div>
        @elseif(!auth()->user()->isVerified())
            <div class="flex items-center gap-3 px-4 py-3 rounded-[8px] bg-amber-50 border border-amber-200 mb-5">
                <svg viewBox="0 0 24 24" class="w-4 h-4 text-amber-600 flex-none" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                <span class="text-[13px] text-amber-700 flex-1">Verify your ID to enable withdrawals.</span>
                <a href="{{ route('settings.verification') }}" class="btn btn-sm border-amber-300 text-amber-700 hover:bg-amber-100">Verify ID</a>
            </div>
        @endif

        {{-- Tabs --}}
        <div class="tabs">
            <button class="tab" :class="tab === 'overview' ? 'active' : ''" @click="tab = 'overview'">Overview</button>
            <button class="tab" :class="tab === 'contributions' ? 'active' : ''" @click="tab = 'contributions'">Contributions</button>
            <button class="tab" :class="tab === 'share' ? 'active' : ''" @click="tab = 'share'">Share & promote</button>
            <button class="tab" :class="tab === 'settings' ? 'active' : ''" @click="tab = 'settings'">Settings</button>
        </div>

        {{-- ── Overview tab ── --}}
        <div x-show="tab === 'overview'" x-cloak>
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-4">

                {{-- Recent contributions --}}
                <div class="card">
                    <div class="card-head">
                        <div class="card-title">Recent contributions</div>
                        <button class="tab btn-ghost btn-sm text-[#6B6862]" @click="tab = 'contributions'">
                            View all
                            <svg viewBox="0 0 24 24" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                        </button>
                    </div>
                    @if($moneyBox->contributions->count() > 0)
                        <table class="data-table">
                            <thead><tr>
                                <th>Contributor</th><th>Method</th><th>Status</th><th class="num">Amount</th><th>When</th>
                            </tr></thead>
                            <tbody>
                                @foreach($moneyBox->contributions->take(8) as $c)
                                    @php
                                        $name     = $c->getDisplayName();
                                        $initials = $name === 'Anonymous' ? '·' : collect(explode(' ', $name))->map(fn($p) => strtoupper($p[0] ?? ''))->take(2)->implode('');
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="flex items-center gap-2">
                                                <div class="w-[22px] h-[22px] rounded-full bg-primary-600 text-white grid place-items-center text-[9.5px] font-semibold flex-none">{{ $initials }}</div>
                                                <div>
                                                    <div class="font-medium text-[#15140F]">{{ $name }}</div>
                                                    @if($c->message)
                                                        <div class="tiny italic">"{{ Str::limit($c->message, 36) }}"</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="pill pill-muted"><span class="pill-dot"></span>{{ $c->payment_method ?? 'Card' }}</span></td>
                                        <td>
                                            <span class="pill {{ $c->payment_status->value === 'completed' ? 'pill-ok' : 'pill-warn' }}">
                                                <span class="pill-dot"></span>{{ ucfirst($c->payment_status->value) }}
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

                {{-- Sidebar details --}}
                <div class="flex flex-col gap-4">

                    {{-- Quick share --}}
                    <div class="card">
                        <div class="card-head"><div class="card-title">Quick share</div></div>
                        <div class="card-body flex flex-col gap-2">
                            <button
                                @click="navigator.clipboard.writeText('{{ route('box.show', $moneyBox->slug) }}').then(() => toast('Link copied!'))"
                                class="btn w-full justify-center"
                            >
                                <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="8" width="13" height="13" rx="2"/><path d="M16 8V4a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h4"/></svg>
                                Copy link
                            </button>
                            <a
                                href="https://wa.me/?text={{ urlencode($moneyBox->title . "\n\n" . route('box.show', $moneyBox->slug)) }}"
                                target="_blank"
                                class="btn w-full justify-center text-green-700 border-green-200 hover:bg-green-50"
                            >
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                WhatsApp
                            </a>
                        </div>
                    </div>

                    {{-- QR code --}}
                    <div class="card">
                        <div class="card-head"><div class="card-title">QR code</div></div>
                        <div class="card-body">
                            @if($moneyBox->hasQrCode())
                                <img src="{{ $moneyBox->getQrCodeUrl() }}" alt="QR Code" class="w-full aspect-square rounded-[6px] border border-[#E6E3DC] mb-3" />
                                <a href="{{ route('money-boxes.download-qr', $moneyBox) }}" class="btn w-full justify-center">
                                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v12"/><path d="m6 10 6 6 6-6"/><path d="M4 20h16"/></svg>
                                    Download QR
                                </a>
                            @else
                                <div class="text-center py-3">
                                    <div class="tiny mb-3">Generate a QR code to let contributors scan and give.</div>
                                    <form action="{{ route('money-boxes.generate-qr', $moneyBox) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary w-full justify-center">Generate QR code</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Details --}}
                    <div class="card">
                        <div class="card-head"><div class="card-title">Details</div></div>
                        <div class="card-body">
                            <dl class="space-y-3 text-[13px]">
                                <div class="flex justify-between"><dt class="text-[#6B6862]">Created</dt><dd class="font-medium">{{ $moneyBox->created_at->format('M j, Y') }}</dd></div>
                                @if($moneyBox->start_date)
                                    <div class="flex justify-between"><dt class="text-[#6B6862]">Start</dt><dd class="font-medium">{{ $moneyBox->start_date->format('M j, Y') }}</dd></div>
                                @endif
                                @if($moneyBox->end_date)
                                    <div class="flex justify-between"><dt class="text-[#6B6862]">End</dt><dd class="font-medium">{{ $moneyBox->end_date->format('M j, Y') }}</dd></div>
                                @endif
                                <div class="flex justify-between"><dt class="text-[#6B6862]">Amount type</dt><dd class="font-medium">{{ ucfirst($moneyBox->amount_type->value) }}</dd></div>
                                <div class="flex justify-between"><dt class="text-[#6B6862]">Currency</dt><dd class="font-medium">{{ $moneyBox->currency_code }}</dd></div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Contributions tab ── --}}
        <div x-show="tab === 'contributions'" x-cloak>
            <div class="card">
                @if($moneyBox->contributions->count() > 0)
                    <table class="data-table">
                        <thead><tr>
                            <th>Contributor</th><th>Message</th><th>Method</th><th>Status</th><th class="num">Amount</th><th>Date</th><th></th>
                        </tr></thead>
                        <tbody>
                            @foreach($moneyBox->contributions as $c)
                                @php
                                    $name     = $c->getDisplayName();
                                    $initials = $name === 'Anonymous' ? '·' : collect(explode(' ', $name))->map(fn($p) => strtoupper($p[0] ?? ''))->take(2)->implode('');
                                @endphp
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div class="w-[22px] h-[22px] rounded-full bg-primary-600 text-white grid place-items-center text-[9.5px] font-semibold flex-none">{{ $initials }}</div>
                                            <span class="font-medium text-[#15140F]">{{ $name }}</span>
                                        </div>
                                    </td>
                                    <td class="muted text-[12.5px] max-w-[200px]">{{ $c->message ? Str::limit($c->message, 48) : '—' }}</td>
                                    <td><span class="pill pill-muted"><span class="pill-dot"></span>{{ $c->payment_method ?? 'Card' }}</span></td>
                                    <td>
                                        <span class="pill {{ $c->payment_status->value === 'completed' ? 'pill-ok' : 'pill-warn' }}">
                                            <span class="pill-dot"></span>{{ ucfirst($c->payment_status->value) }}
                                        </span>
                                    </td>
                                    <td class="num font-semibold text-[#15140F] tnum">{{ $sym }}{{ number_format($c->amount, 2) }}</td>
                                    <td class="muted text-[12px]">
                                        <div>{{ $c->created_at->format('M j, Y') }}</div>
                                        <div class="tiny">{{ $c->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="card-body text-center py-12">
                        <div class="text-[#9C998F] mb-1">No contributions yet</div>
                        <div class="tiny">Share your box to start receiving contributions.</div>
                    </div>
                @endif
            </div>

            {{-- Withdrawals --}}
            @if($moneyBox->withdrawals->count() > 0)
                <div class="card mt-4">
                    <div class="card-head">
                        <div class="card-title">Withdrawal requests</div>
                        <span class="tiny">{{ $moneyBox->withdrawals->count() }} total</span>
                    </div>
                    <table class="data-table">
                        <thead><tr>
                            <th>Reference</th><th>Amount</th><th>Fee</th><th>Net</th><th>Account</th><th>Status</th><th>Date</th>
                        </tr></thead>
                        <tbody>
                            @foreach($moneyBox->withdrawals as $w)
                                @php
                                    $wStatus = match($w->status->value) {
                                        'pending'   => 'pill-warn',
                                        'in_review' => 'pill-info',
                                        'approved'  => 'pill-ok',
                                        'disbursed' => 'pill-ok',
                                        'rejected'  => 'pill-danger',
                                        default     => 'pill-muted',
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <div class="font-mono text-[12px] font-medium text-[#15140F]">{{ $w->reference }}</div>
                                        @if($w->user_note)<div class="tiny">{{ Str::limit($w->user_note, 30) }}</div>@endif
                                    </td>
                                    <td class="tnum text-[#15140F]">{{ $sym }}{{ number_format($w->amount, 2) }}</td>
                                    <td class="tnum muted">−{{ $sym }}{{ number_format($w->fee, 2) }}</td>
                                    <td class="tnum font-semibold text-primary-600">{{ $sym }}{{ number_format($w->net_amount, 2) }}</td>
                                    <td class="text-[13px]">{{ $w->withdrawalAccount?->getDisplayName() ?? '—' }}</td>
                                    <td>
                                        <span class="pill {{ $wStatus }}"><span class="pill-dot"></span>{{ $w->status->label() }}</span>
                                        @if($w->rejection_reason)<div class="tiny text-red-600 mt-0.5">{{ Str::limit($w->rejection_reason, 20) }}</div>@endif
                                    </td>
                                    <td class="muted text-[12px]">
                                        <div>{{ $w->created_at->format('M j, Y') }}</div>
                                        <div class="tiny">{{ $w->created_at->format('h:i A') }}</div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- ── Share tab ── --}}
        <div x-show="tab === 'share'" x-cloak>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                <div class="card">
                    <div class="card-head"><div class="card-title">Share your box</div></div>
                    <div class="card-body flex flex-col gap-4">
                        <div>
                            <div class="text-[12.5px] font-medium text-[#6B6862] mb-1.5">Public link</div>
                            <div class="flex items-center border border-[#E6E3DC] rounded-[6px] bg-white px-2.5 overflow-hidden">
                                <span class="text-[13px] text-[#9C998F] shrink-0">mypiggybox.com/</span>
                                <input type="text" value="{{ $moneyBox->slug }}" readonly
                                       class="flex-1 border-0 py-2 text-[13.5px] bg-transparent focus:ring-0 focus:outline-none px-0" />
                                <button
                                    @click="navigator.clipboard.writeText('{{ route('box.show', $moneyBox->slug) }}').then(() => toast('Link copied!'))"
                                    class="btn btn-ghost btn-sm text-[#6B6862] shrink-0"
                                >
                                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="8" width="13" height="13" rx="2"/><path d="M16 8V4a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h4"/></svg>
                                    Copy
                                </button>
                            </div>
                        </div>

                        <div class="divider"></div>

                        <div>
                            <div class="text-[12.5px] font-medium text-[#6B6862] mb-2">Send via</div>
                            <div class="flex flex-wrap gap-2">
                                <a href="https://wa.me/?text={{ urlencode($moneyBox->title . "\n\n" . route('box.show', $moneyBox->slug)) }}" target="_blank" class="btn">
                                    WhatsApp
                                </a>
                                <a href="https://twitter.com/intent/tweet?text={{ urlencode($moneyBox->title . ' ' . route('box.show', $moneyBox->slug)) }}" target="_blank" class="btn">
                                    Twitter / X
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('box.show', $moneyBox->slug)) }}" target="_blank" class="btn">
                                    Facebook
                                </a>
                                <a href="mailto:?subject={{ urlencode($moneyBox->title) }}&body={{ urlencode(route('box.show', $moneyBox->slug)) }}" class="btn">
                                    Email
                                </a>
                                <button
                                    @click="navigator.clipboard.writeText('{{ route('box.show', $moneyBox->slug) }}').then(() => toast('Link copied!'))"
                                    class="btn"
                                >
                                    Copy to clipboard
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head"><div class="card-title">QR code</div></div>
                    <div class="card-body flex flex-col items-center gap-4">
                        @if($moneyBox->hasQrCode())
                            <img src="{{ $moneyBox->getQrCodeUrl() }}" alt="QR Code"
                                 class="w-44 h-44 border border-[#E6E3DC] rounded-[6px]" />
                            <div class="tiny text-center">Scan to contribute. Print on invitations, posters, or screens.</div>
                            <div class="flex gap-2">
                                <a href="{{ route('money-boxes.download-qr', $moneyBox) }}" class="btn btn-sm">
                                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v12"/><path d="m6 10 6 6 6-6"/><path d="M4 20h16"/></svg>
                                    Download
                                </a>
                                <a href="https://wa.me/?text={{ urlencode($moneyBox->title . "\n\nScan my QR or visit: " . route('box.show', $moneyBox->slug)) }}" target="_blank" class="btn btn-sm">
                                    Share via WhatsApp
                                </a>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="tiny mb-4">Generate a QR code to let contributors scan and give.</div>
                                <form action="{{ route('money-boxes.generate-qr', $moneyBox) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">Generate QR code</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Settings tab ── --}}
        <div x-show="tab === 'settings'" x-cloak>
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-4">

                {{-- Left: editable settings --}}
                <div class="flex flex-col gap-4">
                    {{-- Visibility & access --}}
                    <div class="card">
                        <div class="card-head"><div class="card-title">Visibility & access</div></div>
                        <div class="card-body flex flex-col gap-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-[13px] font-medium text-[#15140F]">Public listing</div>
                                    <div class="tiny">Who can find and view this box</div>
                                </div>
                                <span class="pill {{ $moneyBox->visibility->value === 'public' ? 'pill-ok' : 'pill-muted' }}">
                                    <span class="pill-dot"></span>{{ ucfirst($moneyBox->visibility->value) }}
                                </span>
                            </div>
                            <div class="divider"></div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-[13px] font-medium text-[#15140F]">Accepting contributions</div>
                                    <div class="tiny">{{ $moneyBox->canAcceptContributions() ? 'Open for contributions' : 'Closed — not accepting' }}</div>
                                </div>
                                <span class="pill {{ $moneyBox->canAcceptContributions() ? 'pill-ok' : 'pill-danger' }}">
                                    <span class="pill-dot"></span>{{ $moneyBox->canAcceptContributions() ? 'Open' : 'Closed' }}
                                </span>
                            </div>
                            <div class="divider"></div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-[13px] font-medium text-[#15140F]">Featured on homepage</div>
                                    <div class="tiny">Shown in the hero campaign spotlight</div>
                                </div>
                                <span class="pill {{ $moneyBox->is_featured ? 'pill-ok' : 'pill-muted' }}">
                                    <span class="pill-dot"></span>{{ $moneyBox->is_featured ? 'Featured' : 'Not featured' }}
                                </span>
                            </div>
                            <div class="divider"></div>
                            <a href="{{ route('money-boxes.edit', $moneyBox) }}" wire:navigate
                               class="btn btn-sm self-start">
                                <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 4l6 6L8 22H2v-6L14 4Z"/></svg>
                                Edit visibility settings
                            </a>
                        </div>
                    </div>

                    {{-- Box details --}}
                    <div class="card">
                        <div class="card-head"><div class="card-title">Box details</div></div>
                        <div class="card-body">
                            <dl class="space-y-3 text-[13px]">
                                <div class="flex justify-between gap-4">
                                    <dt class="text-[#6B6862]">Title</dt>
                                    <dd class="font-medium text-right">{{ $moneyBox->title }}</dd>
                                </div>
                                <div class="flex justify-between gap-4">
                                    <dt class="text-[#6B6862]">Category</dt>
                                    <dd class="font-medium">{{ $moneyBox->category?->name ?? '—' }}</dd>
                                </div>
                                <div class="flex justify-between gap-4">
                                    <dt class="text-[#6B6862]">Amount type</dt>
                                    <dd class="font-medium">{{ ucfirst($moneyBox->amount_type->value) }}</dd>
                                </div>
                                @if($moneyBox->goal_amount)
                                    <div class="flex justify-between gap-4">
                                        <dt class="text-[#6B6862]">Goal</dt>
                                        <dd class="font-medium tnum">{{ $sym }}{{ number_format($moneyBox->goal_amount, 2) }}</dd>
                                    </div>
                                @endif
                                <div class="flex justify-between gap-4">
                                    <dt class="text-[#6B6862]">Currency</dt>
                                    <dd class="font-medium">{{ $moneyBox->currency_code }}</dd>
                                </div>
                                <div class="flex justify-between gap-4">
                                    <dt class="text-[#6B6862]">Created</dt>
                                    <dd class="font-medium">{{ $moneyBox->created_at->format('M j, Y') }}</dd>
                                </div>
                                @if($moneyBox->start_date)
                                    <div class="flex justify-between gap-4">
                                        <dt class="text-[#6B6862]">Start date</dt>
                                        <dd class="font-medium">{{ $moneyBox->start_date->format('M j, Y') }}</dd>
                                    </div>
                                @endif
                                @if($moneyBox->end_date)
                                    <div class="flex justify-between gap-4">
                                        <dt class="text-[#6B6862]">End date</dt>
                                        <dd class="font-medium">{{ $moneyBox->end_date->format('M j, Y') }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>

                {{-- Right: danger zone --}}
                <div class="flex flex-col gap-4">
                    <div class="card">
                        <div class="card-head"><div class="card-title">Actions</div></div>
                        <div class="card-body flex flex-col gap-2">
                            <a href="{{ route('money-boxes.edit', $moneyBox) }}" wire:navigate class="btn w-full justify-start gap-2.5">
                                <svg viewBox="0 0 24 24" class="w-3.5 h-3.5 text-[#6B6862]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 4l6 6L8 22H2v-6L14 4Z"/></svg>
                                Edit box details
                            </a>
                            <a href="{{ route('money-boxes.share', $moneyBox) }}" wire:navigate class="btn w-full justify-start gap-2.5">
                                <svg viewBox="0 0 24 24" class="w-3.5 h-3.5 text-[#6B6862]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="12" r="3"/><circle cx="18" cy="6" r="3"/><circle cx="18" cy="18" r="3"/><path d="M8.6 13.5 15.4 17"/><path d="M15.4 7 8.6 10.5"/></svg>
                                Share & promote
                            </a>
                            @if($moneyBox->getAvailableBalance() > 0)
                                <a href="{{ route('money-boxes.withdraw.create', $moneyBox) }}" wire:navigate class="btn w-full justify-start gap-2.5">
                                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5 text-[#6B6862]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18"/><circle cx="17" cy="14.5" r="1"/></svg>
                                    Withdraw {{ $sym }}{{ number_format($moneyBox->getAvailableBalance(), 2) }}
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="card border border-red-100">
                        <div class="card-head"><div class="card-title text-red-700">Danger zone</div></div>
                        <div class="card-body flex flex-col gap-2">
                            <p class="tiny mb-1">These actions affect your box's availability. Proceed with care.</p>
                            <a href="{{ route('money-boxes.edit', $moneyBox) }}" wire:navigate
                               class="btn w-full justify-start gap-2.5 text-red-600 border-red-200 hover:bg-red-50">
                                <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                                {{ $moneyBox->is_active ? 'Pause contributions' : 'Re-open contributions' }}
                            </a>
                            <a href="{{ route('money-boxes.edit', $moneyBox) }}" wire:navigate
                               class="btn w-full justify-start gap-2.5 text-red-600 border-red-200 hover:bg-red-50">
                                <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                                Archive this box
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>