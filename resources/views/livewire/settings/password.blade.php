<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Security')" :subheading="__('Password, two-factor authentication & active sessions')">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            {{-- Password card --}}
            <div class="card">
                <div class="card-head"><div class="card-title">Update password</div></div>
                <div class="card-body">
                    <form method="POST" wire:submit="updatePassword" class="space-y-4">
                        <flux:input
                            wire:model="current_password"
                            :label="__('Current password')"
                            type="password"
                            required
                            autocomplete="current-password"
                        />
                        <flux:input
                            wire:model="password"
                            :label="__('New password')"
                            type="password"
                            required
                            autocomplete="new-password"
                        />
                        <flux:input
                            wire:model="password_confirmation"
                            :label="__('Confirm Password')"
                            type="password"
                            required
                            autocomplete="new-password"
                        />

                        <div class="flex items-center gap-4">
                            <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                            <x-action-message class="me-3" on="password-updated">
                                {{ __('Saved.') }}
                            </x-action-message>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Security info card --}}
            <div class="flex flex-col gap-5">
                <div class="card">
                    <div class="card-head"><div class="card-title">Two-factor authentication</div></div>
                    <div class="card-body flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-[13px] font-medium text-[#15140F]">Status</div>
                                <div class="tiny">Authenticator app</div>
                            </div>
                            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                                <a href="{{ route('two-factor.show') }}" wire:navigate>
                                    @if(auth()->user()->two_factor_secret)
                                        <span class="pill pill-ok">enabled</span>
                                    @else
                                        <span class="pill pill-warn">disabled</span>
                                    @endif
                                </a>
                            @endif
                        </div>
                        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                            <a href="{{ route('two-factor.show') }}" wire:navigate class="btn btn-sm text-[12px]">
                                Manage 2FA settings
                                <svg viewBox="0 0 24 24" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-head"><div class="card-title">Account</div></div>
                    <div class="card-body flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-[13px] font-medium text-[#15140F]">Email verified</div>
                                <div class="tiny">{{ auth()->user()->email }}</div>
                            </div>
                            @if(auth()->user()->hasVerifiedEmail())
                                <span class="pill pill-ok">verified</span>
                            @else
                                <span class="pill pill-warn">unverified</span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-[13px] font-medium text-[#15140F]">ID verification</div>
                                <div class="tiny">Required for withdrawals</div>
                            </div>
                            <a href="{{ route('settings.verification') }}" wire:navigate>
                                @if(auth()->user()->isVerified())
                                    <span class="pill pill-ok">verified</span>
                                @else
                                    <span class="pill pill-warn">pending</span>
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-settings.layout>
</section>
