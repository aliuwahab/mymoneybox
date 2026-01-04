<x-layouts.app>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50">

        <!-- Modern Hero Header with Gradient -->
        <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600">
            <!-- Animated Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-6">
                    <div class="text-white">
                        <div class="flex items-center space-x-3 mb-2">
                            <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <h1 class="text-3xl sm:text-4xl font-bold">Dashboard</h1>
                        </div>
                        <p class="text-blue-100 text-base sm:text-lg">Welcome back, {{ auth()->user()->name }}! Track your campaigns and see how much you've raised.</p>
                    </div>
                    <a
                        href="{{ route('money-boxes.create') }}"
                        class="group relative inline-flex items-center justify-center px-6 py-3 sm:px-8 sm:py-4 text-base font-semibold text-indigo-600 bg-white rounded-xl hover:bg-gray-50 shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-200"
                    >
                        <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Money Box
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Section with Modern Cards -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 sm:-mt-12 relative z-10">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">

                <!-- Total Piggy Boxes Card -->
                <div class="group relative bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full -mr-16 -mt-16 opacity-10 group-hover:opacity-20 transition-opacity"></div>
                    <div class="relative p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">{{ $moneyBoxes->count() }}</p>
                            </div>
                        </div>
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Money Boxes</p>
                        <div class="mt-3 flex items-center text-sm text-green-600">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">Active</span>
                        </div>
                    </div>
                </div>

                <!-- Total Raised Card -->
                <div class="group relative bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-green-400 to-emerald-600 rounded-full -mr-16 -mt-16 opacity-10 group-hover:opacity-20 transition-opacity"></div>
                    <div class="relative p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                                    {{ auth()->user()->country?->currency_symbol ?? '₵' }}{{ number_format($moneyBoxes->sum('total_contributions'), 2) }}
                                </p>
                            </div>
                        </div>
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Raised</p>
                        <div class="mt-3 flex items-center text-sm text-green-600">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">All campaigns</span>
                        </div>
                    </div>
                </div>

                <!-- Total Contributors Card -->
                <div class="group relative bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden sm:col-span-2 lg:col-span-1">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-purple-400 to-pink-600 rounded-full -mr-16 -mt-16 opacity-10 group-hover:opacity-20 transition-opacity"></div>
                    <div class="relative p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">{{ $moneyBoxes->sum('contribution_count') }}</p>
                            </div>
                        </div>
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Contributors</p>
                        <div class="mt-3 flex items-center text-sm text-purple-600">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                            </svg>
                            <span class="font-medium">Supporters</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Piggy Boxes Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            @if($moneyBoxes->count() > 0)
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Your Money Boxes</h2>
                            <p class="mt-1 text-sm text-gray-600">Manage your campaigns and track contributions</p>
                        </div>
                        <div class="hidden sm:flex items-center space-x-2 text-sm text-gray-600">
                            <span class="font-medium">{{ $moneyBoxes->count() }}</span>
                            <span>{{ Str::plural('box', $moneyBoxes->count()) }}</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($moneyBoxes as $moneyBox)
                        <div class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden border border-gray-100">

                            <!-- Gradient Top Border -->
                            <div class="h-2 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500"></div>

                            <!-- Card Content -->
                            <div class="p-6">
                                <!-- Header with Badge -->
                                <div class="flex items-start justify-between mb-4">
                                    <h3 class="text-xl font-bold text-gray-900 line-clamp-2 flex-1 group-hover:text-blue-600 transition-colors">
                                        {{ $moneyBox->title }}
                                    </h3>
                                    @if($moneyBox->visibility->value === 'public')
                                        <span class="ml-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-md">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Public
                                        </span>
                                    @else
                                        <span class="ml-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-200 text-gray-700">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Private
                                        </span>
                                    @endif
                                </div>

                                <!-- Amount Display -->
                                <div class="space-y-4">
                                    <div class="flex items-baseline justify-between">
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Current Amount</p>
                                            <span class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                                                {{ $moneyBox->formatAmount($moneyBox->total_contributions) }}
                                            </span>
                                        </div>
                                        @if($moneyBox->goal_amount)
                                            <div class="text-right">
                                                <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Goal</p>
                                                <span class="text-lg font-semibold text-gray-700">{{ $moneyBox->formatAmount($moneyBox->goal_amount) }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Progress Bar -->
                                    @if($moneyBox->goal_amount)
                                        <div>
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-sm font-bold text-gray-900">{{ round($moneyBox->getProgressPercentage()) }}%</span>
                                                <span class="text-xs text-gray-500">Progress</span>
                                            </div>
                                            <div class="relative w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                                <div
                                                    class="absolute top-0 left-0 h-full bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 rounded-full transition-all duration-500 shadow-lg"
                                                    style="width: {{ min(100, $moneyBox->getProgressPercentage()) }}%"
                                                ></div>
                                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-30 animate-pulse"></div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Contributors Badge -->
                                    <div class="flex items-center space-x-4 pt-2">
                                        <div class="flex items-center text-sm text-gray-600 bg-gray-100 px-3 py-2 rounded-lg">
                                            <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <span class="font-semibold">{{ $moneyBox->contribution_count }}</span>
                                            <span class="ml-1">{{ Str::plural('contributor', $moneyBox->contribution_count) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Actions with Modern Buttons -->
                            <div class="border-t border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4">
                                <div class="flex items-center justify-between space-x-2">
                                    <a
                                        href="{{ route('money-boxes.show', $moneyBox) }}"
                                        class="flex-1 flex items-center justify-center px-4 py-2.5 text-sm font-bold text-blue-700 bg-blue-50 border-2 border-blue-200 rounded-xl hover:bg-blue-100 hover:border-blue-300 transition-all duration-200 group"
                                    >
                                        <svg class="w-4 h-4 mr-1.5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View
                                    </a>
                                    <a
                                        href="{{ route('money-boxes.statistics', $moneyBox) }}"
                                        class="flex-1 flex items-center justify-center px-4 py-2.5 text-sm font-bold text-amber-700 bg-amber-50 border-2 border-amber-200 rounded-xl hover:bg-amber-100 hover:border-amber-300 transition-all duration-200 group"
                                    >
                                        <svg class="w-4 h-4 mr-1.5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        Stats
                                    </a>
                                    <a
                                        href="{{ route('money-boxes.edit', $moneyBox) }}"
                                        class="flex-1 flex items-center justify-center px-4 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 rounded-xl shadow-md hover:shadow-xl transform hover:scale-105 transition-all duration-200 group"
                                    >
                                        <svg class="w-4 h-4 mr-1.5 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State with Modern Design -->
                <div class="relative bg-white rounded-3xl shadow-xl p-12 sm:p-16 text-center overflow-hidden">
                    <!-- Decorative Background -->
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 opacity-50"></div>
                    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-blue-400 to-purple-600 rounded-full -mr-32 -mt-32 opacity-10"></div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 bg-gradient-to-tr from-pink-400 to-purple-600 rounded-full -ml-32 -mb-32 opacity-10"></div>

                    <div class="relative">
                        <!-- Icon -->
                        <div class="mx-auto w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-3xl flex items-center justify-center mb-6 shadow-2xl transform hover:scale-110 transition-transform duration-300">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>

                        <!-- Content -->
                        <h3 class="text-3xl font-bold text-gray-900 mb-3">No Money Boxes Yet</h3>
                        <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">Create your first money box to raise funds for a cause. Set your goal and start collecting contributions!</p>

                        <!-- CTA Button -->
                        <a
                            href="{{ route('money-boxes.create') }}"
                            class="inline-flex items-center px-8 py-4 text-base font-bold text-white bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 rounded-2xl shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-200"
                        >
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Create Your First Money Box
                        </a>

                        <!-- Features List -->
                        <div class="mt-12 grid grid-cols-1 sm:grid-cols-3 gap-6 text-left max-w-3xl mx-auto">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">Set Your Goal</p>
                                    <p class="text-sm text-gray-600">Define your target amount</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">Get Support</p>
                                    <p class="text-sm text-gray-600">Receive contributions easily</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-pink-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">Reach Your Goal</p>
                                    <p class="text-sm text-gray-600">Collect funds for your cause</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
