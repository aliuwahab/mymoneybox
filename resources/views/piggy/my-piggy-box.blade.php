<x-layouts.app :title="'My Piggy Wallet'">
    @php
        $sym      = auth()->user()->country?->currency_symbol ?? '₵';
        $balance  = $piggyBox->getAvailableBalance();
        $canWd    = auth()->user()->isVerified()
                    && $balance >= config('withdrawal.min_amount', 10)
                    && auth()->user()->withdrawalAccounts()->active()->count() > 0;
    @endphp

    <div class="page-wrap max-w-[1080px]"
         x-data="{
             copied: false,
             codeCopied: false,
             shareUrl: '{{ $shareUrl }}',
             piggyCode: '{{ auth()->user()->piggy_code }}',
             userName: '{{ addslashes(auth()->user()->name) }}',
             copyLink() {
                 navigator.clipboard.writeText(this.shareUrl)
                     .then(() => { this.copied = true; setTimeout(() => this.copied = false, 2000); });
             },
             copyCode() {
                 const msg = `🎁 ${this.userName} is collecting gifts!\n\nPiggy Code: ${this.piggyCode}\nOr visit: ${this.shareUrl}`;
                 navigator.clipboard.writeText(msg)
                     .then(() => { this.codeCopied = true; setTimeout(() => this.codeCopied = false, 2000); });
             },
             whatsapp() {
                 const msg = `🎁 ${this.userName} is collecting gifts!\n\nMy Piggy Wallet Code: ${this.piggyCode}\n\nOr use this link: ${this.shareUrl}`;
                 window.open('https://wa.me/?text=' + encodeURIComponent(msg), '_blank');
             }
         }">

        {{-- Page header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h1 class="page-title" style="font-size:1.875rem;">Piggy Wallet</h1>
                <p class="text-[13px] text-[#6B6862] mt-1">Your personal gift wallet</p>
            </div>
            <div class="flex items-center gap-2 flex-none flex-wrap">
                @if($canWd)
                    <a href="{{ route('piggy.withdraw.create') }}" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5"/><path d="m5 12 7-7 7 7"/></svg>
                        Withdraw
                    </a>
                @elseif(!auth()->user()->isVerified())
                    <a href="{{ route('settings.verification') }}" class="btn">
                        <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Verify ID to Withdraw
                    </a>
                @elseif(auth()->user()->withdrawalAccounts()->active()->count() === 0)
                    <a href="{{ route('settings.withdrawal-accounts') }}" class="btn">
                        <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        Add Withdrawal Account
                    </a>
                @endif
                <a href="{{ $shareUrl }}" target="_blank" class="btn">
                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    View donation page
                </a>
            </div>
        </div>

        {{-- Stat row --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
            <div class="stat-card">
                <div class="stat-label">Total received</div>
                <div class="stat-value">{{ $piggyBox->formatAmount($piggyBox->total_received) }}</div>
                <div class="stat-delta">All time</div>
            </div>

            <div class="stat-card" style="background:var(--color-primary-50);border-color:var(--color-primary-200);">
                <div class="stat-label" style="color:var(--color-primary-700);">Available balance</div>
                <div class="stat-value" style="color:var(--color-primary-600);">{{ $piggyBox->formatAmount($balance) }}</div>
                <div class="stat-delta" style="color:var(--color-primary-500);">Ready to withdraw</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Total gifts</div>
                <div class="stat-value" style="font-variant-numeric:tabular-nums;">{{ number_format($piggyBox->donation_count) }}</div>
                <div class="stat-delta">{{ Str::plural('gift', $piggyBox->donation_count) }} received</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Piggy code</div>
                <div class="text-[18px] font-bold font-mono tracking-wider text-[#15140F] mt-1.5">{{ auth()->user()->piggy_code }}</div>
                <div class="stat-delta mt-1.5">
                    <span class="pill {{ $piggyBox->canReceiveDonations() ? 'pill-ok' : 'pill-muted' }}">
                        <span class="pill-dot"></span>
                        {{ $piggyBox->canReceiveDonations() ? 'Accepting gifts' : 'Paused' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-4 mb-4">

            {{-- Left: Share + Recent gifts --}}
            <div class="space-y-4">

                {{-- Share card --}}
                <div class="card">
                    <div class="card-head">
                        <h2 class="card-title">Share your Piggy Wallet</h2>
                    </div>
                    <div class="card-body space-y-3">

                        {{-- Direct link --}}
                        <div>
                            <div class="text-[11.5px] font-medium text-[#9C998F] uppercase tracking-[0.06em] mb-1.5">Direct link</div>
                            <div class="flex items-center gap-2">
                                <code class="flex-1 text-[12px] bg-[#F3F1EB] border border-[#E6E3DC] rounded-[6px] px-2.5 py-2 text-[#15140F] truncate font-mono">{{ $shareUrl }}</code>
                                <button @click="copyLink()" class="btn btn-sm flex-none">
                                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                                    <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                                </button>
                            </div>
                        </div>

                        {{-- Piggy code --}}
                        <div>
                            <div class="text-[11.5px] font-medium text-[#9C998F] uppercase tracking-[0.06em] mb-1.5">Piggy code — share this with anyone</div>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 flex items-center gap-3 bg-[#F3F1EB] border border-[#E6E3DC] rounded-[6px] px-3 py-2">
                                    <span class="font-mono text-[15px] font-bold tracking-widest text-[#15140F]">{{ auth()->user()->piggy_code }}</span>
                                </div>
                                <button @click="copyCode()" class="btn btn-sm flex-none">
                                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                                    <span x-text="codeCopied ? 'Copied!' : 'Copy message'"></span>
                                </button>
                            </div>
                            <p class="text-[11.5px] text-[#9C998F] mt-1.5">Tell people to visit the app and click "Piggy Someone", then enter this code</p>
                        </div>

                        {{-- Social share row --}}
                        <div class="flex flex-wrap gap-2 pt-1">
                            <button @click="whatsapp()" class="btn btn-sm" style="background:#22C55E;color:#fff;border-color:#16A34A;">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.076 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374A9.86 9.86 0 012.1 11.974C2.1 6.524 6.536 2.09 11.988 2.09c2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884"/></svg>
                                WhatsApp
                            </button>
                            <a href="https://wa.me/?text={{ urlencode('🎁 Gift me! Scan my QR code or visit: ' . $shareUrl) }}" target="_blank" class="btn btn-sm">
                                <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12v8a2 2 0 002 2h12a2 2 0 002-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"/></svg>
                                Share QR via WhatsApp
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Recent gifts --}}
                <div class="card">
                    <div class="card-head">
                        <h2 class="card-title">Recent gifts</h2>
                        @if($recentDonations->count() > 0)
                            <span class="pill">{{ $recentDonations->count() }} shown</span>
                        @endif
                    </div>
                    @if($recentDonations->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>From</th>
                                        <th class="num">Amount</th>
                                        <th>Status</th>
                                        <th>When</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentDonations as $donation)
                                        <tr>
                                            <td>
                                                <div class="font-medium text-[#15140F]">{{ $donation->getDisplayName() }}</div>
                                                @if($donation->message)
                                                    <div class="text-[11.5px] text-[#9C998F] italic mt-0.5">"{{ Str::limit($donation->message, 48) }}"</div>
                                                @endif
                                            </td>
                                            <td class="num font-semibold text-[#15140F]">{{ $piggyBox->formatAmount($donation->amount) }}</td>
                                            <td>
                                                <span class="pill {{ $donation->payment_status->value === 'completed' ? 'pill-ok' : 'pill-warn' }}">
                                                    <span class="pill-dot"></span>
                                                    {{ ucfirst($donation->payment_status->value) }}
                                                </span>
                                            </td>
                                            <td class="text-[#6B6862]">{{ $donation->created_at->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="card-body text-center py-10">
                            <div class="text-[40px] mb-3">🎁</div>
                            <div class="text-[14px] font-medium text-[#15140F] mb-1">No gifts yet</div>
                            <p class="tiny mb-4">Share your Piggy Wallet code or link to start receiving gifts from friends.</p>
                            <button @click="whatsapp()" class="btn btn-sm">Share via WhatsApp</button>
                        </div>
                    @endif
                </div>

                {{-- Withdrawals --}}
                @if($withdrawals->count() > 0)
                <div class="card">
                    <div class="card-head">
                        <h2 class="card-title">Withdrawals</h2>
                        <span class="pill">{{ $withdrawals->count() }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th class="num">Amount</th>
                                    <th class="num">Net</th>
                                    <th>Account</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($withdrawals as $w)
                                    <tr>
                                        <td>
                                            <code class="text-[11.5px] font-mono text-[#6B6862]">{{ $w->reference }}</code>
                                            @if($w->user_note)
                                                <div class="text-[11px] text-[#9C998F] mt-0.5">{{ Str::limit($w->user_note, 32) }}</div>
                                            @endif
                                        </td>
                                        <td class="num text-[#6B6862]">{{ $piggyBox->formatAmount($w->amount) }}</td>
                                        <td class="num font-semibold text-primary-600">{{ $piggyBox->formatAmount($w->net_amount) }}</td>
                                        <td class="text-[#6B6862]">{{ $w->withdrawalAccount?->getDisplayName() ?? '—' }}</td>
                                        <td>
                                            <span class="pill {{ match($w->status->value) {
                                                'disbursed' => 'pill-ok',
                                                'rejected','failed' => 'pill-danger',
                                                'approved' => 'pill-info',
                                                default => 'pill-warn',
                                            } }}">
                                                <span class="pill-dot"></span>
                                                {{ $w->status->label() }}
                                            </span>
                                        </td>
                                        <td class="text-[#6B6862]">{{ $w->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>

            {{-- Right: QR code --}}
            <div class="space-y-4">
                <div class="card">
                    <div class="card-head"><h2 class="card-title">QR code</h2></div>
                    <div class="card-body">
                        @if($piggyBox->hasQrCode())
                            <div class="flex flex-col items-center gap-3">
                                <img src="{{ $piggyBox->getQrCodeUrl() }}" alt="Piggy QR Code"
                                     class="w-48 h-48 rounded-[8px] border border-[#E6E3DC]">
                                <div class="w-full space-y-2">
                                    <a href="{{ route('piggy.download-qr') }}" class="btn btn-primary w-full justify-center">
                                        <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                        Download QR
                                    </a>
                                    <a href="https://wa.me/?text={{ urlencode('🎁 Gift me! Scan my QR or visit: ' . $shareUrl) }}"
                                       target="_blank" class="btn w-full justify-center">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.076 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374A9.86 9.86 0 012.1 11.974C2.1 6.524 6.536 2.09 11.988 2.09c2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884"/></svg>
                                        Share QR on WhatsApp
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-6">
                                <div class="w-14 h-14 rounded-xl bg-[#F3F1EB] grid place-items-center mx-auto mb-3">
                                    <svg viewBox="0 0 24 24" class="w-7 h-7 text-[#9C998F]" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z"/></svg>
                                </div>
                                <p class="text-[13px] text-[#6B6862] mb-4">Generate a QR code so people can contribute by scanning it.</p>
                                <form action="{{ route('piggy.generate-qr') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-full justify-center">
                                        <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                                        Generate QR code
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- How to share --}}
                <div class="card">
                    <div class="card-head"><h2 class="card-title">How to share</h2></div>
                    <div class="card-body space-y-3">
                        @foreach([
                            ['01', 'Share your code', 'Tell friends to visit the app → "Piggy Someone" → enter your code.'],
                            ['02', 'Send the link', 'Share your direct donation link via WhatsApp, email, or any social platform.'],
                            ['03', 'Print your QR', 'Download your QR code and put it on invitations, flyers, or your phone wallpaper.'],
                        ] as [$n, $title, $desc])
                            <div class="flex gap-3">
                                <span class="font-serif text-[22px] text-[#D9D6CE] leading-none flex-none w-7">{{ $n }}</span>
                                <div>
                                    <div class="text-[13px] font-semibold text-[#15140F]">{{ $title }}</div>
                                    <div class="text-[12px] text-[#6B6862] mt-0.5">{{ $desc }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>