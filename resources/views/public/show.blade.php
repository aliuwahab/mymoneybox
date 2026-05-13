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
        $fixedOnly    = $amtType === 'fixed';
        $minAmt       = $moneyBox->minimum_amount ?? 0.01;
        $maxAmt       = $moneyBox->maximum_amount ?? null;
        $avgGift      = $moneyBox->contribution_count > 0
                            ? $moneyBox->total_contributions / $moneyBox->contribution_count
                            : 0;
        $daysLeft     = $moneyBox->end_date ? max(0, (int) now()->diffInDays($moneyBox->end_date, false)) : null;
        $creatorName  = $moneyBox->user->name;
        $creatorInitials = collect(explode(' ', $creatorName))->map(fn($p) => strtoupper($p[0] ?? ''))->take(2)->implode('');
        $defaultAmt   = $fixedOnly ? number_format($moneyBox->fixed_amount, 2, '.', '') : '';
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
        <div class="w-full {{ $hasImg ? '' : $cover }} relative overflow-hidden" style="{{ $hasImg ? '' : 'min-height:260px;' }}">
            @if($hasImg)
                <img src="{{ $moneyBox->getMainImageUrl() }}" alt="{{ $moneyBox->title }}"
                     class="w-full object-cover" style="max-height:360px;">
                <div class="absolute inset-0 bg-gradient-to-t from-black/55 via-black/15 to-transparent"></div>
            @else
                <div style="min-height:260px;" class="relative"></div>
            @endif

            <div class="absolute bottom-0 left-0 right-0 px-4 sm:px-8 pb-6 pt-20">
                <div class="max-w-5xl mx-auto">
                    <div class="flex items-center gap-2 mb-2.5">
                        <span class="text-[11px] font-medium tracking-[0.05em] uppercase text-white/70">Public box</span>
                        @if($moneyBox->category)
                            <span class="text-white/40">·</span>
                            <span class="pill" style="background:rgba(255,255,255,.15);color:#fff;border-color:rgba(255,255,255,.18);backdrop-filter:blur(6px);font-size:11px;">
                                {{ $moneyBox->category->icon }} {{ $moneyBox->category->name }}
                            </span>
                        @endif
                    </div>
                    <h1 class="text-[26px] sm:text-[34px] font-semibold text-white tracking-tight leading-tight drop-shadow-sm">
                        {{ $moneyBox->title }}
                    </h1>
                    <div class="flex items-center gap-2 mt-2 text-[13px] text-white/75">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 flex-none" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <span>Created by <strong class="text-white font-semibold">{{ $creatorName }}</strong></span>
                        <x-verification-badge :user="$moneyBox->user" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 lg:pt-8">
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-6 lg:gap-8">

                {{-- ── Left column ────────────────────────────── --}}
                <div class="space-y-5 min-w-0">

                    {{-- Progress + stats --}}
                    <div class="card">
                        <div class="card-body">
                            @if($moneyBox->goal_amount)
                                <div class="flex items-baseline justify-between mb-1">
                                    <span class="text-[24px] font-semibold text-[#15140F] tnum tracking-tight">
                                        {{ $moneyBox->formatAmount($moneyBox->total_contributions) }}
                                    </span>
                                    <span class="text-[13px] text-[#6B6862]">
                                        of {{ $moneyBox->formatAmount($moneyBox->goal_amount) }}
                                    </span>
                                </div>
                                <div class="progress-track mb-3">
                                    <div class="progress-fill" style="width:{{ $pct }}%"></div>
                                </div>
                            @else
                                <div class="text-[26px] font-semibold text-[#15140F] tnum tracking-tight mb-3">
                                    {{ $moneyBox->formatAmount($moneyBox->total_contributions) }}
                                </div>
                            @endif

                            {{-- Stats chips --}}
                            <div class="flex flex-wrap gap-x-5 gap-y-2 text-[13px] text-[#6B6862]">
                                @if($moneyBox->goal_amount)
                                    <span class="flex items-center gap-1.5">
                                        <span class="font-semibold text-primary-600 tnum">{{ number_format($pct, 0) }}%</span>
                                        funded
                                    </span>
                                    <span class="text-[#D3D0C8]">|</span>
                                @endif
                                <span class="flex items-center gap-1.5">
                                    <strong class="text-[#15140F] tnum">{{ number_format($moneyBox->contribution_count) }}</strong>
                                    {{ Str::plural('contributor', $moneyBox->contribution_count) }}
                                </span>
                                @if($daysLeft !== null)
                                    <span class="text-[#D3D0C8]">|</span>
                                    <span class="flex items-center gap-1.5">
                                        <strong class="text-[#15140F] tnum">{{ $daysLeft }}d</strong>
                                        remaining
                                    </span>
                                @endif
                                @if($avgGift > 0)
                                    <span class="text-[#D3D0C8]">|</span>
                                    <span class="flex items-center gap-1.5">
                                        <strong class="text-[#15140F] tnum">{{ $moneyBox->formatAmount($avgGift) }}</strong>
                                        avg. gift
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    @if($moneyBox->description)
                        <div class="card">
                            <div class="card-head"><span class="card-title">About this campaign</span></div>
                            <div class="card-body">
                                <p class="text-[13.5px] text-[#6B6862] leading-relaxed whitespace-pre-line">{{ $moneyBox->description }}</p>
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

                    {{-- Recent contributions (mobile: below description, desktop: left col) --}}
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

                {{-- ── Right column (sticky form + creator + contributors) --}}
                <div id="contribute-form" class="space-y-4">

                    {{-- Contribution form card --}}
                    <div class="card lg:sticky lg:top-[76px]">
                        @if($moneyBox->canAcceptContributions())
                            {{-- Amount hint banner --}}
                            @if(in_array($amtType, ['minimum', 'maximum', 'range']))
                                <div class="bg-[#F3F1EB] px-4 py-2.5 text-[12px] text-[#6B6862] flex items-center gap-2 border-b border-[#E6E3DC] rounded-t-[inherit]">
                                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5 flex-none text-primary-600" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                                    @if($amtType === 'minimum')
                                        Min: <strong class="text-[#15140F]">{{ $moneyBox->formatAmount($moneyBox->minimum_amount) }}</strong>
                                    @elseif($amtType === 'maximum')
                                        Max: <strong class="text-[#15140F]">{{ $moneyBox->formatAmount($moneyBox->maximum_amount) }}</strong>
                                    @elseif($amtType === 'range')
                                        {{ $moneyBox->formatAmount($moneyBox->minimum_amount) }} – {{ $moneyBox->formatAmount($moneyBox->maximum_amount) }}
                                    @endif
                                </div>
                            @endif

                            <div class="card-body space-y-4">
                                @if($errors->any())
                                    <div class="bg-red-50 border border-red-200 rounded-[6px] px-3 py-2.5 text-[12.5px] text-red-700 space-y-0.5">
                                        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('box.contribute', $moneyBox->slug) }}" class="space-y-4">
                                    @csrf

                                    {{-- Amount --}}
                                    <div class="space-y-2">
                                        @if(!$fixedOnly)
                                            {{-- Preset chips --}}
                                            <div class="flex flex-wrap gap-1.5">
                                                @foreach($presets as $preset)
                                                    <button type="button"
                                                            @click="setAmount('{{ number_format($preset, 2, '.', '') }}')"
                                                            class="px-3 py-1.5 text-[12.5px] rounded-[6px] border transition-colors font-medium"
                                                            :class="amount == '{{ number_format($preset, 2, '.', '') }}' ? 'bg-[#15140F] text-white border-[#15140F]' : 'bg-white text-[#6B6862] border-[#E6E3DC] hover:border-[#C8C5BC] hover:text-[#15140F]'">
                                                        {{ $sym }}{{ number_format($preset, 0) }}
                                                    </button>
                                                @endforeach
                                            </div>

                                            {{-- Divider --}}
                                            <div class="flex items-center gap-2">
                                                <div class="flex-1 h-px bg-[#E6E3DC]"></div>
                                                <span class="text-[11.5px] text-[#9C998F]">Or enter another amount</span>
                                                <div class="flex-1 h-px bg-[#E6E3DC]"></div>
                                            </div>

                                            {{-- Amount input with currency prefix --}}
                                            <div class="flex items-center border border-[#E6E3DC] rounded-[7px] bg-white overflow-hidden focus-within:ring-2 focus-within:ring-[#15140F]/10 focus-within:border-[#9C998F] transition-all">
                                                <span class="pl-3 pr-2 text-[15px] font-medium text-[#6B6862] select-none flex-none">{{ $sym }}</span>
                                                <input type="number" name="amount" id="amount"
                                                       x-model="amount"
                                                       step="0.01"
                                                       min="{{ $minAmt }}"
                                                       @if($maxAmt) max="{{ $maxAmt }}" @endif
                                                       required
                                                       placeholder="0.00"
                                                       class="flex-1 border-0 ring-0 outline-none py-2.5 pr-3 text-[15px] font-semibold text-[#15140F] bg-transparent focus:ring-0 focus:outline-none"
                                                       style="box-shadow:none!important;">
                                            </div>
                                        @else
                                            <input type="hidden" name="amount" value="{{ number_format($moneyBox->fixed_amount, 2, '.', '') }}">
                                            <div class="flex items-center bg-[#F3F1EB] border border-[#E6E3DC] rounded-[7px] px-3 py-2.5">
                                                <span class="text-[18px] font-semibold text-[#15140F] tnum">{{ $moneyBox->formatAmount($moneyBox->fixed_amount) }}</span>
                                                <span class="text-[12px] text-[#9C998F] ml-auto">Fixed amount</span>
                                            </div>
                                        @endif
                                        @error('amount')<p class="text-[12px] text-red-600 mt-1">{{ $message }}</p>@enderror
                                    </div>

                                    {{-- Name --}}
                                    @if($identity !== 'anonymous_allowed')
                                        <div class="grid gap-1.5">
                                            <label for="contributor_name" class="text-[13px] font-medium text-[#15140F]">
                                                Your name @if($identity === 'must_identify')<span class="text-red-500">*</span>@endif
                                            </label>
                                            <input type="text" name="contributor_name" id="contributor_name"
                                                   value="{{ old('contributor_name', auth()->user()->name ?? '') }}"
                                                   @if($identity === 'must_identify') required @endif
                                                   placeholder="Jane Asiedu"
                                                   class="{{ $errors->has('contributor_name') ? 'border-red-400' : '' }}">
                                            @error('contributor_name')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                                        </div>
                                    @endif

                                    {{-- Email --}}
                                    <div class="grid gap-1.5">
                                        <label for="contributor_email" class="text-[13px] font-medium text-[#15140F]">
                                            Email <span class="text-[#9C998F] font-normal">· for receipt</span>
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <input type="email" name="contributor_email" id="contributor_email" required
                                               value="{{ old('contributor_email', auth()->user()->email ?? '') }}"
                                               placeholder="jane@email.com"
                                               class="{{ $errors->has('contributor_email') ? 'border-red-400' : '' }}">
                                        @error('contributor_email')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                                    </div>

                                    {{-- Message --}}
                                    <div class="grid gap-1.5">
                                        <label for="message" class="text-[13px] font-medium text-[#15140F]">
                                            Leave a message <span class="text-[#9C998F] font-normal">· optional</span>
                                        </label>
                                        <textarea name="message" id="message" rows="2"
                                                  placeholder="Congratulations to the happy couple!"
                                                  class="{{ $errors->has('message') ? 'border-red-400' : '' }}">{{ old('message') }}</textarea>
                                        @error('message')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                                    </div>

                                    {{-- Anonymous toggle --}}
                                    @if($identity !== 'must_identify')
                                        <label class="flex items-center gap-2.5 cursor-pointer select-none">
                                            <input type="checkbox" name="is_anonymous" id="is_anonymous" value="1"
                                                   {{ old('is_anonymous') ? 'checked' : '' }}
                                                   class="w-4 h-4 rounded border-[#D9D6CE] text-primary-600">
                                            <span class="text-[13px] text-[#15140F]">Contribute anonymously</span>
                                        </label>
                                    @endif

                                    {{-- Submit --}}
                                    <button type="submit"
                                            class="btn btn-primary w-full justify-center py-3 text-[14px] rounded-[8px]"
                                            style="display:flex;align-items:center;gap:8px;">
                                        <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                                        <span>Contribute</span>
                                        <span x-show="amount && parseFloat(amount) > 0" x-text="'{{ $sym }}' + parseFloat(amount || 0).toLocaleString('en-GH', {minimumFractionDigits:0,maximumFractionDigits:2})" x-cloak></span>
                                    </button>

                                    {{-- Payment methods --}}
                                    <p class="text-[11.5px] text-[#9C998F] text-center leading-relaxed">
                                        Payments via MTN MoMo, Vodafone Cash, AirtelTigo Money, Card.<br>Secured by TrendiPay.
                                    </p>
                                </form>
                            </div>

                            {{-- Creator strip --}}
                            <div class="border-t border-[#E6E3DC] px-[18px] py-3.5 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-[#15140F] text-white grid place-items-center text-[12px] font-semibold flex-none tracking-tight">
                                    {{ $creatorInitials }}
                                </div>
                                <div class="min-w-0">
                                    <div class="text-[11px] text-[#9C998F] uppercase tracking-[0.05em]">Created by</div>
                                    <div class="text-[13px] font-semibold text-[#15140F] truncate flex items-center gap-1.5">
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
                                <h3 class="text-[14px] font-semibold text-[#15140F] mb-1">Not accepting contributions</h3>
                                <p class="text-[13px] text-[#6B6862]">This campaign is currently closed.</p>
                            </div>
                        @endif

                        {{-- Share section --}}
                        <div class="border-t border-[#E6E3DC] px-[18px] py-4 space-y-2">
                            <p class="text-[11px] font-medium text-[#9C998F] uppercase tracking-[0.06em] mb-2.5">Share this campaign</p>
                            <button type="button"
                                    @click="navigator.clipboard.writeText('{{ $moneyBox->getPublicUrl() }}').then(() => toast('Link copied!'))"
                                    class="btn w-full justify-center">
                                <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                                Copy link
                            </button>
                            <a href="https://wa.me/?text={{ urlencode('Support ' . $moneyBox->user->name . '\'s campaign: ' . $moneyBox->title . ' — ' . $moneyBox->getPublicUrl()) }}"
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

                    {{-- Recent contributors (desktop: right col) --}}
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
</x-layouts.guest>
