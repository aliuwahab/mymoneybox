<x-layouts.app>
    @php
        $currencySymbol = auth()->user()->country?->currency_symbol ?? '₵';
        $categoryLabels = $categories->mapWithKeys(fn ($category) => [
            $category->id => trim(($category->icon ? $category->icon . ' ' : '') . $category->name),
        ])->all();
    @endphp

    <div class="new-box-page"
         x-data="moneyBoxForm()"
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
            .new-box-page button,
            .new-box-page input,
            .new-box-page select,
            .new-box-page textarea { font: inherit; color: inherit; }

            .new-box-page .page-head {
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                gap: 24px;
                margin-bottom: 24px;
            }

            .new-box-page .page-title {
                font-family: "Instrument Serif", Georgia, serif;
                font-size: 38px;
                line-height: 1.05;
                letter-spacing: 0;
                margin: 0;
                color: var(--nb-fg);
                font-weight: 400;
            }

            .new-box-page .page-sub {
                color: var(--nb-fg-2);
                font-size: 13.5px;
                margin-top: 6px;
            }

            .new-box-page .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                min-height: 34px;
                padding: 7px 13px;
                border-radius: var(--nb-radius-sm);
                font-size: 13px;
                font-weight: 500;
                border: 1px solid var(--nb-border);
                background: var(--nb-panel);
                color: var(--nb-fg);
                box-shadow: var(--nb-shadow);
                transition: background .12s, border-color .12s, transform .08s;
            }

            .new-box-page .btn:hover { background: #FBFAF6; border-color: var(--nb-border-2); }
            .new-box-page .btn:active { transform: translateY(.5px); }
            .new-box-page .btn.primary {
                background: var(--nb-accent);
                color: #fff;
                border-color: var(--nb-accent);
            }
            .new-box-page .btn.primary:hover {
                background: var(--nb-accent-hover);
                border-color: var(--nb-accent-hover);
            }
            .new-box-page .btn.ghost {
                background: transparent;
                box-shadow: none;
                border-color: transparent;
            }
            .new-box-page .btn.ghost:hover { background: rgba(0,0,0,.04); }
            .new-box-page .btn:disabled {
                opacity: .5;
                cursor: not-allowed;
                transform: none;
            }

            .new-box-page .stepper {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 24px;
                overflow-x: auto;
                padding-bottom: 1px;
            }

            .new-box-page .step {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 12.5px;
                color: var(--nb-fg-3);
                white-space: nowrap;
            }

            .new-box-page .step .num {
                width: 22px;
                height: 22px;
                border-radius: 50%;
                background: var(--nb-sidebar-2);
                color: var(--nb-fg-2);
                display: grid;
                place-items: center;
                font-size: 11px;
                font-weight: 500;
                flex: none;
            }

            .new-box-page .step.active .num { background: var(--nb-fg); color: var(--nb-bg); }
            .new-box-page .step.done .num { background: var(--nb-accent); color: #fff; }
            .new-box-page .step.active .label,
            .new-box-page .step.done .label { color: var(--nb-fg); font-weight: 500; }
            .new-box-page .step-sep {
                flex: 1;
                height: 1px;
                background: var(--nb-border);
                max-width: 36px;
                min-width: 24px;
            }

            .new-box-page .grid-2 {
                display: grid;
                grid-template-columns: minmax(0, 2fr) minmax(280px, 1fr);
                gap: 18px;
                align-items: start;
            }

            .new-box-page .grid-2-equal {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 18px;
            }

            .new-box-page .card {
                background: var(--nb-panel);
                border: 1px solid var(--nb-border);
                border-radius: var(--nb-radius);
                box-shadow: var(--nb-shadow);
            }

            .new-box-page .card.flat {
                box-shadow: none;
                background: #FBFAF6;
            }

            .new-box-page .card-head {
                padding: 14px 18px;
                border-bottom: 1px solid var(--nb-border);
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
            }

            .new-box-page .card-title {
                font-weight: 600;
                font-size: 14px;
                letter-spacing: 0;
            }

            .new-box-page .card-body { padding: 18px; }
            .new-box-page .col { display: flex; flex-direction: column; gap: 10px; }
            .new-box-page .row { display: flex; align-items: center; gap: 10px; }
            .new-box-page .between {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
            }
            .new-box-page .muted { color: var(--nb-fg-2); }
            .new-box-page .tiny { font-size: 11.5px; color: var(--nb-fg-3); }

            .new-box-page .field {
                display: grid;
                gap: 6px;
            }

            .new-box-page .label {
                font-size: 12.5px;
                color: var(--nb-fg-2);
                font-weight: 500;
            }

            .new-box-page .hint {
                font-size: 11.5px;
                color: var(--nb-fg-3);
                font-weight: 400;
            }

            .new-box-page .input,
            .new-box-page .select,
            .new-box-page .textarea {
                background: var(--nb-panel);
                border: 1px solid var(--nb-border);
                border-radius: var(--nb-radius-sm);
                padding: 8px 10px;
                font-size: 13.5px;
                color: var(--nb-fg);
                width: 100%;
                transition: border-color .12s, box-shadow .12s;
            }

            .new-box-page .input:focus,
            .new-box-page .select:focus,
            .new-box-page .textarea:focus,
            .new-box-page .input-prefix:focus-within {
                outline: 0;
                border-color: var(--nb-accent);
                box-shadow: 0 0 0 3px var(--nb-accent-soft);
            }

            .new-box-page .textarea {
                resize: vertical;
                min-height: 88px;
            }

            .new-box-page .input-prefix {
                display: flex;
                align-items: center;
                background: var(--nb-panel);
                border: 1px solid var(--nb-border);
                border-radius: var(--nb-radius-sm);
                padding-left: 10px;
                transition: border-color .12s, box-shadow .12s;
            }

            .new-box-page .input-prefix input {
                border: 0;
                padding: 8px 10px;
                outline: 0;
                width: 100%;
                background: transparent;
                font-size: 13.5px;
            }

            .new-box-page .input-prefix .prefix {
                color: var(--nb-fg-3);
                font-size: 13px;
                flex: none;
            }

            .new-box-page .opt-grid {
                display: grid;
                gap: 8px;
            }

            .new-box-page .opt-grid.two { grid-template-columns: 1fr 1fr; }

            .new-box-page .opt {
                display: flex;
                align-items: flex-start;
                gap: 10px;
                width: 100%;
                text-align: left;
                padding: 10px 12px;
                border: 1px solid var(--nb-border);
                border-radius: var(--nb-radius-sm);
                background: var(--nb-panel);
                cursor: pointer;
                transition: border-color .12s, background .12s;
            }

            .new-box-page .opt:hover { border-color: var(--nb-border-2); }
            .new-box-page .opt.selected {
                border-color: var(--nb-accent);
                background: var(--nb-accent-soft);
            }

            .new-box-page .opt-radio {
                width: 14px;
                height: 14px;
                border-radius: 50%;
                border: 1.5px solid var(--nb-border-2);
                margin-top: 3px;
                flex: none;
                position: relative;
            }

            .new-box-page .opt.selected .opt-radio { border-color: var(--nb-accent); }
            .new-box-page .opt.selected .opt-radio::after {
                content: "";
                position: absolute;
                inset: 2.5px;
                border-radius: 50%;
                background: var(--nb-accent);
            }

            .new-box-page .opt-title { font-weight: 500; font-size: 13px; }
            .new-box-page .opt-desc {
                color: var(--nb-fg-2);
                font-size: 12px;
                margin-top: 1px;
            }

            .new-box-page .progress {
                height: 6px;
                background: var(--nb-sidebar-2);
                border-radius: 999px;
                overflow: hidden;
                position: relative;
            }

            .new-box-page .progress > span {
                display: block;
                height: 100%;
                background: var(--nb-accent);
                border-radius: 999px;
            }

            .new-box-page .cover {
                height: 80px;
                border-radius: 8px;
                background: linear-gradient(135deg, #1B6B4E 0%, #2E8E6C 100%);
                position: relative;
                overflow: hidden;
                margin-bottom: 14px;
            }

            .new-box-page .cover .glyph {
                position: absolute;
                inset: 0;
                display: grid;
                place-items: center;
                color: rgba(255,255,255,.9);
                font-family: "Instrument Serif", Georgia, serif;
                font-size: 30px;
            }

            .new-box-page .pill {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                font-size: 11.5px;
                font-weight: 500;
                padding: 2px 8px;
                border-radius: 999px;
                background: var(--nb-sidebar-2);
                color: var(--nb-fg-2);
            }

            .new-box-page .success-mark {
                width: 56px;
                height: 56px;
                margin: 0 auto 12px;
                border-radius: 14px;
                background: var(--nb-accent-soft);
                color: var(--nb-accent);
                display: grid;
                place-items: center;
            }

            .new-box-page .upload-box {
                border: 1.5px dashed var(--nb-border-2);
                border-radius: 8px;
                padding: 22px;
                text-align: center;
                background: #FBFAF6;
                transition: border-color .12s, background .12s;
            }

            .new-box-page .upload-box:hover {
                border-color: var(--nb-fg-3);
                background: #fff;
            }

            .new-box-page .preview-grid {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 8px;
            }

            .new-box-page .preview-grid img,
            .new-box-page .main-preview {
                width: 100%;
                object-fit: cover;
                border-radius: 6px;
                border: 1px solid var(--nb-border);
            }

            .new-box-page .preview-grid img { height: 76px; }
            .new-box-page .main-preview { max-height: 180px; }

            .new-box-page .toggle {
                width: 30px;
                height: 18px;
                border-radius: 999px;
                background: var(--nb-border-2);
                position: relative;
                transition: background .15s;
                flex: none;
                border: 0;
                padding: 0;
            }

            .new-box-page .toggle::after {
                content: "";
                position: absolute;
                top: 2px;
                left: 2px;
                width: 14px;
                height: 14px;
                border-radius: 50%;
                background: #fff;
                box-shadow: 0 1px 2px rgba(0,0,0,.2);
                transition: left .15s;
            }

            .new-box-page .toggle.on { background: var(--nb-accent); }
            .new-box-page .toggle.on::after { left: 14px; }

            @media (max-width: 980px) {
                .new-box-page .grid-2 { grid-template-columns: 1fr; }
            }

            @media (max-width: 700px) {
                .new-box-page .page-head {
                    align-items: flex-start;
                    flex-direction: column;
                }
                .new-box-page .page-title { font-size: 32px; }
                .new-box-page .grid-2-equal,
                .new-box-page .opt-grid.two { grid-template-columns: 1fr; }
                .new-box-page .between.action-row {
                    align-items: stretch;
                    flex-direction: column;
                }
                .new-box-page .between.action-row .row {
                    justify-content: flex-end;
                    flex-wrap: wrap;
                }
            }
        </style>

        <div class="page-head">
            <div>
                <h1 class="page-title">Create a PiggyBox</h1>
                <div class="page-sub">Set it up in under a minute. You can change anything later.</div>
            </div>
            <a href="{{ route('money-boxes.index') }}" class="btn ghost" wire:navigate>Cancel</a>
        </div>

        <div class="stepper" aria-label="PiggyBox setup steps">
            <template x-for="(stepInfo, index) in steps" :key="stepInfo.name">
                <div class="row" style="gap: 8px;">
                    <div class="step"
                         :class="{ 'active': currentStep === index + 1, 'done': currentStep > index + 1 }">
                        <div class="num">
                            <span x-show="currentStep <= index + 1" x-text="index + 1"></span>
                            <svg x-show="currentStep > index + 1" viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m5 12 5 5L20 7"/>
                            </svg>
                        </div>
                        <div class="label" x-text="stepInfo.name"></div>
                    </div>
                    <div x-show="index < steps.length - 1" class="step-sep"></div>
                </div>
            </template>
        </div>

        <div class="grid-2">
            <form class="card" @submit.prevent="handleSubmit">
                <div class="card-body col" style="gap: 18px;">
                    <div x-show="currentStep === 1" x-transition x-cloak class="col" style="gap: 18px;">
                        <div class="field">
                            <label class="label" for="title">What are you collecting for? <span class="hint">*</span></label>
                            <input id="title" type="text" class="input" x-model="formData.title" required placeholder="e.g. Kwame & Adwoa's Wedding Fund">
                            <div class="hint">A clear name helps contributors find and recognise it.</div>
                        </div>

                        <div class="field">
                            <label class="label" for="description">Description</label>
                            <textarea id="description" class="textarea" x-model="formData.description" placeholder="Tell contributors what their gift will go toward..."></textarea>
                        </div>

                        <div class="grid-2-equal">
                            <div class="field">
                                <label class="label" for="category_id">Category</label>
                                <select id="category_id" class="select" x-model="formData.category_id">
                                    <option value="">Select a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->icon }} {{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="field">
                                <label class="label" for="goal_amount">Goal amount <span class="hint">optional</span></label>
                                <div class="input-prefix">
                                    <span class="prefix">{{ $currencySymbol }}</span>
                                    <input id="goal_amount" type="number" x-model="formData.goal_amount" step="0.01" min="0" placeholder="25,000">
                                </div>
                            </div>
                        </div>

                        <div class="field">
                            <div class="label">Visibility</div>
                            <div class="opt-grid two">
                                <button type="button" class="opt" :class="{ 'selected': formData.visibility === 'public' }" @click="formData.visibility = 'public'">
                                    <span class="opt-radio"></span>
                                    <span>
                                        <span class="opt-title">Public</span>
                                        <span class="opt-desc">Listed on the homepage</span>
                                    </span>
                                </button>
                                <button type="button" class="opt" :class="{ 'selected': formData.visibility === 'private' }" @click="formData.visibility = 'private'">
                                    <span class="opt-radio"></span>
                                    <span>
                                        <span class="opt-title">Private</span>
                                        <span class="opt-desc">Only people with the link</span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div x-show="currentStep === 2" x-transition x-cloak class="col" style="gap: 18px;">
                        <div class="field">
                            <div class="label">Contribution amount <span class="hint">*</span></div>
                            <div class="opt-grid">
                                <template x-for="option in amountOptions" :key="option.value">
                                    <button type="button" class="opt" :class="{ 'selected': formData.amount_type === option.value }" @click="formData.amount_type = option.value">
                                        <span class="opt-radio"></span>
                                        <span>
                                            <span class="opt-title" x-text="option.title"></span>
                                            <span class="opt-desc" x-text="option.description"></span>
                                        </span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div class="grid-2-equal" x-show="formData.amount_type !== 'variable'" x-cloak>
                            <div x-show="formData.amount_type === 'fixed'" class="field">
                                <label class="label" for="fixed_amount">Fixed amount</label>
                                <div class="input-prefix">
                                    <span class="prefix">{{ $currencySymbol }}</span>
                                    <input id="fixed_amount" type="number" x-model="formData.fixed_amount" step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>
                            <div x-show="formData.amount_type === 'minimum' || formData.amount_type === 'range'" class="field">
                                <label class="label" for="minimum_amount">Minimum amount</label>
                                <div class="input-prefix">
                                    <span class="prefix">{{ $currencySymbol }}</span>
                                    <input id="minimum_amount" type="number" x-model="formData.minimum_amount" step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>
                            <div x-show="formData.amount_type === 'maximum' || formData.amount_type === 'range'" class="field">
                                <label class="label" for="maximum_amount">Maximum amount</label>
                                <div class="input-prefix">
                                    <span class="prefix">{{ $currencySymbol }}</span>
                                    <input id="maximum_amount" type="number" x-model="formData.maximum_amount" step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        <div class="field">
                            <div class="label">Contributor identity <span class="hint">*</span></div>
                            <div class="opt-grid">
                                <template x-for="option in identityOptions" :key="option.value">
                                    <button type="button" class="opt" :class="{ 'selected': formData.contributor_identity === option.value }" @click="formData.contributor_identity = option.value">
                                        <span class="opt-radio"></span>
                                        <span>
                                            <span class="opt-title" x-text="option.title"></span>
                                            <span class="opt-desc" x-text="option.description"></span>
                                        </span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div x-show="currentStep === 3" x-transition x-cloak class="col" style="gap: 18px;">
                        <div class="grid-2-equal">
                            <div class="field">
                                <label class="label" for="start_date">Start date <span class="hint">optional</span></label>
                                <input id="start_date" type="datetime-local" class="input" x-model="formData.start_date">
                                <div class="hint">Leave empty to start immediately.</div>
                            </div>
                            <div class="field" x-show="!formData.is_ongoing" x-cloak>
                                <label class="label" for="end_date">End date</label>
                                <input id="end_date" type="datetime-local" class="input" x-model="formData.end_date">
                            </div>
                        </div>

                        <div class="card flat">
                            <div class="card-body between">
                                <div>
                                    <div style="font-size: 13px; font-weight: 500;">Ongoing PiggyBox</div>
                                    <div class="tiny">Keep this PiggyBox open without an end date.</div>
                                </div>
                                <button type="button" class="toggle" :class="{ 'on': formData.is_ongoing }" @click="formData.is_ongoing = !formData.is_ongoing" aria-label="Toggle ongoing PiggyBox"></button>
                            </div>
                        </div>

                        <div style="text-align: center; padding: 20px 8px;">
                            <div class="success-mark">
                                <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m5 12 5 5L20 7"/>
                                </svg>
                            </div>
                            <div style="font-family: 'Instrument Serif', Georgia, serif; font-size: 26px; margin-bottom: 6px;">Your PiggyBox is ready</div>
                            <div class="muted" style="max-width: 390px; margin: 0 auto;">Create it now and add images on the next step before sharing your link.</div>
                        </div>
                    </div>

                    <div x-show="currentStep === 4" x-transition x-cloak class="col" style="gap: 18px;">
                        <div style="text-align: center; padding: 8px 8px 2px;">
                            <div class="success-mark">
                                <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m5 12 5 5L20 7"/>
                                </svg>
                            </div>
                            <div style="font-family: 'Instrument Serif', Georgia, serif; font-size: 26px; margin-bottom: 6px;">PiggyBox created</div>
                            <div class="muted">Add images to make it more appealing to contributors.</div>
                        </div>

                        <div class="field">
                            <label class="label" for="main-image">Main image</label>
                            <div class="upload-box">
                                <input type="file" @change="handleMainImage" accept="image/*" class="hidden" id="main-image">
                                <label for="main-image" style="cursor: pointer;">
                                    <div x-show="!mainImagePreview">
                                        <svg style="margin: 0 auto 8px; width: 40px; height: 40px; color: var(--nb-border-2);" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                        </svg>
                                        <p class="muted" style="font-size: 13px;">Click to upload main image</p>
                                    </div>
                                    <div x-show="mainImagePreview">
                                        <img :src="mainImagePreview" class="main-preview" alt="">
                                        <p class="tiny" style="color: var(--nb-accent); margin-top: 8px; font-weight: 500;">Main image selected</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="field">
                            <label class="label" for="gallery-images">Gallery images <span class="hint">optional</span></label>
                            <div class="upload-box">
                                <input type="file" @change="handleGallery" accept="image/*" multiple class="hidden" id="gallery-images">
                                <label for="gallery-images" style="cursor: pointer;">
                                    <svg style="margin: 0 auto 8px; width: 40px; height: 40px; color: var(--nb-border-2);" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                    </svg>
                                    <p class="muted" style="font-size: 13px;">Click to upload gallery images</p>
                                    <p class="tiny">You can select multiple images.</p>
                                </label>
                            </div>
                            <div x-show="galleryPreviews.length > 0" class="preview-grid" x-cloak>
                                <template x-for="(preview, index) in galleryPreviews" :key="index">
                                    <img :src="preview" alt="">
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="between action-row" style="margin-top: 8px; padding-top: 18px; border-top: 1px solid var(--nb-border);">
                        <button type="button" class="btn ghost" :disabled="currentStep === 1 || currentStep === 4" @click="previousStep">
                            <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                            Back
                        </button>

                        <div class="row">
                            <button type="button" class="btn primary" x-show="currentStep < 3" @click="nextStep">
                                Continue
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                            </button>

                            <button type="submit" class="btn primary" x-show="currentStep === 3" :disabled="isSubmitting">
                                <span x-show="!isSubmitting">Create PiggyBox</span>
                                <span x-show="isSubmitting">Creating...</span>
                            </button>

                            <button type="button" class="btn primary" x-show="currentStep === 4" :disabled="isUploading" @click="uploadMedia">
                                <span x-show="!isUploading">Save & continue</span>
                                <span x-show="isUploading">Uploading...</span>
                            </button>

                            <button type="button" class="btn" x-show="currentStep === 4" @click="skipMedia">
                                Skip for now
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="col" style="gap: 18px;">
                <div class="card">
                    <div class="card-head">
                        <div class="card-title">Preview</div>
                        <span class="tiny">What contributors see</span>
                    </div>
                    <div class="card-body">
                        <div class="cover">
                            <div class="glyph" x-text="previewInitial()"></div>
                        </div>
                        <div class="between" style="align-items: flex-start; margin-bottom: 6px;">
                            <div style="font-weight: 600; font-size: 16px;" x-text="previewTitle()"></div>
                            <span class="pill" x-text="visibilityLabel()"></span>
                        </div>
                        <div class="muted" style="font-size: 12.5px; min-height: 38px; margin-bottom: 14px;" x-text="previewDescription()"></div>
                        <div class="between" style="margin-bottom: 6px;">
                            <div style="font-weight: 600; font-variant-numeric: tabular-nums;">
                                {{ $currencySymbol }}0 <span class="muted" style="font-weight: 400;">of {{ $currencySymbol }}<span x-text="previewGoal()"></span></span>
                            </div>
                            <div class="tiny">0%</div>
                        </div>
                        <div class="progress"><span style="width: 0%;"></span></div>
                        <button type="button" class="btn primary" style="width: 100%; margin-top: 14px;">Contribute now</button>
                    </div>
                </div>

                <div class="card flat">
                    <div class="card-body col" style="gap: 10px;">
                        <div class="row" style="align-items: flex-start;">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" style="margin-top: 2px; color: var(--nb-accent); flex: none;">
                                <path d="M12 3v5"/><path d="M12 16v5"/><path d="M3 12h5"/><path d="M16 12h5"/><path d="m5 5 3 3"/><path d="m16 16 3 3"/><path d="M19 5l-3 3"/><path d="m8 16-3 3"/>
                            </svg>
                            <div>
                                <div style="font-size: 13px; font-weight: 500; margin-bottom: 2px;">Tips for stronger PiggyBoxes</div>
                                <div class="tiny" style="font-size: 12px;">PiggyBoxes with a clear title and goal raise more. Add a short story so contributors feel connected.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">
                        <div class="card-title">Setup</div>
                        <span class="tiny" x-text="`Step ${Math.min(currentStep, 3)} of 3`"></span>
                    </div>
                    <div class="card-body col" style="gap: 12px;">
                        <div class="between">
                            <div>
                                <div style="font-size: 13px; font-weight: 500;">Details</div>
                                <div class="tiny" x-text="categoryName()"></div>
                            </div>
                            <span class="pill" x-text="formData.title ? 'Ready' : 'Needed'"></span>
                        </div>
                        <div class="between">
                            <div>
                                <div style="font-size: 13px; font-weight: 500;">Rules</div>
                                <div class="tiny" x-text="amountLabel()"></div>
                            </div>
                            <span class="pill" x-text="identityLabel()"></span>
                        </div>
                        <div class="between">
                            <div>
                                <div style="font-size: 13px; font-weight: 500;">Launch</div>
                                <div class="tiny" x-text="formData.is_ongoing ? 'Ongoing' : 'Optional end date'"></div>
                            </div>
                            <span class="pill" x-text="visibilityLabel()"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function moneyBoxForm() {
            return {
                currentStep: 1,
                isSubmitting: false,
                isUploading: false,
                createdMoneyBoxId: null,
                mainImageFile: null,
                mainImagePreview: null,
                galleryFiles: [],
                galleryPreviews: [],
                categoryLabels: @js($categoryLabels),
                steps: [
                    { name: 'Details' },
                    { name: 'Rules' },
                    { name: 'Share & launch' }
                ],
                amountOptions: [
                    { value: 'variable', title: 'Any amount', description: 'Contributors choose what they want to give' },
                    { value: 'range', title: 'Within a range', description: 'Set a minimum and maximum' },
                    { value: 'fixed', title: 'Fixed amount', description: 'Every contribution is the same' },
                    { value: 'minimum', title: 'Minimum only', description: 'At least a certain amount' },
                    { value: 'maximum', title: 'Maximum only', description: 'Up to a certain amount' }
                ],
                identityOptions: [
                    { value: 'user_choice', title: "Contributor's choice", description: 'They can give anonymously or with their name' },
                    { value: 'must_identify', title: 'Require name', description: 'No anonymous contributions' },
                    { value: 'anonymous_allowed', title: 'Always anonymous', description: 'Names are never collected' }
                ],
                formData: {
                    title: '',
                    description: '',
                    category_id: '',
                    amount_type: 'variable',
                    fixed_amount: '',
                    minimum_amount: '',
                    maximum_amount: '',
                    goal_amount: '',
                    contributor_identity: 'user_choice',
                    visibility: 'public',
                    start_date: '',
                    end_date: '',
                    is_ongoing: false
                },

                init() {},

                nextStep() {
                    if (this.currentStep < 3) this.currentStep++;
                },

                previousStep() {
                    if (this.currentStep > 1) this.currentStep--;
                },

                previewTitle() {
                    return this.formData.title || 'Your PiggyBox title';
                },

                previewInitial() {
                    return (this.formData.title || 'P').trim().charAt(0).toUpperCase();
                },

                previewDescription() {
                    return this.formData.description || 'A short description for contributors.';
                },

                previewGoal() {
                    const amount = Number(this.formData.goal_amount || 0);
                    return (amount > 0 ? amount : 1).toLocaleString();
                },

                categoryName() {
                    return this.formData.category_id ? this.categoryLabels[this.formData.category_id] : 'No category selected';
                },

                visibilityLabel() {
                    return this.formData.visibility === 'public' ? 'Public' : 'Private';
                },

                amountLabel() {
                    const option = this.amountOptions.find((item) => item.value === this.formData.amount_type);
                    return option ? option.title : 'Any amount';
                },

                identityLabel() {
                    const option = this.identityOptions.find((item) => item.value === this.formData.contributor_identity);
                    return option ? option.title : "Contributor's choice";
                },

                async handleSubmit() {
                    this.isSubmitting = true;
                    try {
                        const response = await fetch('{{ route("money-boxes.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.formData)
                        });
                        const data = await response.json();
                        if (response.ok) {
                            this.createdMoneyBoxId = data.id;
                            this.currentStep = 4;
                        } else {
                            alert('Error: ' + (data.message || 'Failed to create PiggyBox'));
                        }
                    } catch (error) {
                        alert('Error creating PiggyBox. Please try again.');
                        console.error(error);
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                handleMainImage(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.mainImageFile = file;
                        const reader = new FileReader();
                        reader.onload = (e) => { this.mainImagePreview = e.target.result; };
                        reader.readAsDataURL(file);
                    }
                },

                handleGallery(event) {
                    this.galleryFiles = Array.from(event.target.files);
                    this.galleryPreviews = [];
                    this.galleryFiles.forEach(file => {
                        const reader = new FileReader();
                        reader.onload = (e) => { this.galleryPreviews.push(e.target.result); };
                        reader.readAsDataURL(file);
                    });
                },

                async uploadMedia() {
                    if (!this.mainImageFile && this.galleryFiles.length === 0) {
                        this.skipMedia();
                        return;
                    }
                    this.isUploading = true;
                    try {
                        const formData = new FormData();
                        if (this.mainImageFile) formData.append('main_image', this.mainImageFile);
                        this.galleryFiles.forEach(file => formData.append('gallery[]', file));
                        const response = await fetch(`/money-boxes/${this.createdMoneyBoxId}/upload-media`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                            body: formData
                        });
                        if (response.ok) {
                            window.location.href = `/money-boxes/${this.createdMoneyBoxId}`;
                        } else {
                            alert('Error uploading images. You can add them later from the edit page.');
                            this.skipMedia();
                        }
                    } catch (error) {
                        alert('Error uploading images. You can add them later from the edit page.');
                        this.skipMedia();
                    } finally {
                        this.isUploading = false;
                    }
                },

                skipMedia() {
                    window.location.href = `/money-boxes/${this.createdMoneyBoxId}`;
                }
            }
        }

        if (typeof Alpine !== 'undefined') {
            Alpine.data('moneyBoxForm', moneyBoxForm);
        } else {
            document.addEventListener('alpine:init', () => {
                Alpine.data('moneyBoxForm', moneyBoxForm);
            });
        }
    </script>
</x-layouts.app>
