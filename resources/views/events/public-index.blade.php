<x-layouts.guest>

    {{-- Hero --}}
    <div class="bg-[#15140F] relative overflow-hidden">
        <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 30% 50%, #fff 1px, transparent 0), radial-gradient(circle at 70% 80%, #fff 1px, transparent 0); background-size: 40px 40px;"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-20">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 text-[#1B6B4E] bg-[#1B6B4E]/15 border border-[#1B6B4E]/25 px-3 py-1 rounded-full text-[12px] font-semibold mb-5">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#1B6B4E] animate-pulse"></span>
                    Live events in Ghana
                </div>
                <h1 style="font-family: Georgia,'Times New Roman',serif; font-size: clamp(2rem, 5vw, 3.25rem); font-weight: 400; color: #FAFAF7; letter-spacing: -0.025em; line-height: 1.12; margin: 0 0 16px;">
                    Discover & attend<br>unforgettable events
                </h1>
                <p class="text-[#9C998F] text-[15px] leading-relaxed mb-8 max-w-lg">
                    From concerts to conferences — find events near you and get your tickets in seconds.
                </p>

                {{-- Search --}}
                <form method="GET" action="{{ route('events.public.index') }}" class="flex gap-2 max-w-lg">
                    <div class="flex-1 flex items-center gap-2 bg-white/10 border border-white/15 rounded-[8px] px-3 focus-within:bg-white/15 focus-within:border-white/30 transition">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 text-white/40 flex-none" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search events or venues…"
                               class="flex-1 bg-transparent border-0 outline-none text-[13.5px] text-white placeholder-white/35 py-2.5" />
                    </div>
                    <button type="submit"
                            class="px-5 py-2.5 bg-[#1B6B4E] hover:bg-[#154F3A] text-white font-semibold text-[13.5px] rounded-[8px] transition-colors whitespace-nowrap">
                        Search
                    </button>
                    @if(request('search'))
                        <a href="{{ route('events.public.index') }}"
                           class="px-4 py-2.5 bg-white/10 hover:bg-white/15 text-white/70 text-[13.5px] rounded-[8px] transition-colors flex items-center">
                            Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 pb-20">

        {{-- Result header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                @if(request('search'))
                    <h2 class="text-[17px] font-semibold text-[#15140F]">Results for "{{ request('search') }}"</h2>
                    <p class="text-[13px] text-[#9C998F] mt-0.5">{{ $eventBoxes->total() }} {{ Str::plural('event', $eventBoxes->total()) }} found</p>
                @else
                    <h2 class="text-[17px] font-semibold text-[#15140F]">Upcoming events</h2>
                    <p class="text-[13px] text-[#9C998F] mt-0.5">{{ $eventBoxes->total() }} {{ Str::plural('event', $eventBoxes->total()) }} available</p>
                @endif
            </div>
        </div>

        @if($eventBoxes->count() > 0)

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($eventBoxes as $event)
                    @php
                        $accent    = $event->accent_color ?? '#1B6B4E';
                        $r         = hexdec(substr($accent, 1, 2));
                        $g         = hexdec(substr($accent, 3, 2));
                        $b         = hexdec(substr($accent, 5, 2));
                        $darker    = sprintf('#%02x%02x%02x', max(0,$r-28), max(0,$g-28), max(0,$b-28));
                        $coverUrl  = $event->getCoverImageUrl();
                        $minPrice  = $event->ticketTypes->min('price');
                        $soldOut   = $event->isSoldOut();
                        $isToday   = $event->event_date->isToday();
                        $isTomorrow= $event->event_date->isTomorrow();
                    @endphp

                    <a href="{{ route('events.show', $event->slug) }}"
                       class="group bg-white border border-[#E6E3DC] rounded-[14px] overflow-hidden hover:shadow-[0_8px_30px_-8px_rgba(20,18,12,.18)] hover:-translate-y-0.5 transition-all duration-200 flex flex-col no-underline">

                        {{-- Cover --}}
                        <div class="relative overflow-hidden" style="aspect-ratio: 2/1;">
                            @if($coverUrl)
                                <img src="{{ $coverUrl }}" alt="{{ $event->title }}"
                                     class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                            @else
                                <div class="w-full h-full flex items-center justify-center group-hover:scale-[1.03] transition-transform duration-500"
                                     style="background: linear-gradient(135deg, {{ $accent }} 0%, {{ $darker }} 100%);">
                                    <span style="font-family:Georgia,serif; font-size:3.5rem; color:rgba(255,255,255,.35); font-weight:400; line-height:1; user-select:none;">
                                        {{ substr($event->title, 0, 1) }}
                                    </span>
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                            @endif

                            {{-- Date chip --}}
                            <div class="absolute top-3 left-3">
                                <div class="bg-white rounded-[8px] px-2.5 py-1.5 text-center shadow-sm min-w-[44px]">
                                    <div class="text-[10px] font-bold uppercase tracking-wide text-[#9C998F] leading-none">
                                        {{ $event->event_date->format('M') }}
                                    </div>
                                    <div class="text-[18px] font-bold text-[#15140F] leading-tight">
                                        {{ $event->event_date->format('j') }}
                                    </div>
                                </div>
                            </div>

                            {{-- Status --}}
                            @if($soldOut)
                                <div class="absolute top-3 right-3">
                                    <span class="bg-amber-500/90 backdrop-blur-sm text-white text-[10.5px] font-bold px-2 py-1 rounded-full">Sold Out</span>
                                </div>
                            @elseif($isToday)
                                <div class="absolute top-3 right-3">
                                    <span class="bg-emerald-500/90 backdrop-blur-sm text-white text-[10.5px] font-bold px-2 py-1 rounded-full animate-pulse">Today</span>
                                </div>
                            @elseif($isTomorrow)
                                <div class="absolute top-3 right-3">
                                    <span class="bg-[#1B6B4E]/90 backdrop-blur-sm text-white text-[10.5px] font-bold px-2 py-1 rounded-full">Tomorrow</span>
                                </div>
                            @endif
                        </div>

                        {{-- Body --}}
                        <div class="p-4 flex-1 flex flex-col gap-2">

                            {{-- Title + tagline --}}
                            <div>
                                <h3 style="font-family:Georgia,'Times New Roman',serif; font-size:1.05rem; font-weight:400; color:#15140F; line-height:1.25; margin:0 0 4px;">
                                    {{ $event->title }}
                                </h3>
                                @if($event->tagline)
                                    <p class="text-[12.5px] text-[#6B6862] leading-snug line-clamp-1">{{ $event->tagline }}</p>
                                @endif
                            </div>

                            {{-- Meta --}}
                            <div class="space-y-1">
                                <div class="flex items-center gap-1.5 text-[12.5px] text-[#9C998F]">
                                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5 flex-none" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
                                    <span>
                                        @if($isToday)
                                            Today
                                        @elseif($isTomorrow)
                                            Tomorrow
                                        @else
                                            {{ $event->event_date->format('D, M j') }}
                                        @endif
                                        · {{ $event->event_date->format('g:ia') }}
                                    </span>
                                </div>

                                @if($event->venue)
                                    <div class="flex items-center gap-1.5 text-[12.5px] text-[#9C998F]">
                                        <svg viewBox="0 0 24 24" class="w-3.5 h-3.5 flex-none" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="11" r="3"/><path d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                        <span class="truncate">{{ $event->venue }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1"></div>

                            {{-- Footer: price + CTA --}}
                            <div class="flex items-center justify-between pt-2.5 border-t border-[#F3F1EB] mt-1">
                                <div>
                                    @if($soldOut)
                                        <span class="text-[12px] font-semibold text-amber-600">Sold out</span>
                                    @elseif($minPrice !== null)
                                        <div class="text-[10.5px] text-[#9C998F] leading-none mb-0.5">From</div>
                                        <div class="text-[15px] font-bold text-[#15140F]">GH₵ {{ number_format((float)$minPrice, 2) }}</div>
                                    @else
                                        <span class="text-[13px] font-semibold text-[#15140F]">Free</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1.5 text-[13px] font-semibold text-white px-3.5 py-1.5 rounded-[7px] transition-colors"
                                     style="background: {{ $accent }};">
                                    <span>{{ $soldOut ? 'View' : 'Get tickets' }}</span>
                                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($eventBoxes->hasPages())
                <div class="mt-10">{{ $eventBoxes->appends(request()->query())->links() }}</div>
            @endif

        @else

            {{-- Empty state --}}
            <div class="text-center py-20">
                <div class="w-16 h-16 rounded-2xl bg-[#F3F1EB] flex items-center justify-center mx-auto mb-5">
                    <svg viewBox="0 0 24 24" class="w-8 h-8 text-[#9C998F]" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg>
                </div>
                @if(request('search'))
                    <h3 style="font-family:Georgia,serif;font-size:1.3rem;font-weight:400;color:#15140F;margin:0 0 8px;">No events found</h3>
                    <p class="text-[14px] text-[#9C998F] mb-6">No events match "{{ request('search') }}". Try a different search.</p>
                    <a href="{{ route('events.public.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#15140F] text-white text-[13px] font-medium rounded-[8px] hover:bg-[#2A2820] transition-colors">
                        View all events
                    </a>
                @else
                    <h3 style="font-family:Georgia,serif;font-size:1.3rem;font-weight:400;color:#15140F;margin:0 0 8px;">No upcoming events</h3>
                    <p class="text-[14px] text-[#9C998F] mb-6">Check back soon — new events are added regularly.</p>
                    @auth
                        <a href="{{ route('events.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#1B6B4E] text-white text-[13px] font-medium rounded-[8px] hover:bg-[#154F3A] transition-colors">
                            <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                            Create an event
                        </a>
                    @endauth
                @endif
            </div>

        @endif
    </div>

</x-layouts.guest>