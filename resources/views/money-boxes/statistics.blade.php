<x-layouts.app>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center space-x-4 mb-4">
                    <a href="{{ route('money-boxes.show', $moneyBox) }}" class="text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $moneyBox->title }}</h1>
                        <p class="mt-1 text-gray-600">Statistics & Analytics</p>
                    </div>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-600 mb-2">Total Raised</div>
                    <div class="text-3xl font-bold text-gray-900">
                        {{ $moneyBox->formatAmount($stats['total_amount']) }}
                    </div>
                    @if($moneyBox->goal_amount)
                        <div class="text-sm text-gray-500 mt-1">
                            of {{ $moneyBox->formatAmount($moneyBox->goal_amount) }} goal
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-600 mb-2">Total Contributors</div>
                    <div class="text-3xl font-bold text-gray-900">
                        {{ $stats['total_count'] }}
                    </div>
                    <div class="text-sm text-gray-500 mt-1">
                        {{ Str::plural('person', $stats['total_count']) }} contributed
                    </div>
                </div>

                @if($moneyBox->goal_amount)
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm font-medium text-gray-600 mb-2">Progress</div>
                        <div class="text-3xl font-bold text-gray-900">
                            {{ number_format($stats['progress_percentage'], 1) }}%
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                            <div
                                class="bg-primary-600 h-2 rounded-full transition-all"
                                style="width: {{ min(100, $stats['progress_percentage']) }}%"
                            ></div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm font-medium text-gray-600 mb-2">Remaining</div>
                        <div class="text-3xl font-bold text-gray-900">
                            {{ $moneyBox->formatAmount(max(0, $moneyBox->goal_amount - $stats['total_amount'])) }}
                        </div>
                        <div class="text-sm text-gray-500 mt-1">
                            to reach goal
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm font-medium text-gray-600 mb-2">Average Contribution</div>
                        <div class="text-3xl font-bold text-gray-900">
                            {{ $stats['total_count'] > 0 ? $moneyBox->formatAmount($stats['total_amount'] / $stats['total_count']) : $moneyBox->formatAmount(0) }}
                        </div>
                        <div class="text-sm text-gray-500 mt-1">
                            per contributor
                        </div>
                    </div>
                @endif
            </div>

            <!-- All Contributions -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">All Contributions</h2>
                </div>
                <div class="p-6">
                    @if($stats['recent_contributions']->count() > 0)
                        <div class="space-y-4">
                            @foreach($stats['recent_contributions'] as $contribution)
                                <div class="flex items-start space-x-4 pb-4 border-b border-gray-200 last:border-0">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center">
                                            <span class="text-primary-600 font-semibold text-lg">
                                                {{ substr($contribution->getDisplayName(), 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $contribution->getDisplayName() }}
                                                </p>
                                                @if($contribution->contributor_email && !$contribution->is_anonymous)
                                                    <p class="text-sm text-gray-500">{{ $contribution->contributor_email }}</p>
                                                @endif
                                            </div>
                                            <div class="text-right">
                                                <span class="text-lg font-semibold text-gray-900">
                                                    {{ $moneyBox->formatAmount($contribution->amount) }}
                                                </span>
                                            </div>
                                        </div>
                                        @if($contribution->message)
                                            <p class="text-sm text-gray-600 mt-2 italic">"{{ $contribution->message }}"</p>
                                        @endif
                                        <div class="flex items-center space-x-3 mt-2">
                                            <p class="text-xs text-gray-500">
                                                {{ $contribution->created_at->format('M d, Y g:i A') }}
                                            </p>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $contribution->payment_status->value === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($contribution->payment_status->value) }}
                                            </span>
                                            @if($contribution->payment_provider)
                                                <span class="text-xs text-gray-500">
                                                    via {{ ucfirst($contribution->payment_provider) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No contributions yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Share your money box to start receiving contributions.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
