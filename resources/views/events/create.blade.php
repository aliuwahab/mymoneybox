<x-layouts.app>
    <div class="page-wrap max-w-[640px]">

        {{-- Back --}}
        <div class="mb-3.5">
            <a href="{{ route('events.index') }}" class="btn btn-ghost btn-sm text-[#6B6862]">
                <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                All Events
            </a>
        </div>

        <div class="mb-6">
            <h1 class="page-title">Create an Event</h1>
            <p class="text-[13.5px] text-[#6B6862] mt-1.5">Set up your event and start selling tickets.</p>
        </div>

        <div class="card">
            <div class="card-head">
                <div class="card-title">Event details</div>
            </div>
            <div class="card-body">
                <form action="{{ route('events.store') }}" method="POST" class="space-y-5">
                    @csrf

                    @if($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-[8px] p-3.5">
                            <ul class="text-[13px] text-red-700 space-y-0.5 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div>
                        <label class="block text-[12.5px] font-medium text-[#15140F] mb-1.5">Event title *</label>
                        <input
                            type="text"
                            name="title"
                            value="{{ old('title') }}"
                            placeholder="e.g. Annual Tech Summit 2026"
                            required
                            class="w-full border border-[#E6E3DC] rounded-[7px] px-3 py-2.5 text-[14px] text-[#15140F] bg-white placeholder-[#C5C2BC] focus:outline-none focus:ring-2 focus:ring-[#1B6B4E]/30 focus:border-[#1B6B4E]"
                        />
                    </div>

                    <div>
                        <label class="block text-[12.5px] font-medium text-[#15140F] mb-1.5">Description</label>
                        <textarea
                            name="description"
                            rows="4"
                            placeholder="Tell attendees what to expect..."
                            class="w-full border border-[#E6E3DC] rounded-[7px] px-3 py-2.5 text-[14px] text-[#15140F] bg-white placeholder-[#C5C2BC] focus:outline-none focus:ring-2 focus:ring-[#1B6B4E]/30 focus:border-[#1B6B4E] resize-none"
                        >{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-[12.5px] font-medium text-[#15140F] mb-1.5">Venue</label>
                        <input
                            type="text"
                            name="venue"
                            value="{{ old('venue') }}"
                            placeholder="e.g. Accra International Conference Centre"
                            class="w-full border border-[#E6E3DC] rounded-[7px] px-3 py-2.5 text-[14px] text-[#15140F] bg-white placeholder-[#C5C2BC] focus:outline-none focus:ring-2 focus:ring-[#1B6B4E]/30 focus:border-[#1B6B4E]"
                        />
                    </div>

                    <div>
                        <label class="block text-[12.5px] font-medium text-[#15140F] mb-1.5">Event date & time *</label>
                        <input
                            type="datetime-local"
                            name="event_date"
                            value="{{ old('event_date') }}"
                            required
                            class="w-full border border-[#E6E3DC] rounded-[7px] px-3 py-2.5 text-[14px] text-[#15140F] bg-white focus:outline-none focus:ring-2 focus:ring-[#1B6B4E]/30 focus:border-[#1B6B4E]"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[12.5px] font-medium text-[#15140F] mb-1.5">Ticket price (GHS) *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[13px] text-[#9C998F] font-medium">GH₵</span>
                                <input
                                    type="number"
                                    name="ticket_price"
                                    value="{{ old('ticket_price') }}"
                                    placeholder="0.00"
                                    min="0"
                                    step="0.01"
                                    required
                                    class="w-full border border-[#E6E3DC] rounded-[7px] pl-10 pr-3 py-2.5 text-[14px] text-[#15140F] bg-white placeholder-[#C5C2BC] focus:outline-none focus:ring-2 focus:ring-[#1B6B4E]/30 focus:border-[#1B6B4E]"
                                />
                            </div>
                        </div>
                        <div>
                            <label class="block text-[12.5px] font-medium text-[#15140F] mb-1.5">Capacity (optional)</label>
                            <input
                                type="number"
                                name="capacity"
                                value="{{ old('capacity') }}"
                                placeholder="Leave blank for unlimited"
                                min="1"
                                class="w-full border border-[#E6E3DC] rounded-[7px] px-3 py-2.5 text-[14px] text-[#15140F] bg-white placeholder-[#C5C2BC] focus:outline-none focus:ring-2 focus:ring-[#1B6B4E]/30 focus:border-[#1B6B4E]"
                            />
                        </div>
                    </div>

                    <div class="pt-2 flex items-center gap-3">
                        <button type="submit" class="btn btn-primary">
                            <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
                            Create Event
                        </button>
                        <a href="{{ route('events.index') }}" class="btn">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>