<x-layouts.guest>
    <div class="min-h-screen bg-gray-50 py-4 sm:py-8" x-data="{ showToast: false, toastMessage: '' }">
        <!-- Toast Notification -->
        <div
            x-show="showToast"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed top-4 left-4 right-4 sm:left-auto sm:right-4 z-50 bg-green-600 text-white px-4 sm:px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2"
            style="display: none;"
        >
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span x-text="toastMessage" class="text-sm sm:text-base"></span>
        </div>
        <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-4 sm:space-y-6">
                    <!-- Main Image -->
                    @if($moneyBox->hasMedia('main'))
                        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md overflow-hidden">
                            <img src="{{ $moneyBox->getMainImageUrl() }}" 
                                 alt="{{ $moneyBox->title }}"
                                 class="w-full h-64 sm:h-96 object-cover">
                        </div>
                    @endif

                    <!-- Piggy Box Details -->
                    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-4 sm:p-6">
                        <!-- Category -->
                        @if($moneyBox->category)
                            <div class="mb-3 sm:mb-4">
                                <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium bg-green-100 text-green-800">
                                    <span class="mr-1">{{ $moneyBox->category->icon }}</span>
                                    <span>{{ $moneyBox->category->name }}</span>
                                </span>
                            </div>
                        @endif

                        <!-- Title -->
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3 sm:mb-4 leading-tight">
                            {{ $moneyBox->title }}
                        </h1>

                        <!-- Creator -->
                        <div class="flex items-center mb-4 sm:mb-6 text-gray-600 text-sm sm:text-base">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="truncate">Created by {{ $moneyBox->user->name }}</span>
                        </div>

                        <!-- Progress -->
                        @if($moneyBox->goal_amount)
                            <div class="mb-4 sm:mb-6">
                                <div class="flex justify-between mb-2">
                                    <span class="text-xs sm:text-sm font-medium text-gray-700">Progress</span>
                                    <span class="text-xs sm:text-sm font-medium text-gray-900">{{ number_format($moneyBox->getProgressPercentage(), 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5 sm:h-3">
                                    <div
                                        class="bg-green-600 h-2.5 sm:h-3 rounded-full transition-all"
                                        style="width: {{ min(100, $moneyBox->getProgressPercentage()) }}%"
                                    ></div>
                                </div>
                            </div>
                        @endif

                        <!-- Stats -->
                        <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-4 sm:mb-6 p-3 sm:p-4 bg-gray-50 rounded-lg">
                            <div>
                                <div class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 break-words">
                                    {{ $moneyBox->formatAmount($moneyBox->total_contributions) }}
                                </div>
                                <div class="text-xs sm:text-sm text-gray-600 mt-1">
                                    @if($moneyBox->goal_amount)
                                        <span class="hidden sm:inline">of {{ $moneyBox->formatAmount($moneyBox->goal_amount) }} goal</span>
                                        <span class="sm:hidden">of {{ $moneyBox->formatAmount($moneyBox->goal_amount) }}</span>
                                    @else
                                        raised
                                    @endif
                                </div>
                            </div>
                            <div>
                                <div class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900">
                                    {{ $moneyBox->contribution_count }}
                                </div>
                                <div class="text-xs sm:text-sm text-gray-600 mt-1">
                                    {{ Str::plural('contribution', $moneyBox->contribution_count) }}
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        @if($moneyBox->description)
                            <div class="prose prose-sm sm:prose max-w-none">
                                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white mb-2 sm:mb-3">About</h2>
                                <p class="text-sm sm:text-base text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed">{{ $moneyBox->description }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Gallery -->
                    @if($moneyBox->hasMedia('gallery'))
                        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-4 sm:p-6">
                            <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4">Gallery</h2>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 sm:gap-4">
                                @foreach($moneyBox->getMedia('gallery') as $image)
                                    <a href="{{ $image->getTemporaryUrl(now()->addHour()) }}" target="_blank" class="group">
                                        <img src="{{ $image->getTemporaryUrl(now()->addHour()) }}" 
                                             alt="Gallery image"
                                             class="w-full h-32 sm:h-48 object-cover rounded-lg transition-transform group-hover:scale-105">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Recent Contributions -->
                    @if($moneyBox->contributions->count() > 0)
                        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
                            <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-3 sm:mb-4">Recent Contributions</h2>
                            <div class="space-y-3 sm:space-y-4">
                                @foreach($moneyBox->contributions as $contribution)
                                    <div class="flex items-start space-x-2 sm:space-x-3 pb-3 sm:pb-4 border-b border-gray-200 last:border-0">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-green-100 flex items-center justify-center">
                                                <span class="text-sm sm:text-base text-green-700 font-semibold">
                                                    {{ substr($contribution->getDisplayName(), 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs sm:text-sm font-medium text-gray-900 truncate">
                                                {{ $contribution->getDisplayName() }}
                                            </p>
                                            @if($contribution->message)
                                                <p class="text-xs sm:text-sm text-gray-600 mt-1 line-clamp-2">{{ $contribution->message }}</p>
                                            @endif
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $contribution->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <div class="text-xs sm:text-sm font-semibold text-gray-900 flex-shrink-0">
                                            {{ $moneyBox->formatAmount($contribution->amount) }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar - Contribution Form -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 lg:sticky lg:top-4">
                        @if($moneyBox->canAcceptContributions())
                            <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-3 sm:mb-4">Make a Contribution</h2>

                            <form method="POST" action="{{ route('box.contribute', $moneyBox->slug) }}" class="space-y-4">
                                @csrf

                                <!-- Amount -->
                                <div>
                                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                                        Amount ({{ $moneyBox->getCurrencySymbol() }})
                                    </label>
                                    <input
                                        type="number"
                                        name="amount"
                                        id="amount"
                                        step="0.01"
                                        min="0.01"
                                        required
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                        value="{{ old('amount') }}"
                                    />
                                    @error('amount')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Name -->
                                <div>
                                    <label for="contributor_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Your Name
                                    </label>
                                    <input
                                        type="text"
                                        name="contributor_name"
                                        id="contributor_name"
                                        value="{{ old('contributor_name', auth()->user()->name ?? '') }}"
                                    />
                                    @error('contributor_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="contributor_email" class="block text-sm font-medium text-gray-700 mb-1">
                                        Email Address *
                                    </label>
                                    <input
                                        type="email"
                                        name="contributor_email"
                                        id="contributor_email"
                                        required
                                        value="{{ old('contributor_email', auth()->user()->email ?? '') }}"
                                    />
                                    @error('contributor_email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Message -->
                                <div>
                                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">
                                        Message (Optional)
                                    </label>
                                    <textarea
                                        name="message"
                                        id="message"
                                        rows="3"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                    >{{ old('message') }}</textarea>
                                    @error('message')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Anonymous -->
                                <div class="flex items-center">
                                    <input
                                        type="checkbox"
                                        name="is_anonymous"
                                        id="is_anonymous"
                                        value="1"
                                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                        {{ old('is_anonymous') ? 'checked' : '' }}
                                    />
                                    <label for="is_anonymous" class="ml-2 text-sm text-gray-700">
                                        Contribute anonymously
                                    </label>
                                </div>

                                <!-- Submit -->
                                <button
                                    type="submit"
                                    class="w-full py-3 px-4 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-sm transition"
                                >
                                    Proceed to Payment
                                </button>
                            </form>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Not Accepting Contributions</h3>
                                <p class="mt-1 text-sm text-gray-500">This piggy box is currently not accepting contributions.</p>
                            </div>
                        @endif

                        <!-- Share Section -->
                        <div class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2 sm:mb-3">Share this campaign</h3>
                            <div class="space-y-2 sm:space-y-3">
                                <!-- Copy Link -->
                                <button
                                    type="button"
                                    @click="navigator.clipboard.writeText('{{ $moneyBox->getPublicUrl() }}').then(() => { toastMessage = 'Link copied!'; showToast = true; setTimeout(() => showToast = false, 3000); })"
                                    class="w-full flex items-center justify-center space-x-2 px-3 sm:px-4 py-2 sm:py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition text-sm sm:text-base"
                                >
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>Copy Link</span>
                                </button>

                                <!-- QR Code Download -->
                                @if($moneyBox->qr_code_path)
                                    <a
                                        href="{{ asset('storage/' . $moneyBox->qr_code_path) }}"
                                        download
                                        class="w-full flex items-center justify-center space-x-2 px-3 sm:px-4 py-2 sm:py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition text-sm sm:text-base"
                                    >
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m0 0l-4-4m4 4l4-4"></path>
                                        </svg>
                                        <span>Download QR Code</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.guest>
