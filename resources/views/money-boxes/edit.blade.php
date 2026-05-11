<x-layouts.app>
    <div class="px-7 py-7 max-w-[720px]">

        {{-- Header --}}
        <div class="mb-6">
            <h1 class="page-title" style="font-size:1.875rem;">Edit box</h1>
            <p class="tiny mt-1.5">Update your money box settings</p>
        </div>

        <form method="POST" action="{{ route('money-boxes.update', $moneyBox) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Basic info --}}
            <div class="card">
                <div class="card-head"><span class="card-title">Basic information</span></div>
                <div class="card-body space-y-4">
                    <div class="grid gap-1.5">
                        <label for="title" class="text-[13px] font-medium text-[#6B6862]">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" required value="{{ old('title', $moneyBox->title) }}" class="@error('title') border-red-400 @enderror" />
                        @error('title')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid gap-1.5">
                        <label for="description" class="text-[13px] font-medium text-[#6B6862]">Description</label>
                        <textarea name="description" id="description" rows="4" class="@error('description') border-red-400 @enderror">{{ old('description', $moneyBox->description) }}</textarea>
                        @error('description')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid gap-1.5">
                        <label for="category_id" class="text-[13px] font-medium text-[#6B6862]">Category</label>
                        <select name="category_id" id="category_id" class="@error('category_id') border-red-400 @enderror">
                            <option value="">Select a category (optional)</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $moneyBox->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->icon }} {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Contribution settings --}}
            <div class="card">
                <div class="card-head"><span class="card-title">Contribution settings</span></div>
                <div class="card-body space-y-4">
                    <div class="grid gap-1.5">
                        <label for="amount_type" class="text-[13px] font-medium text-[#6B6862]">Amount type <span class="text-red-500">*</span></label>
                        <select name="amount_type" id="amount_type" required onchange="toggleAmountFields()">
                            <option value="variable" {{ old('amount_type', $moneyBox->amount_type->value) == 'variable' ? 'selected' : '' }}>Variable — contributors choose amount</option>
                            <option value="fixed" {{ old('amount_type', $moneyBox->amount_type->value) == 'fixed' ? 'selected' : '' }}>Fixed — specific amount only</option>
                            <option value="minimum" {{ old('amount_type', $moneyBox->amount_type->value) == 'minimum' ? 'selected' : '' }}>Minimum — at least a certain amount</option>
                            <option value="maximum" {{ old('amount_type', $moneyBox->amount_type->value) == 'maximum' ? 'selected' : '' }}>Maximum — up to a certain amount</option>
                            <option value="range" {{ old('amount_type', $moneyBox->amount_type->value) == 'range' ? 'selected' : '' }}>Range — between min and max</option>
                        </select>
                        @error('amount_type')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div id="fixed_amount_field" class="hidden grid gap-1.5">
                        <label for="fixed_amount" class="text-[13px] font-medium text-[#6B6862]">Fixed amount ({{ $moneyBox->getCurrencySymbol() }})</label>
                        <input type="number" name="fixed_amount" id="fixed_amount" step="0.01" min="0" value="{{ old('fixed_amount', $moneyBox->fixed_amount) }}" placeholder="0.00" />
                        @error('fixed_amount')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div id="minimum_amount_field" class="hidden grid gap-1.5">
                        <label for="minimum_amount" class="text-[13px] font-medium text-[#6B6862]">Minimum amount ({{ $moneyBox->getCurrencySymbol() }})</label>
                        <input type="number" name="minimum_amount" id="minimum_amount" step="0.01" min="0" value="{{ old('minimum_amount', $moneyBox->minimum_amount) }}" placeholder="0.00" />
                        @error('minimum_amount')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div id="maximum_amount_field" class="hidden grid gap-1.5">
                        <label for="maximum_amount" class="text-[13px] font-medium text-[#6B6862]">Maximum amount ({{ $moneyBox->getCurrencySymbol() }})</label>
                        <input type="number" name="maximum_amount" id="maximum_amount" step="0.01" min="0" value="{{ old('maximum_amount', $moneyBox->maximum_amount) }}" placeholder="0.00" />
                        @error('maximum_amount')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid gap-1.5">
                        <label for="goal_amount" class="text-[13px] font-medium text-[#6B6862]">Goal amount ({{ $moneyBox->getCurrencySymbol() }}) <span class="text-[#9C998F] font-normal">(optional)</span></label>
                        <input type="number" name="goal_amount" id="goal_amount" step="0.01" min="0" value="{{ old('goal_amount', $moneyBox->goal_amount) }}" placeholder="Set a fundraising goal" />
                        @error('goal_amount')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid gap-1.5">
                        <label for="contributor_identity" class="text-[13px] font-medium text-[#6B6862]">Contributor identity <span class="text-red-500">*</span></label>
                        <select name="contributor_identity" id="contributor_identity" required>
                            <option value="user_choice" {{ old('contributor_identity', $moneyBox->contributor_identity->value) == 'user_choice' ? 'selected' : '' }}>Let contributors choose</option>
                            <option value="must_identify" {{ old('contributor_identity', $moneyBox->contributor_identity->value) == 'must_identify' ? 'selected' : '' }}>Must identify (no anonymous)</option>
                            <option value="anonymous_allowed" {{ old('contributor_identity', $moneyBox->contributor_identity->value) == 'anonymous_allowed' ? 'selected' : '' }}>Anonymous allowed</option>
                        </select>
                        @error('contributor_identity')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Visibility & timeline --}}
            <div class="card">
                <div class="card-head"><span class="card-title">Visibility & timeline</span></div>
                <div class="card-body space-y-4">
                    <div class="grid gap-1.5">
                        <label for="visibility" class="text-[13px] font-medium text-[#6B6862]">Visibility <span class="text-red-500">*</span></label>
                        <select name="visibility" id="visibility" required>
                            <option value="public" {{ old('visibility', $moneyBox->visibility->value) == 'public' ? 'selected' : '' }}>Public — listed on homepage</option>
                            <option value="private" {{ old('visibility', $moneyBox->visibility->value) == 'private' ? 'selected' : '' }}>Private — only accessible via link</option>
                        </select>
                        @error('visibility')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                               class="rounded border-[#D9D6CE] text-primary-600"
                               {{ old('is_active', $moneyBox->is_active) ? 'checked' : '' }} />
                        <label for="is_active" class="text-[13px] text-[#15140F]">Active (accepting contributions)</label>
                    </div>

                    <div class="grid gap-1.5">
                        <label for="start_date" class="text-[13px] font-medium text-[#6B6862]">Start date <span class="text-[#9C998F] font-normal">(optional)</span></label>
                        <input type="datetime-local" name="start_date" id="start_date" value="{{ old('start_date', $moneyBox->start_date?->format('Y-m-d\TH:i')) }}" />
                        @error('start_date')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_ongoing" id="is_ongoing" value="1"
                               class="rounded border-[#D9D6CE] text-primary-600"
                               {{ old('is_ongoing', $moneyBox->is_ongoing) ? 'checked' : '' }}
                               onchange="toggleEndDate()" />
                        <label for="is_ongoing" class="text-[13px] text-[#15140F]">This box is ongoing (no end date)</label>
                    </div>

                    <div id="end_date_field" class="grid gap-1.5">
                        <label for="end_date" class="text-[13px] font-medium text-[#6B6862]">End date</label>
                        <input type="datetime-local" name="end_date" id="end_date" value="{{ old('end_date', $moneyBox->end_date?->format('Y-m-d\TH:i')) }}" />
                        @error('end_date')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Media management --}}
            <div class="card">
                <div class="card-head"><span class="card-title">Media</span></div>
                <div class="card-body space-y-6">
                    {{-- Main image --}}
                    <div class="grid gap-2">
                        <label class="text-[13px] font-medium text-[#6B6862]">Main image</label>
                        @if($moneyBox->hasMedia('main'))
                            <div class="relative inline-block mb-2">
                                <img src="{{ $moneyBox->getMainImageUrl() }}" alt="Main image" class="w-44 h-44 object-cover rounded-[8px] border border-[#E6E3DC]">
                                <button
                                    type="button"
                                    onclick="confirmDelete(() => { document.getElementById('remove_main_image').value = '1'; this.parentElement.style.display='none'; }, { title: 'Remove Image?', text: 'This image will be removed when you save.', confirmText: 'Yes, remove it!' })"
                                    class="absolute top-1.5 right-1.5 w-6 h-6 bg-[#15140F] text-white rounded-full grid place-items-center hover:bg-red-600 transition-colors duration-100"
                                    title="Remove image">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <input type="hidden" name="remove_main_image" id="remove_main_image" value="0">
                        @endif
                        <input type="file" name="main_image" id="main_image" accept="image/*"
                               class="text-[13px] text-[#6B6862] file:mr-3 file:py-1.5 file:px-3 file:rounded-[6px] file:border-0 file:text-[12px] file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer"
                               onchange="previewImage(event, 'main_preview')" />
                        <div id="main_preview"></div>
                        @error('main_image')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                    </div>

                    {{-- Gallery images --}}
                    <div class="grid gap-2">
                        <label class="text-[13px] font-medium text-[#6B6862]">Gallery images</label>
                        @if($moneyBox->hasMedia('gallery'))
                            <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 mb-2">
                                @foreach($moneyBox->getMedia('gallery') as $media)
                                    <div class="relative">
                                        <img src="{{ $media->getTemporaryUrl(now()->addHour()) }}" alt="Gallery image" class="w-full h-24 object-cover rounded-[6px] border border-[#E6E3DC]">
                                        <button
                                            type="button"
                                            onclick="confirmDelete(() => { let input = document.getElementById('remove_gallery_images'); input.value = input.value ? input.value + ',' + {{ $media->id }} : {{ $media->id }}; this.parentElement.style.display='none'; }, { title: 'Remove Image?', text: 'This image will be removed when you save.', confirmText: 'Yes, remove it!' })"
                                            class="absolute top-1 right-1 w-5 h-5 bg-[#15140F] text-white rounded-full grid place-items-center hover:bg-red-600 transition-colors duration-100">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="remove_gallery_images" id="remove_gallery_images" value="">
                        @endif
                        <input type="file" id="gallery_images_input" accept="image/*" multiple
                               class="text-[13px] text-[#6B6862] file:mr-3 file:py-1.5 file:px-3 file:rounded-[6px] file:border-0 file:text-[12px] file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer"
                               onchange="handleGallerySelection(event)" />
                        <div id="gallery_preview" class="grid grid-cols-4 sm:grid-cols-5 gap-2 mt-1"></div>
                        <div id="gallery_files_container"></div>
                        @error('gallery_images')<p class="text-[12px] text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between gap-3">
                @if($moneyBox->contribution_count > 0)
                    <div class="relative group">
                        <button type="button" disabled class="btn opacity-40 cursor-not-allowed">Delete</button>
                        <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block w-60 p-2.5 bg-[#15140F] text-white text-[11.5px] rounded-[6px] shadow-lg z-10">
                            This box has {{ $moneyBox->contribution_count }} {{ Str::plural('contribution', $moneyBox->contribution_count) }} and cannot be deleted.
                        </div>
                    </div>
                @else
                    <button
                        type="button"
                        onclick="confirmDelete(() => document.getElementById('delete-form').submit(), { title: 'Delete box?', text: 'All contributions and data will be permanently deleted!', confirmText: 'Yes, delete it!' })"
                        class="btn bg-red-50 border-red-200 text-red-700 hover:bg-red-100 hover:border-red-300">
                        Delete box
                    </button>
                @endif

                <div class="flex items-center gap-2">
                    <a href="{{ route('money-boxes.show', $moneyBox) }}" class="btn" wire:navigate>Cancel</a>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </form>

        {{-- Delete form --}}
        <form id="delete-form" method="POST" action="{{ route('money-boxes.destroy', $moneyBox) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>

    <script>
        function toggleAmountFields() {
            const amountType = document.getElementById('amount_type').value;
            document.getElementById('fixed_amount_field').classList.add('hidden');
            document.getElementById('minimum_amount_field').classList.add('hidden');
            document.getElementById('maximum_amount_field').classList.add('hidden');
            if (amountType === 'fixed') document.getElementById('fixed_amount_field').classList.remove('hidden');
            if (amountType === 'minimum' || amountType === 'range') document.getElementById('minimum_amount_field').classList.remove('hidden');
            if (amountType === 'maximum' || amountType === 'range') document.getElementById('maximum_amount_field').classList.remove('hidden');
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
                reader.onload = (e) => {
                    preview.innerHTML = `<img src="${e.target.result}" class="w-44 h-44 object-cover rounded-[8px] border border-[#E6E3DC] mt-2" alt="Preview">`;
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '';
            }
        }

        let galleryFiles = [];
        let fileCounter = 0;

        function handleGallerySelection(event) {
            Array.from(event.target.files).forEach(file => {
                const fileId = fileCounter++;
                galleryFiles.push({ id: fileId, file });
                addGalleryPreview(file, fileId);
            });
            event.target.value = '';
            updateGalleryFilesInput();
        }

        function addGalleryPreview(file, fileId) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const div = document.createElement('div');
                div.className = 'relative';
                div.id = `gallery_preview_${fileId}`;
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-20 object-cover rounded-[6px] border border-[#E6E3DC]">
                    <button type="button" onclick="removeGalleryFile(${fileId})"
                            class="absolute top-1 right-1 w-5 h-5 bg-[#15140F] text-white rounded-full grid place-items-center hover:bg-red-600 transition-colors">
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>`;
                document.getElementById('gallery_preview').appendChild(div);
            };
            reader.readAsDataURL(file);
        }

        function removeGalleryFile(fileId) {
            confirmDelete(() => {
                galleryFiles = galleryFiles.filter(f => f.id !== fileId);
                document.getElementById(`gallery_preview_${fileId}`)?.remove();
                updateGalleryFilesInput();
            }, { title: 'Remove Image?', text: 'Remove from upload queue.', confirmText: 'Yes, remove!' });
        }

        function updateGalleryFilesInput() {
            const container = document.getElementById('gallery_files_container');
            if (!container) return;
            container.innerHTML = '';
            const dt = new DataTransfer();
            galleryFiles.forEach(({ file }) => dt.items.add(file));
            const input = document.createElement('input');
            input.type = 'file';
            input.name = 'gallery_images[]';
            input.multiple = true;
            input.files = dt.files;
            container.appendChild(input);
        }

        document.addEventListener('DOMContentLoaded', () => {
            toggleAmountFields();
            toggleEndDate();
        });
    </script>
</x-layouts.app>