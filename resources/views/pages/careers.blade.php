<x-layouts.guest>
    <section class="bg-[#FAFAF7] py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-serif font-normal text-[#15140F] mb-4">Careers at MyPiggyBox</h1>
            <p class="text-[#6B6862] text-lg mb-8">We are building contribution tools for families, communities, creators, and organisations across Africa.</p>

            <div class="bg-white border border-[#E6E3DC] rounded-[10px] p-7 shadow-sm mb-6">
                <h2 class="text-xl font-semibold text-[#15140F] mb-3">Current openings</h2>
                <p class="text-[#6B6862]">We are not advertising open roles right now, but we are always interested in strong product, engineering, design, support, and partnerships talent.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-4">
                @foreach(['Customer trust', 'Clear ownership', 'Practical craft'] as $value)
                    <div class="bg-white border border-[#E6E3DC] rounded-[10px] p-5">
                        <h3 class="font-semibold text-[#15140F]">{{ $value }}</h3>
                        <p class="text-[14px] text-[#6B6862] mt-2">We value calm execution, transparent communication, and products people can rely on when money is involved.</p>
                    </div>
                @endforeach
            </div>

            <p class="mt-8 text-[#6B6862]">To introduce yourself, email <a class="text-[#1B6B4E] font-medium" href="mailto:sales@mypiggybox.com">sales@mypiggybox.com</a>.</p>
        </div>
    </section>
</x-layouts.guest>
