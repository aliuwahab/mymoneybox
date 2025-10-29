<x-layouts.guest>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Discover Money Boxes</h1>
                <p class="mt-2 text-sm sm:text-base text-gray-600">Support causes you care about</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
            <form method="GET" action="{{ route('browse') }}" class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                <div class="flex-1">
                    <input
                        type="text"
                        name="search"
                        placeholder="Search money boxes..."
                        value="{{ request('search') }}"
                    />
                </div>

                <div class="sm:w-64">
                    <select name="category">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option
                                value="{{ $category->id }}"
                                {{ request('category') == $category->id ? 'selected' : '' }}
                            >
                                {{ $category->icon }} {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button
                    type="submit"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-sm transition"
                >
                    Search
                </button>

                @if(request('search') || request('category'))
                    <a
                        href="{{ route('browse') }}"
                        class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition"
                    >
                        Clear
                    </a>
                @endif
            </form>

            <!-- Active Filters Indicator -->
            @if(request('search') || request('category'))
                <div class="mt-4 flex flex-wrap gap-2">
                    @if(request('search'))
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                            Search: "{{ request('search') }}"
                        </span>
                    @endif
                    @if(request('category'))
                        @php
                            $selectedCategory = $categories->find(request('category'));
                        @endphp
                        @if($selectedCategory)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                                Category: {{ $selectedCategory->icon }} {{ $selectedCategory->name }}
                            </span>
                        @endif
                    @endif
                </div>
            @endif
        </div>

        <!-- Money Boxes Grid -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 pb-12">
            @if($moneyBoxes->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                    @foreach($moneyBoxes as $moneyBox)
                        <x-money-box-card :moneyBox="$moneyBox" />
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $moneyBoxes->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No money boxes found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filters.</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.guest>
