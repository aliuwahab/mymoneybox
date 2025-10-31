<x-layouts.app>
    <div class="min-h-screen bg-gray-50 py-8" x-data="{ showToast: false, toastMessage: '' }">
        <!-- Toast Notification -->
        <div
            x-show="showToast"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed top-4 right-4 z-50 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2"
            style="display: none;"
        >
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span x-text="toastMessage"></span>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header with Actions -->
            <div class="bg-white rounded-lg shadow mb-6 p-4 sm:p-6">
                <div class="flex flex-col space-y-4">
                    <div>
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-3">{{ $moneyBox->title }}</h1>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs sm:text-sm font-medium {{ $moneyBox->visibility->value === 'public' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $moneyBox->visibility->value === 'public' ? 'Public' : 'Private' }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs sm:text-sm font-medium {{ $moneyBox->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $moneyBox->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            @if($moneyBox->category)
                                <span class="text-xs sm:text-sm text-gray-600">
                                    {{ $moneyBox->category->icon }} {{ $moneyBox->category->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                        <a
                            href="{{ route('box.show', $moneyBox->slug) }}"
                            target="_blank"
                            class="flex-1 sm:flex-none text-center px-4 py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                        >
                            View Public Page
                        </a>
                        <a
                            href="{{ route('money-boxes.share', $moneyBox) }}"
                            class="flex-1 sm:flex-none text-center px-4 py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                        >
                            Share
                        </a>
                        <a
                            href="{{ route('money-boxes.edit', $moneyBox) }}"
                            class="flex-1 sm:flex-none text-center px-4 py-2 text-xs sm:text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition"
                        >
                            Edit
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Stats Overview -->
                <div class="lg:col-span-3">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="text-sm font-medium text-gray-600 mb-1">Total Raised</div>
                            <div class="text-3xl font-bold text-gray-900">
                                {{ $moneyBox->formatAmount($moneyBox->total_contributions) }}
                            </div>
                            @if($moneyBox->goal_amount)
                                <div class="text-sm text-gray-500 mt-1">
                                    of {{ $moneyBox->formatAmount($moneyBox->goal_amount) }}
                                </div>
                            @endif
                        </div>

                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="text-sm font-medium text-gray-600 mb-1">Contributors</div>
                            <div class="text-3xl font-bold text-gray-900">
                                {{ $moneyBox->contribution_count }}
                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                                {{ Str::plural('person', $moneyBox->contribution_count) }}
                            </div>
                        </div>

                        @if($moneyBox->goal_amount)
                            <div class="bg-white rounded-lg shadow p-6">
                                <div class="text-sm font-medium text-gray-600 mb-1">Progress</div>
                                <div class="text-3xl font-bold text-gray-900">
                                    {{ number_format($moneyBox->getProgressPercentage(), 1) }}%
                                </div>
                                <div class="text-sm text-gray-500 mt-1">
                                    of goal
                                </div>
                            </div>
                        @endif

                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="text-sm font-medium text-gray-600 mb-1">Status</div>
                            <div class="text-xl font-bold {{ $moneyBox->canAcceptContributions() ? 'text-green-600' : 'text-red-600' }}">
                                {{ $moneyBox->canAcceptContributions() ? 'Accepting' : 'Not Accepting' }}
                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                                contributions
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Image & Gallery -->
                @if($moneyBox->hasMedia('main_image') || $moneyBox->hasMedia('gallery'))
                    <div class="lg:col-span-3">
                        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Images</h2>
                                <a href="{{ route('money-boxes.edit', $moneyBox) }}" 
                                   class="text-sm text-green-600 hover:text-green-700">
                                    Manage Images
                                </a>
                            </div>
                            
                            <div class="space-y-4">
                                @if($moneyBox->hasMedia('main'))
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Main Image</h3>
                                        <img src="{{ $moneyBox->getMainImageUrl() }}" 
                                             alt="Main image"
                                             class="w-full max-h-64 object-cover rounded-lg">
                                    </div>
                                @endif
                                
                                @if($moneyBox->hasMedia('gallery'))
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Gallery ({{ $moneyBox->getMedia('gallery')->count() }} images)</h3>
                                        <div class="grid grid-cols-3 gap-2">
                                            @foreach($moneyBox->getMedia('gallery') as $image)
                                                <img src="{{ $image->getTemporaryUrl(now()->addHours(24)) }}" 
                                                     alt="Gallery image"
                                                     class="w-full h-24 object-cover rounded">
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Recent Contributions -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <h2 class="text-lg font-semibold text-gray-900">Recent Contributions</h2>
                            <a
                                href="{{ route('money-boxes.statistics', $moneyBox) }}"
                                class="text-sm text-green-600 hover:text-green-700"
                            >
                                View All
                            </a>
                        </div>
                        <div class="p-6">
                            @if($moneyBox->contributions->count() > 0)
                                <div class="space-y-4">
                                    @foreach($moneyBox->contributions as $contribution)
                                        <div class="flex items-start space-x-3 pb-4 border-b border-gray-200 last:border-0">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                                    <span class="text-green-700 font-semibold">
                                                        {{ substr($contribution->getDisplayName(), 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $contribution->getDisplayName() }}
                                                    </p>
                                                    <span class="text-sm font-semibold text-gray-900">
                                                        {{ $moneyBox->formatAmount($contribution->amount) }}
                                                    </span>
                                                </div>
                                                @if($contribution->message)
                                                    <p class="text-sm text-gray-600 mt-1">{{ $contribution->message }}</p>
                                                @endif
                                                <div class="flex items-center space-x-2 mt-1">
                                                    <p class="text-xs text-gray-500">
                                                        {{ $contribution->created_at->diffForHumans() }}
                                                    </p>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $contribution->payment_status->value === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                        {{ ucfirst($contribution->payment_status->value) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No contributions yet</h3>
                                    <p class="mt-1 text-sm text-gray-500">Share your piggy box to start receiving contributions.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Piggy Box Info Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Quick Share -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Quick Share</h3>
                        <div class="space-y-2">
                            <button
                                type="button"
                                @click="navigator.clipboard.writeText('{{ route('box.show', $moneyBox->slug) }}').then(() => { toastMessage = 'Link copied!'; showToast = true; setTimeout(() => showToast = false, 3000); })"
                                class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition cursor-pointer"
                            >
                                Copy Link
                            </button>
                            <a
                                href="{{ route('money-boxes.share', $moneyBox) }}"
                                class="block w-full px-4 py-2 text-center text-sm font-medium text-green-700 bg-green-50 rounded-lg hover:bg-green-100 transition"
                            >
                                Share Options
                            </a>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Details</h3>
                        <dl class="space-y-3 text-sm">
                            <div>
                                <dt class="text-gray-600">Created</dt>
                                <dd class="text-gray-900 font-medium">{{ $moneyBox->created_at->format('M d, Y') }}</dd>
                            </div>
                            @if($moneyBox->start_date)
                                <div>
                                    <dt class="text-gray-600">Start Date</dt>
                                    <dd class="text-gray-900 font-medium">{{ $moneyBox->start_date->format('M d, Y') }}</dd>
                                </div>
                            @endif
                            @if($moneyBox->end_date)
                                <div>
                                    <dt class="text-gray-600">End Date</dt>
                                    <dd class="text-gray-900 font-medium">{{ $moneyBox->end_date->format('M d, Y') }}</dd>
                                </div>
                            @elseif($moneyBox->is_ongoing)
                                <div>
                                    <dt class="text-gray-600">Duration</dt>
                                    <dd class="text-gray-900 font-medium">Ongoing</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-gray-600">Amount Type</dt>
                                <dd class="text-gray-900 font-medium">{{ ucfirst($moneyBox->amount_type->value) }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-600">Currency</dt>
                                <dd class="text-gray-900 font-medium">{{ $moneyBox->currency_code }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-layouts.app>
