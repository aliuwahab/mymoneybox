<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-zinc-900 py-4 sm:py-8" 
         x-data="moneyBoxForm()" 
         x-init="init()">
        <div class="max-w-4xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6 sm:mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Create Money Box</h1>
                <p class="mt-2 text-sm sm:text-base text-gray-600 dark:text-gray-300">Set up a new money box to collect contributions</p>
            </div>

            <!-- Progress Steps -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <template x-for="(stepInfo, index) in steps" :key="index">
                        <div class="flex items-center" :class="{ 'flex-1': index < steps.length - 1 }">
                            <!-- Step Circle -->
                            <div class="relative">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center font-semibold transition-all"
                                     :class="{
                                         'bg-green-600 text-white': currentStep > index + 1,
                                         'bg-green-600 text-white ring-4 ring-green-100': currentStep === index + 1,
                                         'bg-gray-200 dark:bg-zinc-700 text-gray-500 dark:text-gray-400': currentStep < index + 1
                                     }">
                                    <span x-show="currentStep > index + 1">✓</span>
                                    <span x-show="currentStep <= index + 1" x-text="index + 1"></span>
                                </div>
                                <div class="hidden sm:block absolute top-full mt-2 left-1/2 -translate-x-1/2 w-32 text-center">
                                    <span class="text-xs font-medium" 
                                          :class="{
                                              'text-green-600 dark:text-green-400': currentStep >= index + 1,
                                              'text-gray-500 dark:text-gray-400': currentStep < index + 1
                                          }"
                                          x-text="stepInfo.name"></span>
                                </div>
                            </div>
                            <!-- Connector Line -->
                            <div x-show="index < steps.length - 1" 
                                 class="flex-1 h-1 mx-2 rounded transition-all"
                                 :class="{
                                     'bg-green-600': currentStep > index + 1,
                                     'bg-gray-200 dark:bg-zinc-700': currentStep <= index + 1
                                 }"></div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Form Container -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-4 sm:p-6 lg:p-8">
                <form @submit.prevent="handleSubmit">
                    <!-- Step 1: Basic Information -->
                    <div x-show="currentStep === 1" x-transition x-cloak>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Basic Information</h2>
                        
                        <div class="space-y-4">
                            <!-- Title -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Title *
                                </label>
                                <input type="text" x-model="formData.title" required 
                                       placeholder="e.g., Birthday Gift for Mom"
                                       class="w-full">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Give your money box a clear, descriptive title</p>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Description
                                </label>
                                <textarea x-model="formData.description" rows="4"
                                          placeholder="Tell people about this money box..."></textarea>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Explain the purpose and story behind your money box</p>
                            </div>

                            <!-- Category -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Category
                                </label>
                                <select x-model="formData.category_id">
                                    <option value="">Select a category (optional)</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->icon }} {{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Contribution Settings -->
                    <div x-show="currentStep === 2" x-transition x-cloak>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Contribution Settings</h2>
                        
                        <div class="space-y-4">
                            <!-- Amount Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Amount Type *
                                </label>
                                <select x-model="formData.amount_type" required>
                                    <option value="variable">Variable - Contributors choose amount</option>
                                    <option value="fixed">Fixed - Specific amount only</option>
                                    <option value="minimum">Minimum - At least a certain amount</option>
                                    <option value="maximum">Maximum - Up to a certain amount</option>
                                    <option value="range">Range - Between min and max</option>
                                </select>
                            </div>

                            <!-- Fixed Amount -->
                            <div x-show="formData.amount_type === 'fixed'">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Fixed Amount ({{ auth()->user()->country?->currency_symbol ?? '₵' }})
                                </label>
                                <input type="number" x-model="formData.fixed_amount" step="0.01" min="0">
                            </div>

                            <!-- Minimum Amount -->
                            <div x-show="formData.amount_type === 'minimum' || formData.amount_type === 'range'">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Minimum Amount ({{ auth()->user()->country?->currency_symbol ?? '₵' }})
                                </label>
                                <input type="number" x-model="formData.minimum_amount" step="0.01" min="0">
                            </div>

                            <!-- Maximum Amount -->
                            <div x-show="formData.amount_type === 'maximum' || formData.amount_type === 'range'">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Maximum Amount ({{ auth()->user()->country?->currency_symbol ?? '₵' }})
                                </label>
                                <input type="number" x-model="formData.maximum_amount" step="0.01" min="0">
                            </div>

                            <!-- Goal Amount -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Goal Amount ({{ auth()->user()->country?->currency_symbol ?? '₵' }}) (Optional)
                                </label>
                                <input type="number" x-model="formData.goal_amount" step="0.01" min="0"
                                       placeholder="Set a fundraising goal">
                            </div>

                            <!-- Contributor Identity -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Contributor Identity *
                                </label>
                                <select x-model="formData.contributor_identity" required>
                                    <option value="user_choice">Let contributors choose</option>
                                    <option value="must_identify">Must identify (no anonymous)</option>
                                    <option value="anonymous_allowed">Anonymous allowed</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Visibility & Timeline -->
                    <div x-show="currentStep === 3" x-transition x-cloak>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Visibility & Timeline</h2>
                        
                        <div class="space-y-4">
                            <!-- Visibility -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Visibility *
                                </label>
                                <select x-model="formData.visibility" required>
                                    <option value="public">Public - Listed on homepage</option>
                                    <option value="private">Private - Only accessible via link</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Public boxes appear in search. Private boxes require the direct link.</p>
                            </div>

                            <!-- Start Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Start Date (Optional)
                                </label>
                                <input type="datetime-local" x-model="formData.start_date">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave empty to start immediately</p>
                            </div>

                            <!-- Is Ongoing -->
                            <div class="flex items-center">
                                <input type="checkbox" x-model="formData.is_ongoing" id="is_ongoing" class="mr-2">
                                <label for="is_ongoing" class="text-sm text-gray-700 dark:text-gray-300">
                                    This money box is ongoing (no end date)
                                </label>
                            </div>

                            <!-- End Date -->
                            <div x-show="!formData.is_ongoing">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    End Date
                                </label>
                                <input type="datetime-local" x-model="formData.end_date">
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <p class="text-sm text-green-800 dark:text-green-200">
                                <strong>Next step:</strong> After creating your money box, you'll be able to upload images to make it more appealing to contributors.
                            </p>
                        </div>
                    </div>

                    <!-- Step 4: Media Upload (After Creation) -->
                    <div x-show="currentStep === 4" x-transition x-cloak>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Add Images</h2>
                        
                        <div class="space-y-6">
                            <div class="text-center py-4">
                                <svg class="mx-auto h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">Money Box Created!</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Now add images to make it more appealing</p>
                            </div>

                            <!-- Main Image Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Main Image
                                </label>
                                <div class="border-2 border-dashed border-gray-300 dark:border-zinc-600 rounded-lg p-6 text-center">
                                    <input type="file" @change="handleMainImage" accept="image/*" class="hidden" id="main-image">
                                    <label for="main-image" class="cursor-pointer">
                                        <div x-show="!mainImagePreview">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Click to upload main image</p>
                                        </div>
                                        <div x-show="mainImagePreview">
                                            <img :src="mainImagePreview" class="mx-auto max-h-48 rounded">
                                            <p class="mt-2 text-sm text-green-600">Main image selected</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Gallery Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Gallery Images (Optional)
                                </label>
                                <div class="border-2 border-dashed border-gray-300 dark:border-zinc-600 rounded-lg p-6 text-center">
                                    <input type="file" @change="handleGallery" accept="image/*" multiple class="hidden" id="gallery-images">
                                    <label for="gallery-images" class="cursor-pointer">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Click to upload gallery images</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">You can select multiple images</p>
                                    </label>
                                </div>
                                <div x-show="galleryPreviews.length > 0" class="mt-4 grid grid-cols-3 gap-2">
                                    <template x-for="(preview, index) in galleryPreviews" :key="index">
                                        <img :src="preview" class="h-24 w-full object-cover rounded">
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="mt-8 flex justify-between items-center pt-6 border-t border-gray-200 dark:border-zinc-700">
                        <button type="button" @click="previousStep" 
                                x-show="currentStep > 1 && currentStep < 4"
                                class="px-6 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-zinc-700 border border-gray-300 dark:border-zinc-600 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-600 transition">
                            Previous
                        </button>

                        <div class="flex space-x-3 ml-auto">
                            <a href="{{ route('dashboard') }}" 
                               x-show="currentStep < 4"
                               class="px-6 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-zinc-700 border border-gray-300 dark:border-zinc-600 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-600 transition">
                                Cancel
                            </a>

                            <button type="button" @click="nextStep" 
                                    x-show="currentStep < 3"
                                    class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-sm transition">
                                Next
                            </button>

                            <button type="submit" 
                                    x-show="currentStep === 3"
                                    :disabled="isSubmitting"
                                    class="px-6 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-semibold rounded-lg shadow-sm transition">
                                <span x-show="!isSubmitting">Create Money Box</span>
                                <span x-show="isSubmitting">Creating...</span>
                            </button>

                            <button type="button" @click="uploadMedia" 
                                    x-show="currentStep === 4"
                                    :disabled="isUploading"
                                    class="px-6 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-semibold rounded-lg shadow-sm transition">
                                <span x-show="!isUploading">Save & Continue</span>
                                <span x-show="isUploading">Uploading...</span>
                            </button>

                            <button type="button" @click="skipMedia" 
                                    x-show="currentStep === 4"
                                    class="px-6 py-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition">
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
                    { name: 'Basic Info' },
                    { name: 'Contribution' },
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

                init() {
                    // Initialize
                },

                nextStep() {
                    if (this.currentStep < 3) {
                        this.currentStep++;
                    }
                },

                previousStep() {
                    if (this.currentStep > 1) {
                        this.currentStep--;
                    }
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
                            this.currentStep = 4; // Move to media upload step
                        } else {
                            alert('Error: ' + (data.message || 'Failed to create money box'));
                        }
                    } catch (error) {
                        alert('Error creating money box. Please try again.');
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
                        reader.onload = (e) => {
                            this.mainImagePreview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                },

                handleGallery(event) {
                    this.galleryFiles = Array.from(event.target.files);
                    this.galleryPreviews = [];
                    
                    this.galleryFiles.forEach(file => {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.galleryPreviews.push(e.target.result);
                        };
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
                        
                        if (this.mainImageFile) {
                            formData.append('main_image', this.mainImageFile);
                        }
                        
                        this.galleryFiles.forEach((file, index) => {
                            formData.append(`gallery[${index}]`, file);
                        });

                        const response = await fetch(`/money-boxes/${this.createdMoneyBoxId}/upload-media`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
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
                        console.error(error);
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
    </script>

    <script>
        // Register Alpine component when Alpine is ready
        if (typeof Alpine !== 'undefined') {
            Alpine.data('moneyBoxForm', moneyBoxForm);
        } else {
            document.addEventListener('alpine:init', () => {
                Alpine.data('moneyBoxForm', moneyBoxForm);
            });
        }
    </script>
</x-layouts.app>
