<x-layouts.guest>
    <section class="bg-[#FAFAF7] py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-serif font-normal text-[#15140F] mb-4">System Status</h1>
            <p class="text-[#6B6862] text-lg mb-8">Current service status for key MyPiggyBox experiences.</p>

            <div class="bg-white border border-[#E6E3DC] rounded-[10px] overflow-hidden shadow-sm">
                @foreach([
                    ['Public PiggyBox pages', 'Operational'],
                    ['Piggy Wallet gifts', 'Operational'],
                    ['Dashboard', 'Operational'],
                    ['Payment provider redirects', 'Operational'],
                    ['Email notifications', 'Operational'],
                ] as [$service, $status])
                    <div class="flex items-center justify-between gap-4 px-5 py-4 border-b border-[#E6E3DC] last:border-b-0">
                        <span class="font-medium text-[#15140F]">{{ $service }}</span>
                        <span class="inline-flex items-center gap-2 text-[#1B6B4E] text-[13px] font-medium"><span class="w-2 h-2 rounded-full bg-[#1B6B4E]"></span>{{ $status }}</span>
                    </div>
                @endforeach
            </div>

            <p class="mt-6 text-[13px] text-[#6B6862]">If you are experiencing an issue that is not shown here, contact <a class="text-[#1B6B4E] font-medium" href="mailto:support@mypiggybox.com">support@mypiggybox.com</a>.</p>
        </div>
    </section>
</x-layouts.guest>
