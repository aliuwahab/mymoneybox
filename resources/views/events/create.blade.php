<x-layouts.app>
    <div class="new-box-page"
         x-data="eventBoxForm()"
         x-init="init()">
        <style>
            .new-box-page {
                --nb-bg: #FAFAF7;
                --nb-panel: #FFFFFF;
                --nb-border: #E6E3DC;
                --nb-border-2: #D9D6CE;
                --nb-sidebar-2: #ECEAE3;
                --nb-fg: #15140F;
                --nb-fg-2: #6B6862;
                --nb-fg-3: #9C998F;
                --nb-accent: #1B6B4E;
                --nb-accent-hover: #154F3A;
                --nb-accent-soft: #E6F1EB;
                --nb-radius: 10px;
                --nb-radius-sm: 6px;
                --nb-shadow: 0 1px 0 rgba(20,18,12,.04), 0 1px 2px rgba(20,18,12,.04);
                width: 100%;
                max-width: 1280px;
                color: var(--nb-fg);
                font-size: 14px;
                line-height: 1.5;
            }
            .new-box-page [x-cloak] { display: none !important; }
            .new-box-page * { box-sizing: border-box; }
            .new-box-page button, .new-box-page input, .new-box-page select, .new-box-page textarea { font: inherit; color: inherit; }
            .new-box-page .page-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 24px; margin-bottom: 24px; }
            .new-box-page .page-title { font-family: "Instrument Serif", Georgia, serif; font-size: 38px; line-height: 1.05; letter-spacing: 0; margin: 0; color: var(--nb-fg); font-weight: 400; }
            .new-box-page .page-sub { color: var(--nb-fg-2); font-size: 13.5px; margin-top: 6px; }
            .new-box-page .btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 34px; padding: 7px 13px; border-radius: var(--nb-radius-sm); font-size: 13px; font-weight: 500; border: 1px solid var(--nb-border); background: var(--nb-panel); color: var(--nb-fg); box-shadow: var(--nb-shadow); transition: background .12s, border-color .12s, transform .08s; cursor: pointer; }
            .new-box-page .btn:hover { background: #FBFAF6; border-color: var(--nb-border-2); }
            .new-box-page .btn:active { transform: translateY(.5px); }
            .new-box-page .btn.primary { background: var(--nb-accent); color: #fff; border-color: var(--nb-accent); }
            .new-box-page .btn.primary:hover { background: var(--nb-accent-hover); border-color: var(--nb-accent-hover); }
            .new-box-page .btn.ghost { background: transparent; box-shadow: none; border-color: transparent; }
            .new-box-page .btn.ghost:hover { background: rgba(0,0,0,.04); }
            .new-box-page .btn.danger { background: #FEF2F2; color: #B91C1C; border-color: #FECACA; }
            .new-box-page .btn.danger:hover { background: #FEE2E2; }
            .new-box-page .btn:disabled { opacity: .5; cursor: not-allowed; transform: none; }
            .new-box-page .stepper { display: flex; align-items: center; gap: 8px; margin-bottom: 24px; overflow-x: auto; padding-bottom: 1px; }
            .new-box-page .step { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--nb-fg-3); white-space: nowrap; }
            .new-box-page .step .num { width: 22px; height: 22px; border-radius: 50%; background: var(--nb-sidebar-2); color: var(--nb-fg-2); display: grid; place-items: center; font-size: 11px; font-weight: 500; flex: none; }
            .new-box-page .step.active .num { background: var(--nb-fg); color: var(--nb-bg); }
            .new-box-page .step.done .num { background: var(--nb-accent); color: #fff; }
            .new-box-page .step.active .label, .new-box-page .step.done .label { color: var(--nb-fg); font-weight: 500; }
            .new-box-page .step-sep { flex: 1; height: 1px; background: var(--nb-border); max-width: 36px; min-width: 24px; }
            .new-box-page .grid-2 { display: grid; grid-template-columns: minmax(0, 2fr) minmax(280px, 1fr); gap: 18px; align-items: start; }
            .new-box-page .grid-2-equal { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
            .new-box-page .card { background: var(--nb-panel); border: 1px solid var(--nb-border); border-radius: var(--nb-radius); box-shadow: var(--nb-shadow); }
            .new-box-page .card.flat { box-shadow: none; background: #FBFAF6; }
            .new-box-page .card-head { padding: 14px 18px; border-bottom: 1px solid var(--nb-border); display: flex; align-items: center; justify-content: space-between; gap: 12px; }
            .new-box-page .card-title { font-weight: 600; font-size: 14px; }
            .new-box-page .card-body { padding: 18px; }
            .new-box-page .col { display: flex; flex-direction: column; gap: 10px; }
            .new-box-page .row { display: flex; align-items: center; gap: 10px; }
            .new-box-page .between { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
            .new-box-page .muted { color: var(--nb-fg-2); }
            .new-box-page .tiny { font-size: 11.5px; color: var(--nb-fg-3); }
            .new-box-page .field { display: grid; gap: 6px; }
            .new-box-page .label { font-size: 12.5px; color: var(--nb-fg-2); font-weight: 500; }
            .new-box-page .hint { font-size: 11.5px; color: var(--nb-fg-3); font-weight: 400; }
            .new-box-page .input, .new-box-page .select, .new-box-page .textarea { background: var(--nb-panel); border: 1px solid var(--nb-border); border-radius: var(--nb-radius-sm); padding: 8px 10px; font-size: 13.5px; color: var(--nb-fg); width: 100%; transition: border-color .12s, box-shadow .12s; }
            .new-box-page .input:focus, .new-box-page .select:focus, .new-box-page .textarea:focus, .new-box-page .input-prefix:focus-within { outline: 0; border-color: var(--nb-accent); box-shadow: 0 0 0 3px var(--nb-accent-soft); }
            .new-box-page .textarea { resize: vertical; min-height: 88px; }
            .new-box-page .input-prefix { display: flex; align-items: center; background: var(--nb-panel); border: 1px solid var(--nb-border); border-radius: var(--nb-radius-sm); padding-left: 10px; transition: border-color .12s, box-shadow .12s; }
            .new-box-page .input-prefix input { border: 0; padding: 8px 10px; outline: 0; width: 100%; background: transparent; font-size: 13.5px; }
            .new-box-page .input-prefix .prefix { color: var(--nb-fg-3); font-size: 13px; flex: none; }
            .new-box-page .type-row { display: grid; grid-template-columns: 1fr 120px 90px 1fr 32px; gap: 8px; align-items: end; }
            .new-box-page .pill { display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 500; padding: 2px 8px; border-radius: 999px; background: var(--nb-sidebar-2); color: var(--nb-fg-2); }
            .new-box-page .success-mark { width: 56px; height: 56px; margin: 0 auto 12px; border-radius: 14px; background: var(--nb-accent-soft); color: var(--nb-accent); display: grid; place-items: center; }
            .new-box-page .cover-preview { height: 100px; border-radius: 8px; background: linear-gradient(135deg, #1B6B4E 0%, #2E8E6C 100%); position: relative; overflow: hidden; margin-bottom: 14px; transition: background .3s; }
            .new-box-page .cover-preview img { width: 100%; height: 100%; object-fit: cover; }
            .new-box-page .cover-preview .glyph { position: absolute; inset: 0; display: grid; place-items: center; color: rgba(255,255,255,.9); font-family: "Instrument Serif", Georgia, serif; font-size: 30px; }
            .new-box-page .upload-zone { border: 2px dashed var(--nb-border); border-radius: var(--nb-radius-sm); padding: 16px; text-align: center; cursor: pointer; transition: border-color .15s, background .15s; }
            .new-box-page .upload-zone:hover { border-color: var(--nb-accent); background: var(--nb-accent-soft); }
            @media (max-width: 980px) { .new-box-page .grid-2 { grid-template-columns: 1fr; } }
            @media (max-width: 700px) {
                .new-box-page .page-head { align-items: flex-start; flex-direction: column; }
                .new-box-page .page-title { font-size: 32px; }
                .new-box-page .grid-2-equal { grid-template-columns: 1fr; }
                .new-box-page .type-row { grid-template-columns: 1fr 1fr; }
                .new-box-page .between.action-row { align-items: stretch; flex-direction: column; }
                .new-box-page .between.action-row .row { justify-content: flex-end; flex-wrap: wrap; }
            }
        </style>

        <div class="page-head">
            <div>
                <h1 class="page-title">Create EventBox</h1>
                <div class="page-sub">Set up your event and start selling tickets.</div>
            </div>
            <a href="{{ route('events.index') }}" class="btn ghost">Cancel</a>
        </div>

        <div class="stepper" aria-label="Event setup steps">
            <template x-for="(stepInfo, index) in steps" :key="stepInfo.name">
                <div class="row" style="gap: 8px;">
                    <div class="step" :class="{ 'active': currentStep === index + 1, 'done': currentStep > index + 1 }">
                        <div class="num">
                            <span x-show="currentStep <= index + 1" x-text="index + 1"></span>
                            <svg x-show="currentStep > index + 1" viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
                        </div>
                        <div class="label" x-text="stepInfo.name"></div>
                    </div>
                    <div x-show="index < steps.length - 1" class="step-sep"></div>
                </div>
            </template>
        </div>

        <div class="grid-2">
            <form class="card" @submit.prevent="handleSubmit" x-ref="theForm">
                <div class="card-body col" style="gap: 18px;">

                    {{-- Step 1: Event Details + Design --}}
                    <div x-show="currentStep === 1" x-transition x-cloak class="col" style="gap: 18px;">
                        <div class="field">
                            <label class="label" for="title">Event title <span class="hint">*</span></label>
                            <input id="title" type="text" class="input" x-model="formData.title" required placeholder="e.g. Annual Tech Summit 2026">
                        </div>

                        <div class="field">
                            <label class="label" for="tagline">Tagline <span class="hint">optional</span></label>
                            <input id="tagline" type="text" class="input" x-model="formData.tagline" placeholder="A short, compelling line for your event page" maxlength="180">
                        </div>

                        <div class="field">
                            <label class="label" for="description">Description</label>
                            <textarea id="description" class="textarea" x-model="formData.description" placeholder="Tell attendees what to expect..."></textarea>
                        </div>

                        <div class="field">
                            <label class="label" for="venue">Venue</label>
                            <input id="venue" type="text" class="input" x-model="formData.venue" placeholder="e.g. Accra International Conference Centre">
                        </div>

                        <div class="grid-2-equal">
                            <div class="field">
                                <label class="label" for="event_date">Event date & time <span class="hint">*</span></label>
                                <input id="event_date" type="datetime-local" class="input" x-model="formData.event_date" required>
                            </div>
                            <div class="field">
                                <label class="label" for="capacity">Total capacity <span class="hint">optional</span></label>
                                <input id="capacity" type="number" class="input" x-model="formData.capacity" min="1" placeholder="Unlimited">
                                <div class="hint">Overall cap across all ticket types.</div>
                            </div>
                        </div>

                        {{-- Cover image --}}
                        <div class="field">
                            <label class="label">Cover image <span class="hint">optional · max 5 MB</span></label>
                            <label class="upload-zone" @dragover.prevent @drop.prevent="handleDrop($event)">
                                <template x-if="coverPreviewUrl">
                                    <img :src="coverPreviewUrl" style="width:100%;border-radius:6px;object-fit:cover;max-height:140px;">
                                </template>
                                <template x-if="!coverPreviewUrl">
                                    <div>
                                        <svg viewBox="0 0 24 24" style="width:28px;height:28px;color:#9C998F;margin:0 auto 6px;display:block;" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                                        <div class="muted" style="font-size:13px;font-weight:500;">Click to upload or drag & drop</div>
                                        <div class="hint" style="margin-top:2px;">PNG, JPG, WebP · Recommended 1500×500</div>
                                    </div>
                                </template>
                                <input type="file" x-ref="coverInput" accept="image/*" style="display:none;" @change="handleCoverChange($event)">
                            </label>
                            <template x-if="coverPreviewUrl">
                                <button type="button" class="btn" style="align-self:flex-start;font-size:12px;" @click="coverPreviewUrl = null; $refs.coverInput.value = ''">
                                    Remove image
                                </button>
                            </template>
                        </div>

                        {{-- Organizer + Accent colour --}}
                        <div class="grid-2-equal">
                            <div class="field">
                                <label class="label" for="organizer_name">Organizer name <span class="hint">optional</span></label>
                                <input id="organizer_name" type="text" class="input" x-model="formData.organizer_name" placeholder="e.g. Accra Tech Hub">
                                <div class="hint">Shown as "Organized by …" on event page.</div>
                            </div>
                            <div class="field">
                                <label class="label">Accent colour <span class="hint">optional</span></label>
                                <div class="row" style="gap: 8px;">
                                    <div style="width:32px;height:32px;border-radius:6px;border:1px solid var(--nb-border);flex:none;transition:background .2s;" :style="'background:' + formData.accent_color"></div>
                                    <input type="color" class="input" x-model="formData.accent_color" style="height:32px;padding:2px 4px;cursor:pointer;">
                                </div>
                                <div class="hint">Button & highlight colour on the public page.</div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 2: Ticket Types --}}
                    <div x-show="currentStep === 2" x-transition x-cloak class="col" style="gap: 18px;">
                        <div>
                            <div class="label" style="margin-bottom: 4px;">Ticket types <span class="hint">*</span></div>
                            <div class="hint">Define the ticket categories attendees can choose from.</div>
                        </div>

                        <div class="col" style="gap: 10px;">
                            <div class="row" style="gap: 8px; font-size: 11px; color: var(--nb-fg-3); font-weight: 600; text-transform: uppercase; letter-spacing: .05em; padding: 0 2px;">
                                <div style="flex: 1.5;">Name</div>
                                <div style="width: 110px;">Price (GH₵)</div>
                                <div style="width: 90px;">Capacity</div>
                                <div style="flex: 1;">Description</div>
                                <div style="width: 32px;"></div>
                            </div>

                            <template x-for="(type, index) in ticketTypes" :key="index">
                                <div class="card flat" style="padding: 12px;">
                                    <div class="type-row">
                                        <div class="field" style="gap: 4px;">
                                            <input type="text" class="input" x-model="type.name" :placeholder="'e.g. ' + defaultNames[index % 3]" required>
                                        </div>
                                        <div class="field" style="gap: 4px;">
                                            <div class="input-prefix">
                                                <span class="prefix">₵</span>
                                                <input type="number" x-model="type.price" min="0" step="0.01" placeholder="0.00" required>
                                            </div>
                                        </div>
                                        <div class="field" style="gap: 4px;">
                                            <input type="number" class="input" x-model="type.capacity" min="1" placeholder="∞">
                                        </div>
                                        <div class="field" style="gap: 4px;">
                                            <input type="text" class="input" x-model="type.description" placeholder="Optional note">
                                        </div>
                                        <button type="button" class="btn danger" style="min-height: 34px; padding: 0 8px;" @click="removeType(index)" :disabled="ticketTypes.length === 1" title="Remove">
                                            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <button type="button" class="btn" @click="addType" style="align-self: flex-start;">
                                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                                Add ticket type
                            </button>
                        </div>

                        <div style="text-align: center; padding: 12px 8px 4px;">
                            <div class="success-mark">
                                <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
                            </div>
                            <div style="font-family: 'Instrument Serif', Georgia, serif; font-size: 22px; margin-bottom: 4px;">Ready to launch</div>
                            <div class="muted" style="font-size: 13px;">Your event starts as a draft — activate it when you're ready to sell.</div>
                        </div>
                    </div>

                    <div class="between action-row" style="margin-top: 8px; padding-top: 18px; border-top: 1px solid var(--nb-border);">
                        <button type="button" class="btn ghost" :disabled="currentStep === 1" @click="currentStep--">
                            <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                            Back
                        </button>
                        <div class="row">
                            <button type="button" class="btn primary" x-show="currentStep === 1" @click="nextStep">
                                Continue
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                            </button>
                            <button type="submit" class="btn primary" x-show="currentStep === 2" :disabled="isSubmitting">
                                <span x-show="!isSubmitting">Create EventBox</span>
                                <span x-show="isSubmitting">Creating...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Sidebar --}}
            <div class="col" style="gap: 18px;">
                <div class="card">
                    <div class="card-head">
                        <div class="card-title">Preview</div>
                        <span class="tiny">What attendees see</span>
                    </div>
                    <div class="card-body">
                        <div class="cover-preview"
                             :style="coverPreviewUrl
                                 ? 'background: none;'
                                 : 'background: linear-gradient(135deg, ' + formData.accent_color + ' 0%, ' + formData.accent_color + 'cc 100%);'">
                            <img x-show="coverPreviewUrl" :src="coverPreviewUrl" style="width:100%;height:100%;object-fit:cover;">
                            <div class="glyph" x-show="!coverPreviewUrl" x-text="previewInitial()"></div>
                        </div>
                        <div style="font-weight: 600; font-size: 15px; margin-bottom: 2px;" x-text="formData.title || 'Your event title'"></div>
                        <div class="muted" style="font-size: 12px; margin-bottom: 2px; font-style: italic;" x-show="formData.tagline" x-text="formData.tagline"></div>
                        <div class="muted" style="font-size: 12.5px; margin-bottom: 6px;" x-text="formData.venue ? '📍 ' + formData.venue : 'Venue TBD'"></div>
                        <div class="muted" style="font-size: 12.5px; margin-bottom: 14px;" x-text="formData.event_date ? new Date(formData.event_date).toLocaleDateString('en-GB', {day:'numeric',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}) : 'Date TBD'"></div>
                        <template x-if="ticketTypes.length > 0">
                            <div class="col" style="gap: 6px; margin-bottom: 14px;">
                                <div class="tiny" style="font-weight: 600; text-transform: uppercase; letter-spacing: .05em;">Tickets</div>
                                <template x-for="type in ticketTypes.filter(t => t.name)" :key="type.name">
                                    <div class="between" style="font-size: 12.5px;">
                                        <span x-text="type.name"></span>
                                        <span style="font-weight: 600;" x-text="type.price ? 'GH₵ ' + Number(type.price).toFixed(2) : 'Free'"></span>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <button type="button"
                            class="btn primary" style="width: 100%;"
                            :style="'background:' + formData.accent_color + ';border-color:' + formData.accent_color">
                            Buy Ticket
                        </button>
                        <div class="tiny" style="margin-top: 8px; text-align: center;" x-show="formData.organizer_name">
                            Organized by <span x-text="formData.organizer_name" style="font-weight:500;"></span>
                        </div>
                    </div>
                </div>

                <div class="card flat">
                    <div class="card-body col" style="gap: 10px;">
                        <div class="row" style="align-items: flex-start;">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" style="margin-top: 2px; color: var(--nb-accent); flex: none;"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                            <div>
                                <div style="font-size: 13px; font-weight: 500; margin-bottom: 2px;">Event starts as a draft</div>
                                <div class="tiny">After creating, go to your event dashboard and activate it when ready to sell tickets.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">
                        <div class="card-title">Setup</div>
                        <span class="tiny" x-text="`Step ${currentStep} of 2`"></span>
                    </div>
                    <div class="card-body col" style="gap: 12px;">
                        <div class="between">
                            <div>
                                <div style="font-size: 13px; font-weight: 500;">Event details</div>
                                <div class="tiny" x-text="formData.venue || 'Venue not set'"></div>
                            </div>
                            <span class="pill" x-text="formData.title && formData.event_date ? 'Ready' : 'Needed'"></span>
                        </div>
                        <div class="between">
                            <div>
                                <div style="font-size: 13px; font-weight: 500;">Ticket types</div>
                                <div class="tiny" x-text="ticketTypes.filter(t => t.name).length + ' type(s) defined'"></div>
                            </div>
                            <span class="pill" x-text="ticketTypes.some(t => t.name && t.price) ? 'Ready' : 'Needed'"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function eventBoxForm() {
            return {
                currentStep: 1,
                isSubmitting: false,
                coverPreviewUrl: null,
                steps: [
                    { name: 'Event details' },
                    { name: 'Ticket types' },
                ],
                defaultNames: ['VIP', 'Regular', 'Student'],
                formData: {
                    title: '',
                    tagline: '',
                    description: '',
                    venue: '',
                    organizer_name: '',
                    accent_color: '#1B6B4E',
                    event_date: '',
                    capacity: '',
                },
                ticketTypes: [
                    { name: '', price: '', capacity: '', description: '' }
                ],

                init() {},

                handleCoverChange(e) {
                    const file = e.target.files[0];
                    if (file) this.coverPreviewUrl = URL.createObjectURL(file);
                },

                handleDrop(e) {
                    const file = e.dataTransfer.files[0];
                    if (file && file.type.startsWith('image/')) {
                        this.$refs.coverInput.files = e.dataTransfer.files;
                        this.coverPreviewUrl = URL.createObjectURL(file);
                    }
                },

                nextStep() {
                    if (this.currentStep === 1) {
                        if (!this.formData.title || !this.formData.event_date) {
                            alert('Please fill in the event title and date before continuing.');
                            return;
                        }
                    }
                    if (this.currentStep < 2) this.currentStep++;
                },

                addType() {
                    this.ticketTypes.push({ name: '', price: '', capacity: '', description: '' });
                },

                removeType(index) {
                    if (this.ticketTypes.length > 1) this.ticketTypes.splice(index, 1);
                },

                previewInitial() {
                    return (this.formData.title || 'E').trim().charAt(0).toUpperCase();
                },

                async handleSubmit() {
                    const validTypes = this.ticketTypes.filter(t => t.name && t.price);
                    if (validTypes.length === 0) {
                        alert('Please add at least one ticket type with a name and price.');
                        return;
                    }

                    this.isSubmitting = true;
                    try {
                        const fd = new FormData();
                        fd.append('title', this.formData.title);
                        if (this.formData.tagline)        fd.append('tagline', this.formData.tagline);
                        if (this.formData.description)    fd.append('description', this.formData.description);
                        if (this.formData.venue)          fd.append('venue', this.formData.venue);
                        if (this.formData.organizer_name) fd.append('organizer_name', this.formData.organizer_name);
                        if (this.formData.accent_color)   fd.append('accent_color', this.formData.accent_color);
                        fd.append('event_date', this.formData.event_date);
                        if (this.formData.capacity)       fd.append('capacity', this.formData.capacity);

                        validTypes.forEach((type, i) => {
                            fd.append(`ticket_types[${i}][name]`, type.name);
                            fd.append(`ticket_types[${i}][price]`, type.price);
                            if (type.capacity) fd.append(`ticket_types[${i}][capacity]`, type.capacity);
                            fd.append(`ticket_types[${i}][description]`, type.description || '');
                        });

                        const coverFile = this.$refs.coverInput?.files?.[0];
                        if (coverFile) fd.append('cover_image', coverFile);

                        const response = await fetch('{{ route("events.store") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: fd,
                        });

                        const data = await response.json();

                        if (response.ok) {
                            window.location.href = `/dashboard/events/${data.id}/dashboard`;
                        } else {
                            const messages = data.errors
                                ? Object.values(data.errors).flat().join('\n')
                                : (data.message || 'Failed to create event.');
                            alert(messages);
                        }
                    } catch (error) {
                        alert('Error creating event. Please try again.');
                        console.error(error);
                    } finally {
                        this.isSubmitting = false;
                    }
                },
            };
        }

        if (typeof Alpine !== 'undefined') {
            Alpine.data('eventBoxForm', eventBoxForm);
        } else {
            document.addEventListener('alpine:init', () => {
                Alpine.data('eventBoxForm', eventBoxForm);
            });
        }
    </script>
</x-layouts.app>