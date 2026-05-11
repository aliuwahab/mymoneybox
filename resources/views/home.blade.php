<x-layouts.guest>
    {{-- Hero --}}
    <div class="relative bg-[#15140F] overflow-hidden">
        {{-- Subtle grid pattern --}}
        <div class="absolute inset-0 opacity-[0.04]">
            <svg class="absolute w-full h-full" xmlns="http://www.w3.org/2000/svg">
                <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                    <circle cx="20" cy="20" r="1" fill="white"/>
                </pattern>
                <rect width="100%" height="100%" fill="url(#grid)"/>
            </svg>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                {{-- Hero text --}}
                <div class="text-white">
                    <div class="inline-block mb-4 px-3 py-1.5 bg-white/10 rounded-full text-[12.5px] font-medium border border-white/15 text-[#FAFAF7]">
                        The modern way to collect & give
                    </div>
                    <h1 class="font-serif text-[2.75rem] md:text-[3.5rem] leading-[1.05] tracking-tight mb-5">
                        Collect Gifts & Contributions <span class="text-primary-400">The Easy Way</span>
                    </h1>
                    <p class="text-[#9C998F] text-[15px] md:text-[16px] mb-8 leading-relaxed max-w-lg">
                        From weddings to charities, birthdays to tithes—everyone contributes with just a link, QR code, or Piggy Number. <strong class="text-[#FAFAF7] font-medium">Transparent. Accessible. Trusted.</strong>
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('piggy.lookup') }}" class="btn btn-primary justify-center">
                            Send a gift
                        </a>
                        <a href="{{ route('register') }}" class="btn justify-center bg-white/10 border-white/20 text-white hover:bg-white/15">
                            Create your box
                        </a>
                        <a href="{{ route('browse') }}" class="btn justify-center bg-transparent border-white/15 text-[#9C998F] hover:bg-white/5 hover:text-white">
                            Browse boxes
                        </a>
                    </div>

                    {{-- Platform stats --}}
                    <div class="mt-10 flex gap-8">
                        <div>
                            <div class="text-[24px] font-semibold text-white tnum">{{ \App\Models\MoneyBox::count() }}+</div>
                            <div class="text-[12px] text-[#6B6862]">Money boxes</div>
                        </div>
                        <div>
                            <div class="text-[24px] font-semibold text-white tnum">{{ \App\Models\Contribution::count() }}+</div>
                            <div class="text-[12px] text-[#6B6862]">Contributions</div>
                        </div>
                        <div>
                            <div class="text-[24px] font-semibold text-white tnum">{{ \App\Models\User::count() }}+</div>
                            <div class="text-[12px] text-[#6B6862]">Users</div>
                        </div>
                    </div>
                </div>

                {{-- Sample cards --}}
                <div class="relative hidden lg:grid grid-cols-2 gap-4">
                    @php
                        $samples = [
                            ['label' => 'Wedding', 'sub' => 'Sarah & John', 'color' => 'from-[#1B6B4E] to-[#2E8E6C]', 'pct' => 75, 'raised' => 'GH₵7,500', 'goal' => 'GH₵10,000', 'donors' => 42],
                            ['label' => 'Church', 'sub' => 'Grace Chapel', 'color' => 'from-[#3F2A6E] to-[#6B4DB8]', 'pct' => 65, 'raised' => 'GH₵3,250', 'goal' => 'GH₵5,000', 'donors' => 28],
                            ['label' => 'Birthday', 'sub' => 'For Emma', 'color' => 'from-[#B8810D] to-[#E0A535]', 'pct' => 45, 'raised' => 'GH₵450', 'goal' => 'GH₵1,000', 'donors' => 18],
                            ['label' => 'Medical', 'sub' => 'For Michael', 'color' => 'from-[#883647] to-[#B85773]', 'pct' => 82, 'raised' => 'GH₵16,400', 'goal' => 'GH₵20,000', 'donors' => 156],
                        ];
                    @endphp
                    @foreach($samples as $s)
                        <div class="bg-white/5 border border-white/10 rounded-[12px] p-4 backdrop-blur-sm">
                            <div class="h-16 bg-gradient-to-br {{ $s['color'] }} rounded-[8px] mb-3 grid place-items-center">
                                <span class="font-serif text-[22px] text-white/80">{{ substr($s['label'], 0, 1) }}</span>
                            </div>
                            <div class="text-[11px] font-medium text-[#9C998F] mb-0.5">{{ $s['label'] }}</div>
                            <div class="text-[12px] font-semibold text-white mb-2">{{ $s['sub'] }}</div>
                            <div class="h-1.5 bg-white/10 rounded-full overflow-hidden mb-2">
                                <div class="h-full bg-primary-400 rounded-full" style="width: {{ $s['pct'] }}%"></div>
                            </div>
                            <div class="flex justify-between items-baseline">
                                <div>
                                    <div class="text-[13px] font-semibold text-white tnum">{{ $s['raised'] }}</div>
                                    <div class="text-[10.5px] text-[#6B6862]">of {{ $s['goal'] }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[13px] font-semibold text-white tnum">{{ $s['donors'] }}</div>
                                    <div class="text-[10.5px] text-[#6B6862]">donors</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Features --}}
    <div class="py-20 bg-[#FAFAF7]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h2 class="font-serif text-[2rem] md:text-[2.375rem] leading-tight tracking-tight text-[#15140F] mb-3">Why people choose MyMoneyBox</h2>
                <p class="text-[14.5px] text-[#6B6862] max-w-xl mx-auto">Modernize the age-old practice of communal contributions with complete transparency and security</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @php
                    $features = [
                        ['icon' => '<path d="M13 10V3L4 14h7v7l9-11h-7z"/>', 'title' => 'Lightning fast setup', 'desc' => 'Create your box in under 2 minutes. No complicated forms, no hassle—just simple, intuitive design.'],
                        ['icon' => '<rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>', 'title' => 'Bank-level security', 'desc' => 'Accept payments via cards and mobile money with enterprise-grade encryption. Data always protected.'],
                        ['icon' => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.59 13.51 6.83 3.98M15.41 6.51l-6.82 3.98"/>', 'title' => 'Share anywhere', 'desc' => 'QR codes for events, unique links for social media, or your Piggy Number—reach contributors on any platform.'],
                    ];
                @endphp
                @foreach($features as $f)
                    <div class="card p-6">
                        <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 grid place-items-center mb-4">
                            <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">{!! $f['icon'] !!}</svg>
                        </div>
                        <h3 class="text-[14px] font-semibold text-[#15140F] mb-1.5">{{ $f['title'] }}</h3>
                        <p class="text-[13px] text-[#6B6862] leading-relaxed">{{ $f['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Use cases --}}
    <div class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h2 class="font-serif text-[2rem] md:text-[2.375rem] leading-tight tracking-tight text-[#15140F] mb-3">Perfect for every occasion</h2>
                <p class="text-[14.5px] text-[#6B6862]">From personal milestones to community causes—MyMoneyBox adapts to your needs</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    $cases = [
                        ['emoji' => '💍', 'title' => 'Weddings & Celebrations', 'desc' => 'Guests scan QR codes at tables to send gifts instantly. Modern, seamless, and memorable.'],
                        ['emoji' => '⛪', 'title' => 'Church & Tithes', 'desc' => 'A permanent box for offerings, tithes, and donations. Transparent and convenient for your congregation.'],
                        ['emoji' => '🎓', 'title' => 'Group Events & Trips', 'desc' => 'Friends split costs for dinners, trips, or activities. Everyone contributes their share hassle-free.'],
                        ['emoji' => '🏥', 'title' => 'Medical Fundraisers', 'desc' => 'Rally community support for medical needs. Track progress and thank donors in real-time.'],
                        ['emoji' => '💰', 'title' => 'Tips & Gratuities', 'desc' => 'Service workers receive tips via their Piggy Number or QR code. Digital tipping made simple.'],
                        ['emoji' => '🎯', 'title' => 'Community Projects', 'desc' => 'Political parties, clubs, and societies raise funds with clarity and accountability.'],
                    ];
                @endphp
                @foreach($cases as $c)
                    <div class="card p-5 hover:shadow-[0_1px_0_rgba(20,18,12,.04),0_8px_24px_-8px_rgba(20,18,12,.10)] transition-shadow duration-150">
                        <div class="text-[30px] mb-3">{{ $c['emoji'] }}</div>
                        <h3 class="text-[14px] font-semibold text-[#15140F] mb-1.5">{{ $c['title'] }}</h3>
                        <p class="text-[13px] text-[#6B6862] leading-relaxed">{{ $c['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Featured boxes --}}
    <div class="py-20 bg-[#F3F1EB]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <h2 class="font-serif text-[2rem] md:text-[2.375rem] leading-tight tracking-tight text-[#15140F] mb-1">Featured boxes</h2>
                    <p class="text-[13.5px] text-[#6B6862]">Support these amazing causes</p>
                </div>
                <a href="{{ route('browse') }}" class="text-[13px] font-medium text-primary-600 hover:text-primary-700 flex items-center gap-1">
                    View all
                    <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>
            </div>

            @if($featuredMoneyBoxes->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($featuredMoneyBoxes as $moneyBox)
                        <x-money-box-card :moneyBox="$moneyBox" />
                    @endforeach
                </div>
            @else
                <div class="border-2 border-dashed border-[#D9D6CE] rounded-[10px] p-12 text-center">
                    <h3 class="text-[15px] font-semibold text-[#15140F] mb-1">No boxes yet</h3>
                    <p class="tiny mb-5">Be the first to create one!</p>
                    <a href="{{ route('register') }}" class="btn btn-primary">Create a box</a>
                </div>
            @endif
        </div>
    </div>

    {{-- How it works --}}
    <div class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h2 class="font-serif text-[2rem] md:text-[2.375rem] leading-tight tracking-tight text-[#15140F] mb-3">Get started in 3 simple steps</h2>
                <p class="text-[14.5px] text-[#6B6862]">From creation to collection—it takes less than 5 minutes</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                @php
                    $steps = [
                        ['n' => '1', 'title' => 'Create your box', 'desc' => 'Sign up free and set up your box in under 2 minutes. Add a title, goal, and description.'],
                        ['n' => '2', 'title' => 'Share with anyone', 'desc' => 'Get your unique link, QR code, or Piggy Number. Share on WhatsApp, Facebook, or print for events.'],
                        ['n' => '3', 'title' => 'Track & collect', 'desc' => 'Watch contributions come in real-time. Withdraw anytime. 100% transparent and secure.'],
                    ];
                @endphp
                @foreach($steps as $step)
                    <div class="text-center">
                        <div class="w-14 h-14 rounded-full bg-[#15140F] text-white grid place-items-center mx-auto mb-5 text-[22px] font-semibold font-serif">
                            {{ $step['n'] }}
                        </div>
                        <h3 class="text-[15px] font-semibold text-[#15140F] mb-2">{{ $step['title'] }}</h3>
                        <p class="text-[13px] text-[#6B6862] leading-relaxed">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('register') }}" class="btn btn-primary px-6 py-2.5 text-[13.5px]">
                    Start collecting now — it's free
                    <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
                </a>
                <p class="text-[12px] text-[#9C998F] mt-3">No credit card required · Setup in 2 minutes · Free forever</p>
            </div>
        </div>
    </div>

    {{-- CTA --}}
    <div class="bg-[#15140F] py-16">
        <div class="max-w-3xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="font-serif text-[2rem] md:text-[2.375rem] leading-tight tracking-tight text-white mb-4">
                Join the digital giving revolution
            </h2>
            <p class="text-[14px] text-[#9C998F] mb-8 leading-relaxed">
                Whether it's a wedding gift, church offering, or community fundraiser—start collecting contributions the modern way. <strong class="text-[#FAFAF7] font-medium">Free to create. Easy to share. Secure for everyone.</strong>
            </p>
            <a href="{{ route('register') }}" class="btn btn-primary px-6 py-2.5 text-[13.5px]">
                Create your free account
                <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</x-layouts.guest>