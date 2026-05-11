<x-layouts.app>
    <div class="page-wrap max-w-[720px] mx-auto w-full"
         x-data="moneyBoxForm()"
         x-init="init()">

        {{-- Page header --}}
        <div class="mb-8">
            <h1 class="page-title" style="font-size:1.875rem;">Create a box</h1>
            <p class="tiny mt-1.5">Set up a new money box to collect contributions</p>
        </div>

        {{-- Step indicator --}}
        <div class="mb-8 flex items-center gap-0">
            <template x-for="(stepInfo, index) in steps" :key="index">
                <div class="flex items-center" :class="{ 'flex-1': index < steps.length - 1 }">
                    <div class="flex flex-col items-center gap-1.5">
                        {{-- Circle --}}
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-[13px] font-semibold transition-all"
                             :class="{
                                 'bg-primary-600 text-white': currentStep > index + 1,
                                 'bg-primary-600 text-white ring-4 ring-primary-600/15': currentStep === index + 1,
                                 'bg-[#ECEAE3] text-[#9C998F]': currentStep < index + 1
                             }">
                            <span x-show="currentStep > index + 1">
                                <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
                            </span>
                            <span x-show="currentStep <= index + 1" x-text="index + 1"></span>
                        </div>
                        {{-- Label --}}
                        <span class="hidden sm:block text-[10.5px] font-medium whitespace-nowrap"
                              :class="{ 'text-primary-600': currentStep >= index + 1, 'text-[#9C998F]': currentStep < index + 1 }"
                              x-text="stepInfo.name"></span>
                    </div>
                    {{-- Connector --}}
                    <div x-show="index < steps.length - 1"
                         class="flex-1 h-[2px] mx-3 mb-5 rounded-full transition-all"
                         :class="{ 'bg-primary-600': currentStep > index + 1, 'bg-[#E6E3DC]': currentStep <= index + 1 }"></div>
                </div>
            </template>
        </div>

        {{-- Form card --}}
        <div class="card">
            <div class="card-body">
                <form @submit.prevent="handleSubmit">

                    {{-- Step 1: Basic info --}}
                    <div x-show="currentStep === 1" x-transition x-cloak>
                        <h2 class="text-[15px] font-semibold text-[#15140F] mb-5">Basic information</h2>
                        <div class="space-y-4">
                            <div class="grid gap-1.5">
                                <label class="text-[13px] font-medium text-[#6B6862]">Title <span class="text-red-500">*</span></label>
                                <input type="text" x-model="formData.title" required placeholder="e.g., Birthday Gift for Mom">
                                <p class="text-[11.5px] text-[#9C998F]">Give your box a clear, descriptive title</p>
                            </div>
                            <div class="grid gap-1.5">
                                <label class="text-[13px] font-medium text-[#6B6862]">Description</label>
                                <textarea x-model="formData.description" rows="4" placeholder="Tell people about this box…"></textarea>
                                <p class="text-[11.5px] text-[#9C998F]">Explain the purpose and story behind your box</p>
                            </div>
                            <div class="grid gap-1.5">
                                <label class="text-[13px] font-medium text-[#6B6862]">Category</label>
                                <select x-model="formData.category_id">
                                    <option value="">Select a category (optional)</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->icon }} {{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Step 2: Contribution settings --}}
                    <div x-show="currentStep === 2" x-transition x-cloak>
                        <h2 class="text-[15px] font-semibold text-[#15140F] mb-5">Contribution settings</h2>
                        <div class="space-y-4">
                            <div class="grid gap-1.5">
                                <label class="text-[13px] font-medium text-[#6B6862]">Amount type <span class="text-red-500">*</span></label>
                                <select x-model="formData.amount_type" required>
                                    <option value="variable">Variable — contributors choose amount</option>
                                    <option value="fixed">Fixed — specific amount only</option>
                                    <option value="minimum">Minimum — at least a certain amount</option>
                                    <option value="maximum">Maximum — up to a certain amount</option>
                                    <option value="range">Range — between min and max</option>
                                </select>
                            </div>
                            <div x-show="formData.amount_type === 'fixed'" class="grid gap-1.5">
                                <label class="text-[13px] font-medium text-[#6B6862]">Fixed amount ({{ auth()->user()->country?->currency_symbol ?? '₵' }})</label>
                                <input type="number" x-model="formData.fixed_amount" step="0.01" min="0" placeholder="0.00">
                            </div>
                            <div x-show="formData.amount_type === 'minimum' || formData.amount_type === 'range'" class="grid gap-1.5">
                                <label class="text-[13px] font-medium text-[#6B6862]">Minimum amount ({{ auth()->user()->country?->currency_symbol ?? '₵' }})</label>
                                <input type="number" x-model="formData.minimum_amount" step="0.01" min="0" placeholder="0.00">
                            </div>
                            <div x-show="formData.amount_type === 'maximum' || formData.amount_type === 'range'" class="grid gap-1.5">
                                <label class="text-[13px] font-medium text-[#6B6862]">Maximum amount ({{ auth()->user()->country?->currency_symbol ?? '₵' }})</label>
                                <input type="number" x-model="formData.maximum_amount" step="0.01" min="0" placeholder="0.00">
                            </div>
                            <div class="grid gap-1.5">
                                <label class="text-[13px] font-medium text-[#6B6862]">Goal amount ({{ auth()->user()->country?->currency_symbol ?? '₵' }}) <span class="text-[#9C998F] font-normal">(optional)</span></label>
                                <input type="number" x-model="formData.goal_amount" step="0.01" min="0" placeholder="Set a fundraising goal">
                            </div>
                            <div class="grid gap-1.5">
                                <label class="text-[13px] font-medium text-[#6B6862]">Contributor identity <span class="text-red-500">*</span></label>
                                <select x-model="formData.contributor_identity" required>
                                    <option value="user_choice">Let contributors choose</option>
                                    <option value="must_identify">Must identify (no anonymous)</option>
                                    <option value="anonymous_allowed">Anonymous allowed</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3: Visibility & timeline --}}
                    <div x-show="currentStep === 3" x-transition x-cloak>
                        <h2 class="text-[15px] font-semibold text-[#15140F] mb-5">Visibility & timeline</h2>
                        <div class="space-y-4">
                            <div class="grid gap-1.5">
                                <label class="text-[13px] font-medium text-[#6B6862]">Visibility <span class="text-red-500">*</span></label>
                                <select x-model="formData.visibility" required>
                                    <option value="public">Public — listed on homepage</option>
                                    <option value="private">Private — only accessible via link</option>
                                </select>
                                <p class="text-[11.5px] text-[#9C998F]">Public boxes appear in search. Private boxes require the direct link.</p>
                            </div>
                            <div class="grid gap-1.5">
                                <label class="text-[13px] font-medium text-[#6B6862]">Start date <span class="text-[#9C998F] font-normal">(optional)</span></label>
                                <input type="datetime-local" x-model="formData.start_date">
                                <p class="text-[11.5px] text-[#9C998F]">Leave empty to start immediately</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="checkbox" x-model="formData.is_ongoing" id="is_ongoing" class="rounded border-[#D9D6CE]">
                                <label for="is_ongoing" class="text-[13px] text-[#15140F]">This box is ongoing (no end date)</label>
                            </div>
                            <div x-show="!formData.is_ongoing" class="grid gap-1.5">
                                <label class="text-[13px] font-medium text-[#6B6862]">End date</label>
                                <input type="datetime-local" x-model="formData.end_date">
                            </div>
                        </div>

                        <div class="mt-5 bg-[#F3F1EB] rounded-[8px] px-4 py-3 text-[12.5px] text-[#6B6862]">
                            After creating your box, you'll be able to upload images to make it more appealing to contributors.
                        </div>
                    </div>

                    {{-- Step 4: Media --}}
                    <div x-show="currentStep === 4" x-transition x-cloak>
                        <h2 class="text-[15px] font-semibold text-[#15140F] mb-5">Add images</h2>
                        <div class="space-y-6">
                            <div class="text-center py-4">
                                <div class="w-12 h-12 rounded-xl bg-primary-50 text-primary-600 grid place-items-center mx-auto mb-3">
                                    <svg viewBox="0 0 24 24" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m9 12 2 2 4-4"/><circle cx="12" cy="12" r="10"/>
                                    </svg>
                                </div>
                                <h3 class="text-[14px] font-semibold text-[#15140F]">Box created!</h3>
                                <p class="tiny mt-0.5">Now add images to make it more appealing</p>
                            </div>

                            {{-- Main image --}}
                            <div class="grid gap-2">
                                <label class="text-[13px] font-medium text-[#6B6862]">Main image</label>
                                <div class="border-2 border-dashed border-[#D9D6CE] rounded-[8px] p-6 text-center hover:border-[#9C998F] transition-colors duration-150">
                                    <input type="file" @change="handleMainImage" accept="image/*" class="hidden" id="main-image">
                                    <label for="main-image" class="cursor-pointer">
                                        <div x-show="!mainImagePreview">
                                            <svg class="mx-auto w-10 h-10 text-[#D9D6CE] mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                            </svg>
                                            <p class="text-[13px] text-[#6B6862]">Click to upload main image</p>
                                        </div>
                                        <div x-show="mainImagePreview">
                                            <img :src="mainImagePreview" class="mx-auto max-h-40 rounded-[6px] mb-2">
                                            <p class="text-[12px] text-primary-600 font-medium">Main image selected</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Gallery --}}
                            <div class="grid gap-2">
                                <label class="text-[13px] font-medium text-[#6B6862]">Gallery images <span class="text-[#9C998F] font-normal">(optional)</span></label>
                                <div class="border-2 border-dashed border-[#D9D6CE] rounded-[8px] p-6 text-center hover:border-[#9C998F] transition-colors duration-150">
                                    <input type="file" @change="handleGallery" accept="image/*" multiple class="hidden" id="gallery-images">
                                    <label for="gallery-images" class="cursor-pointer">
                                        <svg class="mx-auto w-10 h-10 text-[#D9D6CE] mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                        </svg>
                                        <p class="text-[13px] text-[#6B6862]">Click to upload gallery images</p>
                                        <p class="text-[11.5px] text-[#9C998F]">You can select multiple images</p>
                                    </label>
                                </div>
                                <div x-show="galleryPreviews.length > 0" class="grid grid-cols-4 gap-2">
                                    <template x-for="(preview, index) in galleryPreviews" :key="index">
                                        <img :src="preview" class="h-20 w-full object-cover rounded-[6px]">
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Navigation --}}
                    <div class="mt-6 flex justify-between items-center pt-5 border-t border-[#E6E3DC]">
                        <button type="button" @click="previousStep"
                                x-show="currentStep > 1 && currentStep < 4"
                                class="btn">
                            Previous
                        </button>

                        <div class="flex items-center gap-2 ml-auto">
                            <a href="{{ route('dashboard') }}"
                               x-show="currentStep < 4"
                               class="btn"
                               wire:navigate>
                                Cancel
                            </a>

                            <button type="button" @click="nextStep"
                                    x-show="currentStep < 3"
                                    class="btn btn-primary">
                                Next
                            </button>

                            <button type="submit"
                                    x-show="currentStep === 3"
                                    :disabled="isSubmitting"
                                    class="btn btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!isSubmitting">Create box</span>
                                <span x-show="isSubmitting">Creating…</span>
                            </button>

                            <button type="button" @click="uploadMedia"
                                    x-show="currentStep === 4"
                                    :disabled="isUploading"
                                    class="btn btn-primary disabled:opacity-50">
                                <span x-show="!isUploading">Save & continue</span>
                                <span x-show="isUploading">Uploading…</span>
                            </button>

                            <button type="button" @click="skipMedia"
                                    x-show="currentStep === 4"
                                    class="btn">
                                Skip for now
                            </button>
                        </div>
                    </div>
                </form>
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
                steps: [
                    { name: 'Basic info' },
                    { name: 'Contributions' },
                    { name: 'Visibility' },
                    { name: 'Media' }
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
                            alert('Error: ' + (data.message || 'Failed to create box'));
                        }
                    } catch (error) {
                        alert('Error creating box. Please try again.');
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