<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket on its way — {{ $eventBox->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

            {{-- Green top band --}}
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

                @if($ticket)
                    <div class="text-center mb-6">
                        <p class="text-[15px] font-medium text-[#15140F] mb-1">
                            Your ticket is on its way to
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
                                <dd class="font-mono text-[12px] font-medium text-[#15140F]">{{ $ticket->payment_reference }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="bg-[#FFF8E7] border border-[#F0D980] rounded-[8px] px-4 py-3 mb-6">
                        <p class="text-[13px] text-[#6B5900]">
                            Check your inbox — your ticket with QR code will arrive shortly. Check your spam folder if you don't see it within a few minutes.
                        </p>
                    </div>
                @else
                    <div class="text-center mb-6">
                        <p class="text-[14px] text-[#6B6862]">
                            Payment is being processed. Once confirmed, your ticket will be emailed to you.
                        </p>
                    </div>
                @endif

                <a
                    href="{{ route('events.show', $eventBox->slug) }}"
                    class="block w-full text-center bg-[#15140F] hover:bg-[#2A2820] text-white font-semibold text-[14px] py-3 px-4 rounded-[8px] transition-colors"
                >
                    Back to event
                </a>
            </div>
        </div>
    </div>

    <footer class="border-t border-[#E6E3DC] bg-white py-5 text-center text-[12px] text-[#9C998F]">
        Questions? <a href="mailto:support@mypiggybox.com" class="text-[#1B6B4E]">support@mypiggybox.com</a>
    </footer>

</body>
</html>