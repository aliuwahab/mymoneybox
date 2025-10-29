@props(['moneyBox'])

<a href="{{ route('box.show', $moneyBox->slug) }}" class="block">
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden">
        <!-- Category Badge -->
        @if($moneyBox->category)
            <div class="px-4 pt-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-800">
                    {{ $moneyBox->category->icon }} {{ $moneyBox->category->name }}
                </span>
            </div>
        @endif

        <div class="p-4">
            <!-- Title -->
            <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                {{ $moneyBox->title }}
            </h3>

            <!-- Description -->
            @if($moneyBox->description)
                <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                    {{ $moneyBox->description }}
                </p>
            @endif

            <!-- Progress Bar -->
            @if($moneyBox->goal_amount)
                <div class="mb-3">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Progress</span>
                        <span class="font-medium text-gray-900">{{ number_format($moneyBox->getProgressPercentage(), 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div
                            class="bg-primary-600 h-2 rounded-full transition-all"
                            style="width: {{ min(100, $moneyBox->getProgressPercentage()) }}%"
                        ></div>
                    </div>
                </div>
            @endif

            <!-- Stats -->
            <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                <div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{ $moneyBox->formatAmount($moneyBox->total_contributions) }}
                    </div>
                    @if($moneyBox->goal_amount)
                        <div class="text-sm text-gray-500">
                            of {{ $moneyBox->formatAmount($moneyBox->goal_amount) }}
                        </div>
                    @endif
                </div>
                <div class="text-right">
                    <div class="text-lg font-semibold text-gray-900">
                        {{ $moneyBox->contribution_count }}
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ Str::plural('contribution', $moneyBox->contribution_count) }}
                    </div>
                </div>
            </div>

            <!-- Creator -->
            <div class="mt-3 pt-3 border-t border-gray-200">
                <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    by {{ $moneyBox->user->name }}
                </div>
            </div>
        </div>
    </div>
</a>
