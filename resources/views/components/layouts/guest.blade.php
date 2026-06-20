<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MyPiggyBox') }}</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    @include('partials.head')
</head>
<body class="antialiased bg-[#FAFAF7] text-[#15140F]" x-data="{ mobileMenuOpen: false }">

    {{-- Navigation --}}
    <nav class="bg-white border-b border-[#E6E3DC] sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-14">
                {{-- Brand --}}
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 no-underline">
                        <div class="w-7 h-7 rounded-[7px] bg-[#15140F] text-[#FAFAF7] grid place-items-center text-[13px] font-bold tracking-tight flex-none">
                            M
                        </div>
                        <span class="text-[14.5px] font-semibold tracking-tight text-[#15140F]">{{ config('app.name', 'MyMoneyBox') }}</span>
                    </a>
                </div>

                {{-- Desktop nav --}}
                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('browse') }}"
                       class="px-3 py-1.5 text-[13px] font-medium rounded-[6px] transition-colors duration-100 {{ request()->routeIs('browse') ? 'text-[#15140F] bg-[#F3F1EB]' : 'text-[#6B6862] hover:text-[#15140F] hover:bg-[#F3F1EB]' }}">
                        Browse
                    </a>
                    <a href="{{ '/events' }}"
                       class="px-3 py-1.5 text-[13px] font-medium rounded-[6px] transition-colors duration-100 {{ request()->is('events') || request()->is('events/*') ? 'text-[#15140F] bg-[#F3F1EB]' : 'text-[#6B6862] hover:text-[#15140F] hover:bg-[#F3F1EB]' }}">
                        Events
                    </a>
                    <a href="{{ route('features') }}" class="px-3 py-1.5 text-[13px] font-medium rounded-[6px] transition-colors duration-100 {{ request()->routeIs('features') ? 'text-[#15140F] bg-[#F3F1EB]' : 'text-[#6B6862] hover:text-[#15140F] hover:bg-[#F3F1EB]' }}">
                        Features
                    </a>
                    <a href="{{ route('about') }}" class="px-3 py-1.5 text-[13px] font-medium rounded-[6px] transition-colors duration-100 {{ request()->routeIs('about') ? 'text-[#15140F] bg-[#F3F1EB]' : 'text-[#6B6862] hover:text-[#15140F] hover:bg-[#F3F1EB]' }}">
                        About
                    </a>
                    <a href="{{ route('donations-protection') }}" class="px-3 py-1.5 text-[13px] font-medium rounded-[6px] transition-colors duration-100 {{ request()->routeIs('donations-protection') ? 'text-[#15140F] bg-[#F3F1EB]' : 'text-[#6B6862] hover:text-[#15140F] hover:bg-[#F3F1EB]' }}">
                        Donations &amp; Protections
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-3 py-1.5 text-[13px] font-medium text-[#6B6862] hover:text-[#15140F] rounded-[6px] hover:bg-[#F3F1EB] transition-colors duration-100">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-3 py-1.5 text-[13px] font-medium text-[#6B6862] hover:text-[#15140F] rounded-[6px] hover:bg-[#F3F1EB] transition-colors duration-100">
                            Sign in
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-primary ml-1">
                            Get started
                        </a>
                    @endauth
                </div>

                {{-- Mobile toggle --}}
                <div class="flex md:hidden items-center gap-2">
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Get started</a>
                    @endguest
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                            class="p-1.5 rounded-[6px] text-[#6B6862] hover:bg-[#F3F1EB] transition-colors duration-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="md:hidden border-t border-[#E6E3DC] bg-white"
             @click.away="mobileMenuOpen = false"
             x-cloak>
            <div class="px-4 py-3 space-y-1">
                <a href="{{ route('browse') }}" class="block px-3 py-2 text-[13px] font-medium text-[#6B6862] hover:bg-[#F3F1EB] hover:text-[#15140F] rounded-[6px] transition-colors duration-100">Browse</a>
                <a href="{{ '/events' }}" class="block px-3 py-2 text-[13px] font-medium text-[#6B6862] hover:bg-[#F3F1EB] hover:text-[#15140F] rounded-[6px] transition-colors duration-100">Events</a>
                <a href="{{ route('features') }}" class="block px-3 py-2 text-[13px] font-medium text-[#6B6862] hover:bg-[#F3F1EB] hover:text-[#15140F] rounded-[6px] transition-colors duration-100">Features</a>
                <a href="{{ route('about') }}" class="block px-3 py-2 text-[13px] font-medium text-[#6B6862] hover:bg-[#F3F1EB] hover:text-[#15140F] rounded-[6px] transition-colors duration-100">About</a>
                <a href="{{ route('donations-protection') }}" class="block px-3 py-2 text-[13px] font-medium text-[#6B6862] hover:bg-[#F3F1EB] hover:text-[#15140F] rounded-[6px] transition-colors duration-100">Donations &amp; Protections</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-[13px] font-medium text-[#6B6862] hover:bg-[#F3F1EB] hover:text-[#15140F] rounded-[6px] transition-colors duration-100">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 text-[13px] font-medium text-[#6B6862] hover:bg-[#F3F1EB] hover:text-[#15140F] rounded-[6px] transition-colors duration-100">Sign in</a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Main content --}}
    <main>{{ $slot }}</main>

    {{-- Footer --}}
    <footer class="bg-[#15140F] text-[#9C998F] mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-7 h-7 rounded-[7px] bg-[#FAFAF7] text-[#15140F] grid place-items-center text-[13px] font-bold flex-none">M</div>
                        <span class="text-[14.5px] font-semibold text-[#FAFAF7]">{{ config('app.name') }}</span>
                    </div>
                    <p class="text-[13px] leading-relaxed max-w-xs">
                        Create and share PiggyBoxes for any occasion. Collect contributions easily and securely.
                    </p>
                </div>

                <div>
                    <h3 class="text-[12px] font-semibold uppercase tracking-[0.08em] text-[#6B6862] mb-4">Platform</h3>
                    <ul class="space-y-2.5 text-[13px]">
                        <li><a href="{{ route('browse') }}" class="hover:text-[#FAFAF7] transition-colors duration-100">Browse PiggyBoxes</a></li>
                        <li><a href="{{ '/events' }}" class="hover:text-[#FAFAF7] transition-colors duration-100">Upcoming Events</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-[#FAFAF7] transition-colors duration-100">Create Account</a></li>
                        <li><a href="{{ route('features') }}" class="hover:text-[#FAFAF7] transition-colors duration-100">Features</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-[#FAFAF7] transition-colors duration-100">About Us</a></li>
                        <li><a href="{{ route('donations-protection') }}" class="hover:text-[#FAFAF7] transition-colors duration-100">Donations &amp; Protections</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-[12px] font-semibold uppercase tracking-[0.08em] text-[#6B6862] mb-4">Legal</h3>
                    <ul class="space-y-2.5 text-[13px]">
                        <li><a href="{{ route('terms') }}" class="hover:text-[#FAFAF7] transition-colors duration-100">Terms & Conditions</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-[#FAFAF7] transition-colors duration-100">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-white/10 pt-8 text-center text-[12px]">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>
    </footer>

    @stack('scripts')
    @fluxScripts
</body>
</html>
