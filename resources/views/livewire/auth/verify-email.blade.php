<x-layouts.auth>
    <div class="flex flex-col items-center gap-6">

        {{-- Email icon in green circle --}}
        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-[#E6F1EB]">
            <svg class="h-8 w-8 text-[#1B6B4E]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
            </svg>
        </div>

        {{-- Heading & subtext --}}
        <div class="flex flex-col items-center gap-2 text-center">
            <flux:heading size="xl" class="font-bold tracking-tight">
                {{ __('Check your inbox') }}
            </flux:heading>
            <flux:text class="max-w-xs text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('We sent a verification link to your email address. Click it to activate your account and start using MyPiggyBox.') }}
            </flux:text>
        </div>

        {{-- Success banner --}}
        @if (session('status') == 'verification-link-sent')
            <div class="w-full rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-center dark:border-green-800 dark:bg-green-950">
                <flux:text class="text-sm font-medium text-green-700 dark:text-green-400">
                    {{ __('A new verification link has been sent!') }}
                </flux:text>
            </div>
        @endif

        {{-- Actions --}}
        <div class="flex w-full flex-col items-center gap-3">
            <form method="POST" action="{{ route('verification.send') }}" class="w-full">
                @csrf
                <flux:button type="submit" variant="primary" class="w-full">
                    {{ __('Resend verification email') }}
                </flux:button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:button variant="ghost" type="submit" class="cursor-pointer text-sm">
                    {{ __('Log out') }}
                </flux:button>
            </form>
        </div>

        {{-- Spam folder hint --}}
        <flux:text class="text-center text-xs text-zinc-400 dark:text-zinc-500">
            {{ __("Check your spam folder if you don't see it within a few minutes.") }}
        </flux:text>

    </div>
</x-layouts.auth>