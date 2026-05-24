<x-layouts.app>
    <div class="page-wrap max-w-[1280px]">

        {{-- Page header --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 sm:gap-6 mb-6">
            <div>
                <h1 class="page-title">Your Events</h1>
                <p class="text-[13.5px] text-[#6B6862] mt-1.5">
                    {{ $eventBoxes->total() }} total
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('events.create') }}" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    Create EventBox
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-5 flex items-center gap-2 bg-[#E6F1EB] border border-[#90C7A9] text-[#154F3A] text-[13px] px-4 py-3 rounded-[8px]">
                <svg viewBox="0 0 24 24" class="w-4 h-4 flex-none" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
                {{ session('success') }}
            </div>
        @endif

        @if($eventBoxes->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($eventBoxes as $event)
                    <div class="card overflow-hidden">
                        {{-- Cover / header band --}}
                        <div class="h-[70px] bg-gradient-to-br from-[#1B6B4E] to-[#154F3A] relative p-3.5 pb-0">
                            <div class="absolute inset-0 flex items-center justify-center text-white/80 font-serif text-[28px]">
                                {{ substr($event->title, 0, 1) }}
                            </div>
                        </div>

                        <div class="p-4">
                            {{-- Status badge --}}
                            <div class="flex items-center gap-1.5 mb-2">
                                <span class="pill {{ $event->status->color() }}">
                                    <span class="pill-dot"></span>
                                    {{ $event->status->label() }}
                                </span>
                            </div>

                            <div class="text-[15px] font-semibold text-[#15140F] tracking-tight mb-0.5 leading-snug">
                                {{ Str::limit($event->title, 52) }}
                            </div>

                            <div class="tiny mb-3">
                                {{ $event->event_date->format('M j, Y · g:ia') }}
                                @if($event->venue)
                                    · {{ Str::limit($event->venue, 30) }}
                                @endif
                            </div>

                            {{-- Tickets progress --}}
                            <div class="flex items-baseline justify-between mb-1 text-[13px]">
                                <span class="font-semibold text-[#15140F] tnum">{{ $event->tickets_sold }}</span>
                                <span class="tiny">
                                    @if($event->capacity)
                                        of {{ $event->capacity }} sold
                                    @else
                                        tickets sold
                                    @endif
                                </span>
                            </div>
                            @if($event->capacity)
                                @php $pct = min(100, round(($event->tickets_sold / $event->capacity) * 100)); @endphp
                                <div class="progress-track mb-3">
                                    <div class="progress-fill" style="width: {{ $pct }}%"></div>
                                </div>
                            @endif

                            <div class="flex items-center gap-2 mt-3">
                                <a href="{{ route('events.dashboard', $event) }}" class="btn btn-primary btn-sm flex-1 justify-center">
                                    Dashboard
                                </a>
                                <a href="{{ route('events.edit', $event) }}" class="btn btn-sm">
                                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 4l6 6L8 22H2v-6L14 4Z"/></svg>
                                </a>
                                <a href="{{ route('events.show', $event->slug) }}" target="_blank" class="btn btn-sm">
                                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($eventBoxes->hasPages())
                <div class="mt-8">{{ $eventBoxes->links() }}</div>
            @endif
        @else
            <div class="border-2 border-dashed border-[#D9D6CE] rounded-[10px] p-12 text-center">
                <div class="w-12 h-12 rounded-xl bg-primary-50 text-primary-600 grid place-items-center mx-auto mb-4">
                    <svg viewBox="0 0 24 24" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
                </div>
                <h3 class="text-[15px] font-semibold text-[#15140F] mb-1">No events yet</h3>
                <p class="tiny mb-5">Create your first event and start selling tickets.</p>
                <a href="{{ route('events.create') }}" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    Create EventBox
                </a>
            </div>
        @endif
    </div>
</x-layouts.app>