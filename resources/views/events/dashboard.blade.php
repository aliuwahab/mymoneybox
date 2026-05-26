<x-layouts.app>
    <div
        class="page-wrap max-w-[1280px]"
        x-data="{
            tab: 'attendees',
            showValidateModal: false,
            scanTab: 'enter',
            code: '',
            result: null,
            loading: false,
            redeemLoading: false,
            redeemDone: false,
            scanner: null,
            scannerControls: null,
            scannerStarting: false,
            scannerStatus: 'idle',
            scannerError: '',

            openModal() {
                this.showValidateModal = true;
                this.result = null;
                this.code = '';
                this.redeemDone = false;
                this.scannerError = '';
                this.scannerStatus = 'idle';
            },
            closeModal() {
                this.showValidateModal = false;
                this.stopScanner();
            },
            switchScanTab(t) {
                this.scanTab = t;
                if (t === 'scan') {
                    this.$nextTick(() => this.startScanner());
                } else {
                    this.stopScanner();
                }
            },
            async startScanner() {
                if (this.scannerStarting || this.scannerControls) return;

                this.scannerError = '';
                this.scannerStatus = 'starting';

                if (!window.isSecureContext) {
                    this.scannerStatus = 'error';
                    this.scannerError = 'Camera access requires HTTPS. Open this page on the secure site to scan tickets.';
                    return;
                }

                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    this.scannerStatus = 'error';
                    this.scannerError = 'This browser does not support camera access. Enter the ticket code instead.';
                    return;
                }

                if (!window.ZXingBrowser || !window.ZXingBrowser.BrowserQRCodeReader) {
                    this.scannerStatus = 'error';
                    this.scannerError = 'The QR scanner could not load. Refresh the page and try again.';
                    return;
                }

                const videoEl = document.getElementById('qr-video');
                if (!videoEl) return;

                this.scannerStarting = true;
                this.scanner = new window.ZXingBrowser.BrowserQRCodeReader();

                try {
                    this.scannerControls = await this.scanner.decodeFromConstraints(
                        { video: { facingMode: { ideal: 'environment' } }, audio: false },
                        videoEl,
                        (res, err, controls) => {
                            if (!res) return;

                            this.code = res.getText();
                            this.scannerStatus = 'detected';
                            try { controls.stop(); } catch(e) {}
                            this.scannerControls = null;
                            this.scanTab = 'enter';
                            this.$nextTick(() => this.validateCode());
                        }
                    );

                    this.scannerStatus = 'ready';
                } catch(e) {
                    this.stopScanner();
                    this.scannerStatus = 'error';

                    if (e && (e.name === 'NotAllowedError' || e.name === 'PermissionDeniedError')) {
                        this.scannerError = 'Camera permission was blocked. Allow camera access for this site and try again.';
                    } else if (e && (e.name === 'NotFoundError' || e.name === 'DevicesNotFoundError')) {
                        this.scannerError = 'No camera was found on this device. Enter the ticket code instead.';
                    } else if (e && (e.name === 'NotReadableError' || e.name === 'TrackStartError')) {
                        this.scannerError = 'The camera is already in use by another app. Close it and try again.';
                    } else {
                        this.scannerError = 'Could not start the camera. Enter the ticket code or refresh and try again.';
                    }
                } finally {
                    this.scannerStarting = false;
                }
            },
            stopScanner() {
                if (this.scannerControls) {
                    try { this.scannerControls.stop(); } catch(e) {}
                }
                this.scannerControls = null;
                this.scanner = null;
                this.scannerStarting = false;

                const videoEl = document.getElementById('qr-video');
                if (videoEl && videoEl.srcObject) {
                    videoEl.srcObject.getTracks().forEach((track) => track.stop());
                    videoEl.srcObject = null;
                }

                if (this.scannerStatus !== 'error' && this.scannerStatus !== 'detected') {
                    this.scannerStatus = 'idle';
                }
            },
            async validateCode() {
                if (!this.code.trim()) return;
                this.stopScanner();
                this.loading = true;
                this.result = null;
                this.redeemDone = false;
                try {
                    const resp = await fetch('{{ route('events.tickets.validate', $eventBox) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ code: this.code.trim() }),
                    });
                    this.result = await resp.json();
                } catch(e) {
                    this.result = { status: 'error', message: 'Network error. Please try again.' };
                } finally {
                    this.loading = false;
                }
            },
            async redeemTicket(ticketId) {
                this.redeemLoading = true;
                try {
                    const resp = await fetch(`/events/{{ $eventBox->slug }}/tickets/${ticketId}/redeem`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        },
                    });
                    const data = await resp.json();
                    if (data.status === 'redeemed') {
                        this.redeemDone = true;
                        this.result = { status: 'redeemed_now', holder_name: this.result.holder_name };
                    }
                } catch(e) {
                    alert('Network error. Please try again.');
                } finally {
                    this.redeemLoading = false;
                }
            },
            async voidTicket(ticketId) {
                if (!confirm('Void this ticket? It will be marked refunded and can no longer be used.')) return;
                try {
                    const resp = await fetch(`/events/{{ $eventBox->slug }}/tickets/${ticketId}/void`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        },
                    });
                    const data = await resp.json();
                    if (data.status === 'voided') {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Could not void ticket.');
                    }
                } catch(e) {
                    alert('Network error. Please try again.');
                }
            }
        }"
    >
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- Back --}}
        <div class="mb-3.5">
            <a href="{{ route('events.index') }}" class="btn btn-ghost btn-sm text-[#6B6862]">
                <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                All Events
            </a>
        </div>

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
            <div class="flex items-center gap-4 min-w-0">
                <div class="w-14 h-14 rounded-[12px] bg-gradient-to-br from-[#1B6B4E] to-[#154F3A] flex items-center justify-center flex-none">
                    <span class="text-white/80 font-serif text-[22px]">{{ substr($eventBox->title, 0, 1) }}</span>
                </div>
                <div>
                    <div class="flex items-center flex-wrap gap-1.5 mb-1">
                        <span class="pill {{ $eventBox->status->color() }}">
                            <span class="pill-dot"></span>{{ $eventBox->status->label() }}
                        </span>
                    </div>
                    <h1 class="page-title" style="font-size:1.6rem">{{ $eventBox->title }}</h1>
                    <p class="text-[13px] text-[#6B6862] mt-1">
                        {{ $eventBox->event_date->format('D, M j, Y · g:ia') }}
                        @if($eventBox->venue) · {{ $eventBox->venue }} @endif
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <button @click="openModal()" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7h18M3 12h18M3 17h18"/></svg>
                    Validate Ticket
                </button>
                <a href="{{ route('events.edit', $eventBox) }}" class="btn">
                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 4l6 6L8 22H2v-6L14 4Z"/></svg>
                    Edit
                </a>
                <a href="{{ route('events.show', $eventBox->slug) }}" target="_blank" class="btn">
                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                    Public page
                </a>
            </div>
        </div>

        {{-- Stat grid --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 mb-6">
            <div class="stat-card">
                <div class="stat-label">Tickets sold</div>
                <div class="stat-value tnum">{{ number_format($eventBox->tickets_sold) }}</div>
                @if($eventBox->capacity)
                    <div class="stat-delta">of {{ number_format($eventBox->capacity) }} capacity</div>
                @else
                    <div class="stat-delta">Unlimited capacity</div>
                @endif
            </div>
            <div class="stat-card">
                <div class="stat-label">Revenue</div>
                <div class="stat-value tnum text-primary-600">GH₵ {{ number_format($revenue, 2) }}</div>
                <div class="stat-delta">Gross ticket sales</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Ticket types</div>
                <div class="stat-value tnum">{{ $ticketTypes->count() }}</div>
                <div class="stat-delta">
                    @if($ticketTypes->count() === 1)
                        GH₵ {{ number_format((float) $ticketTypes->first()->price, 2) }} per ticket
                    @else
                        {{ $ticketTypes->count() }} price tiers
                    @endif
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Redeemed</div>
                <div class="stat-value tnum">{{ $tickets->where('status.value', 'redeemed')->count() }}</div>
                <div class="stat-delta">Checked in</div>
            </div>
        </div>

        {{-- Status controls --}}
        <div class="flex flex-wrap items-center gap-2 mb-6">
            @if($eventBox->status->value === 'draft')
                <form action="{{ route('events.status', $eventBox) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="active">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m10 8 6 4-6 4V8z"/></svg>
                        Activate Event
                    </button>
                </form>
            @elseif($eventBox->status->value === 'active')
                <form action="{{ route('events.status', $eventBox) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="ended">
                    <button type="submit" class="btn btn-sm border-amber-200 text-amber-700 hover:bg-amber-50" onclick="return confirm('Mark this event as ended?')">
                        Mark as Ended
                    </button>
                </form>
            @elseif($eventBox->status->value === 'ended')
                <form action="{{ route('events.status', $eventBox) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="active">
                    <button type="submit" class="btn btn-sm">Re-activate</button>
                </form>
            @endif
        </div>

        {{-- Tabs --}}
        <div class="tabs">
            <button class="tab" :class="tab === 'attendees' ? 'active' : ''" @click="tab = 'attendees'">Attendees</button>
            <button class="tab" :class="tab === 'details' ? 'active' : ''" @click="tab = 'details'">Event details</button>
            <button class="tab" :class="tab === 'images' ? 'active' : ''" @click="tab = 'images'">Images</button>
        </div>

        {{-- Attendees tab --}}
        <div x-show="tab === 'attendees'" x-cloak>
            <div class="card">
                <div class="card-head">
                    <div class="card-title">Attendees ({{ $tickets->where('payment_status.value', 'completed')->count() }} confirmed)</div>
                </div>
                @if($tickets->where('payment_status.value', 'completed')->count() > 0)
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Ticket code</th>
                                <th>Status</th>
                                <th>Redeemed at</th>
                                <th class="num">Amount</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets->where('payment_status.value', 'completed') as $ticket)
                                <tr>
                                    <td class="font-medium text-[#15140F]">{{ $ticket->buyer_name }}</td>
                                    <td class="muted text-[12.5px]">{{ $ticket->buyer_email }}</td>
                                    <td class="text-[12.5px] text-[#6B6862]">{{ $ticket->ticket_type_name ?? '—' }}</td>
                                    <td>
                                        <span class="font-mono text-[12px] font-medium text-[#15140F]">{{ $ticket->code ?? '—' }}</span>
                                    </td>
                                    <td>
                                        @if($ticket->code)
                                            <span class="pill {{ $ticket->status->color() }}">
                                                <span class="pill-dot"></span>{{ $ticket->status->label() }}
                                            </span>
                                        @else
                                            <span class="pill pill-muted"><span class="pill-dot"></span>Pending</span>
                                        @endif
                                    </td>
                                    <td class="muted text-[12px]">
                                        {{ $ticket->redeemed_at?->format('M j, Y · g:ia') ?? '—' }}
                                    </td>
                                    <td class="num font-semibold text-[#15140F] tnum">GH₵ {{ number_format((float) $ticket->amount, 2) }}</td>
                                    <td>
                                        @if($ticket->status->value !== 'voided')
                                            <button type="button" @click="voidTicket({{ $ticket->id }})"
                                                class="text-[12px] text-red-500 hover:text-red-700 underline whitespace-nowrap">
                                                Void
                                            </button>
                                        @else
                                            <span class="text-[11.5px] text-[#C5C2BC]">Voided</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="card-body text-center py-12">
                        <div class="text-[#9C998F] mb-1">No confirmed attendees yet</div>
                        <div class="tiny">Share your event to start selling tickets.</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Details tab --}}
        <div x-show="tab === 'details'" x-cloak>
            <div class="card mb-4">
                <div class="card-head">
                    <div class="card-title">Event details</div>
                    <a href="{{ route('events.edit', $eventBox) }}" class="btn btn-sm">Edit</a>
                </div>
                <div class="card-body">
                    <dl class="space-y-3 text-[13px]">
                        <div class="flex justify-between gap-4"><dt class="text-[#6B6862]">Title</dt><dd class="font-medium text-right">{{ $eventBox->title }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-[#6B6862]">Date & time</dt><dd class="font-medium">{{ $eventBox->event_date->format('D, M j, Y · g:ia') }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-[#6B6862]">Venue</dt><dd class="font-medium">{{ $eventBox->venue ?? '—' }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-[#6B6862]">Capacity</dt><dd class="font-medium">{{ $eventBox->capacity ? number_format($eventBox->capacity) . ' seats' : 'Unlimited' }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-[#6B6862]">Status</dt><dd><span class="pill {{ $eventBox->status->color() }}"><span class="pill-dot"></span>{{ $eventBox->status->label() }}</span></dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-[#6B6862]">Public link</dt>
                            <dd><a href="{{ route('events.show', $eventBox->slug) }}" target="_blank" class="text-[#1B6B4E] text-[12px] font-medium">View event page →</a></dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Ticket type breakdown --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-title">Ticket types</div>
                </div>
                @if($ticketTypes->isNotEmpty())
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th class="num">Price</th>
                                <th class="num">Sold</th>
                                <th class="num">Capacity</th>
                                <th class="num">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ticketTypes as $type)
                                @php
                                    $typeRevenue = $tickets
                                        ->where('payment_status.value', 'completed')
                                        ->where('ticket_type_id', $type->id)
                                        ->sum('amount');
                                @endphp
                                <tr>
                                    <td class="font-medium text-[#15140F]">
                                        {{ $type->name }}
                                        @if($type->description)
                                            <span class="block text-[11.5px] text-[#9C998F] font-normal">{{ $type->description }}</span>
                                        @endif
                                    </td>
                                    <td class="num tnum">GH₵ {{ number_format((float) $type->price, 2) }}</td>
                                    <td class="num tnum">{{ number_format($type->sold) }}</td>
                                    <td class="num tnum text-[#6B6862]">{{ $type->capacity ? number_format($type->capacity) : '∞' }}</td>
                                    <td class="num tnum font-semibold text-[#15140F]">GH₵ {{ number_format((float) $typeRevenue, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="card-body text-center py-8 text-[#9C998F] text-[13px]">No ticket types configured.</div>
                @endif
            </div>
        </div>

        {{-- Images tab --}}
        <div x-show="tab === 'images'" x-cloak>

            {{-- Cover image --}}
            <div class="card mb-4">
                <div class="card-head">
                    <div class="card-title">Cover image</div>
                    <a href="{{ route('events.edit', $eventBox) }}" class="btn btn-sm">Edit</a>
                </div>
                @if($coverUrl)
                    <div class="p-4">
                        <img src="{{ $coverUrl }}" alt="Cover" class="w-full max-h-[320px] object-cover rounded-[8px]">
                    </div>
                @else
                    <div class="card-body text-center py-8 text-[#9C998F] text-[13px]">
                        No cover image uploaded.
                    </div>
                @endif
            </div>

            {{-- Gallery --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-title">Gallery ({{ count($gallery) }} {{ Str::plural('photo', count($gallery)) }})</div>
                    <a href="{{ route('events.edit', $eventBox) }}" class="btn btn-sm">Edit</a>
                </div>
                @if(count($gallery) > 0)
                    <div class="p-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        @foreach($gallery as $item)
                            <a href="{{ $item['url'] }}" target="_blank" rel="noopener"
                               class="block rounded-[8px] overflow-hidden bg-[#F3F1EB] hover:opacity-90 transition-opacity"
                               style="aspect-ratio:1;">
                                <img src="{{ $item['url'] }}" alt="Gallery photo {{ $loop->iteration }}"
                                     class="w-full h-full object-cover">
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="card-body text-center py-8 text-[#9C998F] text-[13px]">
                        No gallery photos uploaded.
                    </div>
                @endif
            </div>

        </div>

    {{-- ── Validate Ticket Modal ── --}}
    <div
        x-show="showValidateModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-[#15140F]/60" @click="closeModal()"></div>

        {{-- Modal panel --}}
        <div
            class="relative bg-white rounded-[14px] shadow-2xl w-full max-w-[440px] overflow-hidden"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            {{-- Modal header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E6E3DC]">
                <div class="text-[15px] font-semibold text-[#15140F]">Validate Ticket</div>
                <button @click="closeModal()" class="w-7 h-7 rounded-full bg-[#F3F1EB] flex items-center justify-center text-[#6B6862] hover:bg-[#E6E3DC]">
                    <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>

            {{-- Tabs --}}
            <div class="flex border-b border-[#E6E3DC]">
                <button
                    class="flex-1 py-2.5 text-[13px] font-medium transition"
                    :class="scanTab === 'enter' ? 'text-[#15140F] border-b-2 border-[#15140F]' : 'text-[#9C998F]'"
                    @click="switchScanTab('enter')"
                >
                    Enter Code
                </button>
                <button
                    class="flex-1 py-2.5 text-[13px] font-medium transition"
                    :class="scanTab === 'scan' ? 'text-[#15140F] border-b-2 border-[#15140F]' : 'text-[#9C998F]'"
                    @click="switchScanTab('scan')"
                >
                    Scan QR
                </button>
            </div>

            <div class="p-6">

                {{-- Enter code tab --}}
                <div x-show="scanTab === 'enter'">
                    <div class="mb-4">
                        <label class="block text-[12.5px] font-medium text-[#6B6862] mb-1.5">Ticket code</label>
                        <input
                            type="text"
                            x-model="code"
                            placeholder="TKT-XXXX-XXXX-XXXX"
                            @keydown.enter="validateCode()"
                            class="w-full border border-[#E6E3DC] rounded-[7px] px-3 py-2.5 text-[14px] font-mono text-[#15140F] bg-white placeholder-[#C5C2BC] focus:outline-none focus:ring-2 focus:ring-[#1B6B4E]/30 focus:border-[#1B6B4E] uppercase"
                        />
                    </div>
                    <button
                        @click="validateCode()"
                        :disabled="loading || !code.trim()"
                        class="w-full bg-[#15140F] hover:bg-[#2A2820] disabled:opacity-50 text-white font-semibold text-[14px] py-2.5 px-4 rounded-[8px] transition-colors flex items-center justify-center gap-2"
                    >
                        <span x-show="!loading">Check ticket</span>
                        <span x-show="loading" class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8h4z"/></svg>
                            Checking...
                        </span>
                    </button>
                </div>

                {{-- Scan QR tab --}}
                <div x-show="scanTab === 'scan'">
                    <div class="relative rounded-[8px] overflow-hidden bg-black mb-3 aspect-square">
                        <video id="qr-video" class="w-full h-full object-cover" autoplay muted playsinline></video>
                        <div
                            x-show="scannerStatus === 'starting'"
                            class="absolute inset-0 flex items-center justify-center bg-black/70 text-white text-[13px] font-medium"
                        >
                            Starting camera...
                        </div>
                    </div>
                    <template x-if="scannerStatus === 'error'">
                        <div class="bg-red-50 border border-red-200 rounded-[8px] px-3 py-2 mb-3">
                            <p class="text-[12px] text-red-700" x-text="scannerError"></p>
                        </div>
                    </template>
                    <p class="text-[12px] text-[#9C998F] text-center">Point the camera at the attendee's QR code. It will be detected automatically.</p>
                </div>

                {{-- Result --}}
                <div x-show="result !== null" class="mt-4">

                    {{-- Valid --}}
                    <div x-show="result && result.status === 'valid'" class="bg-[#E6F1EB] border border-[#90C7A9] rounded-[10px] p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <svg viewBox="0 0 24 24" class="w-5 h-5 text-[#1B6B4E]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
                            <span class="font-semibold text-[#154F3A] text-[14px]">Valid ticket</span>
                        </div>
                        <p class="text-[13px] text-[#154F3A] mb-3">Holder: <strong x-text="result && result.holder_name"></strong></p>
                        <button
                            @click="redeemTicket(result.ticket_id)"
                            :disabled="redeemLoading"
                            class="w-full bg-[#1B6B4E] hover:bg-[#154F3A] disabled:opacity-50 text-white font-semibold text-[13px] py-2 px-4 rounded-[7px] transition-colors flex items-center justify-center gap-2"
                        >
                            <span x-show="!redeemLoading">Redeem ticket</span>
                            <span x-show="redeemLoading" class="flex items-center gap-2">
                                <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8h4z"/></svg>
                                Redeeming...
                            </span>
                        </button>
                    </div>

                    {{-- Redeemed now --}}
                    <div x-show="result && result.status === 'redeemed_now'" class="bg-[#E6F1EB] border border-[#90C7A9] rounded-[10px] p-4 text-center">
                        <svg viewBox="0 0 24 24" class="w-8 h-8 text-[#1B6B4E] mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
                        <div class="font-semibold text-[#154F3A] text-[15px] mb-1">Redeemed</div>
                        <div class="text-[13px] text-[#154F3A]" x-text="'Welcome, ' + (result && result.holder_name) + '!'"></div>
                    </div>

                    {{-- Already redeemed --}}
                    <div x-show="result && result.status === 'already_redeemed'" class="bg-amber-50 border border-amber-200 rounded-[10px] p-4">
                        <div class="font-semibold text-amber-700 text-[14px] mb-1">Already redeemed</div>
                        <p class="text-[13px] text-amber-700">This ticket was already used by <strong x-text="result && result.holder_name"></strong>.</p>
                        <p class="text-[12px] text-amber-600 mt-1">At: <span x-text="result && result.redeemed_at"></span></p>
                    </div>

                    {{-- Not found --}}
                    <div x-show="result && result.status === 'not_found'" class="bg-red-50 border border-red-200 rounded-[10px] p-4">
                        <div class="font-semibold text-red-700 text-[14px] mb-1">Ticket not found</div>
                        <p class="text-[13px] text-red-600">No ticket with this code exists for this event. Please double-check the code.</p>
                    </div>

                    {{-- Payment pending --}}
                    <div x-show="result && result.status === 'payment_pending'" class="bg-amber-50 border border-amber-200 rounded-[10px] p-4">
                        <div class="font-semibold text-amber-700 text-[14px] mb-1">Payment not confirmed</div>
                        <p class="text-[13px] text-amber-700">Payment has not been confirmed yet for this ticket.</p>
                    </div>

                    {{-- Voided --}}
                    <div x-show="result && result.status === 'voided'" class="bg-red-50 border border-red-200 rounded-[10px] p-4">
                        <div class="font-semibold text-red-700 text-[14px] mb-1">Ticket voided</div>
                        <p class="text-[13px] text-red-600">This ticket has been voided and cannot be used.</p>
                    </div>

                    {{-- Error --}}
                    <div x-show="result && result.status === 'error'" class="bg-red-50 border border-red-200 rounded-[10px] p-4">
                        <div class="font-semibold text-red-700 text-[14px] mb-1">Error</div>
                        <p class="text-[13px] text-red-600" x-text="result && result.message"></p>
                    </div>

                    {{-- Try another --}}
                    <div x-show="result !== null" class="mt-3">
                        <button
                            @click="result = null; code = ''; redeemDone = false; scannerError = ''; scannerStatus = scanTab === 'scan' ? 'idle' : scannerStatus; if (scanTab === 'scan') $nextTick(() => startScanner());"
                            class="text-[13px] text-[#6B6862] hover:text-[#15140F] underline"
                        >
                            Check another ticket
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>{{-- end page-wrap x-data --}}

</x-layouts.app>
