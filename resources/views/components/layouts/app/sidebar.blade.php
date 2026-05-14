<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-[#FAFAF7]">
        <flux:sidebar sticky stashable
            class="border-e border-[#E6E3DC] bg-[#F3F1EB] !w-[248px]">

            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            {{-- Brand --}}
            <div class="flex items-center gap-2.5 px-2 pb-4 pt-1">
                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2.5 no-underline">
                    <div class="w-7 h-7 rounded-[7px] bg-[#15140F] text-[#FAFAF7] grid place-items-center text-[13px] font-bold tracking-tight flex-none">
                        M
                    </div>
                    <div>
                        <div class="text-[14.5px] font-semibold tracking-tight text-[#15140F] leading-none">MyPiggyBox</div>
                        <div class="text-[11px] text-[#9C998F] mt-0.5">
                            {{ auth()->user()->country?->name ?? 'Ghana' }} · {{ auth()->user()->country?->currency_code ?? 'GHS' }}
                        </div>
                    </div>
                </a>
            </div>

            {{-- Workspace nav --}}
            <div class="text-[10.5px] font-medium uppercase tracking-[0.08em] text-[#9C998F] px-2.5 pt-2 pb-1.5">
                Workspace
            </div>

            <flux:navlist>
                <flux:navlist.item
                    icon="home"
                    :href="route('dashboard')"
                    :current="request()->routeIs('dashboard')"
                    wire:navigate
                    class="mmb-nav-item"
                >
                    Overview
                </flux:navlist.item>

                <flux:navlist.item
                    icon="archive-box"
                    :href="route('money-boxes.index')"
                    :current="request()->routeIs('money-boxes.index')"
                    wire:navigate
                    class="mmb-nav-item"
                >
                    <span class="flex-1">My Boxes</span>
                    @php $boxCount = auth()->user()->moneyBoxes()->where('is_active', true)->count(); @endphp
                    @if($boxCount > 0)
                        <span class="ml-auto text-[11px] text-[#9C998F] bg-[#ECEAE3] px-1.5 py-0.5 rounded-full tabular-nums">{{ $boxCount }}</span>
                    @endif
                </flux:navlist.item>

                <flux:navlist.item
                    icon="plus-circle"
                    :href="route('money-boxes.create')"
                    :current="request()->routeIs('money-boxes.create')"
                    wire:navigate
                    class="mmb-nav-item"
                >
                    New Box
                </flux:navlist.item>

            <flux:navlist.item
                    icon="users"
                    :href="route('contributors.index')"
                    :current="request()->routeIs('contributors.index')"
                    wire:navigate
                    class="mmb-nav-item"
                >
                    Contributors
                </flux:navlist.item>

                <flux:navlist.item
                    icon="chart-bar"
                    :href="route('analytics.index')"
                    :current="request()->routeIs('analytics.index')"
                    wire:navigate
                    class="mmb-nav-item"
                >
                    Analytics
                </flux:navlist.item>
            </flux:navlist>

            {{-- Account nav --}}
            <div class="text-[10.5px] font-medium uppercase tracking-[0.08em] text-[#9C998F] px-2.5 pt-4 pb-1.5">
                Account
            </div>

            <flux:navlist>
                <flux:navlist.item
                    icon="gift"
                    :href="route('piggy.my-piggy-box')"
                    :current="request()->routeIs('piggy.my-piggy-box')"
                    wire:navigate
                    class="mmb-nav-item"
                >
                    My Piggy
                </flux:navlist.item>

                <flux:navlist.item
                    icon="globe-alt"
                    :href="route('browse')"
                    :current="request()->routeIs('browse')"
                    wire:navigate
                    class="mmb-nav-item"
                >
                    Browse
                </flux:navlist.item>

                <flux:navlist.item
                    icon="cog-6-tooth"
                    :href="route('profile.edit')"
                    :current="request()->routeIs('profile.edit')"
                    wire:navigate
                    class="mmb-nav-item"
                >
                    Settings
                </flux:navlist.item>

                @if(auth()->user()->isAdmin())
                    <flux:navlist.item icon="shield-check" href="/admin" target="_blank" rel="noopener noreferrer" class="mmb-nav-item">
                        Admin
                    </flux:navlist.item>
                @endif
            </flux:navlist>

            <flux:spacer />

            {{-- User footer --}}
            <div class="border-t border-[#E6E3DC] pt-3 pb-1">
                <flux:dropdown position="top" align="start">
                    <button class="flex items-center gap-2.5 px-1 py-1.5 rounded-lg hover:bg-black/5 transition w-full text-left">
                        <div class="w-[30px] h-[30px] rounded-full bg-primary-600 text-white grid place-items-center text-[11.5px] font-semibold flex-none tracking-wide">
                            {{ auth()->user()->initials() }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-[13px] font-medium text-[#15140F] leading-tight truncate">{{ auth()->user()->name }}</div>
                            <div class="text-[11px] text-[#9C998F] truncate">{{ auth()->user()->email }}</div>
                        </div>
                        <flux:icon.chevrons-up-down class="w-3.5 h-3.5 text-[#9C998F] flex-none" />
                    </button>

                    <flux:menu class="w-[220px]">
                        <flux:menu.radio.group>
                            <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>Settings</flux:menu.item>
                        </flux:menu.radio.group>
                        <flux:menu.separator />
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                                Log Out
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </div>
        </flux:sidebar>

        {{-- Mobile top bar --}}
        <flux:header class="lg:hidden border-b border-[#E6E3DC] bg-[#FAFAF7]">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
            <div class="flex items-center gap-2 ms-3">
                <div class="w-6 h-6 rounded-[5px] bg-[#15140F] text-[#FAFAF7] grid place-items-center text-[11px] font-bold">M</div>
                <span class="text-[13.5px] font-semibold text-[#15140F]">MyPiggyBox</span>
            </div>
            <flux:spacer />
            <flux:dropdown position="bottom" align="end">
                <button class="w-8 h-8 rounded-full bg-primary-600 text-white grid place-items-center text-[11px] font-semibold">
                    {{ auth()->user()->initials() }}
                </button>
                <flux:menu>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>Settings</flux:menu.item>
                    <flux:menu.separator />
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            Log Out
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{-- Topbar --}}
        <div class="sticky top-0 z-10 flex items-center gap-3 px-5 lg:px-7 py-3 border-b border-[#E6E3DC] bg-[#FAFAF7]">
            {{-- Breadcrumbs --}}
            <nav class="flex items-center gap-1.5 text-[12.5px] text-[#9C998F]">
                @php
                    $crumbs = match(true) {
                        request()->routeIs('dashboard') => ['Workspace', 'Overview'],
                        request()->routeIs('money-boxes.index') => ['Workspace', 'My Boxes'],
                        request()->routeIs('money-boxes.create') => ['Workspace', 'New Box'],
                        request()->routeIs('money-boxes.show') => ['Workspace', 'My Boxes', $title ?? 'Box'],
                        request()->routeIs('money-boxes.edit') => ['Workspace', 'My Boxes', 'Edit'],
                        request()->routeIs('money-boxes.share') => ['Workspace', 'My Boxes', 'Share'],
                        request()->routeIs('money-boxes.statistics') => ['Workspace', 'My Boxes', 'Statistics'],
                        request()->routeIs('contributors.index') => ['Workspace', 'Contributors'],
                        request()->routeIs('analytics.index') => ['Workspace', 'Analytics'],
                        request()->routeIs('piggy.my-piggy-box') => ['Account', 'My Piggy'],
                        request()->routeIs('browse') => ['Account', 'Browse'],
                        request()->routeIs('profile.edit') || request()->routeIs('user-password.edit') || request()->routeIs('settings.*') || request()->routeIs('two-factor.show') || request()->routeIs('appearance.edit') => ['Account', 'Settings'],
                        default => ['Workspace'],
                    };
                @endphp
                @foreach($crumbs as $i => $crumb)
                    @if($i > 0)
                        <span class="text-[#D9D6CE]">/</span>
                    @endif
                    @if($loop->last)
                        <span class="text-[#15140F] font-medium">{{ $crumb }}</span>
                    @else
                        <span>{{ $crumb }}</span>
                    @endif
                @endforeach
            </nav>

            {{-- Search --}}
            <div class="ml-auto hidden sm:flex items-center gap-2 bg-white border border-[#E6E3DC] rounded-[6px] px-2.5 py-1.5 w-[260px] text-[#9C998F] text-[13px]">
                <svg viewBox="0 0 24 24" class="w-3.5 h-3.5 flex-none" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <span class="flex-1 truncate">Search boxes, contributors…</span>
                <span class="font-mono text-[10.5px] bg-[#ECEAE3] px-1.5 py-0.5 rounded border border-[#E6E3DC] text-[#6B6862]">⌘K</span>
            </div>

            {{-- Notification bell --}}
            <button class="btn btn-ghost btn-icon hidden sm:inline-flex" title="Notifications">
                <svg viewBox="0 0 24 24" class="w-4 h-4 text-[#6B6862]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 1 1 12 0c0 7 3 7 3 9H3c0-2 3-2 3-9Z"/><path d="M10.5 21a1.5 1.5 0 0 0 3 0"/></svg>
            </button>
        </div>

        {{ $slot }}

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