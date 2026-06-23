<x-layouts.guest>
    @php
        $sym         = $moneyBox->getCurrencySymbol();
        $pct         = $moneyBox->goal_amount > 0 ? min(100, (int) $moneyBox->getProgressPercentage()) : 0;
        $hasImg      = $moneyBox->hasMedia('main');
        $coverUrl    = $moneyBox->getMainImageUrl();
        $identity    = $moneyBox->contributor_identity->value ?? 'user_choice';
        $amtType     = $moneyBox->amount_type->value ?? 'variable';
        $presets     = match($amtType) {
            'fixed'   => [$moneyBox->fixed_amount],
            'minimum' => [$moneyBox->minimum_amount, $moneyBox->minimum_amount * 2, $moneyBox->minimum_amount * 5, $moneyBox->minimum_amount * 10],
            'range'   => [$moneyBox->minimum_amount, round(($moneyBox->minimum_amount + $moneyBox->maximum_amount) / 2), $moneyBox->maximum_amount],
            default   => [50, 100, 250, 500],
        };
        $fixedOnly   = $amtType === 'fixed';
        $minAmt      = $moneyBox->minimum_amount ?? 0.01;
        $maxAmt      = $moneyBox->maximum_amount ?? null;
        $avgGift     = $moneyBox->contribution_count > 0
                           ? $moneyBox->total_contributions / $moneyBox->contribution_count : 0;
        $daysLeft    = $moneyBox->end_date ? max(0, (int) now()->diffInDays($moneyBox->end_date, false)) : null;
        $creatorName = $moneyBox->user->name;
        $defaultAmt  = $fixedOnly ? number_format($moneyBox->fixed_amount, 2, '.', '') : '';
        $coverInitials = collect(preg_split('/\s+/', $moneyBox->title))
            ->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))
            ->filter()->take(3)->join('');
    @endphp

    <div class="box-pub" x-data="{
        amount: @js(old('amount', $defaultAmt)),
        anon: {{ old('is_anonymous') || $identity === 'anonymous_allowed' ? 'true' : 'false' }},
        showToast: false,
        toastMsg: '',
        toast(m){ this.toastMsg = m; this.showToast = true; setTimeout(() => this.showToast = false, 3200); },
        setAmount(v){ this.amount = v; }
    }" x-init="
        @if(session('success')) toast('{{ addslashes(session('success')) }}'); @endif
        @if(session('error'))   toast('{{ addslashes(session('error')) }}');   @endif
    ">

        <style>
            .box-pub {
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

            .box-pub * { box-sizing: border-box; }
            .box-pub button, .box-pub input, .box-pub textarea { font: inherit; color: inherit; }

            .box-pub .wrap { max-width: 1120px; margin: 0 auto; }

            /* Two-column shell */
            .box-pub .pub-shell {
                background: #F7F5EF;
                border: 1px solid var(--border);
                border-radius: var(--radius);
                padding: 28px;
                display: grid;
                grid-template-columns: minmax(0, 1.2fr) minmax(300px, 1fr);
                gap: 24px;
                min-width: 0;
            }

            /* Card */
            .box-pub .card {
                background: var(--panel);
                border: 1px solid var(--border);
                border-radius: var(--radius);
                box-shadow: var(--shadow-1);
            }
            .box-pub .card.flat { box-shadow: none; }
            .box-pub .card-head {
                padding: 14px 18px;
                border-bottom: 1px solid var(--border);
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
            }
            .box-pub .card-title { font-weight: 600; font-size: 14px; }
            .box-pub .card-body  { padding: 18px; }

            /* Flex helpers */
            .box-pub .row     { display: flex; align-items: center; gap: 10px; }
            .box-pub .col     { display: flex; flex-direction: column; gap: 10px; }
            .box-pub .between { display: flex; align-items: center; justify-content: space-between; gap: 12px; min-width: 0; }

            /* Text helpers */
            .box-pub .muted { color: var(--fg-2); }
            .box-pub .tiny  { font-size: 11.5px; color: var(--fg-3); }
            .box-pub .tnum  { font-variant-numeric: tabular-nums; }

            /* Pill */
            .box-pub .pill {
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

            /* Form field */
            .box-pub .field { display: grid; gap: 6px; }
            .box-pub .label { font-size: 12.5px; color: var(--fg-2); font-weight: 500; }
            .box-pub .hint  { font-size: 11.5px; color: var(--fg-3); font-weight: 400; }

            /* Inputs */
            .box-pub .input,
            .box-pub .textarea {
                background: var(--panel);
                border: 1px solid var(--border);
                border-radius: var(--radius-sm);
                padding: 8px 10px;
                font-size: 13.5px;
                color: var(--fg);
                width: 100%;
                transition: border-color .12s, box-shadow .12s;
            }
            .box-pub .textarea { resize: vertical; min-height: 72px; }
            .box-pub .input:focus,
            .box-pub .textarea:focus,
            .box-pub .input-prefix:focus-within {
                outline: 0;
                border-color: var(--accent);
                box-shadow: 0 0 0 3px var(--accent-soft);
            }
            .box-pub .input-prefix {
                display: flex;
                align-items: center;
                background: var(--panel);
                border: 1px solid var(--border);
                border-radius: var(--radius-sm);
                padding-left: 10px;
                transition: border-color .12s, box-shadow .12s;
            }
            .box-pub .input-prefix input {
                border: 0;
                padding: 8px 10px;
                outline: 0;
                width: 100%;
                background: transparent;
                font-size: 13.5px;
            }
            .box-pub .prefix { color: var(--fg-3); font-size: 13px; flex: none; }

            /* Grid */
            .box-pub .grid-2-equal { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

            /* Buttons */
            .box-pub .btn {
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
                cursor: pointer;
                text-decoration: none;
            }
            .box-pub .btn:hover  { background: #FBFAF6; border-color: var(--border-2); }
            .box-pub .btn:active { transform: translateY(.5px); }
            .box-pub .btn.primary {
                background: var(--accent);
                color: #fff;
                border-color: var(--accent);
            }
            .box-pub .btn.primary:hover  { background: var(--accent-hover); border-color: var(--accent-hover); }
            .box-pub .btn.sm { font-size: 12px; padding: 5px 10px; min-height: 28px; }

            /* Amount preset grid */
            .box-pub .amount-grid { display: flex; gap: 8px; }
            .box-pub .amount-grid .btn { flex: 1; justify-content: center; }

            /* Progress bar */
            .box-pub .progress { height: 6px; background: var(--sidebar-2); border-radius: 999px; overflow: hidden; }
            .box-pub .progress > span { display: block; height: 100%; background: var(--accent); border-radius: 999px; }

            /* Cover image */
            .box-pub .cover {
                height: 200px;
                border-radius: var(--radius);
                background: linear-gradient(135deg, #1B6B4E 0%, #2E8E6C 100%);
                position: relative;
                overflow: hidden;
                flex-shrink: 0;
            }
            .box-pub .cover.has-image { background-size: cover; background-position: center 30%; }
            .box-pub .cover .mark {
                position: absolute;
                inset: 0;
                display: grid;
                place-items: center;
                color: rgba(255,255,255,.95);
                font-family: "Instrument Serif", Georgia, serif;
                font-size: 56px;
            }
            .box-pub .cover .caption {
                position: absolute;
                bottom: 12px;
                left: 14px;
                right: 14px;
                color: rgba(255,255,255,.85);
                font-size: 11px;
                letter-spacing: .05em;
                text-transform: uppercase;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            /* Avatar */
            .box-pub .avatar {
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

            /* QR */
            .box-pub .qr {
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
            .box-pub .qr img { width: 100%; height: 100%; object-fit: cover; display: block; }
            .box-pub .qr::before,
            .box-pub .qr::after {
                content: "";
                position: absolute;
                width: 20px;
                height: 20px;
                border: 3px solid var(--fg);
                border-radius: 4px;
                background: var(--panel);
                pointer-events: none;
            }
            .box-pub .qr::before { top: 5px; left: 5px; }
            .box-pub .qr::after  { top: 5px; right: 5px; }
            .box-pub .qr.has-image::before,
            .box-pub .qr.has-image::after { display: none; }

            /* Toggle */
            .box-pub .toggle-row {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
                font-size: 13px;
                color: var(--fg);
                margin-top: 4px;
            }
            .box-pub .toggle-track {
                width: 30px;
                height: 18px;
                border-radius: 999px;
                background: var(--border-2);
                position: relative;
                transition: background .15s;
            }
            .box-pub .toggle-track::after {
                content: "";
                position: absolute;
                top: 2px; left: 2px;
                width: 14px; height: 14px;
                border-radius: 50%;
                background: #fff;
                box-shadow: 0 1px 2px rgba(0,0,0,.2);
                transition: left .15s;
            }
            .box-pub .toggle-track.on { background: var(--accent); }
            .box-pub .toggle-track.on::after { left: 14px; }

            /* Error/info boxes */
            .box-pub .error-box {
                background: var(--danger-soft);
                border: 1px solid rgba(181,49,30,.22);
                color: var(--danger);
                border-radius: var(--radius-sm);
                padding: 10px 12px;
                font-size: 13px;
            }
            .box-pub .info-hint {
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 8px 12px;
                background: var(--sidebar-2);
                border-radius: var(--radius-sm);
                font-size: 12px;
                color: var(--fg-2);
            }

            /* Form column */
            .box-pub .form-col { display: flex; flex-direction: column; gap: 12px; }

            /* Mobile sticky CTA (hidden on desktop) */
            .box-pub .mobile-cta { display: none; }

            @media (max-width: 900px) {
                .box-pub .pub-shell { grid-template-columns: 1fr; }
            }

            @media (max-width: 640px) {
                .box-pub { padding: 24px 12px 88px; }
                .box-pub .pub-shell { padding: 16px; }
                .box-pub .grid-2-equal { grid-template-columns: 1fr; }
                .box-pub .amount-grid { flex-wrap: wrap; }
                .box-pub .amount-grid .btn { flex: 1 1 calc(50% - 4px); }
                .box-pub .cover { height: 180px; }
                .box-pub .mobile-cta {
                    display: block;
                    position: fixed;
                    bottom: 0; left: 0; right: 0;
                    z-index: 40;
                    padding: 16px;
                    background: rgba(255,255,255,0.96);
                    backdrop-filter: blur(8px);
                    -webkit-backdrop-filter: blur(8px);
                    border-top: 1px solid var(--border);
                }
            }

            @media (max-width: 380px) {
                .box-pub .amount-grid .btn { flex: 1 1 100%; }
            }
        </style>

        {{-- Toast --}}
        <div x-show="showToast" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-end="opacity-0"
             style="position:fixed;top:16px;right:16px;z-index:50;display:flex;align-items:center;gap:10px;padding:12px 16px;background:#15140F;color:#fff;font-size:13px;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.18);max-width:320px;">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
            <span x-text="toastMsg"></span>
        </div>

        {{-- Mobile sticky CTA --}}
        @if($moneyBox->canAcceptContributions())
        <div class="mobile-cta">
            <a href="#contribute-form"
               style="display:flex;align-items:center;justify-content:center;gap:6px;width:100%;padding:12px 16px;background:#1B6B4E;color:#fff;border:0;border-radius:8px;font-size:14px;font-weight:500;text-decoration:none;">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                Contribute now
            </a>
        </div>
        @endif

        <div class="wrap">
            <div class="pub-shell">

                {{-- ── LEFT: pill + title + description + progress + form ── --}}
                <div id="contribute-form">

                    <span class="pill" style="margin-bottom:14px;">
                        <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a13 13 0 0 1 0 20"/><path d="M12 2a13 13 0 0 0 0 20"/></svg>
                        Public PiggyBox@if($moneyBox->category) · {{ $moneyBox->category->name }}@endif
                    </span>

                    <h1 style="font-family:'Instrument Serif',Georgia,serif;font-size:clamp(26px,3.6vw,36px);font-weight:400;line-height:1.1;letter-spacing:-0.01em;color:#15140F;margin:0 0 8px;">
                        {{ $moneyBox->title }}
                    </h1>
                    @if($moneyBox->description)
                        <p class="muted" style="font-size:14px;line-height:1.55;margin:0 0 16px;max-width:520px;">{{ $moneyBox->description }}</p>
                    @endif

                    {{-- Progress card --}}
                    <div class="card flat" style="margin-bottom:16px;">
                        <div class="card-body">
                            @if($moneyBox->goal_amount)
                                <div class="between" style="margin-bottom:6px;">
                                    <div class="tnum" style="font-weight:600;">
                                        {{ $moneyBox->formatAmount($moneyBox->total_contributions) }}
                                        <span class="muted" style="font-weight:400;">of {{ $moneyBox->formatAmount($moneyBox->goal_amount) }}</span>
                                    </div>
                                    <div class="tiny tnum">{{ number_format($pct) }}%</div>
                                </div>
                                <div class="progress"><span style="width:{{ $pct }}%;"></span></div>
                            @else
                                <div class="tnum" style="font-size:22px;font-weight:600;letter-spacing:-0.02em;">
                                    {{ $moneyBox->formatAmount($moneyBox->total_contributions) }}
                                </div>
                            @endif

                            <div class="row" style="margin-top:12px;gap:18px;flex-wrap:wrap;">
                                <div>
                                    <div class="tnum" style="font-weight:600;">{{ number_format($moneyBox->contribution_count) }}</div>
                                    <div class="tiny">{{ Str::plural('contributor', $moneyBox->contribution_count) }}</div>
                                </div>
                                @if($daysLeft !== null)
                                    <div>
                                        <div class="tnum" style="font-weight:600;">{{ $daysLeft }}d</div>
                                        <div class="tiny">remaining</div>
                                    </div>
                                @endif
                                @if($avgGift > 0)
                                    <div>
                                        <div class="tnum" style="font-weight:600;">{{ $moneyBox->formatAmount($avgGift) }}</div>
                                        <div class="tiny">avg. gift</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($moneyBox->canAcceptContributions())

                        @if($errors->any())
                            <div class="error-box" style="margin-bottom:12px;">
                                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('box.contribute', $moneyBox->slug) }}" class="form-col">
                            @csrf

                            @if(in_array($amtType, ['minimum','maximum','range']))
                                <div class="info-hint">
                                    <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="#1B6B4E" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="flex:none;"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                                    @if($amtType === 'minimum') Min: <strong>{{ $moneyBox->formatAmount($moneyBox->minimum_amount) }}</strong>
                                    @elseif($amtType === 'maximum') Max: <strong>{{ $moneyBox->formatAmount($moneyBox->maximum_amount) }}</strong>
                                    @elseif($amtType === 'range') {{ $moneyBox->formatAmount($moneyBox->minimum_amount) }} – {{ $moneyBox->formatAmount($moneyBox->maximum_amount) }}
                                    @endif
                                </div>
                            @endif

                            @if(!$fixedOnly)
                                <div class="amount-grid">
                                    @foreach($presets as $preset)
                                        @php $val = number_format($preset, 2, '.', ''); @endphp
                                        <button type="button"
                                                @click="setAmount('{{ $val }}')"
                                                class="btn"
                                                :class="amount == '{{ $val }}' ? 'primary' : ''">
                                            {{ $sym }}{{ number_format($preset, 0) }}
                                        </button>
                                    @endforeach
                                </div>

                                <div class="field">
                                    <label class="label" for="amount">Or enter another amount</label>
                                    <div class="input-prefix">
                                        <span class="prefix">{{ $sym }}</span>
                                        <input id="amount" name="amount" type="number" step="0.01" min="{{ $minAmt }}"
                                               @if($maxAmt) max="{{ $maxAmt }}" @endif
                                               required x-model="amount" placeholder="0.00">
                                    </div>
                                    @error('amount')<div class="tiny" style="color:var(--danger);">{{ $message }}</div>@enderror
                                </div>
                            @else
                                <input type="hidden" name="amount" value="{{ number_format($moneyBox->fixed_amount, 2, '.', '') }}">
                                <div style="display:flex;align-items:center;background:var(--sidebar-2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px 14px;">
                                    <span class="tnum" style="font-size:18px;font-weight:600;">{{ $moneyBox->formatAmount($moneyBox->fixed_amount) }}</span>
                                    <span class="tiny" style="margin-left:auto;">Fixed amount</span>
                                </div>
                            @endif

                            <div class="grid-2-equal">
                                @if($identity !== 'anonymous_allowed')
                                    <div class="field">
                                        <label class="label" for="contributor_name">
                                            Your name
                                            @if($identity === 'must_identify')
                                                <span style="color:#EF4444;">*</span>
                                            @else
                                                <span class="hint" x-show="!anon">· required unless anonymous</span>
                                                <span class="hint" x-show="anon">· optional</span>
                                            @endif
                                        </label>
                                        <input id="contributor_name" name="contributor_name" class="input"
                                               @if($identity === 'must_identify') required @else :required="!anon" @endif
                                               :disabled="anon"
                                               placeholder="Jane Asiedu"
                                               value="{{ old('contributor_name', auth()->user()->name ?? '') }}">
                                        @error('contributor_name')<div class="tiny" style="color:var(--danger);">{{ $message }}</div>@enderror
                                    </div>
                                @endif

                                <div class="field">
                                    <label class="label" for="contributor_email">
                                        Email <span class="hint" x-text="anon ? '· optional' : '· for receipt'"></span>
                                        <span x-show="!anon" style="color:#EF4444;">*</span>
                                    </label>
                                    <input id="contributor_email" name="contributor_email" type="email" class="input"
                                           :required="!anon" :disabled="anon"
                                           value="{{ old('contributor_email', auth()->user()->email ?? '') }}"
                                           placeholder="jane@email.com">
                                    @error('contributor_email')<div class="tiny" style="color:var(--danger);">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            @if($identity === 'anonymous_allowed')
                                <input type="hidden" name="is_anonymous" value="1">
                            @endif

                            <div class="field">
                                <label class="label" for="message">Leave a message <span class="hint">· optional</span></label>
                                <textarea id="message" name="message" class="textarea" maxlength="500"
                                          placeholder="Congratulations to the happy couple!">{{ old('message') }}</textarea>
                                @error('message')<div class="tiny" style="color:var(--danger);">{{ $message }}</div>@enderror
                            </div>

                            @if($identity !== 'must_identify')
                                <label class="toggle-row">
                                    <input type="checkbox" name="is_anonymous" value="1" x-model="anon"
                                           style="position:absolute;opacity:0;width:0;height:0;pointer-events:none;">
                                    <span class="toggle-track" :class="{ 'on': anon }"></span>
                                    Contribute anonymously
                                </label>
                            @endif

                            <button type="submit" class="btn primary" style="width:100%;padding:11px 16px;font-size:14px;margin-top:4px;">
                                <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor" stroke="none"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                                <span>Contribute <span x-text="'{{ $sym }}' + parseFloat(amount||0).toLocaleString('en-GH',{minimumFractionDigits:0,maximumFractionDigits:2})"></span></span>
                            </button>

                            <div class="tiny" style="text-align:center;line-height:1.6;">
                                Payments via MTN MoMo, Vodafone Cash, AirtelTigo Money, Card. Secured by TrendiPay.
                            </div>
                        </form>

                    @else
                        {{-- Closed state --}}
                        <div style="text-align:center;padding:32px 0;">
                            <div style="width:48px;height:48px;border-radius:10px;background:var(--sidebar-2);display:grid;place-items:center;margin:0 auto 12px;">
                                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" style="color:var(--fg-3);"><rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </div>
                            <h3 style="font-size:14px;font-weight:600;color:var(--fg);margin:0 0 4px;">Not accepting contributions</h3>
                            <p class="muted" style="font-size:13px;margin:0;">This campaign is currently closed.</p>
                        </div>
                    @endif

                </div>

                {{-- ── RIGHT: cover image + contributors + QR/share ── --}}
                <div class="col" style="gap: 16px;">

                    {{-- Cover image --}}
                    <div class="cover {{ ($hasImg && $coverUrl) ? 'has-image' : '' }}"
                         @if($hasImg && $coverUrl) style="background-image:url('{{ $coverUrl }}');" @endif>
                        @if(!$hasImg || !$coverUrl)
                            <div class="mark">{{ $coverInitials }}</div>
                        @else
                            <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55) 0%,rgba(0,0,0,.05) 60%,transparent 100%);"></div>
                        @endif
                        <div class="caption">
                            Created by
                            <span style="color:#fff;text-transform:none;letter-spacing:0;font-size:12.5px;font-weight:500;">{{ $creatorName }}</span>
                            <x-verification-badge :user="$moneyBox->user" />
                        </div>
                    </div>

                    {{-- Recent contributors --}}
                    @if($moneyBox->contributions->count() > 0)
                        <div class="card">
                            <div class="card-head">
                                <div class="card-title">Recent contributors</div>
                            </div>
                            <div class="card-body col" style="gap:12px;">
                                @php $avColors = ['#1B6B4E','#15140F','#B8810D','#3F2A6E','#883647']; @endphp
                                @foreach($moneyBox->contributions->take(5) as $c)
                                    @php
                                        $cName    = $c->getDisplayName();
                                        $isAnon   = $cName === 'Anonymous';
                                        $cInitials = $isAnon ? '·'
                                            : collect(explode(' ', $cName))->map(fn($p) => strtoupper($p[0] ?? ''))->take(2)->implode('');
                                        $avColor  = $isAnon ? '#ECEAE3' : $avColors[$loop->index % count($avColors)];
                                        $avText   = $isAnon ? '#9C998F' : '#fff';
                                    @endphp
                                    <div class="row" style="align-items:flex-start;">
                                        <div class="avatar" style="background:{{ $avColor }};color:{{ $avText }};">{{ $cInitials }}</div>
                                        <div style="flex:1;min-width:0;">
                                            <div class="between" style="align-items:baseline;">
                                                <span style="font-size:13px;font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $cName }}</span>
                                                <span class="tnum" style="font-weight:600;font-size:13px;flex:none;">{{ $sym }}{{ number_format($c->amount, 0) }}</span>
                                            </div>
                                            @if($c->message)
                                                <div class="tiny" style="font-style:italic;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">"{{ $c->message }}"</div>
                                            @endif
                                            <div class="tiny">{{ $c->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                @endforeach
                                @if($moneyBox->contributions->count() > 5)
                                    <div class="tiny" style="text-align:center;">+ {{ $moneyBox->contributions->count() - 5 }} more</div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- QR / share card --}}
                    <div class="card flat">
                        <div class="card-body row" style="gap:14px;align-items:center;">
                            <div class="qr {{ $moneyBox->hasQrCode() ? 'has-image' : '' }}">
                                @if($moneyBox->hasQrCode())
                                    <img src="{{ $moneyBox->getQrCodeUrl() }}" alt="QR code for {{ $moneyBox->title }}">
                                @endif
                            </div>
                            <div>
                                <div style="font-size:13px;font-weight:500;">Scan to contribute</div>
                                <div class="tiny" style="margin-top:2px;">Share this QR on invitations, posters, or screens.</div>
                                <div class="row" style="margin-top:10px;flex-wrap:wrap;gap:8px;">
                                    <button type="button" class="btn sm"
                                            @click="navigator.clipboard.writeText('{{ $moneyBox->getPublicUrl() }}').then(() => toast('Link copied!'))">
                                        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                                        Copy link
                                    </button>
                                    <a href="https://wa.me/?text={{ urlencode('Support ' . $creatorName . '\'s campaign: ' . $moneyBox->title . ' — ' . $moneyBox->getPublicUrl()) }}"
                                       target="_blank" class="btn sm">
                                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.076 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374A9.86 9.86 0 012.1 11.974C2.1 6.524 6.536 2.09 11.988 2.09c2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884"/></svg>
                                        WhatsApp
                                    </a>
                                    @if($moneyBox->hasQrCode())
                                        <a href="{{ route('money-boxes.download-qr', $moneyBox) }}" class="btn sm">
                                            <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v12"/><path d="m6 10 6 6 6-6"/><path d="M4 20h16"/></svg>
                                            QR
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Gallery --}}
            @if($moneyBox->hasMedia('gallery'))
                <div style="background:#F7F5EF;border:1px solid #E6E3DC;border-radius:12px;padding:20px;margin-top:16px;">
                    <div style="font-size:13px;font-weight:600;color:#15140F;margin-bottom:12px;">Gallery</div>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px;">
                        @foreach($moneyBox->getMedia('gallery') as $img)
                            @php
                                try { $url = $img->getTemporaryUrl(now()->addHour()); }
                                catch (\Exception $e) { $url = $img->getUrl(); }
                            @endphp
                            <a href="{{ $url }}" target="_blank"
                               style="display:block;border-radius:7px;overflow:hidden;aspect-ratio:1;">
                                <img src="{{ $url }}" alt="Gallery" style="width:100%;height:100%;object-fit:cover;">
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-layouts.guest>