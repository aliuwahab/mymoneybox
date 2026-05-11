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
                        <div class="text-[14.5px] font-semibold tracking-tight text-[#15140F] leading-none">MyMoneyBox</div>
                        <div class="text-[11px] text-[#9C998F] mt-0.5">
                            {{ auth()->user()->country ?? 'Ghana' }} · {{ auth()->user()->currency_code ?? 'GHS' }}
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
                    :href="route('money-boxes.index')"
                    :current="false"
                    wire:navigate
                    class="mmb-nav-item"
                >
                    Contributors
                </flux:navlist.item>

                <flux:navlist.item
                    icon="chart-bar"
                    :href="route('money-boxes.index')"
                    :current="false"
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
                    icon="globe-alt"
                    :href="route('browse')"
                    :current="request()->routeIs('browse')"
                    wire:navigate
                    class="mmb-nav-item"
                >
                    Public Page
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
                    <flux:navlist.item icon="shield-check" href="/admin" wire:navigate class="mmb-nav-item">
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
                <span class="text-[13.5px] font-semibold text-[#15140F]">MyMoneyBox</span>
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