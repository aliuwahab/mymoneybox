<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket confirmation — {{ $eventBox->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.pwa')
</head>
<body class="bg-[#F3F1EB] min-h-screen flex flex-col">

    {{-- Nav --}}
    <nav class="bg-[#15140F] px-4 py-3">
        <div class="max-w-xl mx-auto flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <span style="display:inline-block;width:28px;height:28px;background:#1B6B4E;border-radius:6px;text-align:center;line-height:28px;font-weight:700;font-size:14px;color:#FAFAF7;font-family:Arial,sans-serif;">M</span>
                <span class="text-[#FAFAF7] font-semibold text-[15px]">MyPiggyBox</span>
            </a>
        </div>
    </nav>

    <div class="flex-1 flex items-center justify-center px-4 py-16">
        <div class="bg-white border border-[#E6E3DC] rounded-[16px] shadow-sm w-full max-w-[480px] overflow-hidden">

            @if($pending)
                {{-- ── Payment pending: poll until code is ready ── --}}
                <div
                    x-data="{
                        ready: false,
                        failed: false,
                        email: '{{ $ticket?->buyer_email }}',
                        quantity: {{ $tickets->count() }},
                        pollTimer: null,
                        async poll() {
                            try {
                                const r = await fetch('{{ route('events.ticket-status', [$eventBox->slug, $reference]) }}');
                                const d = await r.json();
                                if (d.status === 'completed' && d.code_ready) {
                                    this.ready = true;
                                    this.email = d.email || this.email;
                                    this.quantity = d.quantity || this.quantity;
                                    clearInterval(this.pollTimer);
                                } else if (d.status === 'failed') {
                                    this.failed = true;
                                    clearInterval(this.pollTimer);
                                }
                            } catch(e) {}
                        },
                        init() {
                            this.poll();
                            this.pollTimer = setInterval(() => this.poll(), 4000);
                        }
                    }"
                    x-init="init()"
                >
                    {{-- Pending: spinner --}}
                    <div x-show="!ready && !failed">
                        <div class="bg-gradient-to-br from-[#2A2820] to-[#15140F] px-8 py-8 text-center">
                            <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="animate-spin w-8 h-8 text-white/80" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8h4z"/>
                                </svg>
                            </div>
                            <h1 style="font-family:Georgia,'Times New Roman',serif; font-size:1.3rem; font-weight:400; color:white; margin:0 0 6px;">
                                Confirming payment…
                            </h1>
                            <p class="text-white/60 text-[13px]">{{ $eventBox->title }}</p>
                        </div>
                        <div class="px-8 py-7 text-center">
                            <p class="text-[14px] text-[#6B6862] mb-2">
                                Your payment is being verified. This usually takes a few seconds.
                            </p>
                            @if($ticket)
                                <p class="text-[13px] text-[#9C998F]">
                                    Your ticket(s) will be emailed to <strong class="text-[#15140F]">{{ $ticket->buyer_email }}</strong> once confirmed.
                                </p>
                            @endif
                            <div class="flex items-center justify-center gap-1.5 mt-5">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#1B6B4E] animate-bounce" style="animation-delay:0ms"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-[#1B6B4E] animate-bounce" style="animation-delay:150ms"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-[#1B6B4E] animate-bounce" style="animation-delay:300ms"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Ready: ticket confirmed --}}
                    <div x-show="ready" x-cloak>
                        <div class="bg-gradient-to-br from-[#1B6B4E] to-[#154F3A] px-8 py-8 text-center">
                            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg viewBox="0 0 24 24" class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
                            </div>
                            <h1 style="font-family:Georgia,'Times New Roman',serif; font-size:1.4rem; font-weight:400; color:white; margin:0 0 6px;">
                                You're going to
                            </h1>
                            <p class="text-white/80 text-[14px]">{{ $eventBox->title }}</p>
                        </div>
                        <div class="px-8 py-7">
                            <div class="text-center mb-6">
                                <p class="text-[15px] font-medium text-[#15140F] mb-1">
                                    <span x-text="quantity > 1 ? quantity + ' tickets are' : 'Your ticket is'"></span> on the way to
                                </p>
                                <p class="text-[#1B6B4E] font-semibold text-[15px]" x-text="email"></p>
                            </div>
                            <div class="bg-[#FAFAF7] border border-[#E6E3DC] rounded-[10px] p-4 mb-6">
                                <dl class="space-y-2.5 text-[13px]">
                                    <div class="flex justify-between">
                                        <dt class="text-[#9C998F]">Event</dt>
                                        <dd class="font-medium text-[#15140F]">{{ $eventBox->title }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-[#9C998F]">Date</dt>
                                        <dd class="font-medium text-[#15140F]">{{ $eventBox->event_date->format('D, M j, Y · g:ia') }}</dd>
                                    </div>
                                    @if($eventBox->venue)
                                        <div class="flex justify-between">
                                            <dt class="text-[#9C998F]">Venue</dt>
                                            <dd class="font-medium text-[#15140F]">{{ $eventBox->venue }}</dd>
                                        </div>
                                    @endif
                                    <div class="flex justify-between">
                                        <dt class="text-[#9C998F]">Reference</dt>
                                        <dd class="font-mono text-[12px] font-medium text-[#15140F]">{{ $reference }}</dd>
                                    </div>
                                </dl>
                            </div>
                            <div class="bg-[#FFF8E7] border border-[#F0D980] rounded-[8px] px-4 py-3 mb-6">
                                <p class="text-[13px] text-[#6B5900]">
                                    Check your inbox — your ticket(s) with QR code will arrive shortly. Check your spam folder if you don't see it within a few minutes.
                                </p>
                            </div>
                            <a href="{{ route('events.show', $eventBox->slug) }}"
                                class="block w-full text-center bg-[#15140F] hover:bg-[#2A2820] text-white font-semibold text-[14px] py-3 px-4 rounded-[8px] transition-colors">
                                Back to event
                            </a>
                        </div>
                    </div>

                    {{-- Failed --}}
                    <div x-show="failed" x-cloak>
                        <div class="bg-gradient-to-br from-[#7F1D1D] to-[#991B1B] px-8 py-8 text-center">
                            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg viewBox="0 0 24 24" class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                            </div>
                            <h1 style="font-family:Georgia,'Times New Roman',serif; font-size:1.3rem; font-weight:400; color:white; margin:0 0 6px;">Payment failed</h1>
                            <p class="text-white/70 text-[13px]">{{ $eventBox->title }}</p>
                        </div>
                        <div class="px-8 py-7 text-center">
                            <p class="text-[14px] text-[#6B6862] mb-5">Your payment could not be processed. No charge was made.</p>
                            <a href="{{ route('events.show', $eventBox->slug) }}"
                                class="block w-full text-center bg-[#15140F] hover:bg-[#2A2820] text-white font-semibold text-[14px] py-3 px-4 rounded-[8px] transition-colors">
                                Try again
                            </a>
                        </div>
                    </div>
                </div>

            @elseif($ticket)
                {{-- ── Payment confirmed ── --}}
                <div class="bg-gradient-to-br from-[#1B6B4E] to-[#154F3A] px-8 py-8 text-center">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg viewBox="0 0 24 24" class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
                    </div>
                    <h1 style="font-family:Georgia,'Times New Roman',serif; font-size:1.4rem; font-weight:400; color:white; margin:0 0 6px;">
                        You're going to
                    </h1>
                    <p class="text-white/80 text-[14px]">{{ $eventBox->title }}</p>
                </div>

                <div class="px-8 py-7">
                    <div class="text-center mb-6">
                        <p class="text-[15px] font-medium text-[#15140F] mb-1">
                            {{ $tickets->count() > 1 ? $tickets->count() . ' tickets are' : 'Your ticket is' }} on the way to
                        </p>
                        <p class="text-[#1B6B4E] font-semibold text-[15px]">{{ $ticket->buyer_email }}</p>
                    </div>

                    <div class="bg-[#FAFAF7] border border-[#E6E3DC] rounded-[10px] p-4 mb-6">
                        <dl class="space-y-2.5 text-[13px]">
                            <div class="flex justify-between">
                                <dt class="text-[#9C998F]">Event</dt>
                                <dd class="font-medium text-[#15140F]">{{ $eventBox->title }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-[#9C998F]">Date</dt>
                                <dd class="font-medium text-[#15140F]">{{ $eventBox->event_date->format('D, M j, Y · g:ia') }}</dd>
                            </div>
                            @if($eventBox->venue)
                                <div class="flex justify-between">
                                    <dt class="text-[#9C998F]">Venue</dt>
                                    <dd class="font-medium text-[#15140F]">{{ $eventBox->venue }}</dd>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <dt class="text-[#9C998F]">Reference</dt>
                                <dd class="font-mono text-[12px] font-medium text-[#15140F]">{{ $reference }}</dd>
                            </div>
                            @if($tickets->count() > 1)
                                <div class="flex justify-between">
                                    <dt class="text-[#9C998F]">Tickets</dt>
                                    <dd class="font-medium text-[#15140F]">{{ $tickets->count() }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    <div class="bg-[#FFF8E7] border border-[#F0D980] rounded-[8px] px-4 py-3 mb-6">
                        <p class="text-[13px] text-[#6B5900]">
                            Check your inbox — your ticket{{ $tickets->count() > 1 ? 's' : '' }} with QR code{{ $tickets->count() > 1 ? 's' : '' }} will arrive shortly. Check your spam folder if you don't see it within a few minutes.
                        </p>
                    </div>

                    <a
                        href="{{ route('events.show', $eventBox->slug) }}"
                        class="block w-full text-center bg-[#15140F] hover:bg-[#2A2820] text-white font-semibold text-[14px] py-3 px-4 rounded-[8px] transition-colors"
                    >
                        Back to event
                    </a>
                </div>

            @else
                {{-- ── Generic fallback ── --}}
                <div class="bg-gradient-to-br from-[#2A2820] to-[#15140F] px-8 py-8 text-center">
                    <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg viewBox="0 0 24 24" class="w-8 h-8 text-white/70" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                    </div>
                    <h1 style="font-family:Georgia,'Times New Roman',serif; font-size:1.3rem; font-weight:400; color:white; margin:0 0 6px;">
                        Payment processing
                    </h1>
                    <p class="text-white/60 text-[13px]">{{ $eventBox->title }}</p>
                </div>
                <div class="px-8 py-7 text-center">
                    <p class="text-[14px] text-[#6B6862] mb-6">
                        Your payment is being processed. Once confirmed, your ticket will be emailed to you.
                    </p>
                    <a href="{{ route('events.show', $eventBox->slug) }}"
                        class="block w-full text-center bg-[#15140F] hover:bg-[#2A2820] text-white font-semibold text-[14px] py-3 px-4 rounded-[8px] transition-colors">
                        Back to event
                    </a>
                </div>
            @endif

        </div>
    </div>

    <footer class="border-t border-[#E6E3DC] bg-white py-5 text-center text-[12px] text-[#9C998F]">
        Questions? <a href="mailto:support@mypiggybox.com" class="text-[#1B6B4E]">support@mypiggybox.com</a>
    </footer>

</body>
</html>