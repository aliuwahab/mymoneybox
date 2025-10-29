<x-layouts.app>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">My Dashboard</h1>
                        <p class="mt-2 text-gray-600">Manage your money boxes</p>
                    </div>
                    <a
                        href="{{ route('money-boxes.create') }}"
                        class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition"
                    >
                        Create Money Box
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-primary-100 rounded-lg">
                            <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Money Boxes</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $moneyBoxes->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-secondary-100 rounded-lg">
                            <svg class="h-6 w-6 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Raised</p>
                            <p class="text-2xl font-semibold text-gray-900">
                                {{ auth()->user()->country?->currency_symbol ?? '₵' }}{{ number_format($moneyBoxes->sum('total_contributions'), 2) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-primary-100 rounded-lg">
                            <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Contributors</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $moneyBoxes->sum('contribution_count') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Money Boxes List -->
            @if($moneyBoxes->count() > 0)
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Your Money Boxes</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($moneyBoxes as $moneyBox)
                            <div class="p-6 hover:bg-gray-50 transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                {{ $moneyBox->title }}
                                            </h3>
                                            @if($moneyBox->visibility->value === 'public')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                                    Public
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    Private
                                                </span>
                                            @endif
                                        </div>

                                        <div class="flex items-center space-x-6 text-sm text-gray-600">
                                            <div>
                                                <span class="font-semibold text-gray-900">{{ $moneyBox->formatAmount($moneyBox->total_contributions) }}</span>
                                                @if($moneyBox->goal_amount)
                                                    / {{ $moneyBox->formatAmount($moneyBox->goal_amount) }}
                                                @endif
                                            </div>
                                            <div>
                                                {{ $moneyBox->contribution_count }} {{ Str::plural('contribution', $moneyBox->contribution_count) }}
                                            </div>
                                        </div>

                                        @if($moneyBox->goal_amount)
                                            <div class="mt-3">
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div
                                                        class="bg-primary-600 h-2 rounded-full transition-all"
                                                        style="width: {{ min(100, $moneyBox->getProgressPercentage()) }}%"
                                                    ></div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="ml-6 flex items-center space-x-2">
                                        <a
                                            href="{{ route('money-boxes.show', $moneyBox) }}"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                                        >
                                            View
                                        </a>
                                        <a
                                            href="{{ route('money-boxes.statistics', $moneyBox) }}"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                                        >
                                            Stats
                                        </a>
                                        <a
                                            href="{{ route('money-boxes.edit', $moneyBox) }}"
                                            class="px-4 py-2 text-sm font-medium text-primary-700 bg-primary-50 border border-primary-300 rounded-lg hover:bg-primary-100 transition"
                                        >
                                            Edit
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No money boxes</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating your first money box.</p>
                    <div class="mt-6">
                        <a
                            href="{{ route('money-boxes.create') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                        >
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Create Money Box
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
