<x-layouts.guest>
    @php
        $covers  = ['cover-emerald','cover-amber','cover-slate','cover-rose','cover-violet'];
        $cover   = $covers[crc32($moneyBox->id) % count($covers)];
        $sym     = $moneyBox->getCurrencySymbol();
        $pct     = $moneyBox->goal_amount > 0 ? min(100, $moneyBox->getProgressPercentage()) : 0;
        $hasImg  = $moneyBox->hasMedia('main');
        $identity = $moneyBox->contributor_identity->value ?? 'user_choice';
        $amtType  = $moneyBox->amount_type->value ?? 'variable';
        $presets  = match($amtType) {
            'fixed'   => [$moneyBox->fixed_amount],
            'minimum' => [$moneyBox->minimum_amount, $moneyBox->minimum_amount * 2, $moneyBox->minimum_amount * 5, $moneyBox->minimum_amount * 10],
            'range'   => [$moneyBox->minimum_amount, round(($moneyBox->minimum_amount + $moneyBox->maximum_amount) / 2), $moneyBox->maximum_amount],
            default   => [50, 100, 250, 500],
        };
        $fixedOnly       = $amtType === 'fixed';
        $minAmt          = $moneyBox->minimum_amount ?? 0.01;
        $maxAmt          = $moneyBox->maximum_amount ?? null;
        $avgGift         = $moneyBox->contribution_count > 0
                               ? $moneyBox->total_contributions / $moneyBox->contribution_count
                               : 0;
        $daysLeft        = $moneyBox->end_date ? max(0, (int) now()->diffInDays($moneyBox->end_date, false)) : null;
        $creatorName     = $moneyBox->user->name;
        $creatorInitials = collect(explode(' ', $creatorName))->map(fn($p) => strtoupper($p[0] ?? ''))->take(2)->implode('');
        $defaultAmt      = $fixedOnly ? number_format($moneyBox->fixed_amount, 2, '.', '') : '';
    @endphp

    <style>
        .pub-hero { position:relative; overflow:hidden; }
        .pub-hero-overlay {
            position:absolute; bottom:0; left:0; right:0;
            padding: 6rem 2rem 2rem;
            background: linear-gradient(to top, rgba(0,0,0,.6) 0%, rgba(0,0,0,.12) 60%, transparent 100%);
        }
        .pub-stat-chip { display:flex; align-items:center; gap:6px; font-size:13px; color:var(--fg-2,#6B6862); }
        .pub-stat-chip strong { color:var(--fg,#15140F); font-variant-numeric:tabular-nums; }
        .pub-stat-sep { color:#D3D0C8; user-select:none; }
        .pub-progress-val { font-size:28px; font-weight:600; color:#15140F; font-variant-numeric:tabular-nums; letter-spacing:-.02em; line-height:1; }
        .pub-progress-of  { font-size:13px; color:#6B6862; }
        .pub-pct          { font-size:13px; font-weight:600; color:#1B6B4E; font-variant-numeric:tabular-nums; }
        .pub-preset { padding:6px 14px; border-radius:7px; border:1px solid #E6E3DC; font-size:13px; font-weight:500; background:#fff; color:#6B6862; cursor:pointer; transition:all .12s; }
        .pub-preset:hover { border-color:#C8C5BC; color:#15140F; }
        .pub-amt-wrap {
            display:flex; align-items:center;
            border:1px solid #E6E3DC; border-radius:8px; background:#fff;
            overflow:hidden; transition:box-shadow .15s, border-color .15s;
        }
        .pub-amt-wrap:focus-within { border-color:#9C998F; box-shadow:0 0 0 3px rgba(21,20,15,.06); }
        .pub-amt-sym { padding:0 10px 0 14px; font-size:16px; font-weight:500; color:#6B6862; user-select:none; flex:none; }
        .pub-amt-input { flex:1; border:0; outline:none; padding:11px 14px 11px 0; font-size:17px; font-weight:600; color:#15140F; background:transparent; box-shadow:none!important; }
        .pub-creator-strip { display:flex; align-items:center; gap:12px; padding:14px 20px; border-top:1px solid #E6E3DC; }
        .pub-creator-av { width:36px; height:36px; border-radius:50%; background:#15140F; color:#fff; display:grid; place-items:center; font-size:12.5px; font-weight:600; flex:none; letter-spacing:-.01em; }
        .pub-share-section { padding:16px 20px; border-top:1px solid #E6E3DC; }
        .contrib-row { display:flex; align-items:flex-start; gap:12px; padding:12px 20px; border-bottom:1px solid #F0EDE6; }
        .contrib-row:last-child { border-bottom:0; }
        .contrib-av { width:34px; height:34px; border-radius:50%; display:grid; place-items:center; font-size:11.5px; font-weight:600; flex:none; letter-spacing:.01em; }
        .contrib-name { font-size:13px; font-weight:500; color:#15140F; }
        .contrib-amt  { font-size:13px; font-weight:600; color:#15140F; font-variant-numeric:tabular-nums; margin-left:auto; flex:none; padding-left:12px; }
        .contrib-msg  { font-size:11.5px; color:#9C998F; font-style:italic; margin-top:2px; line-height:1.4; }
        .contrib-time { font-size:11px; color:#C0BDB5; margin-top:2px; }
    </style>

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

    <div class="min-h-screen bg-[#FAFAF7] pb-24 lg:pb-12"
         x-data="{
             showToast: false,
             toastMsg: '',
             toast(m){ this.toastMsg=m; this.showToast=true; setTimeout(()=>this.showToast=false,3200); },
             amount: '{{ old('amount', $defaultAmt) }}',
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

        {{-- Hero cover --}}
        <div class="pub-hero w-full {{ $hasImg ? '' : $cover }}" style="{{ $hasImg ? '' : 'min-height:240px;' }}">
            @if($hasImg)
                <img src="{{ $moneyBox->getMainImageUrl() }}" alt="{{ $moneyBox->title }}"
                     class="w-full object-cover" style="max-height:340px;display:block;">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/15 to-transparent"></div>
            @else
                <div style="min-height:240px;"></div>
            @endif

            <div class="pub-hero-overlay">
                <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-2 mb-3">
                        <span style="font-size:11px;font-weight:500;letter-spacing:.05em;text-transform:uppercase;color:rgba(255,255,255,.65);">Public box</span>
                        @if($moneyBox->category)
                            <span style="color:rgba(255,255,255,.35);">·</span>
                            <span class="pill" style="background:rgba(255,255,255,.14);color:#fff;border-color:rgba(255,255,255,.16);backdrop-filter:blur(6px);font-size:11px;">
                                {{ $moneyBox->category->icon }} {{ $moneyBox->category->name }}
                            </span>
                        @endif
                    </div>
                    <h1 style="font-size:clamp(22px,4vw,34px);font-weight:600;color:#fff;letter-spacing:-.02em;line-height:1.2;margin:0 0 10px;">
                        {{ $moneyBox->title }}
                    </h1>
                    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(255,255,255,.72);">
                        <svg viewBox="0 0 24 24" style="width:15px;height:15px;flex:none;" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Created by <strong style="color:#fff;font-weight:600;">{{ $creatorName }}</strong>
                        <x-verification-badge :user="$moneyBox->user" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 lg:pt-8">

            {{-- Two-column grid: on mobile right col (stats+form) comes first via order --}}
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-6 lg:gap-8">

                {{-- ── LEFT column: description + gallery ── --}}
                {{-- order-2 on mobile means it shows AFTER the right col (form) --}}
                <div class="order-2 lg:order-1 space-y-5 min-w-0">

                    {{-- Description --}}
                    @if($moneyBox->description)
                        <div class="card">
                            <div class="card-head"><span class="card-title">About this campaign</span></div>
                            <div class="card-body">
                                <p style="font-size:13.5px;color:#6B6862;line-height:1.75;white-space:pre-line;">{{ $moneyBox->description }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Gallery --}}
                    @if($moneyBox->hasMedia('gallery'))
                        <div class="card">
                            <div class="card-head"><span class="card-title">Gallery</span></div>
                            <div class="card-body">
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                                    @foreach($moneyBox->getMedia('gallery') as $img)
                                        @php
                                            try { $url = $img->getTemporaryUrl(now()->addHour()); }
                                            catch (\Exception $e) { $url = $img->getUrl(); }
                                        @endphp
                                        <a href="{{ $url }}" target="_blank" class="group block overflow-hidden rounded-[6px]">
                                            <img src="{{ $url }}" alt="Gallery"
                                                 class="w-full h-32 sm:h-40 object-cover transition-transform duration-200 group-hover:scale-[1.03]">
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Recent contributors: desktop shows in right col; mobile shows here --}}
                    @if($moneyBox->contributions->count() > 0)
                        <div class="card lg:hidden">
                            <div class="card-head">
                                <span class="card-title">Recent contributors</span>
                                <span class="pill pill-muted">{{ $moneyBox->contributions->count() }}</span>
                            </div>
                            <x-public.contributors-list :contributions="$moneyBox->contributions" :moneyBox="$moneyBox" />
                        </div>
                    @endif
                </div>

                {{-- ── RIGHT column: stats + form + creator + share + contributors ── --}}
                {{-- order-1 on mobile means it shows BEFORE the left col (description) --}}
                <div id="contribute-form" class="order-1 lg:order-2">
                    <div class="lg:sticky lg:top-[76px] space-y-4">

                        {{-- Progress + stats card --}}
                        <div class="card">
                            <div class="card-body">
                                {{-- Amount raised --}}
                                @if($moneyBox->goal_amount)
                                    <div style="display:flex;align-items:baseline;justify-content:space-between;margin-bottom:6px;">
                                        <span class="pub-progress-val">{{ $moneyBox->formatAmount($moneyBox->total_contributions) }}</span>
                                        <span class="pub-progress-of">of {{ $moneyBox->formatAmount($moneyBox->goal_amount) }}</span>
                                    </div>
                                    <div class="progress-track" style="margin-bottom:10px;">
                                        <div class="progress-fill" style="width:{{ $pct }}%"></div>
                                    </div>
                                @else
                                    <div style="margin-bottom:10px;">
                                        <span class="pub-progress-val">{{ $moneyBox->formatAmount($moneyBox->total_contributions) }}</span>
                                        <div style="font-size:12px;color:#6B6862;margin-top:3px;">Total raised</div>
                                    </div>
                                @endif

                                {{-- Stats row --}}
                                <div style="display:flex;flex-wrap:wrap;align-items:center;gap:8px 0;">
                                    @if($moneyBox->goal_amount)
                                        <span class="pub-stat-chip"><span class="pub-pct">{{ number_format($pct, 0) }}%</span> funded</span>
                                        <span class="pub-stat-sep" style="margin:0 8px;">|</span>
                                    @endif
                                    <span class="pub-stat-chip"><strong>{{ number_format($moneyBox->contribution_count) }}</strong> {{ Str::plural('contributor', $moneyBox->contribution_count) }}</span>
                                    @if($daysLeft !== null)
                                        <span class="pub-stat-sep" style="margin:0 8px;">|</span>
                                        <span class="pub-stat-chip"><strong>{{ $daysLeft }}d</strong> remaining</span>
                                    @endif
                                    @if($avgGift > 0)
                                        <span class="pub-stat-sep" style="margin:0 8px;">|</span>
                                        <span class="pub-stat-chip"><strong>{{ $moneyBox->formatAmount($avgGift) }}</strong> avg. gift</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Contribution form card --}}
                        <div class="card">
                            @if($moneyBox->canAcceptContributions())

                                {{-- Amount constraint banner --}}
                                @if(in_array($amtType, ['minimum','maximum','range']))
                                    <div style="background:#F3F1EB;padding:10px 18px;font-size:12px;color:#6B6862;display:flex;align-items:center;gap:6px;border-bottom:1px solid #E6E3DC;border-radius:var(--card-radius,10px) var(--card-radius,10px) 0 0;">
                                        <svg viewBox="0 0 24 24" style="width:13px;height:13px;flex:none;color:#1B6B4E;" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                                        @if($amtType === 'minimum') Min: <strong style="color:#15140F;">{{ $moneyBox->formatAmount($moneyBox->minimum_amount) }}</strong>
                                        @elseif($amtType === 'maximum') Max: <strong style="color:#15140F;">{{ $moneyBox->formatAmount($moneyBox->maximum_amount) }}</strong>
                                        @elseif($amtType === 'range') {{ $moneyBox->formatAmount($moneyBox->minimum_amount) }} – {{ $moneyBox->formatAmount($moneyBox->maximum_amount) }}
                                        @endif
                                    </div>
                                @endif

                                <div class="card-body" style="padding:20px;">
                                    @if($errors->any())
                                        <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:6px;padding:10px 14px;font-size:12.5px;color:#B91C1C;margin-bottom:16px;space-y:2px;">
                                            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('box.contribute', $moneyBox->slug) }}" style="display:flex;flex-direction:column;gap:16px;">
                                        @csrf

                                        {{-- Amount --}}
                                        <div>
                                            @if(!$fixedOnly)
                                                {{-- Preset chips --}}
                                                <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px;">
                                                    @foreach($presets as $preset)
                                                        <button type="button"
                                                                @click="setAmount('{{ number_format($preset, 2, '.', '') }}')"
                                                                class="pub-preset"
                                                                :style="amount == '{{ number_format($preset, 2, '.', '') }}' ? 'background:#15140F;color:#fff;border-color:#15140F;' : ''">
                                                            {{ $sym }}{{ number_format($preset, 0) }}
                                                        </button>
                                                    @endforeach
                                                </div>

                                                {{-- "Or enter" divider --}}
                                                <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                                                    <div style="flex:1;height:1px;background:#E6E3DC;"></div>
                                                    <span style="font-size:11.5px;color:#9C998F;white-space:nowrap;">Or enter another amount</span>
                                                    <div style="flex:1;height:1px;background:#E6E3DC;"></div>
                                                </div>

                                                {{-- Currency-prefix input --}}
                                                <div class="pub-amt-wrap">
                                                    <span class="pub-amt-sym">{{ $sym }}</span>
                                                    <input type="number" name="amount" id="amount"
                                                           x-model="amount"
                                                           step="0.01" min="{{ $minAmt }}"
                                                           @if($maxAmt) max="{{ $maxAmt }}" @endif
                                                           required placeholder="0.00"
                                                           class="pub-amt-input">
                                                </div>
                                            @else
                                                <input type="hidden" name="amount" value="{{ number_format($moneyBox->fixed_amount, 2, '.', '') }}">
                                                <div style="display:flex;align-items:center;background:#F3F1EB;border:1px solid #E6E3DC;border-radius:8px;padding:10px 14px;">
                                                    <span style="font-size:18px;font-weight:600;color:#15140F;font-variant-numeric:tabular-nums;">{{ $moneyBox->formatAmount($moneyBox->fixed_amount) }}</span>
                                                    <span style="font-size:12px;color:#9C998F;margin-left:auto;">Fixed amount</span>
                                                </div>
                                            @endif
                                            @error('amount')<p style="font-size:12px;color:#DC2626;margin-top:6px;">{{ $message }}</p>@enderror
                                        </div>

                                        {{-- Name --}}
                                        @if($identity !== 'anonymous_allowed')
                                            <div style="display:grid;gap:6px;">
                                                <label for="contributor_name" style="font-size:13px;font-weight:500;color:#15140F;">
                                                    Your name @if($identity === 'must_identify')<span style="color:#EF4444;">*</span>@endif
                                                </label>
                                                <input type="text" name="contributor_name" id="contributor_name"
                                                       value="{{ old('contributor_name', auth()->user()->name ?? '') }}"
                                                       @if($identity === 'must_identify') required @endif
                                                       placeholder="Jane Asiedu"
                                                       class="{{ $errors->has('contributor_name') ? 'border-red-400' : '' }}">
                                                @error('contributor_name')<p style="font-size:12px;color:#DC2626;">{{ $message }}</p>@enderror
                                            </div>
                                        @endif

                                        {{-- Email --}}
                                        <div style="display:grid;gap:6px;">
                                            <label for="contributor_email" style="font-size:13px;font-weight:500;color:#15140F;">
                                                Email <span style="color:#9C998F;font-weight:400;">· for receipt</span>
                                                <span style="color:#EF4444;">*</span>
                                            </label>
                                            <input type="email" name="contributor_email" id="contributor_email" required
                                                   value="{{ old('contributor_email', auth()->user()->email ?? '') }}"
                                                   placeholder="jane@email.com"
                                                   class="{{ $errors->has('contributor_email') ? 'border-red-400' : '' }}">
                                            @error('contributor_email')<p style="font-size:12px;color:#DC2626;">{{ $message }}</p>@enderror
                                        </div>

                                        {{-- Message --}}
                                        <div style="display:grid;gap:6px;">
                                            <label for="message" style="font-size:13px;font-weight:500;color:#15140F;">
                                                Leave a message <span style="color:#9C998F;font-weight:400;">· optional</span>
                                            </label>
                                            <textarea name="message" id="message" rows="2"
                                                      placeholder="Congratulations to the happy couple!"
                                                      class="{{ $errors->has('message') ? 'border-red-400' : '' }}">{{ old('message') }}</textarea>
                                            @error('message')<p style="font-size:12px;color:#DC2626;">{{ $message }}</p>@enderror
                                        </div>

                                        {{-- Anonymous toggle --}}
                                        @if($identity !== 'must_identify')
                                            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;user-select:none;">
                                                <input type="checkbox" name="is_anonymous" id="is_anonymous" value="1"
                                                       {{ old('is_anonymous') ? 'checked' : '' }}
                                                       style="width:16px;height:16px;border-radius:4px;border-color:#D9D6CE;cursor:pointer;">
                                                <span style="font-size:13px;color:#15140F;">Contribute anonymously</span>
                                            </label>
                                        @endif

                                        {{-- Submit --}}
                                        <button type="submit"
                                                class="btn btn-primary w-full justify-center py-3 rounded-[8px]"
                                                style="display:flex;align-items:center;gap:8px;font-size:14px;">
                                            <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                                            <span>Contribute</span>
                                            <span x-show="amount && parseFloat(amount) > 0"
                                                  x-text="'{{ $sym }}' + parseFloat(amount||0).toLocaleString('en-GH',{minimumFractionDigits:0,maximumFractionDigits:2})"
                                                  x-cloak></span>
                                        </button>

                                        {{-- Payment methods --}}
                                        <p style="font-size:11.5px;color:#9C998F;text-align:center;line-height:1.6;">
                                            Payments via MTN MoMo, Vodafone Cash, AirtelTigo Money, Card.<br>Secured by TrendiPay.
                                        </p>
                                    </form>
                                </div>

                                {{-- Creator strip --}}
                                <div class="pub-creator-strip">
                                    <div class="pub-creator-av">{{ $creatorInitials }}</div>
                                    <div style="min-width:0;">
                                        <div style="font-size:11px;color:#9C998F;text-transform:uppercase;letter-spacing:.05em;margin-bottom:1px;">Created by</div>
                                        <div style="font-size:13px;font-weight:600;color:#15140F;display:flex;align-items:center;gap:6px;">
                                            {{ $creatorName }}
                                            <x-verification-badge :user="$moneyBox->user" />
                                        </div>
                                    </div>
                                </div>

                            @else
                                <div class="card-body text-center py-10">
                                    <div class="w-12 h-12 rounded-xl bg-[#F3F1EB] grid place-items-center mx-auto mb-3">
                                        <svg viewBox="0 0 24 24" class="w-6 h-6 text-[#9C998F]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                    </div>
                                    <h3 style="font-size:14px;font-weight:600;color:#15140F;margin:0 0 4px;">Not accepting contributions</h3>
                                    <p style="font-size:13px;color:#6B6862;margin:0;">This campaign is currently closed.</p>
                                </div>
                            @endif

                            {{-- Share --}}
                            <div class="pub-share-section">
                                <p style="font-size:11px;font-weight:500;color:#9C998F;text-transform:uppercase;letter-spacing:.06em;margin:0 0 10px;">Share this campaign</p>
                                <div style="display:flex;flex-direction:column;gap:8px;">
                                    <button type="button"
                                            @click="navigator.clipboard.writeText('{{ $moneyBox->getPublicUrl() }}').then(() => toast('Link copied!'))"
                                            class="btn w-full justify-center">
                                        <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                                        Copy link
                                    </button>
                                    <a href="https://wa.me/?text={{ urlencode('Support ' . $creatorName . '\'s campaign: ' . $moneyBox->title . ' — ' . $moneyBox->getPublicUrl()) }}"
                                       target="_blank" class="btn w-full justify-center">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.076 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374A9.86 9.86 0 012.1 11.974C2.1 6.524 6.536 2.09 11.988 2.09c2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884"/></svg>
                                        Share on WhatsApp
                                    </a>
                                    @if($moneyBox->hasQrCode())
                                        <a href="{{ route('money-boxes.download-qr', $moneyBox) }}" class="btn w-full justify-center">
                                            <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                            Download QR code
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Recent contributors: desktop only (mobile shows in left col) --}}
                        @if($moneyBox->contributions->count() > 0)
                            <div class="card hidden lg:block">
                                <div class="card-head">
                                    <span class="card-title">Recent contributors</span>
                                    <span class="pill pill-muted">{{ $moneyBox->contributions->count() }}</span>
                                </div>
                                <x-public.contributors-list :contributions="$moneyBox->contributions" :moneyBox="$moneyBox" />
                            </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layouts.guest>
