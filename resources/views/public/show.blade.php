<x-layouts.app>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Money Box Details -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <!-- Category -->
                        @if($moneyBox->category)
                            <div class="mb-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-800">
                                    {{ $moneyBox->category->icon }} {{ $moneyBox->category->name }}
                                </span>
                            </div>
                        @endif

                        <!-- Title -->
                        <h1 class="text-3xl font-bold text-gray-900 mb-4">
                            {{ $moneyBox->title }}
                        </h1>

                        <!-- Creator -->
                        <div class="flex items-center mb-6 text-gray-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Created by {{ $moneyBox->user->name }}
                        </div>

                        <!-- Progress -->
                        @if($moneyBox->goal_amount)
                            <div class="mb-6">
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700">Progress</span>
                                    <span class="text-sm font-medium text-gray-900">{{ number_format($moneyBox->getProgressPercentage(), 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div
                                        class="bg-primary-600 h-3 rounded-full transition-all"
                                        style="width: {{ min(100, $moneyBox->getProgressPercentage()) }}%"
                                    ></div>
                                </div>
                            </div>
                        @endif

                        <!-- Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
                            <div>
                                <div class="text-3xl font-bold text-gray-900">
                                    {{ $moneyBox->formatAmount($moneyBox->total_contributions) }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    @if($moneyBox->goal_amount)
                                        of {{ $moneyBox->formatAmount($moneyBox->goal_amount) }} goal
                                    @else
                                        raised
                                    @endif
                                </div>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-gray-900">
                                    {{ $moneyBox->contribution_count }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ Str::plural('contribution', $moneyBox->contribution_count) }}
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        @if($moneyBox->description)
                            <div class="prose max-w-none">
                                <h2 class="text-xl font-semibold text-gray-900 mb-3">About</h2>
                                <p class="text-gray-700 whitespace-pre-line">{{ $moneyBox->description }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Recent Contributions -->
                    @if($moneyBox->contributions->count() > 0)
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Contributions</h2>
                            <div class="space-y-4">
                                @foreach($moneyBox->contributions as $contribution)
                                    <div class="flex items-start space-x-3 pb-4 border-b border-gray-200 last:border-0">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
                                                <span class="text-primary-600 font-semibold">
                                                    {{ substr($contribution->getDisplayName(), 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $contribution->getDisplayName() }}
                                            </p>
                                            @if($contribution->message)
                                                <p class="text-sm text-gray-600 mt-1">{{ $contribution->message }}</p>
                                            @endif
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $contribution->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <div class="text-sm font-semibold text-gray-900">
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
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                        @if($moneyBox->canAcceptContributions())
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Make a Contribution</h2>

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
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                        value="{{ old('contributor_name') }}"
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
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                        value="{{ old('contributor_email') }}"
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
                                    class="w-full py-3 px-4 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition"
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
                                <p class="mt-1 text-sm text-gray-500">This money box is currently not accepting contributions.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
