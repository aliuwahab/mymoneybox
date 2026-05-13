<x-layouts.app>
    <div class="page-wrap max-w-[720px] mx-auto w-full">

        {{-- Header --}}
        <div class="mb-6">
            <div class="mb-4">
                <a href="{{ route('dashboard') }}" wire:navigate class="btn btn-ghost btn-sm text-[#6B6862]">
                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    Dashboard
                </a>
            </div>
            <h1 class="page-title" style="font-size:1.875rem;">Create a piggy box</h1>
            <p class="tiny mt-1.5">Set up a new piggy box to collect contributions from anyone.</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-[8px] px-4 py-3 mb-5 text-[13px] text-red-700">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('money-boxes.store') }}" class="space-y-4">
            @csrf

            {{-- Basic Information --}}
            <div class="card">
                <div class="card-head"><h2 class="card-title">Basic information</h2></div>
                <div class="card-body space-y-4">
                    <div class="grid gap-1.5">
                        <label class="text-[13px] font-medium" for="title">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" required value="{{ old('title') }}" placeholder="e.g., Birthday Gift for Mom">
                        @error('title')<p class="text-[11.5px] text-red-600 mt-0.5">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid gap-1.5">
                        <label class="text-[13px] font-medium" for="description">Description</label>
                        <textarea name="description" id="description" rows="4" placeholder="Tell people about this box…">{{ old('description') }}</textarea>
                        @error('description')<p class="text-[11.5px] text-red-600 mt-0.5">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid gap-1.5">
                        <label class="text-[13px] font-medium" for="category_id">Category</label>
                        <select name="category_id" id="category_id">
                            <option value="">Select a category (optional)</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->icon }} {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="text-[11.5px] text-red-600 mt-0.5">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Contribution Settings --}}
            <div class="card">
                <div class="card-head"><h2 class="card-title">Contribution settings</h2></div>
                <div class="card-body space-y-4">
                    <div class="grid gap-1.5">
                        <label class="text-[13px] font-medium" for="amount_type">Amount type <span class="text-red-500">*</span></label>
                        <select name="amount_type" id="amount_type" required onchange="toggleAmountFields()">
                            <option value="variable" {{ old('amount_type') == 'variable' ? 'selected' : '' }}>Variable — contributors choose amount</option>
                            <option value="fixed"    {{ old('amount_type') == 'fixed'    ? 'selected' : '' }}>Fixed — specific amount only</option>
                            <option value="minimum"  {{ old('amount_type') == 'minimum'  ? 'selected' : '' }}>Minimum — at least a certain amount</option>
                            <option value="maximum"  {{ old('amount_type') == 'maximum'  ? 'selected' : '' }}>Maximum — up to a certain amount</option>
                            <option value="range"    {{ old('amount_type') == 'range'    ? 'selected' : '' }}>Range — between min and max</option>
                        </select>
                        @error('amount_type')<p class="text-[11.5px] text-red-600 mt-0.5">{{ $message }}</p>@enderror
                    </div>

                    <div id="fixed_amount_field" class="hidden grid gap-1.5">
                        <label class="text-[13px] font-medium" for="fixed_amount">Fixed amount ({{ auth()->user()->country?->currency_symbol ?? '₵' }})</label>
                        <input type="number" name="fixed_amount" id="fixed_amount" step="0.01" min="0" value="{{ old('fixed_amount') }}" placeholder="0.00">
                        @error('fixed_amount')<p class="text-[11.5px] text-red-600 mt-0.5">{{ $message }}</p>@enderror
                    </div>
                    <div id="minimum_amount_field" class="hidden grid gap-1.5">
                        <label class="text-[13px] font-medium" for="minimum_amount">Minimum amount ({{ auth()->user()->country?->currency_symbol ?? '₵' }})</label>
                        <input type="number" name="minimum_amount" id="minimum_amount" step="0.01" min="0" value="{{ old('minimum_amount') }}" placeholder="0.00">
                        @error('minimum_amount')<p class="text-[11.5px] text-red-600 mt-0.5">{{ $message }}</p>@enderror
                    </div>
                    <div id="maximum_amount_field" class="hidden grid gap-1.5">
                        <label class="text-[13px] font-medium" for="maximum_amount">Maximum amount ({{ auth()->user()->country?->currency_symbol ?? '₵' }})</label>
                        <input type="number" name="maximum_amount" id="maximum_amount" step="0.01" min="0" value="{{ old('maximum_amount') }}" placeholder="0.00">
                        @error('maximum_amount')<p class="text-[11.5px] text-red-600 mt-0.5">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid gap-1.5">
                        <label class="text-[13px] font-medium" for="goal_amount">Goal amount ({{ auth()->user()->country?->currency_symbol ?? '₵' }}) <span class="text-[#9C998F] font-normal">(optional)</span></label>
                        <input type="number" name="goal_amount" id="goal_amount" step="0.01" min="0" value="{{ old('goal_amount') }}" placeholder="Set a fundraising goal">
                        @error('goal_amount')<p class="text-[11.5px] text-red-600 mt-0.5">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid gap-1.5">
                        <label class="text-[13px] font-medium" for="contributor_identity">Contributor identity <span class="text-red-500">*</span></label>
                        <select name="contributor_identity" id="contributor_identity" required>
                            <option value="user_choice"         {{ old('contributor_identity') == 'user_choice'         ? 'selected' : '' }}>Let contributors choose</option>
                            <option value="must_identify"       {{ old('contributor_identity') == 'must_identify'       ? 'selected' : '' }}>Must identify (no anonymous)</option>
                            <option value="anonymous_allowed"   {{ old('contributor_identity') == 'anonymous_allowed'   ? 'selected' : '' }}>Anonymous allowed</option>
                        </select>
                        @error('contributor_identity')<p class="text-[11.5px] text-red-600 mt-0.5">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Visibility & Timeline --}}
            <div class="card">
                <div class="card-head"><h2 class="card-title">Visibility &amp; timeline</h2></div>
                <div class="card-body space-y-4">
                    <div class="grid gap-1.5">
                        <label class="text-[13px] font-medium" for="visibility">Visibility <span class="text-red-500">*</span></label>
                        <select name="visibility" id="visibility" required>
                            <option value="public"  {{ old('visibility') == 'public'  ? 'selected' : '' }}>Public — listed on browse page</option>
                            <option value="private" {{ old('visibility') == 'private' ? 'selected' : '' }}>Private — accessible via link only</option>
                        </select>
                        @error('visibility')<p class="text-[11.5px] text-red-600 mt-0.5">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid gap-1.5">
                        <label class="text-[13px] font-medium" for="start_date">Start date <span class="text-[#9C998F] font-normal">(optional)</span></label>
                        <input type="datetime-local" name="start_date" id="start_date" value="{{ old('start_date') }}">
                        <p class="text-[11.5px] text-[#9C998F]">Leave empty to start immediately</p>
                        @error('start_date')<p class="text-[11.5px] text-red-600 mt-0.5">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_ongoing" id="is_ongoing" value="1" {{ old('is_ongoing') ? 'checked' : '' }} onchange="toggleEndDate()">
                        <label for="is_ongoing" class="text-[13px]">No end date (ongoing)</label>
                    </div>

                    <div id="end_date_field" class="grid gap-1.5">
                        <label class="text-[13px] font-medium" for="end_date">End date</label>
                        <input type="datetime-local" name="end_date" id="end_date" value="{{ old('end_date') }}">
                        @error('end_date')<p class="text-[11.5px] text-red-600 mt-0.5">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-1">
                <a href="{{ route('dashboard') }}" wire:navigate class="btn">Cancel</a>
                <button type="submit" class="btn-primary">
                    Create piggy box
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function toggleAmountFields() {
            const t = document.getElementById('amount_type').value;
            document.getElementById('fixed_amount_field').classList.toggle('hidden', t !== 'fixed');
            document.getElementById('minimum_amount_field').classList.toggle('hidden', t !== 'minimum' && t !== 'range');
            document.getElementById('maximum_amount_field').classList.toggle('hidden', t !== 'maximum' && t !== 'range');
        }
        function toggleEndDate() {
            const ongoing = document.getElementById('is_ongoing').checked;
            document.getElementById('end_date_field').classList.toggle('hidden', ongoing);
            if (ongoing) document.getElementById('end_date').value = '';
        }
        document.addEventListener('DOMContentLoaded', () => { toggleAmountFields(); toggleEndDate(); });
    </script>
    @endpush
</x-layouts.app>