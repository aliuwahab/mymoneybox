<x-layouts.guest>
    {{-- Header --}}
    <div class="bg-white border-b border-[#E6E3DC]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <h1 class="page-title" style="font-size:1.875rem;">Discover PiggyBoxes</h1>
            <p class="tiny mt-1.5">Support causes you care about</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
        <form method="GET" action="{{ route('browse') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" placeholder="Search PiggyBoxes…" value="{{ request('search') }}" />
            </div>
            <div class="sm:w-52">
                <select name="category">
                    <option value="">All categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->icon }} {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
            @if(request('search') || request('category'))
                <a href="{{ route('browse') }}" class="btn">Clear</a>
            @endif
        </form>

        @if(request('search') || request('category'))
            <div class="mt-3 flex flex-wrap gap-2">
                @if(request('search'))
                    <span class="pill pill-info">Search: "{{ request('search') }}"</span>
                @endif
                @if(request('category'))
                    @php $selectedCategory = $categories->find(request('category')); @endphp
                    @if($selectedCategory)
                        <span class="pill pill-info">{{ $selectedCategory->icon }} {{ $selectedCategory->name }}</span>
                    @endif
                @endif
            </div>
        @endif
    </div>

    {{-- Grid --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-14">
        @if($moneyBoxes->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($moneyBoxes as $moneyBox)
                    <x-money-box-card :moneyBox="$moneyBox" />
                @endforeach
            </div>
            <div class="mt-8">{{ $moneyBoxes->appends(request()->query())->links() }}</div>
        @else
            <div class="border-2 border-dashed border-[#D9D6CE] rounded-[10px] p-16 text-center">
                <div class="w-12 h-12 rounded-xl bg-[#F3F1EB] grid place-items-center mx-auto mb-4">
                    <svg viewBox="0 0 24 24" class="w-6 h-6 text-[#9C998F]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 7.5 12 3l9 4.5v9L12 21l-9-4.5v-9Z"/><path d="M3 7.5 12 12l9-4.5"/><path d="M12 12v9"/>
                    </svg>
                </div>
                <h3 class="text-[15px] font-semibold text-[#15140F] mb-1">No PiggyBoxes found</h3>
                <p class="tiny">Try adjusting your search or filters.</p>
            </div>
        @endif
    </div>
</x-layouts.guest>