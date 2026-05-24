<x-layouts.guest>
    @php
        $sym         = $moneyBox->getCurrencySymbol();
        $pct         = $moneyBox->goal_amount > 0 ? min(100, (int) $moneyBox->getProgressPercentage()) : 0;
        $hasImg      = $moneyBox->hasMedia('main');
        $identity    = $moneyBox->contributor_identity->value ?? 'user_choice';
        $amtType     = $moneyBox->amount_type->value ?? 'variable';
        $presets     = match($amtType) {
            'fixed'   => [$moneyBox->fixed_amount],
            'minimum' => [$moneyBox->minimum_amount, $moneyBox->minimum_amount * 2, $moneyBox->minimum_amount * 5, $moneyBox->minimum_amount * 10],
            'range'   => [$moneyBox->minimum_amount, round(($moneyBox->minimum_amount + $moneyBox->maximum_amount) / 2), $moneyBox->maximum_amount],
            default   => [50, 100, 250, 500],
        };
        $fixedOnly       = $amtType === 'fixed';
        $minAmt          = $moneyBox->minimum_amount ?? 0.01;
        $maxAmt          = $moneyBox->maximum_amount ?? null;
        $avgGift         = $moneyBox->contribution_count > 0
                               ? $moneyBox->total_contributions / $moneyBox->contribution_count : 0;
        $daysLeft        = $moneyBox->end_date ? max(0, (int) now()->diffInDays($moneyBox->end_date, false)) : null;
        $creatorName     = $moneyBox->user->name;
        $defaultAmt      = $fixedOnly ? number_format($moneyBox->fixed_amount, 2, '.', '') : '';

        // Cover initials from title (first letter of each word, max 3)
        $coverInitials = collect(preg_split('/\s+/', $moneyBox->title))
            ->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))
            ->filter()
            ->take(3)
            ->join('');
    @endphp

    {{-- Mobile sticky CTA --}}
    @if($moneyBox->canAcceptContributions())
    <div class="lg:hidden fixed bottom-0 inset-x-0 z-40 p-4 bg-white/95 backdrop-blur border-t border-[#E6E3DC]"
         x-data="{ show: true }" x-show="show" x-cloak>
        <a href="#contribute-form" @click="show = false"
           class="btn btn-primary w-full justify-center py-3 text-[14px] rounded-[8px]"
           style="display:flex;align-items:center;gap:6px;text-decoration:none;">
            <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
            Contribute now
        </a>
    </div>
    @endif

    <div class="min-h-screen bg-[#FAFAF7] py-8 px-4 sm:px-6 lg:px-8 pb-24 lg:pb-10"
         x-data="{
             showToast: false,
             toastMsg: '',
             toast(m){ this.toastMsg=m; this.showToast=true; setTimeout(()=>this.showToast=false,3200); },
             amount: '{{ old('amount', $defaultAmt) }}',
             anon: {{ old('is_anonymous') || $identity === 'anonymous_allowed' ? 'true' : 'false' }},
             setAmount(v){ this.amount = v; }
         }"
         x-init="
             @if(session('success')) toast('{{ addslashes(session('success')) }}'); @endif
             @if(session('error'))   toast('{{ addslashes(session('error')) }}');   @endif
         ">

        {{-- Toast --}}
        <div x-show="showToast" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-end="opacity-0"
             class="fixed top-4 right-4 z-50 flex items-center gap-2.5 px-4 py-3 bg-[#15140F] text-white text-[13px] rounded-[8px] shadow-xl max-w-xs">
            <svg viewBox="0 0 24 24" class="w-4 h-4 text-primary-400 flex-none" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
            <span x-text="toastMsg"></span>
        </div>

        {{-- pub-shell: the campaign card ──────────────────────────────────────── --}}
        <div class="pub-shell max-w-[980px] mx-auto" style="display:grid;grid-template-columns:1.2fr 1fr;gap:24px;align-items:start;">

            {{-- ── LEFT: pill + title + description + progress + form ── --}}
            <div id="contribute-form" class="pub-left">

                {{-- Category / visibility pill --}}
                <span class="pill pill-info" style="font-size:11.5px;">
                    <svg viewBox="0 0 24 24" style="width:11px;height:11px;" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a13 13 0 0 1 0 20"/><path d="M12 2a13 13 0 0 0 0 20"/></svg>
                    Public PiggyBox
                    @if($moneyBox->category)
                        · {{ $moneyBox->category->name }}
                    @endif
                </span>

                {{-- Instrument Serif title --}}
                <div>
                    <h1 class="font-serif" style="font-size:clamp(26px,3.6vw,36px);font-weight:400;line-height:1.1;letter-spacing:-0.01em;color:#15140F;margin:14px 0 8px;">
                        {{ $moneyBox->title }}
                    </h1>
                    @if($moneyBox->description)
                        <p style="font-size:14px;color:#6B6862;line-height:1.55;margin:0;max-width:520px;">{{ $moneyBox->description }}</p>
                    @endif
                </div>

                {{-- Progress card (flat, white panel) --}}
                <div class="card" style="box-shadow:none;">
                    <div class="card-body">
                        @if($moneyBox->goal_amount)
                            {{-- Raised inline with goal --}}
                            <div style="display:flex;align-items:baseline;justify-content:space-between;margin-bottom:6px;">
                                <div class="tnum" style="font-weight:600;color:#15140F;font-size:15px;letter-spacing:-0.005em;">
                                    {{ $moneyBox->formatAmount($moneyBox->total_contributions) }}
                                    <span class="muted" style="font-weight:400;">of {{ $moneyBox->formatAmount($moneyBox->goal_amount) }}</span>
                                </div>
                                <div class="tiny tnum">{{ number_format($pct) }}%</div>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill" style="width:{{ $pct }}%;"></div>
                            </div>
                        @else
                            <div style="font-size:22px;font-weight:600;color:#15140F;font-variant-numeric:tabular-nums;letter-spacing:-0.02em;">
                                {{ $moneyBox->formatAmount($moneyBox->total_contributions) }}
                            </div>
                        @endif

                        {{-- Stats row --}}
                            <div class="pub-stats-row" style="display:flex;gap:18px;margin-top:12px;">
                            <div>
                                <div class="tnum" style="font-weight:600;color:#15140F;font-size:14px;">{{ number_format($moneyBox->contribution_count) }}</div>
                                <div class="tiny">{{ Str::plural('contributor', $moneyBox->contribution_count) }}</div>
                            </div>
                            @if($daysLeft !== null)
                                <div>
                                    <div class="tnum" style="font-weight:600;color:#15140F;font-size:14px;">{{ $daysLeft }}d</div>
                                    <div class="tiny">remaining</div>
                                </div>
                            @endif
                            @if($avgGift > 0)
                                <div>
                                    <div class="tnum" style="font-weight:600;color:#15140F;font-size:14px;">{{ $moneyBox->formatAmount($avgGift) }}</div>
                                    <div class="tiny">avg. gift</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if($moneyBox->canAcceptContributions())
                    {{-- Error summary --}}
                    @if($errors->any())
                        <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:7px;padding:10px 14px;font-size:12.5px;color:#B91C1C;">
                            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('box.contribute', $moneyBox->slug) }}" class="pub-form">
                        @csrf

                        {{-- Amount constraint hint --}}
                        @if(in_array($amtType, ['minimum','maximum','range']))
                            <div style="display:flex;align-items:center;gap:6px;padding:8px 12px;background:#F3F1EB;border-radius:6px;font-size:12px;color:#6B6862;">
                                <svg viewBox="0 0 24 24" style="width:13px;height:13px;flex:none;color:#1B6B4E;" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                                @if($amtType === 'minimum') Min: <strong style="color:#15140F;">{{ $moneyBox->formatAmount($moneyBox->minimum_amount) }}</strong>
                                @elseif($amtType === 'maximum') Max: <strong style="color:#15140F;">{{ $moneyBox->formatAmount($moneyBox->maximum_amount) }}</strong>
                                @elseif($amtType === 'range') {{ $moneyBox->formatAmount($moneyBox->minimum_amount) }} – {{ $moneyBox->formatAmount($moneyBox->maximum_amount) }}
                                @endif
                            </div>
                        @endif

                        {{-- Amount presets --}}
                        @if(!$fixedOnly)
                            <div class="pub-presets">
                                @foreach($presets as $preset)
                                    @php $val = number_format($preset, 2, '.', ''); @endphp
                                    <button type="button"
                                            @click="setAmount('{{ $val }}')"
                                            :class="amount == '{{ $val }}' ? 'btn btn-primary' : 'btn'"
                                            style="flex:1;justify-content:center;">
                                        {{ $sym }}{{ number_format($preset, 0) }}
                                    </button>
                                @endforeach
                            </div>

                            {{-- "Or enter another amount" input --}}
                            <div class="field">
                                <div class="label">Or enter another amount</div>
                                <div class="input-prefix">
                                    <span class="prefix">{{ $sym }}</span>
                                    <input type="number" name="amount" id="amount"
                                           x-model="amount"
                                           step="0.01" min="{{ $minAmt }}"
                                           @if($maxAmt) max="{{ $maxAmt }}" @endif
                                           required placeholder="0.00">
                                </div>
                                @error('amount')<p style="font-size:12px;color:#DC2626;margin:0;">{{ $message }}</p>@enderror
                            </div>
                        @else
                            <input type="hidden" name="amount" value="{{ number_format($moneyBox->fixed_amount, 2, '.', '') }}">
                            <div style="display:flex;align-items:center;background:#F3F1EB;border:1px solid #E6E3DC;border-radius:7px;padding:10px 14px;">
                                <span style="font-size:18px;font-weight:600;color:#15140F;font-variant-numeric:tabular-nums;">{{ $moneyBox->formatAmount($moneyBox->fixed_amount) }}</span>
                                <span style="font-size:12px;color:#9C998F;margin-left:auto;">Fixed amount</span>
                            </div>
                        @endif

                        {{-- Name + Email side-by-side --}}
                        <div class="grid-2-equal">
                            @if($identity !== 'anonymous_allowed')
                                <div class="field">
                                    <label for="contributor_name" class="label">
                                        Your name @if($identity === 'must_identify')<span style="color:#EF4444;">*</span>@else<span class="hint" x-show="!anon">· required unless anonymous</span><span class="hint" x-show="anon">· optional</span>@endif
                                    </label>
                                    <input type="text" name="contributor_name" id="contributor_name"
                                           value="{{ old('contributor_name', auth()->user()->name ?? '') }}"
                                           @if($identity === 'must_identify') required @else :required="!anon" @endif
                                           :disabled="anon"
                                           placeholder="Jane Asiedu">
                                    @error('contributor_name')<p style="font-size:12px;color:#DC2626;margin:0;">{{ $message }}</p>@enderror
                                </div>
                            @endif

                            <div class="field">
                                <label for="contributor_email" class="label">
                                    Email <span class="hint" x-text="anon ? '· optional' : '· for receipt'"></span> <span x-show="!anon" style="color:#EF4444;">*</span>
                                </label>
                                <input type="email" name="contributor_email" id="contributor_email"
                                       :required="!anon"
                                       :disabled="anon"
                                       value="{{ old('contributor_email', auth()->user()->email ?? '') }}"
                                       placeholder="jane@email.com">
                                @error('contributor_email')<p style="font-size:12px;color:#DC2626;margin:0;">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        @if($identity === 'anonymous_allowed')
                            <input type="hidden" name="is_anonymous" value="1">
                        @endif

                        {{-- Message --}}
                        <div class="field">
                            <label for="message" class="label">
                                Leave a message <span class="hint">· optional</span>
                            </label>
                            <textarea name="message" id="message" rows="2"
                                      placeholder="Congratulations to the happy couple!"
                                      style="resize:vertical;min-height:72px;">{{ old('message') }}</textarea>
                            @error('message')<p style="font-size:12px;color:#DC2626;margin:0;">{{ $message }}</p>@enderror
                        </div>

                        {{-- Anonymous toggle --}}
                        @if($identity !== 'must_identify')
                            <label class="toggle" :class="anon ? 'on' : ''" style="cursor:pointer;">
                                <input type="checkbox" name="is_anonymous" value="1"
                                       x-model="anon"
                                       style="position:absolute;opacity:0;width:0;height:0;pointer-events:none;">
                                <span class="track" aria-hidden="true"><span class="thumb"></span></span>
                                Contribute anonymously
                            </label>
                        @endif

                        {{-- Contribute button --}}
                        <button type="submit"
                                class="btn btn-primary w-full justify-center rounded-[8px]"
                                style="display:flex;align-items:center;gap:8px;padding:11px 16px;font-size:14px;">
                            <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" fill="currentColor" stroke="none"/></svg>
                            <span>Contribute <span x-text="'{{ $sym }}' + parseFloat(amount||0).toLocaleString('en-GH',{minimumFractionDigits:0,maximumFractionDigits:2})"></span></span>
                        </button>

                        <div class="tiny" style="text-align:center;line-height:1.6;">
                            Payments via MTN MoMo, Vodafone Cash, AirtelTigo Money, Card. Secured by TrendiPay.
                        </div>
                    </form>

                @else
                    {{-- Closed state --}}
                    <div style="text-align:center;padding:32px 0;">
                        <div style="width:48px;height:48px;border-radius:10px;background:#F3F1EB;display:grid;place-items:center;margin:0 auto 12px;">
                            <svg viewBox="0 0 24 24" style="width:22px;height:22px;color:#9C998F;" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </div>
                        <h3 style="font-size:14px;font-weight:600;color:#15140F;margin:0 0 4px;">Not accepting contributions</h3>
                        <p style="font-size:13px;color:#6B6862;margin:0;">This campaign is currently closed.</p>
                    </div>
                @endif

            </div>

            {{-- ── RIGHT: cover visual + contributors + QR/share ── --}}
            <div class="pub-right" style="display:flex;flex-direction:column;gap:16px;min-width:0;">

                {{-- Campaign cover visual (200px tall per design) --}}
                @php $coverUrl = $moneyBox->getMainImageUrl(); @endphp
                <div class="pub-cover {{ ($hasImg && $coverUrl) ? '' : 'cover-emerald' }}"
                     @if($hasImg && $coverUrl)
                         style="background-image:url('{{ $coverUrl }}');background-size:cover;background-position:center 30%;"
                     @endif>
                    @if($hasImg && $coverUrl)
                        <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55) 0%,rgba(0,0,0,.05) 60%,transparent 100%);"></div>
                    @else
                        <div style="position:absolute;inset:0;display:grid;place-items:center;color:rgba(255,255,255,0.95);font-family:'Instrument Serif',Georgia,serif;font-size:56px;letter-spacing:.02em;">
                            {{ $coverInitials }}
                        </div>
                    @endif
                    {{-- Creator byline at bottom --}}
                    <div style="position:absolute;bottom:12px;left:14px;right:14px;color:rgba(255,255,255,0.85);font-size:11px;letter-spacing:.06em;text-transform:uppercase;display:flex;align-items:center;gap:6px;">
                        Created by <span style="color:#fff;text-transform:none;letter-spacing:0;font-size:12.5px;font-weight:500;">{{ $creatorName }}</span>
                        <x-verification-badge :user="$moneyBox->user" />
                    </div>
                </div>

                {{-- Recent contributors --}}
                @if($moneyBox->contributions->count() > 0)
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title">Recent contributors</div>
                        </div>
                        <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
                            @foreach($moneyBox->contributions->take(5) as $c)
                                @php
                                    $cName    = $c->getDisplayName();
                                    $isAnon   = $cName === 'Anonymous';
                                    $cInitials = $isAnon ? '·'
                                        : collect(explode(' ', $cName))->map(fn($p) => strtoupper($p[0] ?? ''))->take(2)->implode('');
                                    $avColors = ['#1B6B4E','#15140F','#B8810D','#3F2A6E','#883647'];
                                    $avColor  = $isAnon ? '#ECEAE3' : $avColors[$loop->index % count($avColors)];
                                    $avText   = $isAnon ? '#9C998F' : '#fff';
                                @endphp
                                <div style="display:flex;gap:12px;">
                                    <div style="width:22px;height:22px;border-radius:50%;background:{{ $avColor }};color:{{ $avText }};display:grid;place-items:center;font-size:9.5px;font-weight:600;flex:none;letter-spacing:.02em;">
                                        {{ $cInitials }}
                                    </div>
                                    <div style="flex:1;min-width:0;">
                                        <div style="display:flex;align-items:baseline;justify-content:space-between;gap:6px;">
                                            <span style="font-size:13px;font-weight:500;color:#15140F;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $cName }}</span>
                                            <span class="tnum" style="font-size:13px;font-weight:600;color:#15140F;flex:none;">{{ $sym }}{{ number_format($c->amount, 0) }}</span>
                                        </div>
                                        @if($c->message)
                                            <div class="tiny" style="font-style:italic;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">"{{ $c->message }}"</div>
                                        @endif
                                        <div class="tiny">{{ $c->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            @endforeach
                            @if($moneyBox->contributions->count() > 5)
                                <div class="tiny" style="text-align:center;">
                                    + {{ $moneyBox->contributions->count() - 5 }} more
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- QR / share card (flat, panel bg per design) --}}
                <div class="card" style="background:#FFFFFF;box-shadow:none;">
                    <div class="card-body qr-share-body" style="display:flex;gap:14px;align-items:center;">
                        @if($moneyBox->hasQrCode())
                            <img src="{{ $moneyBox->getQrCodeUrl() }}" alt="QR Code"
                                 style="width:84px;height:84px;border-radius:6px;border:1px solid #E6E3DC;flex:none;background:#fff;">
                        @else
                            <div class="qr-placeholder" style="width:84px;height:84px;border-radius:6px;border:1px solid #E6E3DC;flex:none;"></div>
                        @endif
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:13px;font-weight:500;color:#15140F;">Scan to contribute</div>
                            <div class="tiny" style="margin-top:2px;">Share this QR on invitations, posters, or screens.</div>
                            <div style="display:flex;gap:8px;margin-top:10px;flex-wrap:wrap;">
                                <button type="button"
                                        @click="navigator.clipboard.writeText('{{ $moneyBox->getPublicUrl() }}').then(() => toast('Link copied!'))"
                                        class="btn btn-sm">
                                    <svg viewBox="0 0 24 24" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                                    Copy link
                                </button>
                                <a href="https://wa.me/?text={{ urlencode('Support ' . $creatorName . '\'s campaign: ' . $moneyBox->title . ' — ' . $moneyBox->getPublicUrl()) }}"
                                   target="_blank" class="btn btn-sm">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.076 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374A9.86 9.86 0 012.1 11.974C2.1 6.524 6.536 2.09 11.988 2.09c2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884"/></svg>
                                    WhatsApp
                                </a>
                                @if($moneyBox->hasQrCode())
                                    <a href="{{ route('money-boxes.download-qr', $moneyBox) }}" class="btn btn-sm">
                                        <svg viewBox="0 0 24 24" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v12"/><path d="m6 10 6 6 6-6"/><path d="M4 20h16"/></svg>
                                        QR
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Mobile: gallery stacks below pub-shell --}}
        @if($moneyBox->hasMedia('gallery'))
        <div class="max-w-[980px] mx-auto mt-4" style="background:#F7F5EF;border:1px solid #E6E3DC;border-radius:12px;padding:20px;">
            <div style="font-size:13px;font-weight:600;color:#15140F;margin-bottom:12px;">Gallery</div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px;">
                @foreach($moneyBox->getMedia('gallery') as $img)
                    @php
                        try { $url = $img->getTemporaryUrl(now()->addHour()); }
                        catch (\Exception $e) { $url = $img->getUrl(); }
                    @endphp
                    <a href="{{ $url }}" target="_blank" style="display:block;border-radius:7px;overflow:hidden;aspect-ratio:1;">
                        <img src="{{ $url }}" alt="Gallery" style="width:100%;height:100%;object-fit:cover;transition:transform .2s;">
                    </a>
                @endforeach
            </div>
        </div>
        @endif

    </div>

    {{-- Public page design tokens (matches Figma design file) --}}
    <style>
        .pub-shell {
            background: #F7F5EF;
            border: 1px solid #E6E3DC;
            border-radius: 10px;
            padding: 28px;
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 24px;
            align-items: start;
        }
        .pub-left  { display: flex; flex-direction: column; gap: 16px; }
        .pub-right { display: flex; flex-direction: column; gap: 16px; align-self: start; }

        .pub-form { display: flex; flex-direction: column; gap: 12px; }

        /* Cover image (200px per design) */
        .pub-cover {
            position: relative;
            height: 200px;
            min-height: 200px;
            border-radius: 10px;
            overflow: hidden;
            flex-shrink: 0;
        }

        /* Field + label */
        .pub-left .field { display: grid; gap: 6px; }
        .pub-left .label { font-size: 12.5px; color: #6B6862; font-weight: 500; margin: 0; }
        .pub-left .hint  { font-size: 11.5px; color: #9C998F; font-weight: 400; }

        /* Input with currency prefix */
        .input-prefix {
            display: flex; align-items: center; gap: 0;
            background: #fff;
            border: 1px solid #E6E3DC;
            border-radius: 6px;
            padding-left: 10px;
            transition: border-color .12s, box-shadow .12s;
        }
        .input-prefix:focus-within {
            border-color: #1B6B4E;
            box-shadow: 0 0 0 3px #E6F1EB;
        }
        .input-prefix .prefix { color: #9C998F; font-size: 13px; flex: none; user-select: none; }
        .input-prefix input {
            border: 0 !important;
            padding: 8px 10px !important;
            outline: 0;
            width: 100%;
            background: transparent !important;
            font-size: 13.5px;
            color: #15140F;
            box-shadow: none !important;
        }
        .input-prefix input:focus { outline: 0; box-shadow: none !important; border: 0 !important; }

        /* Preset buttons row */
        .pub-presets { display: flex; gap: 8px; }
        .pub-presets .btn { flex: 1; justify-content: center; }

        /* 2-col grid for name+email */
        .grid-2-equal { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

        /* iOS-style toggle (matches design) */
        .toggle {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 13px;
            color: #15140F;
            background: transparent;
            border: 0;
            padding: 4px 0;
            user-select: none;
        }
        .toggle .track {
            width: 30px; height: 18px;
            border-radius: 999px;
            background: #D9D6CE;
            position: relative;
            transition: background .15s;
            display: inline-block;
        }
        .toggle .thumb {
            position: absolute;
            top: 2px; left: 2px;
            width: 14px; height: 14px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 1px 2px rgba(0,0,0,.2);
            transition: left .15s;
        }
        .toggle.on .track { background: #1B6B4E; }
        .toggle.on .thumb { left: 14px; }

        /* QR placeholder pattern */
        .qr-placeholder {
            background:
              radial-gradient(circle at 12% 12%, #15140F 3px, transparent 4px) 0 0 / 11px 11px,
              radial-gradient(circle at 12% 12%, #15140F 3px, transparent 4px) 5.5px 5.5px / 11px 11px,
              #fff;
        }

        /* Mobile: single column */
        @media (max-width: 768px) {
            .pub-shell {
                grid-template-columns: 1fr !important;
                padding: 20px;
            }
            .pub-left,
            .pub-right { min-width: 0; }
            .grid-2-equal { grid-template-columns: 1fr; }
            .pub-stats-row { flex-wrap: wrap; }
        }

        /* Small mobile: edge-to-edge */
        @media (max-width: 600px) {
            .pub-left h1 { overflow-wrap: anywhere; }
            .pub-shell {
                grid-template-columns: 1fr !important;
                border-radius: 0;
                border-left: 0;
                border-right: 0;
                margin-left: -1rem;
                margin-right: -1rem;
                padding: 16px;
            }
            .pub-presets {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .pub-presets .btn {
                width: 100%;
            }
            .pub-cover {
                height: 180px;
            }
            .qr-share-body {
                flex-direction: column;
                align-items: flex-start !important;
            }
            .qr-share-body img,
            .qr-share-body .qr-placeholder {
                width: 72px !important;
                height: 72px !important;
            }
        }
    </style>
</x-layouts.guest>
