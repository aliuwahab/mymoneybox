@props(['heading' => '', 'subheading' => ''])

<div class="page-wrap max-w-[1280px]">

    {{-- Page header --}}
    <div class="mb-6">
        <h1 class="page-title">Settings</h1>
        <p class="text-[13.5px] text-[#6B6862] mt-1.5">Profile, security, payouts &amp; notifications</p>
    </div>

    {{-- Top tab navigation --}}
    <div class="tabs mb-0">
        <a href="{{ route('profile.edit') }}" wire:navigate
           class="tab {{ request()->routeIs('profile.edit') ? 'active' : '' }}">Profile</a>
        <a href="{{ route('user-password.edit') }}" wire:navigate
           class="tab {{ request()->routeIs('user-password.edit') ? 'active' : '' }}">Password</a>
        <a href="{{ route('settings.withdrawal-accounts') }}" wire:navigate
           class="tab {{ request()->routeIs('settings.withdrawal-accounts') ? 'active' : '' }}">Withdrawal Accounts</a>
        <a href="{{ route('settings.verification') }}" wire:navigate
           class="tab {{ request()->routeIs('settings.verification') ? 'active' : '' }}">
            ID Verification
            @if(auth()->check())
                @if(auth()->user()->isVerified())
                    <span class="ml-1 inline-flex items-center">
                        <svg class="w-3.5 h-3.5 text-[#1B6B4E]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    </span>
                @else
                    <span class="ml-1.5 inline-block w-2 h-2 rounded-full bg-amber-500"></span>
                @endif
            @endif
        </a>
        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
        <a href="{{ route('two-factor.show') }}" wire:navigate
           class="tab {{ request()->routeIs('two-factor.show') ? 'active' : '' }}">Two-Factor Auth</a>
        @endif
        <a href="{{ route('appearance.edit') }}" wire:navigate
           class="tab {{ request()->routeIs('appearance.edit') ? 'active' : '' }}">Appearance</a>
    </div>

    {{-- Tab content --}}
    <div class="pt-6">
        @if($heading)
            <div class="mb-5">
                <h2 class="text-[16px] font-semibold text-[#15140F] tracking-tight">{{ $heading }}</h2>
                @if($subheading)
                    <p class="text-[13px] text-[#6B6862] mt-1">{{ $subheading }}</p>
                @endif
            </div>
        @endif
        {{ $slot }}
    </div>

</div>
