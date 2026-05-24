<x-layouts.app>
    @php
        $ticketTypesJson = $eventBox->ticketTypes->map(fn($t) => [
            'name'        => $t->name,
            'price'       => (string) $t->price,
            'capacity'    => $t->capacity ? (string) $t->capacity : '',
            'description' => $t->description ?? '',
        ])->values()->toJson();
        $coverUrl  = $eventBox->getCoverImageUrl();
        $gallery   = $eventBox->getGalleryUrls();
    @endphp

    <div class="page-wrap max-w-[720px] mx-auto w-full">

        <div class="mb-6">
            <h1 class="page-title" style="font-size:1.875rem;">Edit Event</h1>
            <p class="tiny mt-1.5">{{ $eventBox->title }}</p>
        </div>

        <form method="POST" action="{{ route('events.update', $eventBox) }}" class="space-y-4" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-[8px] p-3.5">
                    <ul class="text-[13px] text-red-700 space-y-0.5 list-disc list-inside">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            {{-- Event details --}}
            <div class="card">
                <div class="card-head"><span class="card-title">Event details</span></div>
                <div class="card-body space-y-4">
                    <div class="grid gap-1.5">
                        <label for="title" class="text-[13px] font-medium text-[#6B6862]">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" required value="{{ old('title', $eventBox->title) }}" class="@error('title') border-red-400 @enderror" />
                        @error('title')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid gap-1.5">
                        <label for="tagline" class="text-[13px] font-medium text-[#6B6862]">Tagline <span class="text-[#9C998F] font-normal">(optional)</span></label>
                        <input type="text" name="tagline" id="tagline" value="{{ old('tagline', $eventBox->tagline) }}" placeholder="A short, punchy line for your event page" maxlength="180" />
                        @error('tagline')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid gap-1.5">
                        <label for="description" class="text-[13px] font-medium text-[#6B6862]">Description</label>
                        <textarea name="description" id="description" rows="4" class="@error('description') border-red-400 @enderror">{{ old('description', $eventBox->description) }}</textarea>
                        @error('description')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid gap-1.5">
                        <label for="venue" class="text-[13px] font-medium text-[#6B6862]">Venue</label>
                        <input type="text" name="venue" id="venue" value="{{ old('venue', $eventBox->venue) }}" placeholder="e.g. Accra International Conference Centre" class="@error('venue') border-red-400 @enderror" />
                        @error('venue')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-1.5">
                            <label for="event_date" class="text-[13px] font-medium text-[#6B6862]">Event date & time <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="event_date" id="event_date" required value="{{ old('event_date', $eventBox->event_date->format('Y-m-d\TH:i')) }}" class="@error('event_date') border-red-400 @enderror" />
                            @error('event_date')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid gap-1.5">
                            <label for="capacity" class="text-[13px] font-medium text-[#6B6862]">Total capacity <span class="text-[#9C998F] font-normal">(optional)</span></label>
                            <input type="number" name="capacity" id="capacity" min="1" value="{{ old('capacity', $eventBox->capacity) }}" placeholder="Unlimited" class="@error('capacity') border-red-400 @enderror" />
                            @error('capacity')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Design --}}
            <div class="card" x-data="{ removeCover: false }">
                <div class="card-head"><span class="card-title">Design & branding</span></div>
                <div class="card-body space-y-4">

                    {{-- Cover image --}}
                    <div class="grid gap-1.5">
                        <label class="text-[13px] font-medium text-[#6B6862]">Cover image <span class="text-[#9C998F] font-normal">(optional · max 5 MB)</span></label>

                        @if($coverUrl)
                            <div x-show="!removeCover" class="relative rounded-[8px] overflow-hidden bg-[#F3F1EB]" style="aspect-ratio:3/1;">
                                <img src="{{ $coverUrl }}" alt="Cover" class="w-full h-full object-cover">
                                <button type="button" @click="removeCover = true"
                                    class="absolute top-2 right-2 bg-black/50 hover:bg-black/70 text-white rounded-full w-7 h-7 flex items-center justify-center transition">
                                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                </button>
                            </div>
                            <div x-show="removeCover" class="text-[12px] text-amber-700 bg-amber-50 border border-amber-200 px-3 py-2 rounded-[7px] flex items-center justify-between">
                                <span>Cover image will be removed on save.</span>
                                <button type="button" @click="removeCover = false" class="underline">Undo</button>
                            </div>
                            <input type="hidden" name="remove_cover" :value="removeCover ? '1' : '0'">
                        @endif

                        <div x-show="{{ $coverUrl ? '!removeCover ? false : true' : 'true' }}">
                            @if(!$coverUrl)
                                {{-- no existing cover --}}
                            @endif
                        </div>

                        @if(!$coverUrl)
                            <label class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-[#E6E3DC] rounded-[8px] px-4 py-6 cursor-pointer hover:border-[#1B6B4E] transition-colors bg-[#FAFAF7]"
                                   x-data="{ preview: null }"
                                   @dragover.prevent
                                   @drop.prevent="preview = URL.createObjectURL($event.dataTransfer.files[0]); $el.querySelector('input').files = $event.dataTransfer.files">
                                <template x-if="preview">
                                    <img :src="preview" class="w-full rounded-[6px] object-cover max-h-40">
                                </template>
                                <template x-if="!preview">
                                    <div class="text-center">
                                        <svg viewBox="0 0 24 24" class="w-8 h-8 text-[#9C998F] mx-auto mb-1" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                                        <div class="text-[13px] font-medium text-[#6B6862]">Click to upload or drag & drop</div>
                                        <div class="text-[11.5px] text-[#9C998F] mt-0.5">PNG, JPG, WebP · Recommended 1500×500</div>
                                    </div>
                                </template>
                                <input type="file" name="cover_image" accept="image/*" class="sr-only" @change="preview = URL.createObjectURL($event.target.files[0])">
                            </label>
                        @else
                            <label x-show="removeCover"
                                class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-[#E6E3DC] rounded-[8px] px-4 py-6 cursor-pointer hover:border-[#1B6B4E] transition-colors bg-[#FAFAF7]"
                                x-data="{ preview: null }"
                                @dragover.prevent
                                @drop.prevent="preview = URL.createObjectURL($event.dataTransfer.files[0])">
                                <template x-if="preview">
                                    <img :src="preview" class="w-full rounded-[6px] object-cover max-h-40">
                                </template>
                                <template x-if="!preview">
                                    <div class="text-center">
                                        <svg viewBox="0 0 24 24" class="w-8 h-8 text-[#9C998F] mx-auto mb-1" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                                        <div class="text-[13px] font-medium text-[#6B6862]">Upload new cover image</div>
                                        <div class="text-[11.5px] text-[#9C998F] mt-0.5">PNG, JPG, WebP · Recommended 1500×500</div>
                                    </div>
                                </template>
                                <input type="file" name="cover_image" accept="image/*" class="sr-only" @change="preview = URL.createObjectURL($event.target.files[0])">
                            </label>
                        @endif
                        @error('cover_image')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-1.5">
                            <label for="organizer_name" class="text-[13px] font-medium text-[#6B6862]">Organizer name <span class="text-[#9C998F] font-normal">(optional)</span></label>
                            <input type="text" name="organizer_name" id="organizer_name" value="{{ old('organizer_name', $eventBox->organizer_name) }}" placeholder="e.g. Accra Tech Hub" />
                            <p class="text-[11.5px] text-[#9C998F]">Shown as "Organized by …" on the event page.</p>
                            @error('organizer_name')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid gap-1.5" x-data="{ color: '{{ old('accent_color', $eventBox->accent_color ?? '#1B6B4E') }}' }">
                            <label for="accent_color" class="text-[13px] font-medium text-[#6B6862]">Accent colour <span class="text-[#9C998F] font-normal">(optional)</span></label>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-[6px] border border-[#E6E3DC] flex-none" :style="'background:' + color"></div>
                                <input type="color" name="accent_color" id="accent_color" x-model="color"
                                    value="{{ old('accent_color', $eventBox->accent_color ?? '#1B6B4E') }}"
                                    class="h-8 w-full rounded-[6px] border border-[#E6E3DC] cursor-pointer bg-white px-1" />
                            </div>
                            <p class="text-[11.5px] text-[#9C998F]">Used for buttons and highlights on the public page.</p>
                            @error('accent_color')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-1.5">
                            <label for="contact_email" class="text-[13px] font-medium text-[#6B6862]">Contact email <span class="text-[#9C998F] font-normal">(optional)</span></label>
                            <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $eventBox->contact_email) }}" placeholder="hello@example.com" />
                            @error('contact_email')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid gap-1.5">
                            <label for="contact_phone" class="text-[13px] font-medium text-[#6B6862]">Contact phone <span class="text-[#9C998F] font-normal">(optional)</span></label>
                            <input type="tel" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $eventBox->contact_phone) }}" placeholder="+233 …" />
                            @error('contact_phone')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Gallery --}}
            <div class="card" x-data="galleryManager()" x-init="init()">
                <div class="card-head">
                    <span class="card-title">Photo gallery</span>
                    <span class="text-[11.5px] text-[#9C998F]">Up to 20 photos · 5 MB each</span>
                </div>
                <div class="card-body space-y-4">

                    {{-- Existing images --}}
                    @if(count($gallery) > 0)
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2" id="gallery-grid">
                            @foreach($gallery as $item)
                                <div class="relative group rounded-[8px] overflow-hidden bg-[#F3F1EB]" style="aspect-ratio:1;" data-media-id="{{ $item['id'] }}">
                                    <img src="{{ $item['url'] }}" alt="" class="w-full h-full object-cover">
                                    <button type="button"
                                        @click="removeImage({{ $item['id'] }}, $el.closest('[data-media-id]'))"
                                        class="absolute top-1.5 right-1.5 w-6 h-6 bg-black/55 hover:bg-black/80 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg viewBox="0 0 24 24" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- New image previews --}}
                    <template x-if="previews.length > 0">
                        <div>
                            <div class="text-[11.5px] font-semibold text-[#9C998F] uppercase tracking-wide mb-2">New photos to add</div>
                            <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                <template x-for="(src, i) in previews" :key="i">
                                    <div class="relative rounded-[8px] overflow-hidden bg-[#F3F1EB] ring-2 ring-[#1B6B4E]/30" style="aspect-ratio:1;">
                                        <img :src="src" class="w-full h-full object-cover">
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Upload zone --}}
                    <label class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-[#E6E3DC] rounded-[8px] px-4 py-5 cursor-pointer hover:border-[#1B6B4E] hover:bg-[#E6F1EB]/30 transition-colors bg-[#FAFAF7]">
                        <svg viewBox="0 0 24 24" class="w-7 h-7 text-[#9C998F]" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        <div class="text-center">
                            <div class="text-[13px] font-medium text-[#6B6862]">Add photos</div>
                            <div class="text-[11.5px] text-[#9C998F]">PNG, JPG, WebP · Select multiple</div>
                        </div>
                        <input type="file" name="gallery_images[]" accept="image/*" multiple class="sr-only"
                               @change="handleFiles($event)">
                    </label>

                    @error('gallery_images.*')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Ticket types (Alpine.js repeater) --}}
            <div class="card" x-data="ticketTypeEditor({{ $ticketTypesJson }})">
                <div class="card-head">
                    <span class="card-title">Ticket types</span>
                    <span class="text-[11.5px] text-[#9C998F]">Redefining types will reset sold counts</span>
                </div>
                <div class="card-body space-y-3">
                    <div class="hidden sm:grid gap-1" style="grid-template-columns: 1fr 110px 90px 1fr 32px;">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-[#9C998F]">Name</p>
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-[#9C998F]">Price (GH₵)</p>
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-[#9C998F]">Capacity</p>
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-[#9C998F]">Description</p>
                        <div></div>
                    </div>

                    <template x-for="(type, index) in types" :key="index">
                        <div class="grid gap-2 p-3 bg-[#FAFAF7] border border-[#E6E3DC] rounded-[8px]"
                             style="grid-template-columns: 1fr 110px 90px 1fr 32px;">
                            <div>
                                <input type="text"
                                    :name="`ticket_types[${index}][name]`"
                                    x-model="type.name"
                                    required
                                    placeholder="e.g. VIP"
                                    class="w-full text-[13.5px]" />
                            </div>
                            <div class="relative">
                                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[12px] text-[#9C998F]">₵</span>
                                <input type="number"
                                    :name="`ticket_types[${index}][price]`"
                                    x-model="type.price"
                                    required min="0" step="0.01"
                                    placeholder="0.00"
                                    class="w-full pl-6 text-[13.5px]" />
                            </div>
                            <div>
                                <input type="number"
                                    :name="`ticket_types[${index}][capacity]`"
                                    x-model="type.capacity"
                                    min="1"
                                    placeholder="∞"
                                    class="w-full text-[13.5px]" />
                            </div>
                            <div>
                                <input type="text"
                                    :name="`ticket_types[${index}][description]`"
                                    x-model="type.description"
                                    placeholder="Optional note"
                                    class="w-full text-[13.5px]" />
                            </div>
                            <button type="button"
                                @click="remove(index)"
                                :disabled="types.length === 1"
                                class="flex items-center justify-center w-8 h-8 rounded-[6px] bg-red-50 border border-red-200 text-red-600 hover:bg-red-100 disabled:opacity-40 disabled:cursor-not-allowed">
                                <svg viewBox="0 0 24 24" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>

                    <button type="button" @click="add"
                        class="btn text-[13px] border-dashed">
                        <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                        Add ticket type
                    </button>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between gap-3">
                <form id="delete-form" method="POST" action="{{ route('events.destroy', $eventBox) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="button"
                        onclick="if(confirm('Delete this event? Existing tickets remain in the database.')) document.getElementById('delete-form').submit();"
                        class="btn bg-red-50 border-red-200 text-red-700 hover:bg-red-100 hover:border-red-300">
                        Delete event
                    </button>
                </form>

                <div class="flex items-center gap-2">
                    <a href="{{ route('events.dashboard', $eventBox) }}" class="btn">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function galleryManager() {
            return {
                previews: [],
                init() {},
                handleFiles(e) {
                    this.previews = Array.from(e.target.files).map(f => URL.createObjectURL(f));
                },
                async removeImage(mediaId, el) {
                    if (!confirm('Remove this photo?')) return;
                    try {
                        const resp = await fetch('{{ route('events.gallery.remove', [$eventBox, '__ID__']) }}'.replace('__ID__', mediaId), {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                        });
                        if (resp.ok) {
                            el.style.transition = 'opacity .2s';
                            el.style.opacity = '0';
                            setTimeout(() => el.remove(), 200);
                        } else {
                            alert('Failed to remove photo.');
                        }
                    } catch (e) {
                        alert('Network error. Please try again.');
                    }
                },
            };
        }

        function ticketTypeEditor(initial) {
            return {
                types: initial.length > 0 ? initial : [{ name: '', price: '', capacity: '', description: '' }],
                add() {
                    this.types.push({ name: '', price: '', capacity: '', description: '' });
                },
                remove(index) {
                    if (this.types.length > 1) this.types.splice(index, 1);
                },
            };
        }

        if (typeof Alpine !== 'undefined') {
            Alpine.data('ticketTypeEditor', ticketTypeEditor);
        } else {
            document.addEventListener('alpine:init', () => {
                Alpine.data('ticketTypeEditor', ticketTypeEditor);
            });
        }
    </script>
</x-layouts.app>