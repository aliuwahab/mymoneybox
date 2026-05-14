<x-layouts.guest>
    @php
        $sym = $piggyBox->getCurrencySymbol();
        $totalReceived = (float) $piggyBox->total_received;
        $giftCount = (int) $piggyBox->donation_count;
        $avgGift = $giftCount > 0 ? $totalReceived / $giftCount : 0;
        $recentGifts = $piggyBox->donations()
            ->where('payment_status', 'completed')
            ->latest()
            ->limit(5)
            ->get();
        $qrUrl = $piggyBox->getQrCodeUrl();
        $initials = $user->initials();
        $walletTitle = "{$user->name} Piggy Wallet";
        $walletDescription = $piggyBox->description ?: "Send a gift directly to {$user->name}'s Piggy Wallet. Every gift lands securely and helps them keep building toward what matters.";
    @endphp

    <div class="wallet-public" x-data="{ amount: @js(old('amount', '100')), anonymous: @js((bool) old('is_anonymous')) }">
        <style>
            .wallet-public {
                --bg: #FAFAF7;
                --panel: #FFFFFF;
                --border: #E6E3DC;
                --border-2: #D9D6CE;
                --sidebar-2: #ECEAE3;
                --fg: #15140F;
                --fg-2: #6B6862;
                --fg-3: #9C998F;
                --accent: #1B6B4E;
                --accent-hover: #154F3A;
                --accent-soft: #E6F1EB;
                --info: #2F6FA8;
                --info-soft: #E6EFF7;
                --danger: #B5311E;
                --danger-soft: #FBEAE6;
                --radius: 10px;
                --radius-sm: 6px;
                --shadow-1: 0 1px 0 rgba(20,18,12,.04), 0 1px 2px rgba(20,18,12,.04);
                background: var(--bg);
                color: var(--fg);
                min-height: calc(100vh - 56px);
                padding: 36px 18px 72px;
                font-size: 14px;
                line-height: 1.5;
                overflow-x: hidden;
            }

            .wallet-public * { box-sizing: border-box; }
            .wallet-public button,
            .wallet-public input,
            .wallet-public textarea { font: inherit; color: inherit; }

            .wallet-public .wrap {
                max-width: 1120px;
                margin: 0 auto;
            }

            .wallet-public .page-head {
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                gap: 24px;
                margin-bottom: 24px;
            }

            .wallet-public .page-title {
                font-family: "Instrument Serif", Georgia, serif;
                font-size: 38px;
                line-height: 1.05;
                letter-spacing: 0;
                margin: 0;
                font-weight: 400;
            }

            .wallet-public .page-sub {
                color: var(--fg-2);
                font-size: 13.5px;
                margin-top: 6px;
                overflow-wrap: anywhere;
            }

            .wallet-public .mono {
                font-family: "JetBrains Mono", ui-monospace, SFMono-Regular, Menlo, monospace;
                font-size: 12px;
                font-variant-numeric: tabular-nums;
            }

            .wallet-public .pub-shell {
                background: #F7F5EF;
                border: 1px solid var(--border);
                border-radius: var(--radius);
                padding: 28px;
                display: grid;
                grid-template-columns: minmax(0, 1.2fr) minmax(300px, 1fr);
                gap: 24px;
                min-width: 0;
            }

            .wallet-public .card {
                background: var(--panel);
                border: 1px solid var(--border);
                border-radius: var(--radius);
                box-shadow: var(--shadow-1);
            }

            .wallet-public .card.flat { box-shadow: none; }
            .wallet-public .card-head {
                padding: 14px 18px;
                border-bottom: 1px solid var(--border);
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
            }

            .wallet-public .card-title {
                font-weight: 600;
                font-size: 14px;
            }

            .wallet-public .card-body { padding: 18px; }
            .wallet-public .row { display: flex; align-items: center; gap: 10px; }
            .wallet-public .col { display: flex; flex-direction: column; gap: 10px; }
            .wallet-public .between {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                min-width: 0;
            }

            .wallet-public .muted { color: var(--fg-2); }
            .wallet-public .tiny { font-size: 11.5px; color: var(--fg-3); }
            .wallet-public .tnum { font-variant-numeric: tabular-nums; }

            .wallet-public .pill {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                font-size: 11.5px;
                font-weight: 500;
                padding: 2px 8px;
                border-radius: 999px;
                background: var(--info-soft);
                color: var(--info);
            }

            .wallet-public .pill .dot {
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background: currentColor;
            }

            .wallet-public .field {
                display: grid;
                gap: 6px;
            }

            .wallet-public .label {
                font-size: 12.5px;
                color: var(--fg-2);
                font-weight: 500;
            }

            .wallet-public .hint {
                font-size: 11.5px;
                color: var(--fg-3);
                font-weight: 400;
            }

            .wallet-public .input,
            .wallet-public .textarea {
                background: var(--panel);
                border: 1px solid var(--border);
                border-radius: var(--radius-sm);
                padding: 8px 10px;
                font-size: 13.5px;
                color: var(--fg);
                width: 100%;
                transition: border-color .12s, box-shadow .12s;
            }

            .wallet-public .textarea {
                resize: vertical;
                min-height: 88px;
            }

            .wallet-public .input:focus,
            .wallet-public .textarea:focus,
            .wallet-public .input-prefix:focus-within {
                outline: 0;
                border-color: var(--accent);
                box-shadow: 0 0 0 3px var(--accent-soft);
            }

            .wallet-public .input-prefix {
                display: flex;
                align-items: center;
                background: var(--panel);
                border: 1px solid var(--border);
                border-radius: var(--radius-sm);
                padding-left: 10px;
                transition: border-color .12s, box-shadow .12s;
            }

            .wallet-public .input-prefix input {
                border: 0;
                padding: 8px 10px;
                outline: 0;
                width: 100%;
                background: transparent;
                font-size: 13.5px;
            }

            .wallet-public .prefix {
                color: var(--fg-3);
                font-size: 13px;
                flex: none;
            }

            .wallet-public .grid-2-equal {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 12px;
            }

            .wallet-public .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                min-height: 34px;
                padding: 7px 13px;
                border-radius: var(--radius-sm);
                font-size: 13px;
                font-weight: 500;
                border: 1px solid var(--border);
                background: var(--panel);
                color: var(--fg);
                box-shadow: var(--shadow-1);
                transition: background .12s, border-color .12s, transform .08s;
            }

            .wallet-public .btn:hover { background: #FBFAF6; border-color: var(--border-2); }
            .wallet-public .btn:active { transform: translateY(.5px); }
            .wallet-public .btn.primary {
                background: var(--accent);
                color: #fff;
                border-color: var(--accent);
            }
            .wallet-public .btn.primary:hover {
                background: var(--accent-hover);
                border-color: var(--accent-hover);
            }

            .wallet-public .amount-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 8px;
            }

            .wallet-public .progress {
                height: 6px;
                background: var(--sidebar-2);
                border-radius: 999px;
                overflow: hidden;
            }

            .wallet-public .progress > span {
                display: block;
                height: 100%;
                width: 100%;
                background: var(--accent);
                border-radius: 999px;
            }

            .wallet-public .wallet-hero {
                height: 200px;
                border-radius: 10px;
                background: linear-gradient(135deg, #1B6B4E 0%, #2E8E6C 100%);
                position: relative;
                overflow: hidden;
            }

            .wallet-public .wallet-hero .mark {
                position: absolute;
                inset: 0;
                display: grid;
                place-items: center;
                color: rgba(255,255,255,.95);
                font-family: "Instrument Serif", Georgia, serif;
                font-size: 56px;
            }

            .wallet-public .wallet-hero .caption {
                position: absolute;
                bottom: 12px;
                left: 14px;
                right: 14px;
                color: rgba(255,255,255,.85);
                font-size: 11px;
                letter-spacing: .05em;
                text-transform: uppercase;
            }

            .wallet-public .avatar {
                width: 22px;
                height: 22px;
                border-radius: 50%;
                background: var(--accent);
                color: #fff;
                display: grid;
                place-items: center;
                font-size: 9.5px;
                font-weight: 600;
                letter-spacing: .02em;
                flex: none;
            }

            .wallet-public .qr {
                width: 84px;
                height: 84px;
                border-radius: var(--radius-sm);
                background:
                    radial-gradient(circle at 12% 12%, var(--fg) 4px, transparent 5px) 0 0 / 14px 14px,
                    radial-gradient(circle at 12% 12%, var(--fg) 4px, transparent 5px) 7px 7px / 14px 14px,
                    var(--panel);
                border: 1px solid var(--border);
                position: relative;
                overflow: hidden;
                flex: none;
            }

            .wallet-public .qr img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            .wallet-public .qr::after,
            .wallet-public .qr::before {
                content: "";
                position: absolute;
                width: 20px;
                height: 20px;
                border: 3px solid var(--fg);
                border-radius: 4px;
                background: var(--panel);
                pointer-events: none;
            }

            .wallet-public .qr::before { top: 5px; left: 5px; }
            .wallet-public .qr::after { top: 5px; right: 5px; }
            .wallet-public .qr.has-image::before,
            .wallet-public .qr.has-image::after { display: none; }

            .wallet-public .toggle-row {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
                font-size: 13px;
                color: var(--fg);
                margin-top: 12px;
            }

            .wallet-public .toggle-track {
                width: 30px;
                height: 18px;
                border-radius: 999px;
                background: var(--border-2);
                position: relative;
                transition: background .15s;
            }

            .wallet-public .toggle-track::after {
                content: "";
                position: absolute;
                top: 2px;
                left: 2px;
                width: 14px;
                height: 14px;
                border-radius: 50%;
                background: #fff;
                box-shadow: 0 1px 2px rgba(0,0,0,.2);
                transition: left .15s;
            }

            .wallet-public .toggle-track.on { background: var(--accent); }
            .wallet-public .toggle-track.on::after { left: 14px; }

            .wallet-public .error-box {
                background: var(--danger-soft);
                border: 1px solid rgba(181,49,30,.22);
                color: var(--danger);
                border-radius: var(--radius-sm);
                padding: 10px 12px;
                font-size: 13px;
            }

            @media (max-width: 900px) {
                .wallet-public .pub-shell { grid-template-columns: 1fr; }
            }

            @media (max-width: 640px) {
                .wallet-public { padding: 24px 12px 56px; }
                .wallet-public .pub-shell { padding: 16px; }
                .wallet-public .page-head { flex-direction: column; align-items: flex-start; gap: 14px; }
                .wallet-public .page-title { font-size: 32px; }
                .wallet-public .page-head .btn { width: 100%; }
                .wallet-public .grid-2-equal { grid-template-columns: 1fr; }
                .wallet-public .amount-grid { grid-template-columns: 1fr 1fr; }
                .wallet-public .between { align-items: flex-start; flex-wrap: wrap; }
                .wallet-public .card-body.row { align-items: flex-start; }
                .wallet-public .wallet-hero { height: 180px; }
            }

            @media (max-width: 380px) {
                .wallet-public .amount-grid { grid-template-columns: 1fr; }
                .wallet-public .card-body.row { flex-direction: column; }
            }
        </style>

        <div class="wrap">
            <div class="page-head">
                <div>
                    <h1 class="page-title">Send a gift</h1>
                    <div class="page-sub">You are gifting <span class="mono">{{ route('piggy.show', $user->piggy_code) }}</span></div>
                </div>
                <a href="{{ route('piggy.lookup') }}" class="btn">Try different code</a>
            </div>

            <div class="pub-shell">
                <div>
                    <span class="pill"><span class="dot"></span>Piggy Wallet · Code {{ $user->piggy_code }}</span>
                    <h2 style="font-family: 'Instrument Serif', Georgia, serif; font-size: 36px; line-height: 1.1; margin: 14px 0 8px; letter-spacing: 0; font-weight: 400;">{{ $walletTitle }}</h2>
                    <div class="muted" style="font-size: 14px; line-height: 1.55; max-width: 540px; margin-bottom: 18px;">
                        {{ $walletDescription }}
                    </div>

                    <div class="card flat" style="margin-bottom: 16px;">
                        <div class="card-body">
                            <div class="between" style="margin-bottom: 6px;">
                                <div class="tnum" style="font-weight: 600;">
                                    {{ $piggyBox->formatAmount($totalReceived) }}
                                </div>
                                <div class="tiny">{{ $giftCount }} {{ Str::plural('gift', $giftCount) }}</div>
                            </div>
                            <div class="progress"><span></span></div>
                            <div class="row" style="margin-top: 12px; gap: 18px; flex-wrap: wrap;">
                                <div>
                                    <div class="tnum" style="font-weight: 600;">{{ $giftCount }}</div>
                                    <div class="tiny">gifts</div>
                                </div>
                                <div>
                                    <div class="tnum" style="font-weight: 600;">{{ $piggyBox->is_active ? 'Open' : 'Closed' }}</div>
                                    <div class="tiny">status</div>
                                </div>
                                <div>
                                    <div class="tnum" style="font-weight: 600;">{{ $sym }}{{ number_format($avgGift, 0) }}</div>
                                    <div class="tiny">avg. gift</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (session('error'))
                        <div class="error-box" style="margin-bottom: 16px;">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('piggy.donate', $user) }}">
                        @csrf

                        <div class="amount-grid">
                            @foreach([50, 100, 250, 500] as $preset)
                                <button type="button"
                                        class="btn"
                                        :class="{ 'primary': String(amount) === '{{ $preset }}' }"
                                        @click="amount = '{{ $preset }}'">
                                    {{ $sym }}{{ $preset }}
                                </button>
                            @endforeach
                        </div>

                        <div class="field" style="margin-top: 12px;">
                            <label class="label" for="amount">Or enter another amount <span class="hint">*</span></label>
                            <div class="input-prefix">
                                <span class="prefix">{{ $sym }}</span>
                                <input id="amount" name="amount" type="number" step="0.01" min="0.01" required x-model="amount" placeholder="0.00">
                            </div>
                            @error('amount')
                                <div class="tiny" style="color: var(--danger);">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="grid-2-equal" style="margin-top: 12px;">
                            <div class="field">
                                <label class="label" for="donor_name">Your name <span class="hint" x-show="!anonymous">*</span><span class="hint" x-show="anonymous">optional</span></label>
                                <input id="donor_name" name="donor_name" class="input" :required="!anonymous" :disabled="anonymous" placeholder="Jane Asiedu" value="{{ old('donor_name', auth()->user()->name ?? '') }}">
                                @error('donor_name')
                                    <div class="tiny" style="color: var(--danger);">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="field">
                                <label class="label" for="donor_email">Email <span class="hint" x-text="anonymous ? 'optional' : 'for receipt'"></span></label>
                                <input id="donor_email" name="donor_email" type="email" class="input" :disabled="anonymous" placeholder="jane@email.com" value="{{ old('donor_email') }}">
                                @error('donor_email')
                                    <div class="tiny" style="color: var(--danger);">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="field" style="margin-top: 12px;">
                            <label class="label" for="message">Leave a message <span class="hint">optional</span></label>
                            <textarea id="message" name="message" class="textarea" maxlength="500" placeholder="Wishing you all the best!">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="tiny" style="color: var(--danger);">{{ $message }}</div>
                            @enderror
                        </div>

                        <label class="toggle-row">
                            <input type="checkbox" name="is_anonymous" value="1" x-model="anonymous" class="sr-only">
                            <span class="toggle-track" :class="{ 'on': anonymous }"></span>
                            Send this gift anonymously
                        </label>

                        <button type="submit" class="btn primary" style="width: 100%; padding: 11px 16px; font-size: 14px; margin-top: 18px;">
                            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20s-7-4.5-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.5-7 10-7 10Z"/></svg>
                            Send <span x-text="'{{ $sym }}' + (amount || 0)"></span>
                        </button>

                        <div class="tiny" style="text-align: center; margin-top: 10px;">
                            Payments are processed securely by TrendiPay.
                        </div>
                    </form>
                </div>

                <div class="col" style="gap: 16px;">
                    <div class="wallet-hero">
                        <div class="mark">{{ $initials }}</div>
                        <div class="caption">Created for {{ $user->name }}</div>
                    </div>

                    <div class="card">
                        <div class="card-head">
                            <div class="card-title">Recent gifts</div>
                        </div>
                        <div class="card-body col" style="gap: 12px;">
                            @forelse($recentGifts as $gift)
                                <div class="row" style="align-items: flex-start;">
                                    <div class="avatar">
                                        {{ $gift->is_anonymous ? '·' : collect(explode(' ', $gift->donor_name))->map(fn ($part) => Str::substr($part, 0, 1))->join('') }}
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <div class="between" style="align-items: baseline;">
                                            <span style="font-size: 13px; font-weight: 500;">{{ $gift->is_anonymous ? 'Anonymous' : $gift->donor_name }}</span>
                                            <span class="tnum" style="font-weight: 600; font-size: 13px;">{{ $sym }}{{ number_format($gift->amount, 0) }}</span>
                                        </div>
                                        @if($gift->message)
                                            <div class="tiny" style="font-style: italic;">"{{ $gift->message }}"</div>
                                        @endif
                                        <div class="tiny">{{ $gift->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="tiny">No gifts yet. Be the first to send one.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="card flat">
                        <div class="card-body row" style="gap: 14px; align-items: center;">
                            <div class="qr {{ $qrUrl ? 'has-image' : '' }}">
                                @if($qrUrl)
                                    <img src="{{ $qrUrl }}" alt="QR code for {{ $user->name }}'s Piggy Wallet">
                                @endif
                            </div>
                            <div>
                                <div style="font-size: 13px; font-weight: 500;">Scan to gift</div>
                                <div class="tiny">Share this QR with anyone who wants to send a gift.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.guest>
