<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-zinc-900 py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create Piggy Box</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-300">Set up a new piggy box to collect contributions</p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('money-boxes.store') }}" class="space-y-6">
                @csrf

                <!-- Basic Information -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6 space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h2>

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
                            value="{{ old('title') }}"
                            placeholder="e.g., Birthday Gift for Mom"
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
                            placeholder="Tell people about this piggy box..."
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Category
                        </label>
                        <select name="category_id" id="category_id">
                            <option value="">Select a category (optional)</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6 space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Contribution Settings</h2>

                    <!-- Amount Type -->
                    <div>
                        <label for="amount_type" class="block text-sm font-medium text-gray-700 mb-1">
                            Amount Type *
                        </label>
                        <select
                            name="amount_type"
                            id="amount_type"
                            required
                            onchange="toggleAmountFields()"
                        >
                            <option value="variable" {{ old('amount_type') == 'variable' ? 'selected' : '' }}>Variable - Contributors choose amount</option>
                            <option value="fixed" {{ old('amount_type') == 'fixed' ? 'selected' : '' }}>Fixed - Specific amount only</option>
                            <option value="minimum" {{ old('amount_type') == 'minimum' ? 'selected' : '' }}>Minimum - At least a certain amount</option>
                            <option value="maximum" {{ old('amount_type') == 'maximum' ? 'selected' : '' }}>Maximum - Up to a certain amount</option>
                            <option value="range" {{ old('amount_type') == 'range' ? 'selected' : '' }}>Range - Between min and max</option>
                        </select>
                        @error('amount_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Amount Fields -->
                    <div id="fixed_amount_field" class="hidden">
                        <label for="fixed_amount" class="block text-sm font-medium text-gray-700 mb-1">
                            Fixed Amount ({{ auth()->user()->country?->currency_symbol ?? '₵' }})
                        </label>
                        <input
                            type="number"
                            name="fixed_amount"
                            id="fixed_amount"
                            step="0.01"
                            min="0"
                            value="{{ old('fixed_amount') }}"
                        />
                        @error('fixed_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="minimum_amount_field" class="hidden">
                        <label for="minimum_amount" class="block text-sm font-medium text-gray-700 mb-1">
                            Minimum Amount ({{ auth()->user()->country?->currency_symbol ?? '₵' }})
                        </label>
                        <input
                            type="number"
                            name="minimum_amount"
                            id="minimum_amount"
                            step="0.01"
                            min="0"
                            value="{{ old('minimum_amount') }}"
                        />
                        @error('minimum_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="maximum_amount_field" class="hidden">
                        <label for="maximum_amount" class="block text-sm font-medium text-gray-700 mb-1">
                            Maximum Amount ({{ auth()->user()->country?->currency_symbol ?? '₵' }})
                        </label>
                        <input
                            type="number"
                            name="maximum_amount"
                            id="maximum_amount"
                            step="0.01"
                            min="0"
                            value="{{ old('maximum_amount') }}"
                        />
                        @error('maximum_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Goal Amount -->
                    <div>
                        <label for="goal_amount" class="block text-sm font-medium text-gray-700 mb-1">
                            Goal Amount ({{ auth()->user()->country?->currency_symbol ?? '₵' }}) (Optional)
                        </label>
                        <input
                            type="number"
                            name="goal_amount"
                            id="goal_amount"
                            step="0.01"
                            min="0"
                            value="{{ old('goal_amount') }}"
                            placeholder="Set a fundraising goal"
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
                        >
                            <option value="user_choice" {{ old('contributor_identity') == 'user_choice' ? 'selected' : '' }}>Let contributors choose</option>
                            <option value="must_identify" {{ old('contributor_identity') == 'must_identify' ? 'selected' : '' }}>Must identify (no anonymous)</option>
                            <option value="anonymous_allowed" {{ old('contributor_identity') == 'anonymous_allowed' ? 'selected' : '' }}>Anonymous allowed</option>
                        </select>
                        @error('contributor_identity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Visibility & Timeline -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6 space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Visibility & Timeline</h2>

                    <!-- Visibility -->
                    <div>
                        <label for="visibility" class="block text-sm font-medium text-gray-700 mb-1">
                            Visibility *
                        </label>
                        <select
                            name="visibility"
                            id="visibility"
                            required
                        >
                            <option value="public" {{ old('visibility') == 'public' ? 'selected' : '' }}>Public - Listed on homepage</option>
                            <option value="private" {{ old('visibility') == 'private' ? 'selected' : '' }}>Private - Only accessible via link</option>
                        </select>
                        @error('visibility')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                            value="{{ old('start_date') }}"
                        />
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Leave empty to start immediately</p>
                    </div>

                    <!-- Is Ongoing Checkbox -->
                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            name="is_ongoing"
                            id="is_ongoing"
                            value="1"
                            {{ old('is_ongoing') ? 'checked' : '' }}
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
                            value="{{ old('end_date') }}"
                        />
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-4">
                    <a
                        href="{{ route('dashboard') }}"
                        class="px-6 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                    >
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-sm transition"
                    >
                        Create Piggy Box
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
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

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleAmountFields();
            toggleEndDate();
        });
    </script>
    @endpush
</x-layouts.app>
