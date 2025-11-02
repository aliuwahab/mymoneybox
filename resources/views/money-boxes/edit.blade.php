<x-layouts.app>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Edit Piggy Box</h1>
                <p class="mt-2 text-gray-600">Update your piggy box settings</p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('money-boxes.update', $moneyBox) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow p-6 space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>

                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                            Title *
                        </label>
                        <input
                            type="text"
                            name="title"
                            id="title"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            value="{{ old('title', $moneyBox->title) }}"
                        />
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                            Description
                        </label>
                        <textarea
                            name="description"
                            id="description"
                            rows="4"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        >{{ old('description', $moneyBox->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Category
                        </label>
                        <select
                            name="category_id"
                            id="category_id"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        >
                            <option value="">Select a category (optional)</option>
                            @foreach($categories as $category)
                                <option
                                    value="{{ $category->id }}"
                                    {{ old('category_id', $moneyBox->category_id) == $category->id ? 'selected' : '' }}
                                >
                                    {{ $category->icon }} {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Contribution Settings -->
                <div class="bg-white rounded-lg shadow p-6 space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Contribution Settings</h2>

                    <!-- Amount Type -->
                    <div>
                        <label for="amount_type" class="block text-sm font-medium text-gray-700 mb-1">
                            Amount Type *
                        </label>
                        <select
                            name="amount_type"
                            id="amount_type"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            onchange="toggleAmountFields()"
                        >
                            <option value="variable" {{ old('amount_type', $moneyBox->amount_type->value) == 'variable' ? 'selected' : '' }}>Variable - Contributors choose amount</option>
                            <option value="fixed" {{ old('amount_type', $moneyBox->amount_type->value) == 'fixed' ? 'selected' : '' }}>Fixed - Specific amount only</option>
                            <option value="minimum" {{ old('amount_type', $moneyBox->amount_type->value) == 'minimum' ? 'selected' : '' }}>Minimum - At least a certain amount</option>
                            <option value="maximum" {{ old('amount_type', $moneyBox->amount_type->value) == 'maximum' ? 'selected' : '' }}>Maximum - Up to a certain amount</option>
                            <option value="range" {{ old('amount_type', $moneyBox->amount_type->value) == 'range' ? 'selected' : '' }}>Range - Between min and max</option>
                        </select>
                        @error('amount_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Amount Fields -->
                    <div id="fixed_amount_field" class="hidden">
                        <label for="fixed_amount" class="block text-sm font-medium text-gray-700 mb-1">
                            Fixed Amount ({{ $moneyBox->getCurrencySymbol() }})
                        </label>
                        <input
                            type="number"
                            name="fixed_amount"
                            id="fixed_amount"
                            step="0.01"
                            min="0"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            value="{{ old('fixed_amount', $moneyBox->fixed_amount) }}"
                        />
                        @error('fixed_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="minimum_amount_field" class="hidden">
                        <label for="minimum_amount" class="block text-sm font-medium text-gray-700 mb-1">
                            Minimum Amount ({{ $moneyBox->getCurrencySymbol() }})
                        </label>
                        <input
                            type="number"
                            name="minimum_amount"
                            id="minimum_amount"
                            step="0.01"
                            min="0"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            value="{{ old('minimum_amount', $moneyBox->minimum_amount) }}"
                        />
                        @error('minimum_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="maximum_amount_field" class="hidden">
                        <label for="maximum_amount" class="block text-sm font-medium text-gray-700 mb-1">
                            Maximum Amount ({{ $moneyBox->getCurrencySymbol() }})
                        </label>
                        <input
                            type="number"
                            name="maximum_amount"
                            id="maximum_amount"
                            step="0.01"
                            min="0"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            value="{{ old('maximum_amount', $moneyBox->maximum_amount) }}"
                        />
                        @error('maximum_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Goal Amount -->
                    <div>
                        <label for="goal_amount" class="block text-sm font-medium text-gray-700 mb-1">
                            Goal Amount ({{ $moneyBox->getCurrencySymbol() }}) (Optional)
                        </label>
                        <input
                            type="number"
                            name="goal_amount"
                            id="goal_amount"
                            step="0.01"
                            min="0"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            value="{{ old('goal_amount', $moneyBox->goal_amount) }}"
                        />
                        @error('goal_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contributor Identity -->
                    <div>
                        <label for="contributor_identity" class="block text-sm font-medium text-gray-700 mb-1">
                            Contributor Identity *
                        </label>
                        <select
                            name="contributor_identity"
                            id="contributor_identity"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        >
                            <option value="user_choice" {{ old('contributor_identity', $moneyBox->contributor_identity->value) == 'user_choice' ? 'selected' : '' }}>Let contributors choose</option>
                            <option value="must_identify" {{ old('contributor_identity', $moneyBox->contributor_identity->value) == 'must_identify' ? 'selected' : '' }}>Must identify (no anonymous)</option>
                            <option value="anonymous_allowed" {{ old('contributor_identity', $moneyBox->contributor_identity->value) == 'anonymous_allowed' ? 'selected' : '' }}>Anonymous allowed</option>
                        </select>
                        @error('contributor_identity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Visibility & Timeline -->
                <div class="bg-white rounded-lg shadow p-6 space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Visibility & Timeline</h2>

                    <!-- Visibility -->
                    <div>
                        <label for="visibility" class="block text-sm font-medium text-gray-700 mb-1">
                            Visibility *
                        </label>
                        <select
                            name="visibility"
                            id="visibility"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        >
                            <option value="public" {{ old('visibility', $moneyBox->visibility->value) == 'public' ? 'selected' : '' }}>Public - Listed on homepage</option>
                            <option value="private" {{ old('visibility', $moneyBox->visibility->value) == 'private' ? 'selected' : '' }}>Private - Only accessible via link</option>
                        </select>
                        @error('visibility')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            name="is_active"
                            id="is_active"
                            value="1"
                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                            {{ old('is_active', $moneyBox->is_active) ? 'checked' : '' }}
                        />
                        <label for="is_active" class="ml-2 text-sm text-gray-700">
                            Active (accepting contributions)
                        </label>
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Start Date (Optional)
                        </label>
                        <input
                            type="datetime-local"
                            name="start_date"
                            id="start_date"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            value="{{ old('start_date', $moneyBox->start_date?->format('Y-m-d\TH:i')) }}"
                        />
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Is Ongoing Checkbox -->
                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            name="is_ongoing"
                            id="is_ongoing"
                            value="1"
                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                            {{ old('is_ongoing', $moneyBox->is_ongoing) ? 'checked' : '' }}
                            onchange="toggleEndDate()"
                        />
                        <label for="is_ongoing" class="ml-2 text-sm text-gray-700">
                            This piggy box is ongoing (no end date)
                        </label>
                    </div>

                    <!-- End Date -->
                    <div id="end_date_field">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                            End Date
                        </label>
                        <input
                            type="datetime-local"
                            name="end_date"
                            id="end_date"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            value="{{ old('end_date', $moneyBox->end_date?->format('Y-m-d\TH:i')) }}"
                        />
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Media Management -->
                <div class="space-y-6 p-6 bg-gray-50 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Media Management</h3>

                    <!-- Main Image -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Main Image
                        </label>
                        
                        @if($moneyBox->hasMedia('main'))
                            <div class="mb-3 relative inline-block">
                                <img src="{{ $moneyBox->getMainImageUrl() }}" 
                                     alt="Main image" 
                                     class="w-48 h-48 object-cover rounded-lg border border-gray-300">
                                <button
                                    type="button"
                                    onclick="confirmDelete(() => { document.getElementById('remove_main_image').value = '1'; this.parentElement.style.display='none'; }, { title: 'Remove Image?', text: 'This image will be removed when you save.', confirmText: 'Yes, remove it!' })"
                                    class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 hover:bg-red-700 transition cursor-pointer"
                                    title="Remove image"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <input type="hidden" name="remove_main_image" id="remove_main_image" value="0">
                        @endif
                        
                        <input
                            type="file"
                            name="main_image"
                            id="main_image"
                            accept="image/*"
                            class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer"
                            onchange="previewImage(event, 'main_preview')"
                        />
                        <div id="main_preview" class="mt-2"></div>
                        @error('main_image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gallery Images -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Gallery Images
                        </label>
                        
                        @if($moneyBox->hasMedia('gallery'))
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-3">
                                @foreach($moneyBox->getMedia('gallery') as $media)
                                    <div class="relative">
                                        <img src="{{ $media->getTemporaryUrl(now()->addHour()) }}" 
                                             alt="Gallery image" 
                                             class="w-full h-32 object-cover rounded-lg border border-gray-300">
                                        <button
                                            type="button"
                                            onclick="confirmDelete(() => { let input = document.getElementById('remove_gallery_images'); input.value = input.value ? input.value + ',' + {{ $media->id }} : {{ $media->id }}; this.parentElement.style.display='none'; }, { title: 'Remove Image?', text: 'This image will be removed when you save.', confirmText: 'Yes, remove it!' })"
                                            class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 hover:bg-red-700 transition cursor-pointer"
                                            title="Remove image"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="remove_gallery_images" id="remove_gallery_images" value="">
                        @endif
                        
                        <input
                            type="file"
                            id="gallery_images_input"
                            accept="image/*"
                            multiple
                            class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer"
                            onchange="handleGallerySelection(event)"
                        />
                        <div id="gallery_preview" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3 mt-3"></div>
                        <!-- Hidden container for actual file inputs that will be submitted -->
                        <div id="gallery_files_container"></div>
                        @error('gallery_images')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('gallery_images.*')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    @if($moneyBox->contribution_count > 0)
                        <div class="relative group">
                            <button
                                type="button"
                                disabled
                                class="w-full sm:w-auto px-4 sm:px-6 py-2.5 sm:py-3 text-sm sm:text-base text-gray-400 bg-gray-100 border border-gray-300 rounded-lg cursor-not-allowed"
                            >
                                Delete
                            </button>
                            <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block w-64 p-2 bg-gray-900 text-white text-xs rounded shadow-lg z-10">
                                This piggy box has {{ $moneyBox->contribution_count }} {{ Str::plural('contribution', $moneyBox->contribution_count) }} and cannot be deleted
                                <div class="absolute top-full left-4 -mt-1 border-4 border-transparent border-t-gray-900"></div>
                            </div>
                        </div>
                    @else
                        <button
                            type="button"
                            onclick="confirmDelete(() => document.getElementById('delete-form').submit(), { title: 'Delete Piggy Box?', text: 'All contributions and data will be permanently deleted!', confirmText: 'Yes, delete it!' })"
                            class="w-full sm:w-auto px-4 sm:px-6 py-2.5 sm:py-3 text-sm sm:text-base text-red-700 bg-red-50 border border-red-300 rounded-lg hover:bg-red-100 transition cursor-pointer"
                        >
                            Delete
                        </button>
                    @endif
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4">
                        <a
                            href="{{ route('money-boxes.show', $moneyBox) }}"
                            class="text-center px-4 sm:px-6 py-2.5 sm:py-3 text-sm sm:text-base text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                        >
                            Cancel
                        </a>
                        <button
                            type="submit"
                            class="px-4 sm:px-6 py-2.5 sm:py-3 text-sm sm:text-base bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition cursor-pointer"
                        >
                            Update Piggy Box
                        </button>
                    </div>
                </div>
            </form>

            <!-- Delete Form -->
            <form
                id="delete-form"
                method="POST"
                action="{{ route('money-boxes.destroy', $moneyBox) }}"
                class="hidden"
            >
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    <script>
        function toggleAmountFields() {
            const amountType = document.getElementById('amount_type').value;

            document.getElementById('fixed_amount_field').classList.add('hidden');
            document.getElementById('minimum_amount_field').classList.add('hidden');
            document.getElementById('maximum_amount_field').classList.add('hidden');

            if (amountType === 'fixed') {
                document.getElementById('fixed_amount_field').classList.remove('hidden');
            } else if (amountType === 'minimum' || amountType === 'range') {
                document.getElementById('minimum_amount_field').classList.remove('hidden');
            }

            if (amountType === 'maximum' || amountType === 'range') {
                document.getElementById('maximum_amount_field').classList.remove('hidden');
            }
        }

        function toggleEndDate() {
            const isOngoing = document.getElementById('is_ongoing').checked;
            const endDateField = document.getElementById('end_date_field');

            if (isOngoing) {
                endDateField.classList.add('hidden');
                document.getElementById('end_date').value = '';
            } else {
                endDateField.classList.remove('hidden');
            }
        }

        function previewImage(event, previewId) {
            const preview = document.getElementById(previewId);
            const file = event.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <div class="mt-3">
                            <p class="text-sm font-medium text-gray-700 mb-2">Preview (New Main Image):</p>
                            <img src="${e.target.result}" class="w-48 h-48 object-cover rounded-lg border-2 border-blue-300 shadow-sm" alt="Preview">
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '';
            }
        }

        // Gallery file management
        let galleryFiles = [];
        let fileCounter = 0;

        function handleGallerySelection(event) {
            const newFiles = Array.from(event.target.files);
            
            newFiles.forEach(file => {
                const fileId = fileCounter++;
                galleryFiles.push({ id: fileId, file: file });
                addGalleryPreview(file, fileId);
            });
            
            // Clear the input so the same file can be selected again
            event.target.value = '';
            updateGalleryFilesInput();
        }

        function addGalleryPreview(file, fileId) {
            const preview = document.getElementById('gallery_preview');
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative';
                div.id = `gallery_preview_${fileId}`;
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg border-2 border-blue-300 shadow-sm" alt="Preview">
                    <button
                        type="button"
                        onclick="removeGalleryFile(${fileId})"
                        class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 hover:bg-red-700 transition"
                        title="Remove image"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <span class="absolute bottom-1 left-1 bg-blue-600 text-white text-xs px-2 py-0.5 rounded">${galleryFiles.length}</span>
                `;
                preview.appendChild(div);
            };
            
            reader.readAsDataURL(file);
        }

        function removeGalleryFile(fileId) {
            confirmDelete(() => {
                // Remove from array
                galleryFiles = galleryFiles.filter(f => f.id !== fileId);
                
                // Remove preview
                const previewElement = document.getElementById(`gallery_preview_${fileId}`);
                if (previewElement) {
                    previewElement.remove();
                }
                
                // Update file input
                updateGalleryFilesInput();
                
                // Update numbering on remaining previews
                updateGalleryNumbering();
            }, { 
                title: 'Remove Image?', 
                text: 'This will remove the image from the upload queue.', 
                confirmText: 'Yes, remove it!' 
            });
        }

        function updateGalleryNumbering() {
            const previews = document.getElementById('gallery_preview').children;
            Array.from(previews).forEach((preview, index) => {
                const badge = preview.querySelector('span');
                if (badge) {
                    badge.textContent = index + 1;
                }
            });
        }

        function updateGalleryFilesInput() {
            const container = document.getElementById('gallery_files_container');
            if (!container) return;
            
            container.innerHTML = '';
            
            // Create a DataTransfer object to hold files
            const dt = new DataTransfer();
            galleryFiles.forEach(({ file }) => {
                dt.items.add(file);
            });
            
            // Create a new file input with all the files
            const input = document.createElement('input');
            input.type = 'file';
            input.name = 'gallery_images[]';
            input.multiple = true;
            input.files = dt.files;
            container.appendChild(input);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleAmountFields();
            toggleEndDate();
        });
    </script>
</x-layouts.app>
