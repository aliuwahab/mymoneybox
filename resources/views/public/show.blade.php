<x-layouts.guest>
    @php
        $covers      = ['cover-emerald','cover-amber','cover-slate','cover-rose','cover-violet'];
        $cover       = $covers[crc32($moneyBox->id) % count($covers)];
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
        $creatorInitials = collect(explode(' ', $creatorName))->map(fn($p) => strtoupper($p[0] ?? ''))->take(2)->implode('');
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
        <div class="pub-shell max-w-[980px] mx-auto"
             style="background:#F7F5EF;border:1px solid #E6E3DC;border-radius:12px;padding:28px;align-items:start;">

            {{-- ── LEFT: pill + title + description + progress + form ── --}}
            <div id="contribute-form" style="display:flex;flex-direction:column;gap:20px;">

                {{-- Category / visibility pill --}}
                <div style="display:flex;align-items:center;gap:8px;">
                    <span class="pill pill-info" style="font-size:11px;">
                        <svg viewBox="0 0 24 24" style="width:11px;height:11px;" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a13 13 0 0 1 0 20"/><path d="M12 2a13 13 0 0 0 0 20"/></svg>
                        Public box
                        @if($moneyBox->category)
                            · {{ $moneyBox->category->name }}
                        @endif
                    </span>
                </div>

                {{-- Instrument Serif title --}}
                <div style="margin-top:-4px;">
                    <h1 style="font-family:'Instrument Serif',Georgia,serif;font-size:clamp(26px,3.5vw,36px);font-weight:400;line-height:1.1;letter-spacing:-0.01em;color:#15140F;margin:0 0 10px;">
                        {{ $moneyBox->title }}
                    </h1>
                    @if($moneyBox->description)
                        <p style="font-size:14px;color:#6B6862;line-height:1.6;margin:0;max-width:520px;">{{ $moneyBox->description }}</p>
                    @endif
                </div>

                {{-- Progress card --}}
                <div style="background:#fff;border:1px solid #E6E3DC;border-radius:10px;">
                    <div style="padding:16px 18px;">
                        @if($moneyBox->goal_amount)
                            {{-- Raised vs goal --}}
                            <div style="display:flex;align-items:baseline;justify-content:space-between;margin-bottom:8px;">
                                <span style="font-size:20px;font-weight:600;color:#15140F;font-variant-numeric:tabular-nums;letter-spacing:-0.02em;">
                                    {{ $moneyBox->formatAmount($moneyBox->total_contributions) }}
                                </span>
                                <span style="font-size:12.5px;color:#9C998F;font-variant-numeric:tabular-nums;">{{ number_format($pct) }}%</span>
                            </div>
                            <div style="display:flex;align-items:baseline;gap:4px;margin-bottom:10px;">
                                <span style="font-size:12.5px;color:#6B6862;">of {{ $moneyBox->formatAmount($moneyBox->goal_amount) }} goal</span>
                            </div>
                            <div class="progress-track" style="margin-bottom:14px;">
                                <div class="progress-fill" style="width:{{ $pct }}%;"></div>
                            </div>
                        @else
                            <div style="font-size:22px;font-weight:600;color:#15140F;font-variant-numeric:tabular-nums;letter-spacing:-0.02em;margin-bottom:14px;">
                                {{ $moneyBox->formatAmount($moneyBox->total_contributions) }}
                            </div>
                        @endif

                        {{-- Stats row --}}
                        <div style="display:flex;gap:20px;">
                            <div>
                                <div style="font-size:14px;font-weight:600;color:#15140F;font-variant-numeric:tabular-nums;">{{ number_format($moneyBox->contribution_count) }}</div>
                                <div class="tiny">{{ Str::plural('contributor', $moneyBox->contribution_count) }}</div>
                            </div>
                            @if($daysLeft !== null)
                                <div>
                                    <div style="font-size:14px;font-weight:600;color:#15140F;font-variant-numeric:tabular-nums;">{{ $daysLeft }}d</div>
                                    <div class="tiny">remaining</div>
                                </div>
                            @endif
                            @if($avgGift > 0)
                                <div>
                                    <div style="font-size:14px;font-weight:600;color:#15140F;font-variant-numeric:tabular-nums;">{{ $moneyBox->formatAmount($avgGift) }}</div>
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

                    <form method="POST" action="{{ route('box.contribute', $moneyBox->slug) }}"
                          style="display:flex;flex-direction:column;gap:14px;">
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
                            <div style="display:flex;gap:8px;">
                                @foreach($presets as $preset)
                                    <button type="button"
                                            @click="setAmount('{{ number_format($preset, 2, '.', '') }}')"
                                            style="flex:1;padding:7px 13px;border-radius:6px;font-size:13px;font-weight:500;border:1px solid;cursor:pointer;transition:background .12s,border-color .12s,transform .08s;text-align:center;box-shadow:0 1px 0 rgba(20,18,12,.04),0 1px 2px rgba(20,18,12,.04);"
                                            :style="amount == '{{ number_format($preset, 2, '.', '') }}'
                                                ? 'background:#1B6B4E;color:#fff;border-color:#1B6B4E;box-shadow:none;'
                                                : 'background:#fff;color:#15140F;border-color:#E6E3DC;'">
                                        {{ $sym }}{{ number_format($preset, 0) }}
                                    </button>
                                @endforeach
                            </div>

                            {{-- "Or enter another amount" input --}}
                            <div style="display:grid;gap:6px;">
                                <div style="font-size:12.5px;color:#6B6862;font-weight:500;">Or enter another amount</div>
                                <div class="pub-amt-field" style="display:flex;align-items:center;background:#fff;border:1px solid #E6E3DC;border-radius:6px;padding-left:10px;transition:border-color .12s,box-shadow .12s;">
                                    <span style="color:#9C998F;font-size:13px;flex:none;user-select:none;">{{ $sym }}</span>
                                    <input type="number" name="amount" id="amount"
                                           x-model="amount"
                                           step="0.01" min="{{ $minAmt }}"
                                           @if($maxAmt) max="{{ $maxAmt }}" @endif
                                           required placeholder="0.00"
                                           style="border:0;padding:8px 10px;outline:0;width:100%;background:transparent;font-size:13.5px;color:#15140F;box-shadow:none!important;">
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
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                            @if($identity !== 'anonymous_allowed')
                                <div style="display:flex;flex-direction:column;gap:5px;">
                                    <label for="contributor_name" style="font-size:12.5px;font-weight:500;color:#6B6862;">
                                        Your name @if($identity === 'must_identify')<span style="color:#EF4444;">*</span>@else<span style="font-size:11.5px;color:#9C998F;font-weight:400;"></span>@endif
                                    </label>
                                    <input type="text" name="contributor_name" id="contributor_name"
                                           value="{{ old('contributor_name', auth()->user()->name ?? '') }}"
                                           @if($identity === 'must_identify') required @endif
                                           placeholder="Jane Asiedu"
                                           class="{{ $errors->has('contributor_name') ? 'border-red-400' : '' }}">
                                    @error('contributor_name')<p style="font-size:12px;color:#DC2626;margin:0;">{{ $message }}</p>@enderror
                                </div>
                            @endif

                            <div style="display:flex;flex-direction:column;gap:5px;" @if($identity === 'anonymous_allowed') style="grid-column:1/-1;" @endif>
                                <label for="contributor_email" style="font-size:12.5px;font-weight:500;color:#6B6862;">
                                    Email <span style="font-size:11.5px;color:#9C998F;">· for receipt</span> <span style="color:#EF4444;">*</span>
                                </label>
                                <input type="email" name="contributor_email" id="contributor_email" required
                                       value="{{ old('contributor_email', auth()->user()->email ?? '') }}"
                                       placeholder="jane@email.com"
                                       class="{{ $errors->has('contributor_email') ? 'border-red-400' : '' }}">
                                @error('contributor_email')<p style="font-size:12px;color:#DC2626;margin:0;">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- Message --}}
                        <div style="display:flex;flex-direction:column;gap:5px;">
                            <label for="message" style="font-size:12.5px;font-weight:500;color:#6B6862;">
                                Leave a message <span style="font-size:11.5px;color:#9C998F;">· optional</span>
                            </label>
                            <textarea name="message" id="message" rows="2"
                                      placeholder="Congratulations to the happy couple!"
                                      style="resize:vertical;min-height:72px;"
                                      class="{{ $errors->has('message') ? 'border-red-400' : '' }}">{{ old('message') }}</textarea>
                            @error('message')<p style="font-size:12px;color:#DC2626;margin:0;">{{ $message }}</p>@enderror
                        </div>

                        {{-- Anonymous toggle --}}
                        @if($identity !== 'must_identify')
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;user-select:none;font-size:13px;color:#15140F;">
                                <input type="checkbox" name="is_anonymous" id="is_anonymous" value="1"
                                       {{ old('is_anonymous') ? 'checked' : '' }}
                                       style="width:15px;height:15px;border-radius:4px;cursor:pointer;">
                                Contribute anonymously
                            </label>
                        @endif

                        {{-- Contribute button --}}
                        <button type="submit"
                                class="btn btn-primary w-full justify-center rounded-[8px]"
                                style="display:flex;align-items:center;gap:8px;padding:11px 16px;font-size:14px;">
                            <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" fill="currentColor" stroke="none"/></svg>
                            <span>Contribute</span>
                            <span x-show="amount && parseFloat(amount) > 0"
                                  x-text="'{{ $sym }}' + parseFloat(amount||0).toLocaleString('en-GH',{minimumFractionDigits:0,maximumFractionDigits:2})"
                                  x-cloak></span>
                        </button>

                        <div style="font-size:11.5px;color:#9C998F;text-align:center;line-height:1.6;">
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
            <div class="pub-right" style="display:flex;flex-direction:column;gap:16px;">

                {{-- Campaign cover visual --}}
                <div style="border-radius:10px;overflow:hidden;position:relative;aspect-ratio:16/10;
                            {{ $hasImg ? '' : 'background:linear-gradient(135deg,#1B6B4E 0%,#2E8E6C 100%);' }}">
                    @if($hasImg)
                        <img src="{{ $moneyBox->getMainImageUrl() }}" alt="{{ $moneyBox->title }}"
                             style="width:100%;height:100%;object-fit:cover;object-position:center 30%;display:block;">
                        <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55) 0%,rgba(0,0,0,.05) 60%,transparent 100%);"></div>
                    @else
                        {{-- Serif initials centered --}}
                        <div style="position:absolute;inset:0;display:grid;place-items:center;color:rgba(255,255,255,0.9);font-family:'Instrument Serif',Georgia,serif;font-size:52px;letter-spacing:.02em;">
                            {{ $coverInitials }}
                        </div>
                    @endif
                    {{-- Creator byline at bottom --}}
                    <div style="position:absolute;bottom:12px;left:14px;right:14px;">
                        <div style="font-size:10.5px;color:rgba(255,255,255,0.7);text-transform:uppercase;letter-spacing:.06em;margin-bottom:2px;">Created by</div>
                        <div style="font-size:13px;font-weight:600;color:#fff;display:flex;align-items:center;gap:6px;">
                            {{ $creatorName }}
                            <x-verification-badge :user="$moneyBox->user" />
                        </div>
                    </div>
                </div>

                {{-- Recent contributors --}}
                @if($moneyBox->contributions->count() > 0)
                    <div style="background:#fff;border:1px solid #E6E3DC;border-radius:10px;overflow:hidden;">
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid #F0EDE6;">
                            <span style="font-size:13px;font-weight:600;color:#15140F;letter-spacing:-.005em;">Recent contributors</span>
                            <span class="pill pill-muted" style="font-size:11px;">{{ $moneyBox->contributions->count() }}</span>
                        </div>
                        <div style="padding:4px 0;">
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
                                <div style="display:flex;align-items:flex-start;gap:10px;padding:10px 16px;border-bottom:1px solid #F5F3EE;">
                                    <div style="width:30px;height:30px;border-radius:50%;background:{{ $avColor }};color:{{ $avText }};display:grid;place-items:center;font-size:11px;font-weight:600;flex:none;letter-spacing:.01em;">
                                        {{ $cInitials }}
                                    </div>
                                    <div style="flex:1;min-width:0;">
                                        <div style="display:flex;align-items:baseline;justify-content:space-between;gap:6px;">
                                            <span style="font-size:13px;font-weight:500;color:#15140F;truncate;">{{ $cName }}</span>
                                            <span style="font-size:13px;font-weight:600;color:#15140F;font-variant-numeric:tabular-nums;flex:none;">{{ $sym }}{{ number_format($c->amount, 0) }}</span>
                                        </div>
                                        @if($c->message)
                                            <div style="font-size:11.5px;color:#9C998F;font-style:italic;margin-top:2px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">"{{ $c->message }}"</div>
                                        @endif
                                        <div style="font-size:11px;color:#C0BDB5;margin-top:1px;">{{ $c->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            @endforeach
                            @if($moneyBox->contributions->count() > 5)
                                <div style="padding:10px 16px;font-size:12px;color:#9C998F;text-align:center;">
                                    + {{ $moneyBox->contributions->count() - 5 }} more contributors
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Share / QR card --}}
                <div style="background:#fff;border:1px solid #E6E3DC;border-radius:10px;overflow:hidden;">
                    <div style="padding:12px 16px;border-bottom:1px solid #F0EDE6;">
                        <span style="font-size:13px;font-weight:600;color:#15140F;">Share this campaign</span>
                    </div>
                    @if($moneyBox->hasQrCode())
                        <div style="display:flex;align-items:center;gap:14px;padding:14px 16px;border-bottom:1px solid #F0EDE6;">
                            <img src="{{ $moneyBox->getQrCodeUrl() }}" alt="QR Code"
                                 style="width:72px;height:72px;border-radius:6px;border:1px solid #E6E3DC;flex:none;">
                            <div>
                                <div style="font-size:13px;font-weight:500;color:#15140F;margin-bottom:3px;">Scan to contribute</div>
                                <div class="tiny">Share this QR on invitations, posters, or screens.</div>
                                <a href="{{ route('money-boxes.download-qr', $moneyBox) }}"
                                   style="display:inline-flex;align-items:center;gap:5px;margin-top:8px;font-size:12px;color:#1B6B4E;font-weight:500;text-decoration:none;">
                                    <svg viewBox="0 0 24 24" style="width:12px;height:12px;" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    Download QR
                                </a>
                            </div>
                        </div>
                    @endif
                    <div style="display:flex;flex-direction:column;gap:8px;padding:14px 16px;">
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
                    </div>
                </div>

            </div>
        </div>

        {{-- Mobile: stacks below pub-shell on small screens --}}
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

    {{-- Scoped styles for amount field --}}
    <style>
        .pub-amt-field:focus-within {
            border-color: #1B6B4E !important;
            box-shadow: 0 0 0 3px #E6F1EB !important;
        }
        .pub-amt-field input:focus {
            outline: 0;
            box-shadow: none !important;
        }
    </style>

    <style>
        /* Desktop: two-column grid */
        .pub-shell {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 24px;
            align-items: start;
        }
        .pub-right {
            position: sticky;
            top: 84px;
            align-self: start;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(0,0,0,.1) transparent;
        }
        .pub-right::-webkit-scrollbar { width: 4px; }
        .pub-right::-webkit-scrollbar-thumb { background: rgba(0,0,0,.1); border-radius: 4px; }
        .pub-right::-webkit-scrollbar-track { background: transparent; }

        /* Mobile: single column, right col drops below */
        @media (max-width: 768px) {
            .pub-shell {
                grid-template-columns: 1fr;
                padding: 20px !important;
            }
            .pub-right {
                position: static;
            }
        }

        /* Small mobile: remove card borders for edge-to-edge feel */
        @media (max-width: 600px) {
            .pub-shell {
                border-radius: 0 !important;
                border-left: 0 !important;
                border-right: 0 !important;
                margin-left: -1rem !important;
                margin-right: -1rem !important;
                padding: 16px !important;
            }
        }
    </style>
</x-layouts.guest>
