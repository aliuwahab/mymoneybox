<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <style>[x-cloak]{display:none!important}</style>
    </head>
    <body class="min-h-screen bg-[#FAFAF7]" x-data="{ sidebarOpen: false }">

        {{-- Mobile overlay --}}
        <div
            x-show="sidebarOpen"
            x-transition:enter="transition-opacity ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-150"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false"
            class="fixed inset-0 z-30 bg-black/30 lg:hidden"
            x-cloak
        ></div>

        {{-- Sidebar --}}
        <aside
            class="fixed inset-y-0 left-0 z-40 flex w-[248px] flex-col overflow-y-auto border-r border-[#CFCABD] bg-[#F7F5EF] shadow-[18px_0_44px_-28px_rgba(20,18,12,.36)] transition-transform duration-300 ease-in-out lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        >
            {{-- Mobile close --}}
            <div class="flex justify-end px-3 pt-3 lg:hidden">
                <button @click="sidebarOpen = false" class="p-1.5 rounded-lg text-[#15140F] hover:bg-[#E6E3DC] transition">
                    <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Brand --}}
            <div class="flex items-center gap-2.5 px-2 pb-4 pt-1 lg:pt-4">
                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2.5 no-underline">
                    <div class="w-7 h-7 rounded-[7px] bg-[#15140F] text-[#FAFAF7] grid place-items-center text-[13px] font-bold tracking-tight flex-none">M</div>
                    <div>
                        <div class="text-[14.5px] font-semibold tracking-tight text-[#15140F] leading-none">MyPiggyBox</div>
                        <div class="text-[11px] text-[#9C998F] mt-0.5">
                            {{ auth()->user()->country?->name ?? 'Ghana' }} · {{ auth()->user()->country?->currency_code ?? 'GHS' }}
                        </div>
                    </div>
                </a>
            </div>

            {{-- Workspace nav --}}
            <div class="text-[10.5px] font-medium uppercase tracking-[0.08em] text-[#9C998F] px-2.5 pt-2 pb-1.5">Workspace</div>

            <flux:navlist>
                <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate class="mmb-nav-item">
                    Overview
                </flux:navlist.item>
                <flux:navlist.item icon="archive-box" :href="route('money-boxes.index')" :current="request()->routeIs('money-boxes.index')" wire:navigate class="mmb-nav-item">
                    <span class="flex-1">My PiggyBoxes</span>
                    @php $boxCount = auth()->user()->moneyBoxes()->where('is_active', true)->count(); @endphp
                    @if($boxCount > 0)
                        <span class="ml-auto text-[11px] text-[#154F3A] bg-[#E6F1EB] px-1.5 py-0.5 rounded-full tabular-nums">{{ $boxCount }}</span>
                    @endif
                </flux:navlist.item>
                <flux:navlist.item icon="users" :href="route('contributors.index')" :current="request()->routeIs('contributors.index')" wire:navigate class="mmb-nav-item">
                    Contributors
                </flux:navlist.item>
                <flux:navlist.item icon="chart-bar" :href="route('analytics.index')" :current="request()->routeIs('analytics.index')" wire:navigate class="mmb-nav-item">
                    Analytics
                </flux:navlist.item>
                <flux:navlist.item icon="arrow-up-circle" :href="route('withdrawals.index')" :current="request()->routeIs('withdrawals.*')" wire:navigate class="mmb-nav-item">
                    Withdrawals
                </flux:navlist.item>
            </flux:navlist>

            {{-- Events nav --}}
            <div class="text-[10.5px] font-medium uppercase tracking-[0.08em] text-[#9C998F] px-2.5 pt-4 pb-1.5">Events</div>

            <flux:navlist>
                <flux:navlist.item icon="ticket" :href="route('events.index')" :current="request()->routeIs('events.*')" wire:navigate class="mmb-nav-item">
                    <span class="flex-1">My EventBoxes</span>
                    @php $eventCount = auth()->user()->eventBoxes()->whereIn('status', ['active', 'draft'])->count(); @endphp
                    @if($eventCount > 0)
                        <span class="ml-auto text-[11px] text-[#154F3A] bg-[#E6F1EB] px-1.5 py-0.5 rounded-full tabular-nums">{{ $eventCount }}</span>
                    @endif
                </flux:navlist.item>
            </flux:navlist>

            {{-- Account nav --}}
            <div class="text-[10.5px] font-medium uppercase tracking-[0.08em] text-[#9C998F] px-2.5 pt-4 pb-1.5">Account</div>

            <flux:navlist>
                <flux:navlist.item icon="gift" :href="route('piggy.my-piggy-box')" :current="request()->routeIs('piggy.my-piggy-box')" wire:navigate class="mmb-nav-item">
                    My Piggy Wallet
                </flux:navlist.item>
                <flux:navlist.item icon="globe-alt" :href="route('browse')" :current="request()->routeIs('browse')" wire:navigate class="mmb-nav-item">
                    Browse
                </flux:navlist.item>
                <flux:navlist.item icon="cog-6-tooth" :href="route('profile.edit')" :current="request()->routeIs('profile.edit')" wire:navigate class="mmb-nav-item">
                    Settings
                </flux:navlist.item>
                @if(auth()->user()->isAdmin())
                    <flux:navlist.item icon="shield-check" href="/admin" target="_blank" rel="noopener noreferrer" class="mmb-nav-item">
                        Admin
                    </flux:navlist.item>
                @endif
            </flux:navlist>

            <div class="flex-1"></div>

            {{-- User footer dropdown --}}
            <div class="border-t border-[#E6E3DC] pt-3 pb-1 px-1 relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2.5 px-1 py-1.5 rounded-lg hover:bg-black/5 transition w-full text-left">
                    <div class="w-[30px] h-[30px] rounded-full bg-primary-600 text-white grid place-items-center text-[11.5px] font-semibold flex-none tracking-wide">
                        {{ auth()->user()->initials() }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[13px] font-medium text-[#15140F] leading-tight truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[11px] text-[#9C998F] truncate">{{ auth()->user()->email }}</div>
                    </div>
                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5 text-[#9C998F] flex-none" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/></svg>
                </button>

                <div
                    x-show="open"
                    @click.outside="open = false"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-end="opacity-0 translate-y-1"
                    class="absolute bottom-full left-1 right-1 mb-1 rounded-[8px] border border-[#E6E3DC] bg-white shadow-lg overflow-hidden"
                    x-cloak
                >
                    <a href="{{ route('profile.edit') }}" wire:navigate class="flex items-center gap-2.5 px-3 py-2.5 text-[13px] text-[#15140F] hover:bg-[#F7F5EF] transition">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 text-[#9C998F]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                        Settings
                    </a>
                    <div class="border-t border-[#E6E3DC]"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2.5 px-3 py-2.5 text-[13px] text-[#15140F] hover:bg-[#F7F5EF] transition w-full text-left">
                            <svg viewBox="0 0 24 24" class="w-4 h-4 text-[#9C998F]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main content area --}}
        <div class="lg:ml-[248px] flex flex-col min-h-screen">

            {{-- Mobile top bar --}}
            <header class="lg:hidden sticky top-0 z-20 flex items-center h-12 border-b border-[#E6E3DC] bg-[#FAFAF7] px-3">
                <button @click="sidebarOpen = true" class="p-1.5 rounded-lg text-[#15140F] hover:bg-[#E6E3DC] transition">
                    <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M4 12h16"/></svg>
                </button>
                <div class="flex items-center gap-2 ms-3">
                    <div class="w-6 h-6 rounded-[5px] bg-[#15140F] text-[#FAFAF7] grid place-items-center text-[11px] font-bold">M</div>
                    <span class="text-[13.5px] font-semibold text-[#15140F]">MyPiggyBox</span>
                </div>
                <div class="flex-1"></div>

                {{-- Mobile user dropdown --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="w-8 h-8 rounded-full bg-primary-600 text-white grid place-items-center text-[11px] font-semibold">
                        {{ auth()->user()->initials() }}
                    </button>
                    <div
                        x-show="open"
                        @click.outside="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="absolute top-full right-0 mt-1 w-[180px] rounded-[8px] border border-[#E6E3DC] bg-white shadow-lg overflow-hidden"
                        x-cloak
                    >
                        <a href="{{ route('profile.edit') }}" wire:navigate class="flex items-center gap-2.5 px-3 py-2.5 text-[13px] text-[#15140F] hover:bg-[#F7F5EF] transition">
                            <svg viewBox="0 0 24 24" class="w-4 h-4 text-[#9C998F]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                            Settings
                        </a>
                        <div class="border-t border-[#E6E3DC]"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-2.5 px-3 py-2.5 text-[13px] text-[#15140F] hover:bg-[#F7F5EF] transition w-full text-left">
                                <svg viewBox="0 0 24 24" class="w-4 h-4 text-[#9C998F]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            {{-- Desktop topbar --}}
            <div class="sticky top-0 z-10 hidden lg:flex items-center gap-3 px-5 lg:px-7 py-3 border-b border-[#E6E3DC] bg-[#FAFAF7]">
                <nav class="flex items-center gap-1.5 text-[12.5px] text-[#9C998F]">
                    @php
                        $crumbs = match(true) {
                            request()->routeIs('dashboard') => ['Workspace', 'Overview'],
                            request()->routeIs('money-boxes.index') => ['Workspace', 'My PiggyBoxes'],
                            request()->routeIs('money-boxes.create') => ['Workspace', 'New PiggyBox'],
                            request()->routeIs('money-boxes.show') => ['Workspace', 'My PiggyBoxes', $title ?? 'PiggyBox'],
                            request()->routeIs('money-boxes.edit') => ['Workspace', 'My PiggyBoxes', 'Edit'],
                            request()->routeIs('money-boxes.share') => ['Workspace', 'My PiggyBoxes', 'Share'],
                            request()->routeIs('money-boxes.statistics') => ['Workspace', 'My PiggyBoxes', 'Statistics'],
                            request()->routeIs('contributors.index') => ['Workspace', 'Contributors'],
                            request()->routeIs('analytics.index') => ['Workspace', 'Analytics'],
                            request()->routeIs('withdrawals.*') => ['Workspace', 'Withdrawals'],
                            request()->routeIs('events.index') => ['Events', 'My EventBoxes'],
                            request()->routeIs('events.create') => ['Events', 'My EventBoxes', 'Create EventBox'],
                            request()->routeIs('events.edit') => ['Events', 'My EventBoxes', 'Edit Event'],
                            request()->routeIs('events.dashboard') => ['Events', 'My EventBoxes', 'Dashboard'],
                            request()->routeIs('piggy.my-piggy-box') => ['Account', 'My Piggy Wallet'],
                            request()->routeIs('browse') => ['Account', 'Browse'],
                            request()->routeIs('profile.edit') || request()->routeIs('user-password.edit') || request()->routeIs('settings.*') || request()->routeIs('two-factor.show') || request()->routeIs('appearance.edit') => ['Account', 'Settings'],
                            default => ['Workspace'],
                        };
                    @endphp
                    @foreach($crumbs as $i => $crumb)
                        @if($i > 0)<span class="text-[#D9D6CE]">/</span>@endif
                        @if($loop->last)
                            <span class="text-[#15140F] font-medium">{{ $crumb }}</span>
                        @else
                            <span>{{ $crumb }}</span>
                        @endif
                    @endforeach
                </nav>

                <div class="ml-auto hidden sm:flex items-center gap-2 bg-white border border-[#E6E3DC] rounded-[6px] px-2.5 py-1.5 w-[260px] text-[#9C998F] text-[13px]">
                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5 flex-none" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    <span class="flex-1 truncate">Search PiggyBoxes, contributors…</span>
                    <span class="font-mono text-[10.5px] bg-[#ECEAE3] px-1.5 py-0.5 rounded border border-[#E6E3DC] text-[#6B6862]">⌘K</span>
                </div>

                <button class="btn btn-ghost btn-icon hidden sm:inline-flex" title="Notifications">
                    <svg viewBox="0 0 24 24" class="w-4 h-4 text-[#6B6862]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 1 1 12 0c0 7 3 7 3 9H3c0-2 3-2 3-9Z"/><path d="M10.5 21a1.5 1.5 0 0 0 3 0"/></svg>
                </button>
            </div>

            @if(session('success') || session('error'))
                <div
                    x-data="{ show: true }"
                    x-init="setTimeout(() => show = false, 4200)"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-end="opacity-0 -translate-y-1"
                    x-cloak
                    class="fixed right-4 top-16 z-50 max-w-[calc(100vw-2rem)] sm:max-w-sm rounded-[8px] border px-4 py-3 text-[13px] shadow-[0_18px_50px_-24px_rgba(20,18,12,.42)] {{ session('error') ? 'border-red-200 bg-red-50 text-red-800' : 'border-primary-200 bg-primary-50 text-primary-800' }}"
                    role="status"
                >
                    {{ session('error') ?: session('success') }}
                </div>
            @endif

            {{ $slot }}

        </div>{{-- end main --}}

        @fluxScripts

        <script>
            const SwalTheme = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-primary mx-1',
                    cancelButton: 'btn mx-1',
                    popup: 'rounded-[10px]',
                },
                buttonsStyling: false,
                reverseButtons: true,
            });

            window.confirmDelete = function(callback, options = {}) {
                SwalTheme.fire({
                    title: options.title || 'Are you sure?',
                    text: options.text || "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: options.confirmText || 'Yes, delete it!',
                    cancelButtonText: options.cancelText || 'Cancel',
                }).then((result) => { if (result.isConfirmed && callback) callback(); });
            };
        </script>
    </body>
</html>