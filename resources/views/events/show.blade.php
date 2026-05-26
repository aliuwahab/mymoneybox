<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $eventBox->title }} — MyPiggyBox</title>
    <meta name="description" content="{{ $eventBox->tagline ?? Str::limit($eventBox->description, 160) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.pwa')

    @php
        $accent      = $eventBox->accent_color ?? '#1B6B4E';
        $r           = hexdec(substr($accent, 1, 2));
        $g           = hexdec(substr($accent, 3, 2));
        $b           = hexdec(substr($accent, 5, 2));
        $accentDark  = sprintf('#%02x%02x%02x', max(0, $r - 28), max(0, $g - 28), max(0, $b - 28));
        $accentRgb   = "$r, $g, $b";
        $coverUrl    = $eventBox->getCoverImageUrl();
        $gallery     = $eventBox->getGalleryUrls();
    @endphp

    <style>
        :root {
            --evt-accent:      {{ $accent }};
            --evt-accent-dark: {{ $accentDark }};
            --evt-accent-rgb:  {{ $accentRgb }};
        }
        .evt-btn {
            background: var(--evt-accent);
            border-color: var(--evt-accent);
        }
        .evt-btn:hover {
            background: var(--evt-accent-dark);
            border-color: var(--evt-accent-dark);
        }
        .evt-btn:disabled {
            background: #9C998F;
            border-color: #9C998F;
            cursor: not-allowed;
        }
        .evt-type-selected {
            border-color: var(--evt-accent) !important;
            background-color: rgba(var(--evt-accent-rgb), 0.06) !important;
        }
        .evt-radio-selected {
            background: var(--evt-accent);
            border-color: var(--evt-accent);
        }
        .evt-badge {
            color: var(--evt-accent);
            background: rgba(var(--evt-accent-rgb), 0.10);
            border: 1px solid rgba(var(--evt-accent-rgb), 0.25);
        }
        .evt-hero {
            background:
                radial-gradient(circle at top left, rgba(var(--evt-accent-rgb), 0.12), transparent 34rem),
                #FAFAF7;
            border-bottom: 1px solid #E6E3DC;
        }
        .evt-hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(320px, 430px);
            gap: 2rem;
            align-items: center;
        }
        .evt-cover-frame {
            background: #F3F1EB;
            border: 1px solid #E6E3DC;
            border-radius: 16px;
            min-height: 320px;
            max-height: 520px;
            aspect-ratio: 4 / 3;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 16px 48px rgba(21, 20, 15, 0.10);
        }
        .evt-cover-frame img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .evt-cover-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FAFAF7;
            background: linear-gradient(135deg, var(--evt-accent), var(--evt-accent-dark));
            font-family: Georgia, 'Times New Roman', serif;
            font-size: clamp(3rem, 10vw, 7rem);
        }
        @media (max-width: 900px) {
            .evt-hero-grid {
                grid-template-columns: 1fr;
                gap: 1.25rem;
            }
            .evt-cover-frame {
                min-height: 240px;
                max-height: none;
                aspect-ratio: 16 / 11;
                order: -1;
            }
        }
    </style>
</head>
<body class="bg-[#F3F1EB] min-h-screen">

    {{-- Nav --}}
    <nav class="bg-[#15140F]/95 backdrop-blur-sm sticky top-0 z-40 border-b border-white/5">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <span style="display:inline-block;width:26px;height:26px;background:var(--evt-accent);border-radius:5px;text-align:center;line-height:26px;font-weight:700;font-size:13px;color:#FAFAF7;font-family:Arial,sans-serif;">M</span>
                <span class="text-[#FAFAF7] font-semibold text-[14px]">MyPiggyBox</span>
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="text-[#9C998F] text-[13px] hover:text-white transition">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="text-[#9C998F] text-[13px] hover:text-white transition">Sign in</a>
            @endauth
        </div>
    </nav>

    {{-- Hero --}}
    <div class="evt-hero">
        <div class="max-w-6xl mx-auto px-4 py-8 sm:py-10">
            <div class="evt-hero-grid">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-5">
                        @if($eventBox->status->value === 'active' && $eventBox->event_date->isFuture())
                            <span class="evt-badge inline-flex items-center gap-1.5 text-[11.5px] font-semibold px-2.5 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>On Sale
                            </span>
                        @elseif($eventBox->isSoldOut())
                            <span class="inline-flex items-center gap-1.5 text-[11.5px] font-semibold px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Sold Out
                            </span>
                        @elseif($eventBox->status->value === 'ended')
                            <span class="inline-flex items-center gap-1.5 text-[11.5px] font-semibold px-2.5 py-1 rounded-full bg-[#ECEAE3] text-[#6B6862] border border-[#D9D6CE]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#9C998F]"></span>Ended
                            </span>
                        @endif

                        @if($eventBox->organizer_name)
                            <span class="inline-flex items-center gap-1.5 text-[11.5px] font-medium px-2.5 py-1 rounded-full bg-white text-[#6B6862] border border-[#E6E3DC]">
                                Organized by <span class="text-[#15140F] font-semibold">{{ $eventBox->organizer_name }}</span>
                            </span>
                        @endif
                    </div>

                    <h1 style="font-family: Georgia,'Times New Roman',serif; font-size: clamp(2.25rem, 7vw, 4.75rem); font-weight: 400; color: #15140F; letter-spacing: 0; line-height: 0.98; margin: 0 0 16px;">
                        {{ $eventBox->title }}
                    </h1>

                    @if($eventBox->tagline)
                        <p style="font-size: clamp(1rem, 2vw, 1.2rem); color: #6B6862; margin: 0 0 24px; line-height: 1.55; max-width: 620px;">
                            {{ $eventBox->tagline }}
                        </p>
                    @elseif($eventBox->description)
                        <p style="font-size: clamp(1rem, 2vw, 1.2rem); color: #6B6862; margin: 0 0 24px; line-height: 1.55; max-width: 620px;">
                            {{ Str::limit($eventBox->description, 170) }}
                        </p>
                    @endif

                    <div class="grid sm:grid-cols-2 gap-3 max-w-[680px]">
                        <div class="bg-white border border-[#E6E3DC] rounded-[12px] p-4">
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-[9px] flex items-center justify-center flex-none" style="background: rgba(var(--evt-accent-rgb), 0.10);">
                                    <svg viewBox="0 0 24 24" class="w-4 h-4" style="color: var(--evt-accent);" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
                                </div>
                                <div>
                                    <div class="text-[13px] font-semibold text-[#15140F]">{{ $eventBox->event_date->format('D, M j, Y') }}</div>
                                    <div class="text-[12.5px] text-[#6B6862]">{{ $eventBox->event_date->format('g:ia') }}</div>
                                </div>
                            </div>
                        </div>

                        @if($eventBox->venue)
                            <div class="bg-white border border-[#E6E3DC] rounded-[12px] p-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 rounded-[9px] flex items-center justify-center flex-none" style="background: rgba(var(--evt-accent-rgb), 0.10);">
                                        <svg viewBox="0 0 24 24" class="w-4 h-4" style="color: var(--evt-accent);" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="11" r="3"/><path d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-[13px] font-semibold text-[#15140F] break-words">{{ $eventBox->venue }}</div>
                                        <div class="text-[12.5px] text-[#6B6862]">Venue</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="evt-cover-frame">
                    @if($coverUrl)
                        <img src="{{ $coverUrl }}" alt="{{ $eventBox->title }} cover image">
                    @else
                        <div class="evt-cover-placeholder">{{ substr($eventBox->title, 0, 1) }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Flash messages --}}
    <div class="max-w-6xl mx-auto px-4 pt-5">
        @if(session('error'))
            <div class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-[13px] px-4 py-3 rounded-[8px]">
                <svg viewBox="0 0 24 24" class="w-4 h-4 flex-none" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="mb-4 flex items-center gap-2 bg-[#E6F1EB] border border-[#90C7A9] text-[#154F3A] text-[13px] px-4 py-3 rounded-[8px]">
                <svg viewBox="0 0 24 24" class="w-4 h-4 flex-none" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
                {{ session('success') }}
            </div>
        @endif
    </div>

    {{-- Body --}}
    <div class="max-w-6xl mx-auto px-4 py-6 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-6 items-start">

            {{-- Left: About + details --}}
            <div class="space-y-5">
                @if($eventBox->description)
                    <div class="bg-white border border-[#E6E3DC] rounded-[14px] p-6">
                        <h2 style="font-family:Georgia,'Times New Roman',serif;font-size:1.15rem;font-weight:400;color:#15140F;margin:0 0 14px;">About this event</h2>
                        <div class="text-[14px] text-[#6B6862] leading-relaxed whitespace-pre-line">{{ $eventBox->description }}</div>
                    </div>
                @endif

                {{-- Gallery --}}
                @if(count($gallery) > 0)
                    @php $galleryJson = json_encode(array_column($gallery, 'url')); @endphp
                    <div class="bg-white border border-[#E6E3DC] rounded-[14px] overflow-hidden"
                         x-data="{ open: null, photos: {{ $galleryJson }} }"
                         @keydown.escape.window="open = null">

                        <div class="px-5 pt-4 pb-3 border-b border-[#F3F1EB]">
                            <h2 style="font-family:Georgia,'Times New Roman',serif;font-size:1.05rem;font-weight:400;color:#15140F;margin:0;">
                                Gallery <span class="text-[13px] text-[#9C998F] font-sans font-normal ml-1">{{ count($gallery) }} photos</span>
                            </h2>
                        </div>

                        <div class="p-3">
                            @php
                                $first   = $gallery[0] ?? null;
                                $others  = array_slice($gallery, 1, 4);
                                $hasMore = count($gallery) > 5;
                                $extra   = count($gallery) - 5;
                            @endphp

                            @if($first)
                                <div class="grid gap-1.5"
                                     style="grid-template-columns: 1fr 1fr; grid-template-rows: auto auto;">

                                    {{-- First (large) --}}
                                    <button type="button"
                                        @click="open = 0"
                                        class="rounded-[8px] overflow-hidden bg-[#F3F1EB] hover:opacity-95 transition-opacity focus:outline-none"
                                        style="grid-row: span 2; aspect-ratio: 1;">
                                        <img src="{{ $first['url'] }}" alt="" class="w-full h-full object-contain">
                                    </button>

                                    {{-- Next 4 in 2×2 --}}
                                    @foreach($others as $idx => $item)
                                        <button type="button"
                                            @click="open = {{ $idx + 1 }}"
                                            class="relative rounded-[8px] overflow-hidden bg-[#F3F1EB] hover:opacity-95 transition-opacity focus:outline-none"
                                            style="aspect-ratio: 1;">
                                            <img src="{{ $item['url'] }}" alt="" class="w-full h-full object-contain">
                                            @if($hasMore && $idx === 3)
                                                <div class="absolute inset-0 bg-black/55 flex items-center justify-center">
                                                    <span class="text-white font-semibold text-[15px]">+{{ $extra + 1 }}</span>
                                                </div>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            @endif

                            @if(count($gallery) > 5)
                                <button type="button" @click="open = 0"
                                    class="mt-2.5 w-full text-[12.5px] font-medium py-2 rounded-[7px] border border-[#E6E3DC] hover:bg-[#F3F1EB] transition-colors text-[#6B6862]">
                                    View all {{ count($gallery) }} photos
                                </button>
                            @endif
                        </div>

                        {{-- Lightbox --}}
                        <div x-show="open !== null" x-cloak
                             class="fixed inset-0 z-50 flex flex-col bg-black/95"
                             @click.self="open = null">

                            {{-- Top bar --}}
                            <div class="flex items-center justify-between px-4 py-3 flex-none">
                                <span class="text-white/50 text-[13px]" x-text="(open + 1) + ' / ' + photos.length"></span>
                                <button @click="open = null" class="text-white/70 hover:text-white transition p-1">
                                    <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                </button>
                            </div>

                            {{-- Image --}}
                            <div class="flex-1 flex items-center justify-center px-4 pb-4 min-h-0">
                                <img :src="photos[open]" alt="" class="max-w-full max-h-full object-contain rounded-[6px] select-none">
                            </div>

                            {{-- Prev / Next --}}
                            <button x-show="open > 0" @click.stop="open--"
                                class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white transition">
                                <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                            </button>
                            <button x-show="open < photos.length - 1" @click.stop="open++"
                                class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white transition">
                                <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                            </button>

                            {{-- Thumbnail strip --}}
                            @if(count($gallery) > 1)
                                <div class="flex gap-1.5 overflow-x-auto px-4 pb-4 flex-none justify-center" style="scrollbar-width:none;">
                                    @foreach($gallery as $idx => $item)
                                        <button type="button" @click.stop="open = {{ $idx }}"
                                            class="flex-none w-12 h-12 rounded-[5px] overflow-hidden transition-all"
                                            :class="open === {{ $idx }} ? 'ring-2 opacity-100' : 'opacity-45 hover:opacity-70'"
                                            :style="open === {{ $idx }} ? 'ring-color: var(--evt-accent)' : ''">
                                            <img src="{{ $item['url'] }}" alt="" class="w-full h-full object-contain">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Event info card --}}
                <div class="bg-white border border-[#E6E3DC] rounded-[14px] p-6">
                    <h2 style="font-family:Georgia,'Times New Roman',serif;font-size:1.15rem;font-weight:400;color:#15140F;margin:0 0 16px;">Event details</h2>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-[9px] flex items-center justify-center flex-none mt-0.5" style="background: rgba(var(--evt-accent-rgb), 0.10);">
                                <svg viewBox="0 0 24 24" class="w-4 h-4" style="color: var(--evt-accent);" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
                            </div>
                            <div>
                                <div class="text-[13px] font-semibold text-[#15140F]">{{ $eventBox->event_date->format('l, F j, Y') }}</div>
                                <div class="text-[12.5px] text-[#9C998F]">{{ $eventBox->event_date->format('g:ia') }} · Doors open</div>
                            </div>
                        </div>

                        @if($eventBox->venue)
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-[9px] flex items-center justify-center flex-none mt-0.5" style="background: rgba(var(--evt-accent-rgb), 0.10);">
                                    <svg viewBox="0 0 24 24" class="w-4 h-4" style="color: var(--evt-accent);" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="11" r="3"/><path d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                </div>
                                <div>
                                    <div class="text-[13px] font-semibold text-[#15140F]">{{ $eventBox->venue }}</div>
                                    <div class="text-[12.5px] text-[#9C998F]">Event venue</div>
                                </div>
                            </div>
                        @endif

                        @if($eventBox->organizer_name)
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-[9px] flex items-center justify-center flex-none mt-0.5" style="background: rgba(var(--evt-accent-rgb), 0.10);">
                                    <svg viewBox="0 0 24 24" class="w-4 h-4" style="color: var(--evt-accent);" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                </div>
                                <div>
                                    <div class="text-[13px] font-semibold text-[#15140F]">{{ $eventBox->organizer_name }}</div>
                                    <div class="text-[12.5px] text-[#9C998F]">Organizer</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right: Ticket panel --}}
            <div x-data="ticketPanel()" x-init="init()" class="lg:sticky lg:top-20">
                <div class="bg-white border border-[#E6E3DC] rounded-[16px] overflow-hidden shadow-sm">

                    @if($eventBox->status->value === 'ended' || $eventBox->status->value === 'cancelled')
                        <div class="p-6 text-center">
                            <div class="w-12 h-12 rounded-full bg-[#F3F1EB] flex items-center justify-center mx-auto mb-3">
                                <svg viewBox="0 0 24 24" class="w-5 h-5 text-[#9C998F]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                            </div>
                            <div class="text-[14px] font-medium text-[#15140F] mb-1">This event has ended</div>
                            <div class="text-[13px] text-[#9C998F]">Ticket sales are closed.</div>
                        </div>
                    @elseif(!$eventBox->isActive())
                        <div class="p-6 text-center">
                            <div class="text-[14px] text-[#9C998F]">Tickets are not available right now.</div>
                        </div>
                    @else

                        {{-- Panel header --}}
                        <div class="px-5 pt-5 pb-4 border-b border-[#F3F1EB]">
                            <div class="text-[11px] font-semibold text-[#9C998F] uppercase tracking-widest mb-1">Get your ticket</div>
                            @if($eventBox->capacity)
                                @php $pct = min(100, round($eventBox->tickets_sold / $eventBox->capacity * 100)); @endphp
                                <div class="flex items-center justify-between text-[12px] text-[#9C998F] mb-1.5">
                                    <span>{{ number_format($eventBox->tickets_sold) }} sold</span>
                                    <span>{{ number_format($eventBox->capacity) }} capacity</span>
                                </div>
                                <div class="w-full bg-[#F3F1EB] rounded-full h-1.5 overflow-hidden">
                                    <div class="h-1.5 rounded-full transition-all" style="width: {{ $pct }}%; background: var(--evt-accent);"></div>
                                </div>
                            @endif
                        </div>

                        {{-- Ticket type cards --}}
                        <div class="px-5 py-4 space-y-2">
                            @foreach($eventBox->ticketTypes as $type)
                                @php $available = $type->isAvailable(); @endphp
                                <button
                                    type="button"
                                    @click="{{ $available ? 'selectType(' . $type->id . ', \'' . addslashes($type->name) . '\', ' . $type->price . ')' : '' }}"
                                    class="w-full text-left px-4 py-3 rounded-[10px] border-2 transition-all duration-100 {{ !$available ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}"
                                    :class="selectedTypeId === {{ $type->id }} ? 'evt-type-selected' : 'border-[#E6E3DC] bg-[#FAFAF7] hover:border-[#D9D6CE]'"
                                    {{ !$available ? 'disabled' : '' }}
                                >
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <div class="w-4 h-4 rounded-full border-2 flex-none transition-colors"
                                                 :class="selectedTypeId === {{ $type->id }} ? 'evt-radio-selected' : 'border-[#D9D6CE]'"></div>
                                            <div class="min-w-0">
                                                <div class="text-[13.5px] font-semibold text-[#15140F] truncate">{{ $type->name }}</div>
                                                @if($type->description)
                                                    <div class="text-[11.5px] text-[#9C998F] mt-0.5 truncate">{{ $type->description }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex-none text-right">
                                            <div class="text-[16px] font-bold text-[#15140F]">{{ $type->formatPrice() }}</div>
                                            @if(!$available)
                                                <div class="text-[10.5px] font-semibold text-amber-600 mt-0.5">Sold out</div>
                                            @elseif($type->capacity !== null)
                                                <div class="text-[11px] text-[#9C998F] mt-0.5">{{ $type->availableCount() }} left</div>
                                            @endif
                                        </div>
                                    </div>
                                </button>
                            @endforeach
                        </div>

                        {{-- Purchase form --}}
                        <div class="px-5 pb-5 space-y-3">
                            <form action="{{ route('events.purchase', $eventBox->slug) }}" method="POST" @submit.prevent="submitForm">
                                @csrf
                                <input type="hidden" name="ticket_type_id" :value="selectedTypeId">

                                @if($errors->any())
                                    <div class="text-[12px] text-red-600 space-y-0.5 mb-3">
                                        @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                                    </div>
                                @endif

                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-[11.5px] font-semibold text-[#6B6862] mb-1 uppercase tracking-wide">Full name</label>
                                        <input type="text" name="buyer_name" value="{{ old('buyer_name') }}" placeholder="Jane Doe" required
                                            class="w-full border border-[#E6E3DC] rounded-[8px] px-3 py-2.5 text-[13.5px] text-[#15140F] bg-white placeholder-[#C5C2BC] focus:outline-none focus:border-[var(--evt-accent)] transition-colors" />
                                    </div>

                                    <div>
                                        <label class="block text-[11.5px] font-semibold text-[#6B6862] mb-1 uppercase tracking-wide">Email address</label>
                                        <input type="email" name="buyer_email" value="{{ old('buyer_email') }}" placeholder="jane@example.com" required
                                            class="w-full border border-[#E6E3DC] rounded-[8px] px-3 py-2.5 text-[13.5px] text-[#15140F] bg-white placeholder-[#C5C2BC] focus:outline-none focus:border-[var(--evt-accent)] transition-colors" />
                                        <p class="text-[11px] text-[#9C998F] mt-1">Your ticket will be emailed here.</p>
                                    </div>

                                    <div>
                                        <label class="block text-[11.5px] font-semibold text-[#6B6862] mb-1 uppercase tracking-wide">Phone <span class="normal-case font-normal text-[#9C998F]">(optional)</span></label>
                                        <input type="tel" name="buyer_phone" value="{{ old('buyer_phone') }}" placeholder="+233 XX XXX XXXX"
                                            class="w-full border border-[#E6E3DC] rounded-[8px] px-3 py-2.5 text-[13.5px] text-[#15140F] bg-white placeholder-[#C5C2BC] focus:outline-none focus:border-[var(--evt-accent)] transition-colors" />
                                    </div>

                                    <div>
                                        <label class="block text-[11.5px] font-semibold text-[#6B6862] mb-1.5 uppercase tracking-wide">Quantity</label>
                                        <div class="flex items-center gap-2">
                                            <button type="button" @click="quantity = Math.max(1, quantity - 1)"
                                                class="w-9 h-9 rounded-[8px] border border-[#E6E3DC] flex items-center justify-center text-[#15140F] hover:bg-[#F3F1EB] transition-colors font-medium text-[18px] leading-none select-none">−</button>
                                            <span class="w-10 text-center text-[14px] font-semibold text-[#15140F]" x-text="quantity"></span>
                                            <button type="button" @click="quantity = Math.min(10, quantity + 1)"
                                                class="w-9 h-9 rounded-[8px] border border-[#E6E3DC] flex items-center justify-center text-[#15140F] hover:bg-[#F3F1EB] transition-colors font-medium text-[18px] leading-none select-none">+</button>
                                            <span class="text-[12px] text-[#9C998F] ml-1">max 10</span>
                                        </div>
                                    </div>

                                    <input type="hidden" name="quantity" :value="quantity">

                                    <button type="submit"
                                        :disabled="!selectedTypeId"
                                        class="evt-btn w-full text-white font-semibold text-[14px] py-3.5 px-4 rounded-[10px] transition-all duration-150 flex items-center justify-center gap-2 mt-1"
                                        style="letter-spacing: 0.01em;">
                                        <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="8" width="18" height="13" rx="2"/><path d="M16 8V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v3"/><path d="M12 13v3"/><path d="M9.5 13h5"/></svg>
                                        <span x-text="selectedTypeId ? 'Buy ' + quantity + (quantity > 1 ? ' Tickets' : ' Ticket') + ' · GH₵ ' + (Number(selectedPrice) * quantity).toFixed(2) : 'Select a ticket type'"></span>
                                    </button>
                                </div>
                            </form>

                            <p class="text-[11px] text-[#9C998F] text-center pt-1">
                                <svg viewBox="0 0 24 24" class="w-3 h-3 inline-block mr-0.5 -mt-0.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                                Secure payment via TrendiPay · Ticket sent to your email
                            </p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <footer class="border-t border-[#E6E3DC] bg-white py-6 text-center text-[12px] text-[#9C998F]">
        <p>Powered by <a href="{{ route('home') }}" class="font-medium" style="color: var(--evt-accent);">MyPiggyBox</a> &middot; <a href="mailto:support@mypiggybox.com" class="text-[#9C998F] hover:text-[#6B6862]">support@mypiggybox.com</a></p>
    </footer>

    <script>
        function ticketPanel() {
            return {
                selectedTypeId: null,
                selectedPrice: 0,
                selectedName: '',
                quantity: 1,
                init() {
                    @php $firstAvailable = $eventBox->ticketTypes->first(fn($t) => $t->isAvailable()); @endphp
                    @if($firstAvailable)
                        this.selectedTypeId = {{ $firstAvailable->id }};
                        this.selectedPrice  = {{ $firstAvailable->price }};
                        this.selectedName   = '{{ addslashes($firstAvailable->name) }}';
                    @endif
                },
                selectType(id, name, price) {
                    this.selectedTypeId = id;
                    this.selectedName   = name;
                    this.selectedPrice  = price;
                    this.quantity       = 1;
                },
                submitForm(e) {
                    if (!this.selectedTypeId) {
                        alert('Please select a ticket type.');
                        return;
                    }
                    e.target.submit();
                },
            };
        }
    </script>
    @fluxScripts

</body>
</html>
