<x-layouts.app>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">My Dashboard</h1>
                        <p class="mt-1 sm:mt-2 text-sm sm:text-base text-gray-600">Manage your piggy boxes</p>
                    </div>
                    <a
                        href="{{ route('money-boxes.create') }}"
                        class="mat-button mat-button-primary mat-ripple whitespace-nowrap text-sm sm:text-base"
                    >
                        Create Piggy Box
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="mat-stat-card smooth-transition">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-primary-100 rounded-lg">
                            <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Piggy Boxes</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $moneyBoxes->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="mat-stat-card smooth-transition">
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

                <div class="mat-stat-card smooth-transition">
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

            <!-- Piggy Boxes Grid -->
            @if($moneyBoxes->count() > 0)
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Your Piggy Boxes</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($moneyBoxes as $moneyBox)
                        <div class="mat-card elevation-hover smooth-transition overflow-hidden">
                            <!-- Card Header -->
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 line-clamp-2 flex-1">
                                        {{ $moneyBox->title }}
                                    </h3>
                                    @if($moneyBox->visibility->value === 'public')
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Public
                                        </span>
                                    @else
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Private
                                        </span>
                                    @endif
                                </div>

                                <!-- Stats -->
                                <div class="space-y-3">
                                    <div class="flex items-baseline justify-between">
                                        <span class="text-2xl font-bold text-gray-900">{{ $moneyBox->formatAmount($moneyBox->total_contributions) }}</span>
                                        @if($moneyBox->goal_amount)
                                            <span class="text-sm text-gray-600">of {{ $moneyBox->formatAmount($moneyBox->goal_amount) }}</span>
                                        @endif
                                    </div>

                                    @if($moneyBox->goal_amount)
                                        <div>
                                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                <div
                                                    class="bg-blue-600 h-2.5 rounded-full transition-all"
                                                    style="width: {{ min(100, $moneyBox->getProgressPercentage()) }}%"
                                                ></div>
                                            </div>
                                            <p class="text-xs text-gray-600 mt-1">{{ round($moneyBox->getProgressPercentage()) }}% funded</p>
                                        </div>
                                    @endif

                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        {{ $moneyBox->contribution_count }} {{ Str::plural('contribution', $moneyBox->contribution_count) }}
                                    </div>
                                </div>
                            </div>

                            <!-- Card Actions -->
                            <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                                <div class="flex items-center justify-between space-x-2">
                                    <a
                                        href="{{ route('money-boxes.show', $moneyBox) }}"
                                        class="flex-1 text-center px-3 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition"
                                    >
                                        View
                                    </a>
                                    <a
                                        href="{{ route('money-boxes.statistics', $moneyBox) }}"
                                        class="flex-1 text-center px-3 py-2 text-sm font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100 transition"
                                    >
                                        Stats
                                    </a>
                                    <a
                                        href="{{ route('money-boxes.edit', $moneyBox) }}"
                                        class="flex-1 text-center px-3 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg smooth-transition elevation-1"
                                    >
                                        Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No piggy boxes</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating your first piggy box.</p>
                    <div class="mt-6">
                        <a
                            href="{{ route('money-boxes.create') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                        >
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Create Piggy Box
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
