<x-layouts.guest>
    <div class="min-h-screen bg-[#FAFAF7] py-6 sm:py-10" x-data="{ showToast: false, toastMessage: '' }">

        {{-- Toast --}}
        <div
            x-show="showToast"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed top-4 right-4 z-50 flex items-center gap-2.5 px-4 py-3 bg-[#15140F] text-white text-[13px] rounded-[8px] shadow-xl"
            style="display: none;"
            x-cloak>
            <svg viewBox="0 0 24 24" class="w-4 h-4 text-primary-400 flex-none" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="m5 12 5 5L20 7"/>
            </svg>
            <span x-text="toastMessage"></span>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">

                {{-- Main content --}}
                <div class="lg:col-span-2 space-y-5">

                    {{-- Main image --}}
                    @if($moneyBox->hasMedia('main'))
                        <div class="card overflow-hidden">
                            <img src="{{ $moneyBox->getMainImageUrl() }}"
                                 alt="{{ $moneyBox->title }}"
                                 class="w-full h-64 sm:h-80 object-cover">
                        </div>
                    @endif

                    {{-- Box details --}}
                    <div class="card">
                        <div class="card-body">
                            {{-- Category --}}
                            @if($moneyBox->category)
                                <div class="mb-3">
                                    <span class="pill pill-info">
                                        {{ $moneyBox->category->icon }} {{ $moneyBox->category->name }}
                                    </span>
                                </div>
                            @endif

                            {{-- Title --}}
                            <h1 class="text-[22px] sm:text-[26px] font-semibold text-[#15140F] tracking-tight leading-snug mb-3">
                                {{ $moneyBox->title }}
                            </h1>

                            {{-- Creator --}}
                            <div class="flex items-center gap-2 mb-5 text-[13px] text-[#6B6862]">
                                <svg viewBox="0 0 24 24" class="w-4 h-4 flex-none" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                </svg>
                                <span>Created by <span class="font-medium text-[#15140F]">{{ $moneyBox->user->name }}</span></span>
                                <x-verification-badge :user="$moneyBox->user" />
                            </div>

                            {{-- Progress --}}
                            @if($moneyBox->goal_amount)
                                @php $pct = min(100, $moneyBox->getProgressPercentage()); @endphp
                                <div class="mb-5">
                                    <div class="flex items-baseline justify-between mb-2">
                                        <span class="text-[13px] font-semibold text-[#15140F] tnum">
                                            {{ $moneyBox->formatAmount($moneyBox->total_contributions) }}
                                            <span class="muted font-normal text-[12px]">of {{ $moneyBox->formatAmount($moneyBox->goal_amount) }}</span>
                                        </span>
                                        <span class="tiny tnum">{{ number_format($pct, 1) }}%</span>
                                    </div>
                                    <div class="progress-track">
                                        <div class="progress-fill" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            @endif

                            {{-- Stats --}}
                            <div class="grid grid-cols-2 gap-4 p-4 bg-[#F3F1EB] rounded-[8px] mb-5">
                                <div>
                                    <div class="text-[22px] font-semibold text-[#15140F] tnum tracking-tight">
                                        {{ $moneyBox->formatAmount($moneyBox->total_contributions) }}
                                    </div>
                                    <div class="tiny mt-0.5">
                                        @if($moneyBox->goal_amount) of {{ $moneyBox->formatAmount($moneyBox->goal_amount) }} goal
                                        @else raised @endif
                                    </div>
                                </div>
                                <div>
                                    <div class="text-[22px] font-semibold text-[#15140F] tnum tracking-tight">
                                        {{ $moneyBox->contribution_count }}
                                    </div>
                                    <div class="tiny mt-0.5">{{ Str::plural('contribution', $moneyBox->contribution_count) }}</div>
                                </div>
                            </div>

                            {{-- Description --}}
                            @if($moneyBox->description)
                                <div>
                                    <h2 class="text-[14px] font-semibold text-[#15140F] mb-2">About</h2>
                                    <p class="text-[13.5px] text-[#6B6862] whitespace-pre-line leading-relaxed">{{ $moneyBox->description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Gallery --}}
                    @if($moneyBox->hasMedia('gallery'))
                        <div class="card">
                            <div class="card-head">
                                <span class="card-title">Gallery</span>
                            </div>
                            <div class="card-body">
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    @foreach($moneyBox->getMedia('gallery') as $image)
                                        <a href="{{ $image->getTemporaryUrl(now()->addHour()) }}" target="_blank" class="group">
                                            <img src="{{ $image->getTemporaryUrl(now()->addHour()) }}"
                                                 alt="Gallery image"
                                                 class="w-full h-32 sm:h-44 object-cover rounded-[6px] transition-transform group-hover:scale-[1.02]">
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Recent contributions --}}
                    @if($moneyBox->contributions->count() > 0)
                        <div class="card">
                            <div class="card-head">
                                <span class="card-title">Recent contributions</span>
                            </div>
                            <table class="data-table">
                                <tbody>
                                    @foreach($moneyBox->contributions as $contribution)
                                        <tr>
                                            <td>
                                                <div class="flex items-center gap-2.5">
                                                    <div class="w-7 h-7 rounded-full bg-primary-50 text-primary-600 grid place-items-center text-[11px] font-semibold flex-none">
                                                        {{ substr($contribution->getDisplayName(), 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="text-[13px] font-medium text-[#15140F]">{{ $contribution->getDisplayName() }}</div>
                                                        @if($contribution->message)
                                                            <div class="tiny line-clamp-1">{{ $contribution->message }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="tiny text-right">{{ $contribution->created_at->diffForHumans() }}</td>
                                            <td class="num font-semibold text-[#15140F] tnum">{{ $moneyBox->formatAmount($contribution->amount) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <div class="lg:col-span-1">
                    <div class="card lg:sticky lg:top-[72px]">
                        <div class="card-body">
                            @if($moneyBox->canAcceptContributions())
                                <h2 class="text-[15px] font-semibold text-[#15140F] mb-4">Make a contribution</h2>

                                <form method="POST" action="{{ route('box.contribute', $moneyBox->slug) }}" class="space-y-4">
                                    @csrf

                                    <div class="grid gap-1.5">
                                        <label for="amount" class="text-[13px] font-medium text-[#6B6862]">
                                            Amount ({{ $moneyBox->getCurrencySymbol() }}) <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" name="amount" id="amount" step="0.01" min="0.01" required
                                               placeholder="0.00" value="{{ old('amount') }}"
                                               class="@error('amount') border-red-400 @enderror" />
                                        @error('amount')
                                            <p class="text-[12px] text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="grid gap-1.5">
                                        <label for="contributor_name" class="text-[13px] font-medium text-[#6B6862]">Your name</label>
                                        <input type="text" name="contributor_name" id="contributor_name"
                                               value="{{ old('contributor_name', auth()->user()->name ?? '') }}" />
                                        @error('contributor_name')
                                            <p class="text-[12px] text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="grid gap-1.5">
                                        <label for="contributor_email" class="text-[13px] font-medium text-[#6B6862]">
                                            Email address <span class="text-red-500">*</span>
                                        </label>
                                        <input type="email" name="contributor_email" id="contributor_email" required
                                               value="{{ old('contributor_email', auth()->user()->email ?? '') }}"
                                               class="@error('contributor_email') border-red-400 @enderror" />
                                        @error('contributor_email')
                                            <p class="text-[12px] text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="grid gap-1.5">
                                        <label for="message" class="text-[13px] font-medium text-[#6B6862]">
                                            Message <span class="text-[#9C998F] font-normal">(optional)</span>
                                        </label>
                                        <textarea name="message" id="message" rows="3"
                                                  placeholder="Leave a kind message…"
                                                  class="@error('message') border-red-400 @enderror">{{ old('message') }}</textarea>
                                        @error('message')
                                            <p class="text-[12px] text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" name="is_anonymous" id="is_anonymous" value="1"
                                               class="rounded border-[#D9D6CE] text-primary-600"
                                               {{ old('is_anonymous') ? 'checked' : '' }} />
                                        <label for="is_anonymous" class="text-[13px] text-[#15140F]">Contribute anonymously</label>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-full justify-center py-2.5">
                                        Proceed to payment
                                    </button>
                                </form>
                            @else
                                <div class="text-center py-8">
                                    <div class="w-12 h-12 rounded-xl bg-[#F3F1EB] grid place-items-center mx-auto mb-3">
                                        <svg viewBox="0 0 24 24" class="w-6 h-6 text-[#9C998F]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                            <rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-[14px] font-semibold text-[#15140F] mb-1">Not accepting contributions</h3>
                                    <p class="tiny">This box is currently not accepting contributions.</p>
                                </div>
                            @endif

                            {{-- Share --}}
                            <div class="mt-5 pt-5 border-t border-[#E6E3DC]">
                                <h3 class="text-[13px] font-semibold text-[#15140F] mb-3">Share this campaign</h3>
                                <div class="space-y-2">
                                    <button
                                        type="button"
                                        @click="navigator.clipboard.writeText('{{ $moneyBox->getPublicUrl() }}').then(() => { toastMessage = 'Link copied!'; showToast = true; setTimeout(() => showToast = false, 3000); })"
                                        class="btn w-full justify-center">
                                        <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <rect width="14" height="14" x="8" y="8" rx="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>
                                        </svg>
                                        Copy link
                                    </button>

                                    @if($moneyBox->hasQrCode())
                                        <a href="{{ $moneyBox->getQrCodeUrl() }}" download target="_blank" class="btn w-full justify-center">
                                            <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M12 15V3m0 12-4-4m4 4 4-4"/><path d="M2 17l.621 2.485A2 2 0 0 0 4.561 21h14.878a2 2 0 0 0 1.94-1.515L22 17"/>
                                            </svg>
                                            Download QR code
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layouts.guest>