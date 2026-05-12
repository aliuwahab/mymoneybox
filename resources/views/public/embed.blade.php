<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $moneyBox->title }} · MyMoneyBox</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; background: #FAFAF7; font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#FAFAF7]" x-data x-cloak>

@php
    $progress      = $moneyBox->goal_amount > 0
        ? min(100, ($moneyBox->total_contributions / $moneyBox->goal_amount) * 100)
        : null;
    $canContrib    = $moneyBox->canAcceptContributions();
    $currency      = $moneyBox->getCurrencySymbol();
    $amountType    = $moneyBox->amount_type;
    $identityRule  = $moneyBox->contributor_identity;
    $mustIdentify  = $identityRule === \App\Enums\ContributorIdentity::MustIdentify;
    $neverIdentify = $identityRule === \App\Enums\ContributorIdentity::Anonymous;

    $presets = match($amountType) {
        \App\Enums\AmountType::Fixed   => [$moneyBox->fixed_amount],
        \App\Enums\AmountType::Minimum => array_values(array_filter([
            $moneyBox->minimum_amount,
            $moneyBox->minimum_amount ? $moneyBox->minimum_amount * 2 : null,
            $moneyBox->minimum_amount ? $moneyBox->minimum_amount * 5 : null,
        ])),
        \App\Enums\AmountType::Range   => array_values(array_filter([
            $moneyBox->minimum_amount,
            ($moneyBox->minimum_amount && $moneyBox->maximum_amount)
                ? round(($moneyBox->minimum_amount + $moneyBox->maximum_amount) / 2)
                : null,
            $moneyBox->maximum_amount,
        ])),
        default                        => [10, 20, 50, 100],
    };
@endphp

<div class="flex flex-col min-h-screen">

    {{-- Header band --}}
    <div class="bg-[#15140F] px-4 py-3 flex items-center gap-3">
        @if($moneyBox->getMainImageUrl())
            <img src="{{ $moneyBox->getMainImageUrl() }}"
                 alt="{{ $moneyBox->title }}"
                 class="w-9 h-9 rounded-[6px] object-cover flex-none" />
        @else
            <div class="w-9 h-9 rounded-[6px] bg-[#1B6B4E] flex-none grid place-items-center">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
            </div>
        @endif
        <div class="flex-1 min-w-0">
            <div class="text-[13px] font-semibold text-white leading-tight truncate">{{ $moneyBox->title }}</div>
            <div class="text-[11px] text-white/50 mt-0.5">by {{ $moneyBox->user->name }}</div>
        </div>
        <a href="{{ route('box.show', $moneyBox->slug) }}" target="_blank"
           class="flex-none text-white/40 hover:text-white/80 transition" title="View full page">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
        </a>
    </div>

    {{-- Progress / stats band --}}
    @if($progress !== null)
        <div class="bg-[#F3F1EB] border-b border-[#E6E3DC] px-4 py-2.5">
            <div class="flex justify-between text-[11.5px] text-[#6B6862] mb-1.5">
                <span class="font-semibold text-[#15140F]">{{ $currency }}{{ number_format($moneyBox->total_contributions, 2) }} raised</span>
                <span>goal: {{ $currency }}{{ number_format($moneyBox->goal_amount, 2) }}</span>
            </div>
            <div class="h-1.5 bg-[#E6E3DC] rounded-full overflow-hidden">
                <div class="h-full bg-[#1B6B4E] rounded-full" style="width: {{ $progress }}%"></div>
            </div>
        </div>
    @else
        <div class="bg-[#F3F1EB] border-b border-[#E6E3DC] px-4 py-2">
            <span class="text-[12px] text-[#6B6862]">
                <span class="font-semibold text-[#15140F]">{{ $currency }}{{ number_format($moneyBox->total_contributions, 2) }}</span>
                collected · {{ number_format($moneyBox->contribution_count) }} {{ Str::plural('contribution', $moneyBox->contribution_count) }}
            </span>
        </div>
    @endif

    {{-- Form area --}}
    <div class="flex-1 px-4 py-4">

        @if(!$canContrib)
            <div class="text-center py-8">
                <div class="w-10 h-10 rounded-xl bg-[#F3F1EB] grid place-items-center mx-auto mb-3">
                    <svg class="w-5 h-5 text-[#9C998F]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25z"/></svg>
                </div>
                <p class="text-[13px] font-semibold text-[#15140F]">This box is closed</p>
                <p class="text-[12px] text-[#9C998F] mt-1">Contributions are no longer being accepted.</p>
            </div>
        @else

        @if($errors->any())
            <div class="mb-3 bg-red-50 border border-red-200/60 rounded-[8px] px-3 py-2.5 text-[12px] text-red-600 space-y-0.5">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @php
            $isFixed    = $amountType === \App\Enums\AmountType::Fixed;
            $isMinimum  = $amountType === \App\Enums\AmountType::Minimum;
            $isRange    = $amountType === \App\Enums\AmountType::Range;
        @endphp

        <form method="POST" action="{{ route('box.contribute', $moneyBox->slug) }}"
              x-data="{
                  amount: '{{ $isFixed ? $moneyBox->fixed_amount : '' }}',
                  customAmount: '',
                  isAnon: {{ $neverIdentify ? 'true' : 'false' }},
                  showCustom: false,
                  setPreset(val) {
                      this.amount = val;
                      this.showCustom = false;
                      this.customAmount = '';
                  },
                  setCustom() {
                      this.showCustom = true;
                      this.amount = '';
                      this.$nextTick(() => this.$refs.customInput.focus());
                  }
              }">
            @csrf

            <input type="hidden" name="amount" :value="showCustom ? customAmount : amount" />

            {{-- Amount --}}
            @if($isFixed)
                <div class="mb-4">
                    <div class="text-[11.5px] font-medium text-[#6B6862] uppercase tracking-wide mb-1.5">Amount</div>
                    <div class="bg-[#F3F1EB] border border-[#E6E3DC] rounded-[8px] px-3 py-2.5 text-[15px] font-semibold text-[#15140F]">
                        {{ $currency }}{{ number_format($moneyBox->fixed_amount, 2) }}
                    </div>
                </div>
            @else
                <div class="mb-4">
                    <div class="text-[11.5px] font-medium text-[#6B6862] uppercase tracking-wide mb-1.5">Choose amount</div>
                    <div class="flex flex-wrap gap-2 mb-2">
                        @foreach($presets as $p)
                            <button type="button"
                                    @click="setPreset('{{ $p }}')"
                                    :class="amount == '{{ $p }}' && !showCustom
                                        ? 'border-[#1B6B4E] bg-[#1B6B4E]/10 text-[#1B6B4E] font-semibold'
                                        : 'border-[#E6E3DC] text-[#6B6862] hover:border-[#D9D6CE]'"
                                    class="px-3 py-1.5 text-[12.5px] rounded-[7px] border transition-colors duration-100">
                                {{ $currency }}{{ number_format($p, 0) }}
                            </button>
                        @endforeach
                        <button type="button"
                                @click="setCustom()"
                                :class="showCustom
                                    ? 'border-[#1B6B4E] bg-[#1B6B4E]/10 text-[#1B6B4E] font-semibold'
                                    : 'border-[#E6E3DC] text-[#6B6862] hover:border-[#D9D6CE]'"
                                class="px-3 py-1.5 text-[12.5px] rounded-[7px] border transition-colors duration-100">
                            Custom
                        </button>
                    </div>
                    <div x-show="showCustom" x-transition class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[13px] text-[#9C998F] pointer-events-none">{{ $currency }}</span>
                        <input type="number"
                               x-ref="customInput"
                               x-model="customAmount"
                               placeholder="Enter amount"
                               min="{{ $moneyBox->minimum_amount ?? 0.01 }}"
                               @if($moneyBox->maximum_amount) max="{{ $moneyBox->maximum_amount }}" @endif
                               class="w-full pl-7 pr-3 py-2 text-[13px] border border-[#E6E3DC] rounded-[7px] bg-white focus:outline-none focus:border-[#1B6B4E]" />
                    </div>
                    @if($isMinimum && $moneyBox->minimum_amount)
                        <p class="text-[11px] text-[#9C998F] mt-1.5">Minimum: {{ $currency }}{{ number_format($moneyBox->minimum_amount, 2) }}</p>
                    @elseif($isRange && $moneyBox->minimum_amount && $moneyBox->maximum_amount)
                        <p class="text-[11px] text-[#9C998F] mt-1.5">{{ $currency }}{{ number_format($moneyBox->minimum_amount, 2) }} – {{ $currency }}{{ number_format($moneyBox->maximum_amount, 2) }}</p>
                    @endif
                </div>
            @endif

            {{-- Name --}}
            @if(!$neverIdentify)
                <div class="mb-3" x-show="!isAnon" x-transition>
                    <input type="text"
                           name="contributor_name"
                           placeholder="Your name"
                           value="{{ old('contributor_name') }}"
                           class="w-full px-3 py-2 text-[13px] border border-[#E6E3DC] rounded-[7px] bg-white focus:outline-none focus:border-[#1B6B4E]" />
                </div>
            @endif

            {{-- Email --}}
            <div class="mb-3">
                <input type="email"
                       name="contributor_email"
                       placeholder="Email address"
                       value="{{ old('contributor_email') }}"
                       required
                       class="w-full px-3 py-2 text-[13px] border border-[#E6E3DC] rounded-[7px] bg-white focus:outline-none focus:border-[#1B6B4E]" />
            </div>

            {{-- Anonymous toggle --}}
            @if(!$mustIdentify && !$neverIdentify)
                <div class="flex items-center gap-2 mb-4">
                    <input type="hidden" name="is_anonymous" :value="isAnon ? '1' : '0'" />
                    <button type="button"
                            @click="isAnon = !isAnon"
                            :class="isAnon ? 'bg-[#1B6B4E] border-[#1B6B4E]' : 'bg-white border-[#E6E3DC]'"
                            class="w-4 h-4 rounded border flex-none grid place-items-center transition-colors">
                        <svg x-show="isAnon" class="w-2.5 h-2.5 text-white" viewBox="0 0 10 8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 4l2.5 2.5L9 1"/></svg>
                    </button>
                    <span class="text-[12px] text-[#6B6862] cursor-pointer select-none" @click="isAnon = !isAnon">Contribute anonymously</span>
                </div>
            @elseif($neverIdentify)
                <input type="hidden" name="is_anonymous" value="1" />
            @endif

            <button type="submit"
                    class="w-full py-2.5 px-4 bg-[#1B6B4E] text-white text-[13.5px] font-semibold rounded-[8px] hover:bg-[#15563E] transition-colors duration-150">
                Contribute now →
            </button>
        </form>
        @endif
    </div>

    {{-- Footer --}}
    <div class="border-t border-[#E6E3DC] px-4 py-2.5 flex items-center justify-center gap-1.5">
        <svg class="w-3 h-3 text-[#9C998F]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
        <span class="text-[11px] text-[#9C998F]">Secured by</span>
        <a href="{{ route('home') }}" target="_blank"
           class="text-[11px] font-semibold text-[#15140F] hover:text-[#1B6B4E] transition-colors">
            MyMoneyBox
        </a>
    </div>

</div>
</body>
</html>