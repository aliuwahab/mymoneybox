<x-layouts.app>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Piggy Boxes</h1>
                    <p class="mt-2 text-gray-600">Manage all your piggy boxes</p>
                </div>
                <a
                    href="{{ route('money-boxes.create') }}"
                    class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition"
                >
                    Create Piggy Box
                </a>
            </div>

            @if($moneyBoxes->count() > 0)
                <!-- Grid of Piggy Boxes -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($moneyBoxes as $moneyBox)
                        <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                            <!-- Header -->
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                            {{ $moneyBox->title }}
                                        </h3>
                                        <div class="flex items-center space-x-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $moneyBox->visibility->value === 'public' ? 'bg-primary-100 text-primary-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $moneyBox->visibility->value === 'public' ? 'Public' : 'Private' }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $moneyBox->is_active ? 'bg-secondary-100 text-secondary-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $moneyBox->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </div>
                                    @if($moneyBox->category)
                                        <span class="text-2xl">{{ $moneyBox->category->icon }}</span>
                                    @endif
                                </div>

                                <!-- Stats -->
                                <div class="space-y-3">
                                    <div>
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-gray-600">Raised</span>
                                            @if($moneyBox->goal_amount)
                                                <span class="text-gray-900 font-medium">{{ number_format($moneyBox->getProgressPercentage(), 1) }}%</span>
                                            @endif
                                        </div>
                                        <div class="text-2xl font-bold text-gray-900">
                                            {{ $moneyBox->formatAmount($moneyBox->total_contributions) }}
                                        </div>
                                        @if($moneyBox->goal_amount)
                                            <div class="text-sm text-gray-500">
                                                of {{ $moneyBox->formatAmount($moneyBox->goal_amount) }}
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                                <div
                                                    class="bg-primary-600 h-2 rounded-full transition-all"
                                                    style="width: {{ min(100, $moneyBox->getProgressPercentage()) }}%"
                                                ></div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex items-center justify-between pt-3 border-t border-gray-200 text-sm">
                                        <div>
                                            <span class="text-gray-600">Contributors</span>
                                            <div class="text-lg font-semibold text-gray-900">{{ $moneyBox->contributions_count }}</div>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Created</span>
                                            <div class="text-lg font-semibold text-gray-900">{{ $moneyBox->created_at->format('M d') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex space-x-2">
                                <a
                                    href="{{ route('money-boxes.show', $moneyBox) }}"
                                    class="flex-1 px-3 py-2 text-center text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                                >
                                    View
                                </a>
                                <a
                                    href="{{ route('money-boxes.edit', $moneyBox) }}"
                                    class="flex-1 px-3 py-2 text-center text-sm font-medium text-primary-700 bg-primary-50 border border-primary-300 rounded-lg hover:bg-primary-100 transition"
                                >
                                    Edit
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $moneyBoxes->links() }}
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
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 transition"
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
