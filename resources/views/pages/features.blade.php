<x-layouts.guest>
<div
    x-data="{
        active: 'moneybox',
        init() {
            const obs = new IntersectionObserver(entries => {
                entries.forEach(e => { if (e.isIntersecting) this.active = e.target.dataset.section })
            }, { rootMargin: '-30% 0px -65% 0px', threshold: 0 })
            document.querySelectorAll('[data-section]').forEach(s => obs.observe(s))
        }
    }"
>

{{-- ── HERO ──────────────────────────────────────────────────────────── --}}
<section class="pt-16 pb-14 px-4">
    <div class="max-w-3xl mx-auto text-center">
        <span class="inline-block text-[11px] font-semibold tracking-[0.12em] uppercase text-[#1B6B4E] mb-5">How it works</span>
        <h1 class="text-4xl md:text-[52px] font-bold text-[#15140F] leading-[1.1] tracking-tight mb-5">
            Three tools.<br>Every way you collect.
        </h1>
        <p class="text-[17px] text-[#6B6862] leading-relaxed max-w-xl mx-auto mb-10">
            MyPiggyBox is built around three distinct products — each designed for a specific job. Here is what each one does, and whether it is the right fit for you.
        </p>
        <div class="flex flex-wrap gap-3 justify-center">
            <a href="#moneybox"
               class="group inline-flex items-center gap-2.5 px-5 py-2.5 rounded-full border border-[#E6E3DC] bg-white hover:border-emerald-200 hover:bg-emerald-50 transition-colors text-[13px] font-medium text-[#15140F]">
                <span class="w-2.5 h-2.5 rounded-full bg-[#1B6B4E] flex-none"></span>
                MoneyBox
                <span class="text-[#6B6862] group-hover:text-emerald-700 transition-colors">· Fundraising</span>
            </a>
            <a href="#piggy-wallet"
               class="group inline-flex items-center gap-2.5 px-5 py-2.5 rounded-full border border-[#E6E3DC] bg-white hover:border-amber-200 hover:bg-amber-50 transition-colors text-[13px] font-medium text-[#15140F]">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-600 flex-none"></span>
                Piggy Wallet
                <span class="text-[#6B6862] group-hover:text-amber-700 transition-colors">· Gift receiving</span>
            </a>
            <a href="#eventbox"
               class="group inline-flex items-center gap-2.5 px-5 py-2.5 rounded-full border border-[#E6E3DC] bg-white hover:border-[#15140F]/20 hover:bg-[#15140F]/5 transition-colors text-[13px] font-medium text-[#15140F]">
                <span class="w-2.5 h-2.5 rounded-full bg-[#15140F] flex-none"></span>
                EventBox
                <span class="text-[#6B6862] group-hover:text-[#15140F] transition-colors">· Ticketing</span>
            </a>
        </div>
    </div>
</section>

{{-- ── SCROLLSPY BAR ─────────────────────────────────────────────────── --}}
<div class="sticky top-14 z-40 bg-[#FAFAF7]/90 backdrop-blur-sm border-b border-[#E6E3DC]">
    <div class="max-w-5xl mx-auto px-4 flex">
        <button
            @click="document.getElementById('moneybox').scrollIntoView({behavior:'smooth',block:'start'})"
            :class="active==='moneybox' ? 'text-[#1B6B4E] border-b-2 border-[#1B6B4E]' : 'text-[#6B6862] border-b-2 border-transparent hover:text-[#15140F]'"
            class="px-4 py-3.5 text-[13px] font-medium transition-colors duration-150 whitespace-nowrap">
            MoneyBox
        </button>
        <button
            @click="document.getElementById('piggy-wallet').scrollIntoView({behavior:'smooth',block:'start'})"
            :class="active==='piggy-wallet' ? 'text-amber-700 border-b-2 border-amber-600' : 'text-[#6B6862] border-b-2 border-transparent hover:text-[#15140F]'"
            class="px-4 py-3.5 text-[13px] font-medium transition-colors duration-150 whitespace-nowrap">
            Piggy Wallet
        </button>
        <button
            @click="document.getElementById('eventbox').scrollIntoView({behavior:'smooth',block:'start'})"
            :class="active==='eventbox' ? 'text-[#15140F] border-b-2 border-[#15140F]' : 'text-[#6B6862] border-b-2 border-transparent hover:text-[#15140F]'"
            class="px-4 py-3.5 text-[13px] font-medium transition-colors duration-150 whitespace-nowrap">
            EventBox
        </button>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- MONEYBOX                                                               --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<section
    id="moneybox"
    data-section="moneybox"
    class="py-20 px-4 bg-[#FAFAF7] scroll-mt-28"
>
    <div class="max-w-6xl mx-auto">
        <div class="grid md:grid-cols-2 gap-12 lg:gap-20 items-center">

            {{-- Text --}}
            <div>
                <div class="flex items-center gap-2.5 mb-6">
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold tracking-[0.1em] uppercase text-[#1B6B4E]">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#1B6B4E] inline-block"></span>
                        MoneyBox
                    </span>
                    <span class="text-[#E6E3DC] select-none">·</span>
                    <span class="text-[12px] text-[#6B6862]">Fundraising campaigns</span>
                </div>

                <h2 class="text-3xl md:text-4xl font-bold text-[#15140F] leading-tight mb-4">
                    Raise money for any cause.<br>Withdraw when you're ready.
                </h2>
                <p class="text-[16px] text-[#6B6862] leading-relaxed mb-4">
                    A MoneyBox is your fundraising campaign page — a link and QR code you share with anyone. Visitors contribute directly by mobile money or card. No account required on their side, no bank details to share, no chasing for payments.
                </p>
                <p class="text-[16px] text-[#6B6862] leading-relaxed mb-8">
                    You control the settings: name a goal amount or run it open-ended, make it public on the platform or keep it private, let contributors show their names or go anonymous. Contributions land in real time, and you request a withdrawal to your registered mobile money account whenever you are ready.
                </p>

                <div class="mb-8">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-[#6B6862] mb-3">Common uses</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Weddings', 'Funerals', 'Medical bills', 'School fees', 'Church offerings', 'Community projects', 'Business startup', 'Birthday surprises'] as $use)
                            <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 text-[12px] font-medium border border-emerald-100">{{ $use }}</span>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1B6B4E] text-white text-[13px] font-semibold rounded-lg hover:bg-[#154F3A] transition-colors">
                    Create a MoneyBox — free
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>

            {{-- Campaign card mockup --}}
            <div class="relative">
                <div class="bg-white rounded-2xl border border-[#E6E3DC] p-6 shadow-sm">
                    <div class="flex items-start gap-3 mb-5">
                        <div class="w-11 h-11 rounded-xl bg-emerald-100 flex-none flex items-center justify-center text-emerald-700 font-bold text-base">K</div>
                        <div>
                            <p class="font-semibold text-[#15140F] text-[15px] leading-tight">Kwame & Ama's Wedding</p>
                            <p class="text-[12px] text-[#6B6862] mt-0.5">Created by Kwame Mensah · Public</p>
                        </div>
                    </div>

                    <div class="mb-5">
                        <div class="flex justify-between text-[12px] mb-1.5">
                            <span class="font-semibold text-[#15140F]">₵14,400 raised</span>
                            <span class="text-[#6B6862]">of ₵20,000 goal</span>
                        </div>
                        <div class="h-2 bg-[#F3F1EB] rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-[#1B6B4E]" style="width:72%"></div>
                        </div>
                        <p class="text-[11px] text-[#6B6862] mt-1.5">72% · 48 contributions</p>
                    </div>

                    <div class="space-y-2.5 mb-5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-[#F3F1EB] flex items-center justify-center text-[10px] font-semibold text-[#6B6862]">AB</div>
                                <span class="text-[13px] text-[#15140F]">Abena B.</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[13px] font-semibold text-[#15140F]">₵500</span>
                                <span class="text-[11px] text-[#6B6862] ml-1.5">2m ago</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-[#F3F1EB] flex items-center justify-center text-[10px] font-semibold text-[#6B6862]">KA</div>
                                <span class="text-[13px] text-[#15140F]">Kofi Asante</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[13px] font-semibold text-[#15140F]">₵200</span>
                                <span class="text-[11px] text-[#6B6862] ml-1.5">11m ago</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-[#F3F1EB] flex items-center justify-center text-[10px] font-semibold text-[#6B6862]">?</div>
                                <span class="text-[13px] text-[#15140F]">Anonymous</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[13px] font-semibold text-[#15140F]">₵1,000</span>
                                <span class="text-[11px] text-[#6B6862] ml-1.5">34m ago</span>
                            </div>
                        </div>
                    </div>

                    <button class="w-full py-2.5 rounded-lg bg-[#1B6B4E] text-white text-[13px] font-semibold">
                        Contribute to this campaign
                    </button>
                </div>

                {{-- Floating notification --}}
                <div class="absolute -bottom-4 -left-4 bg-white border border-[#E6E3DC] rounded-xl px-4 py-3 shadow-md flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-none">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="text-[12px] font-semibold text-[#15140F]">New contribution</p>
                        <p class="text-[11px] text-[#6B6862]">Ama O. just sent ₵300</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Steps --}}
        <div class="mt-16 pt-12 border-t border-[#E6E3DC]">
            <p class="text-[11px] font-semibold uppercase tracking-widest text-[#6B6862] mb-8">How it works</p>
            <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-6">
                @foreach([
                    ['n'=>'1','title'=>'Create your campaign','body'=>'Give it a title, describe what you are raising for, and optionally set a goal amount.'],
                    ['n'=>'2','title'=>'Share the link or QR','body'=>'Send by WhatsApp, post to social media, or print the QR code on your event invitation.'],
                    ['n'=>'3','title'=>'People pay directly','body'=>'MTN MoMo, Vodafone Cash, AirtelTigo, or bank card — no account needed on their side.'],
                    ['n'=>'4','title'=>'Watch it build up','body'=>'See every contribution in real time from your dashboard. Names shown or anonymous — your call.'],
                    ['n'=>'5','title'=>'Withdraw when ready','body'=>'Request a payout to your registered mobile money account. Fees apply only at withdrawal.'],
                ] as $s)
                    <div>
                        <span class="block text-[32px] font-bold text-[#1B6B4E] opacity-20 leading-none mb-2 select-none">{{ $s['n'] }}</span>
                        <h4 class="text-[14px] font-semibold text-[#15140F] mb-1.5">{{ $s['title'] }}</h4>
                        <p class="text-[13px] text-[#6B6862] leading-relaxed">{{ $s['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<div class="border-t border-[#E6E3DC]"></div>


{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- PIGGY WALLET                                                           --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<section
    id="piggy-wallet"
    data-section="piggy-wallet"
    class="py-20 px-4 bg-white scroll-mt-28"
>
    <div class="max-w-6xl mx-auto">
        <div class="grid md:grid-cols-2 gap-12 lg:gap-20 items-center">

            {{-- Wallet mockup (left on desktop) --}}
            <div class="relative md:order-1 order-2">
                <div class="bg-[#FAFAF7] rounded-2xl border border-[#E6E3DC] p-8">
                    <div class="text-center mb-6">
                        <p class="text-[11px] font-semibold uppercase tracking-widest text-[#6B6862] mb-3">Your piggy code</p>
                        <div class="inline-flex items-center px-6 py-4 bg-white rounded-xl border border-[#E6E3DC] shadow-sm">
                            <span class="text-[40px] font-bold tracking-[0.15em] text-[#15140F] leading-none">ABC45</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg border border-[#E6E3DC] px-4 py-3 mb-5 flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#6B6862] flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        <span class="text-[12px] text-[#6B6862] flex-1 truncate">mypiggybox.com/piggy/<strong class="text-[#15140F]">ABC45</strong></span>
                        <span class="text-[11px] font-semibold text-amber-700">Copy</span>
                    </div>

                    {{-- QR code (CSS art, 7×7) --}}
                    <div class="flex justify-center mb-5">
                        <div class="border border-[#E6E3DC] rounded-lg p-2.5" style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px;width:96px;height:96px">
                            @php
                                $qr = [1,1,1,1,1,1,0, 1,0,0,0,0,1,0, 1,0,1,1,1,0,1, 1,0,0,0,0,1,0, 1,1,1,1,1,1,0, 0,1,0,1,0,0,1, 0,0,1,0,1,1,0];
                            @endphp
                            @foreach($qr as $bit)
                                <div class="rounded-[1px]" style="background:{{ $bit ? '#15140F' : '#F3F1EB' }}"></div>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        @foreach(['WhatsApp', 'Copy link', 'QR Code'] as $btn)
                            <div class="py-2 rounded-lg border border-[#E6E3DC] text-center text-[11px] font-medium text-[#6B6862]">{{ $btn }}</div>
                        @endforeach
                    </div>
                </div>

                {{-- Floating gift notification --}}
                <div class="absolute -top-4 -right-4 bg-white border border-[#E6E3DC] rounded-xl px-4 py-3 shadow-md flex items-center gap-3">
                    <span class="text-xl leading-none">🎉</span>
                    <div>
                        <p class="text-[12px] font-semibold text-[#15140F]">Gift received!</p>
                        <p class="text-[11px] text-[#6B6862]">Mama sent you ₵500</p>
                    </div>
                </div>
            </div>

            {{-- Text (right on desktop) --}}
            <div class="md:order-2 order-1">
                <div class="flex items-center gap-2.5 mb-6">
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold tracking-[0.1em] uppercase text-amber-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-600 inline-block"></span>
                        Piggy Wallet
                    </span>
                    <span class="text-[#E6E3DC] select-none">·</span>
                    <span class="text-[12px] text-[#6B6862]">Gift receiving</span>
                </div>

                <h2 class="text-3xl md:text-4xl font-bold text-[#15140F] leading-tight mb-4">
                    Your permanent gift address.
                </h2>
                <p class="text-[16px] text-[#6B6862] leading-relaxed mb-4">
                    Unlike a MoneyBox campaign that you create for one specific occasion, your Piggy Wallet is always there — permanent, personal, and tied to your account. It is your digital identity for receiving gifts.
                </p>
                <p class="text-[16px] text-[#6B6862] leading-relaxed mb-4">
                    You get a short code like <strong class="text-[#15140F] font-semibold">ABC45</strong>, which maps to your personal link. Share it on your birthday, your graduation, or keep it in your social media bio year-round. Whoever visits that link can send you a gift by mobile money or card — no account needed on their side, no awkward bank details to share.
                </p>
                <p class="text-[16px] text-[#6B6862] leading-relaxed mb-8">
                    Gifts land in your wallet instantly. The link never expires, never changes, and never needs setting up again for the next occasion.
                </p>

                <div class="mb-8">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-[#6B6862] mb-3">Common uses</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Birthdays', 'Anniversaries', 'Graduations', 'Baby showers', 'Wedding shower', 'Social media bio', 'Appreciation gifts'] as $use)
                            <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-800 text-[12px] font-medium border border-amber-100">{{ $use }}</span>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-600 text-white text-[13px] font-semibold rounded-lg hover:bg-amber-700 transition-colors">
                    Get your Piggy Wallet — free
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>
        </div>

        {{-- Steps --}}
        <div class="mt-16 pt-12 border-t border-[#E6E3DC]">
            <p class="text-[11px] font-semibold uppercase tracking-widest text-[#6B6862] mb-8">How it works</p>
            <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-6">
                @foreach([
                    ['n'=>'1','title'=>'Yours from the moment you sign up','body'=>'Your Piggy Wallet exists automatically — nothing to configure or create.'],
                    ['n'=>'2','title'=>'Find your piggy code','body'=>'Open your dashboard. You will see your short code (e.g. ABC45) and your personal gift link.'],
                    ['n'=>'3','title'=>'Share it anywhere','body'=>'WhatsApp, your Instagram bio, an invitation card — wherever you want people to find you.'],
                    ['n'=>'4','title'=>'Receive gifts instantly','body'=>'Senders open your link and pay by mobile money or card. No account required on their side.'],
                    ['n'=>'5','title'=>'Withdraw on your schedule','body'=>'Gifts accumulate in your wallet. Request a payout to mobile money whenever you choose.'],
                ] as $s)
                    <div>
                        <span class="block text-[32px] font-bold text-amber-600 opacity-20 leading-none mb-2 select-none">{{ $s['n'] }}</span>
                        <h4 class="text-[14px] font-semibold text-[#15140F] mb-1.5">{{ $s['title'] }}</h4>
                        <p class="text-[13px] text-[#6B6862] leading-relaxed">{{ $s['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<div class="border-t border-[#E6E3DC]"></div>


{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- EVENTBOX — dark stage section                                          --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<section
    id="eventbox"
    data-section="eventbox"
    class="py-20 px-4 bg-[#15140F] scroll-mt-28"
>
    <div class="max-w-6xl mx-auto">
        <div class="grid md:grid-cols-2 gap-12 lg:gap-20 items-center">

            {{-- Text --}}
            <div>
                <div class="flex items-center gap-2.5 mb-6">
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold tracking-[0.1em] uppercase text-[#9C998F]">
                        <span class="w-2.5 h-2.5 rounded-full bg-white inline-block"></span>
                        EventBox
                    </span>
                    <span class="text-white/20 select-none">·</span>
                    <span class="text-[12px] text-[#9C998F]">Event ticketing</span>
                </div>

                <h2 class="text-3xl md:text-4xl font-bold text-white leading-tight mb-4">
                    Sell tickets.<br>Scan them at the door.
                </h2>
                <p class="text-[16px] text-[#9C998F] leading-relaxed mb-4">
                    EventBox is a complete ticketing system for events in Ghana. Create your event, define as many ticket types as you need — VIP, Regular, Early Bird, Student — each with its own price and capacity. Share the event page and buyers pay online by mobile money or card.
                </p>
                <p class="text-[16px] text-[#9C998F] leading-relaxed mb-4">
                    Every buyer receives a QR-coded ticket by email immediately after payment. At the venue, you validate tickets from your phone — the system flags duplicates, voided entries, and invalid codes in real time.
                </p>
                <p class="text-[16px] text-[#9C998F] leading-relaxed mb-8">
                    If a buyer cannot attend, you can void their ticket from your dashboard and issue a refund directly to their mobile money account. Sold-out tracking updates automatically as tickets sell.
                </p>

                <div class="mb-8">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-[#6B6862] mb-3">Common uses</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Concerts', 'Comedy shows', 'Conferences', 'Church events', 'Workshops', 'School events', 'Private parties', 'Award nights'] as $use)
                            <span class="px-3 py-1 rounded-full text-[12px] font-medium border text-white/60 border-white/10 bg-white/5">{{ $use }}</span>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-[#15140F] text-[13px] font-semibold rounded-lg hover:bg-[#F3F1EB] transition-colors">
                    Create an event — free
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>

            {{-- Ticket mockup --}}
            <div class="relative">
                <div class="bg-white rounded-2xl overflow-hidden shadow-2xl">
                    {{-- Header --}}
                    <div class="bg-[#1B6B4E] px-6 py-5">
                        <p class="text-emerald-200 text-[10px] font-semibold uppercase tracking-widest mb-1.5">EventBox · Official Ticket</p>
                        <h3 class="text-white font-bold text-[18px] leading-snug">Jazz Night at Alliance Française</h3>
                        <div class="flex items-center gap-3 mt-2">
                            <span class="text-emerald-200 text-[12px]">Sat 28 Jun 2026</span>
                            <span class="text-emerald-300/40">·</span>
                            <span class="text-emerald-200 text-[12px]">7:00 PM</span>
                            <span class="text-emerald-300/40">·</span>
                            <span class="text-emerald-200 text-[12px]">Accra</span>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="px-6 py-5">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-[10px] text-[#6B6862] uppercase tracking-widest mb-1">Ticket holder</p>
                                <p class="font-semibold text-[#15140F] text-[14px]">Kofi Mensah</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-[#6B6862] uppercase tracking-widest mb-1">Type</p>
                                <p class="font-semibold text-[#15140F] text-[14px]">VIP · ₵150</p>
                            </div>
                        </div>

                        <div class="border-t border-dashed border-[#E6E3DC] my-4"></div>

                        <div class="flex items-center justify-between gap-4">
                            {{-- QR --}}
                            <div class="border border-[#E6E3DC] rounded-lg p-2" style="display:grid;grid-template-columns:repeat(6,1fr);gap:2px;width:72px;height:72px">
                                @php $tqr = [1,1,1,0,1,1, 1,0,1,1,0,1, 1,1,0,0,1,0, 0,1,0,1,1,1, 1,0,1,0,0,1, 1,1,0,1,0,0]; @endphp
                                @foreach($tqr as $bit)
                                    <div class="rounded-[1px]" style="background:{{ $bit ? '#15140F' : '#F3F1EB' }}"></div>
                                @endforeach
                            </div>

                            <div class="flex-1 text-right">
                                <p class="text-[10px] text-[#6B6862] mb-1.5">Ticket code</p>
                                <p class="font-mono text-[12px] font-bold text-[#15140F] tracking-wider leading-tight">TKT-8K2M</p>
                                <p class="font-mono text-[12px] font-bold text-[#15140F] tracking-wider leading-tight">QRXY-5N7P</p>
                                <div class="mt-2.5 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    VALID
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Floating scan confirmation --}}
                <div class="absolute -bottom-4 -right-4 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl px-4 py-3 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center flex-none">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <p class="text-[12px] font-semibold text-white">Ticket scanned</p>
                        <p class="text-[11px] text-white/60">Entry approved · gate 1</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Steps — two-phase layout for EventBox --}}
        <div class="mt-16 pt-12 border-t border-white/10">
            <p class="text-[11px] font-semibold uppercase tracking-widest text-[#6B6862] mb-8">How it works</p>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-10">

                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-[#6B6862] mb-5">Before the event</p>
                    <div class="space-y-5">
                        @foreach([
                            ['n'=>'1','title'=>'Create your event','body'=>'Set a title, date, venue, cover image, and description. Takes under five minutes.'],
                            ['n'=>'2','title'=>'Define your ticket types','body'=>'Name each tier — VIP, Regular, Early Bird — and set a price and seat capacity. Or leave capacity unlimited.'],
                            ['n'=>'3','title'=>'Share your event page','body'=>'Works on any phone. Buyers pay by mobile money or card and receive a QR-coded ticket by email within seconds.'],
                        ] as $s)
                            <div class="flex gap-3">
                                <span class="text-[22px] font-bold text-white/15 leading-none flex-none w-5 text-right select-none">{{ $s['n'] }}</span>
                                <div>
                                    <h4 class="text-[14px] font-semibold text-white mb-1">{{ $s['title'] }}</h4>
                                    <p class="text-[13px] text-[#9C998F] leading-relaxed">{{ $s['body'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-[#6B6862] mb-5">At the venue</p>
                    <div class="space-y-5">
                        @foreach([
                            ['n'=>'4','title'=>'Validate tickets at the door','body'=>'Open the validator on your phone. Scan or type each ticket code — duplicates and voided tickets are flagged instantly.'],
                            ['n'=>'5','title'=>'Mark attendees as they enter','body'=>'Redeem each ticket on entry. A redeemed ticket cannot be scanned again.'],
                        ] as $s)
                            <div class="flex gap-3">
                                <span class="text-[22px] font-bold text-white/15 leading-none flex-none w-5 text-right select-none">{{ $s['n'] }}</span>
                                <div>
                                    <h4 class="text-[14px] font-semibold text-white mb-1">{{ $s['title'] }}</h4>
                                    <p class="text-[13px] text-[#9C998F] leading-relaxed">{{ $s['body'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-[#6B6862] mb-5">Safety net</p>
                    <div class="space-y-3">
                        @foreach([
                            'Void any ticket from your dashboard if a buyer cannot attend',
                            'Issue refunds directly to the buyer\'s mobile money account',
                            'Export your attendee list as a CSV for your records',
                            'Sold-out tracking updates automatically as tickets sell',
                            'Resend lost ticket emails from your dashboard',
                        ] as $item)
                            <div class="flex items-start gap-2.5">
                                <svg class="w-3.5 h-3.5 text-[#6B6862] mt-0.5 flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <p class="text-[13px] text-[#9C998F] leading-relaxed">{{ $item }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- ── CLOSING CTA ───────────────────────────────────────────────────── --}}
<section class="py-16 px-4 bg-gradient-to-br from-[#1B6B4E] to-[#0F3326]">
    <div class="max-w-3xl mx-auto text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
            Pick your tool. Start in minutes.
        </h2>
        <p class="text-[16px] text-emerald-100 mb-8 max-w-xl mx-auto leading-relaxed">
            One account gives you access to all three. MoneyBox, Piggy Wallet, and EventBox — free to start, no card required.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('register') }}"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white text-[#1B6B4E] font-bold rounded-lg hover:bg-emerald-50 transition-colors text-[14px]">
                Create your free account
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
            <a href="{{ route('browse') }}"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-transparent border border-emerald-300/60 text-emerald-100 font-medium rounded-lg hover:bg-white/10 transition-colors text-[14px]">
                Browse active campaigns
            </a>
        </div>
    </div>
</section>

</div>
</x-layouts.guest>