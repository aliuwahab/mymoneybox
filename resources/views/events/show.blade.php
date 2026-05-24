<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $eventBox->title }} — MyPiggyBox</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F3F1EB] min-h-screen">

    {{-- Nav --}}
    <nav class="bg-[#15140F] px-4 py-3">
        <div class="max-w-3xl mx-auto flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <span style="display:inline-block;width:28px;height:28px;background:#1B6B4E;border-radius:6px;text-align:center;line-height:28px;font-weight:700;font-size:14px;color:#FAFAF7;font-family:Arial,sans-serif;">M</span>
                <span class="text-[#FAFAF7] font-semibold text-[15px]">MyPiggyBox</span>
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="text-[#9C998F] text-[13px] hover:text-white transition">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="text-[#9C998F] text-[13px] hover:text-white transition">Sign in</a>
            @endauth
        </div>
    </nav>

    <div class="max-w-3xl mx-auto px-4 py-10">

        {{-- Flash messages --}}
        @if(session('error'))
            <div class="mb-5 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-[13px] px-4 py-3 rounded-[8px]">
                <svg viewBox="0 0 24 24" class="w-4 h-4 flex-none" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="mb-5 flex items-center gap-2 bg-[#E6F1EB] border border-[#90C7A9] text-[#154F3A] text-[13px] px-4 py-3 rounded-[8px]">
                <svg viewBox="0 0 24 24" class="w-4 h-4 flex-none" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Hero --}}
        <div class="bg-white border border-[#E6E3DC] rounded-[14px] overflow-hidden mb-6">
            @if($eventBox->getCoverImageUrl())
                <img src="{{ $eventBox->getCoverImageUrl() }}" alt="{{ $eventBox->title }}" class="w-full h-52 object-cover" />
            @else
                <div class="h-36 bg-gradient-to-br from-[#1B6B4E] to-[#154F3A] flex items-center justify-center">
                    <span class="text-white/80 font-serif text-5xl">{{ substr($eventBox->title, 0, 1) }}</span>
                </div>
            @endif

            <div class="p-6 pb-5">
                {{-- Status badges --}}
                <div class="flex items-center gap-2 mb-3">
                    @if($eventBox->status->value === 'active' && $eventBox->event_date->isFuture())
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#154F3A] bg-[#E6F1EB] border border-[#90C7A9] px-2.5 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#1B6B4E]"></span> On Sale
                        </span>
                    @elseif($eventBox->isSoldOut())
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Sold Out
                        </span>
                    @elseif($eventBox->status->value === 'ended')
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#6B6862] bg-[#F3F1EB] border border-[#E6E3DC] px-2.5 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#9C998F]"></span> Ended
                        </span>
                    @endif

                    <span class="inline-flex items-center gap-1 text-[11px] font-medium text-[#6B6862] bg-[#F3F1EB] border border-[#E6E3DC] px-2.5 py-1 rounded-full">
                        <svg viewBox="0 0 24 24" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
                        {{ $eventBox->event_date->format('D, M j, Y') }}
                    </span>
                </div>

                <h1 style="font-family: Georgia,'Times New Roman',serif; font-size:1.75rem; font-weight:400; color:#15140F; letter-spacing:-0.02em; line-height:1.2; margin:0 0 10px;">
                    {{ $eventBox->title }}
                </h1>

                <div class="flex items-center gap-4 text-[13px] text-[#6B6862]">
                    <span class="flex items-center gap-1">
                        <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="11" r="3"/><path d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        {{ $eventBox->venue ?? 'Venue TBA' }}
                    </span>
                    <span class="flex items-center gap-1">
                        <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        {{ $eventBox->event_date->format('g:ia') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-5">

            {{-- Description --}}
            <div>
                @if($eventBox->description)
                    <div class="bg-white border border-[#E6E3DC] rounded-[12px] p-6 mb-5">
                        <h2 style="font-family:Georgia,'Times New Roman',serif;font-size:1.1rem;font-weight:400;color:#15140F;margin:0 0 12px;">About this event</h2>
                        <div class="text-[14px] text-[#6B6862] leading-relaxed whitespace-pre-line">{{ $eventBox->description }}</div>
                    </div>
                @endif
            </div>

            {{-- Ticket panel --}}
            <div>
                <div class="bg-white border border-[#E6E3DC] rounded-[14px] p-6 sticky top-6">

                    {{-- Price & capacity --}}
                    <div class="mb-5">
                        <div class="text-[11px] font-semibold text-[#9C998F] uppercase tracking-widest mb-1">Ticket price</div>
                        <div style="font-size:2rem;font-weight:700;color:#15140F;letter-spacing:-0.02em;line-height:1;">
                            {{ $eventBox->formatPrice() }}
                        </div>

                        @if($eventBox->capacity !== null)
                            <div class="mt-2 text-[13px] text-[#6B6862]">
                                @if($eventBox->isSoldOut())
                                    <span class="font-medium text-amber-600">Sold out</span>
                                @else
                                    <span class="font-medium text-[#1B6B4E]">{{ $eventBox->availableCapacity() }}</span> spots left
                                @endif
                            </div>

                            {{-- Capacity bar --}}
                            @if(!$eventBox->isSoldOut())
                                @php
                                    $fillPct = min(100, round(($eventBox->tickets_sold / $eventBox->capacity) * 100));
                                @endphp
                                <div class="mt-2 h-1.5 bg-[#F0EDE7] rounded-full overflow-hidden">
                                    <div class="h-full bg-[#1B6B4E] rounded-full" style="width:{{ $fillPct }}%"></div>
                                </div>
                            @endif
                        @endif
                    </div>

                    @if($eventBox->status->value === 'ended' || $eventBox->status->value === 'cancelled')
                        <div class="text-center py-4 text-[14px] text-[#9C998F]">
                            This event has ended.
                        </div>
                    @elseif($eventBox->isSoldOut())
                        <div class="w-full text-center py-3 rounded-[8px] bg-[#F3F1EB] border border-[#E6E3DC] text-[14px] font-semibold text-[#9C998F]">
                            Sold Out
                        </div>
                    @elseif(!$eventBox->isActive())
                        <div class="text-center py-4 text-[14px] text-[#9C998F]">
                            Tickets are not available right now.
                        </div>
                    @else
                        {{-- Purchase form --}}
                        <form action="{{ route('events.purchase', $eventBox->slug) }}" method="POST" class="space-y-3">
                            @csrf

                            @if($errors->any())
                                <div class="text-[12px] text-red-600 space-y-0.5">
                                    @foreach($errors->all() as $error)
                                        <div>{{ $error }}</div>
                                    @endforeach
                                </div>
                            @endif

                            <div>
                                <label class="block text-[12px] font-medium text-[#6B6862] mb-1">Full name *</label>
                                <input
                                    type="text"
                                    name="buyer_name"
                                    value="{{ old('buyer_name') }}"
                                    placeholder="Jane Doe"
                                    required
                                    class="w-full border border-[#E6E3DC] rounded-[7px] px-3 py-2 text-[14px] text-[#15140F] bg-white placeholder-[#C5C2BC] focus:outline-none focus:ring-2 focus:ring-[#1B6B4E]/30 focus:border-[#1B6B4E]"
                                />
                            </div>

                            <div>
                                <label class="block text-[12px] font-medium text-[#6B6862] mb-1">Email address *</label>
                                <input
                                    type="email"
                                    name="buyer_email"
                                    value="{{ old('buyer_email') }}"
                                    placeholder="jane@example.com"
                                    required
                                    class="w-full border border-[#E6E3DC] rounded-[7px] px-3 py-2 text-[14px] text-[#15140F] bg-white placeholder-[#C5C2BC] focus:outline-none focus:ring-2 focus:ring-[#1B6B4E]/30 focus:border-[#1B6B4E]"
                                />
                                <p class="text-[11px] text-[#9C998F] mt-1">Your ticket will be emailed here.</p>
                            </div>

                            <div>
                                <label class="block text-[12px] font-medium text-[#6B6862] mb-1">Phone number (optional)</label>
                                <input
                                    type="tel"
                                    name="buyer_phone"
                                    value="{{ old('buyer_phone') }}"
                                    placeholder="+233 XX XXX XXXX"
                                    class="w-full border border-[#E6E3DC] rounded-[7px] px-3 py-2 text-[14px] text-[#15140F] bg-white placeholder-[#C5C2BC] focus:outline-none focus:ring-2 focus:ring-[#1B6B4E]/30 focus:border-[#1B6B4E]"
                                />
                            </div>

                            <button
                                type="submit"
                                class="w-full bg-[#15140F] hover:bg-[#2A2820] text-white font-semibold text-[14px] py-3 px-4 rounded-[8px] transition-colors duration-150 flex items-center justify-center gap-2"
                            >
                                <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="8" width="18" height="13" rx="2"/><path d="M16 8V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v3"/><path d="M12 13v3"/><path d="M9.5 13h5"/></svg>
                                Buy Ticket — {{ $eventBox->formatPrice() }}
                            </button>

                            <p class="text-[11px] text-[#9C998F] text-center">
                                Secure payment via TrendiPay. Ticket sent by email immediately after payment.
                            </p>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <footer class="border-t border-[#E6E3DC] bg-white mt-12 py-6 text-center text-[12px] text-[#9C998F]">
        <p>Powered by <a href="{{ route('home') }}" class="text-[#1B6B4E] font-medium">MyPiggyBox</a> &middot; <a href="mailto:support@mypiggybox.com" class="text-[#9C998F] hover:text-[#6B6862]">support@mypiggybox.com</a></p>
    </footer>

</body>
</html>