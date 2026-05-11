<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-[#F3F1EB] antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-md flex-col gap-6">
                {{-- Brand mark --}}
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 no-underline" wire:navigate>
                    <div class="w-9 h-9 rounded-[9px] bg-[#15140F] text-[#FAFAF7] grid place-items-center text-[16px] font-bold tracking-tight">
                        M
                    </div>
                    <span class="text-[13px] font-medium text-[#6B6862]">{{ config('app.name', 'MyMoneyBox') }}</span>
                </a>

                {{-- Card --}}
                <div class="card">
                    <div class="px-8 py-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>