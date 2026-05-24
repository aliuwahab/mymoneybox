<x-layouts.guest>
    <section class="bg-[#FAFAF7] py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-serif font-normal text-[#15140F] mb-4">Security</h1>
            <p class="text-[#6B6862] text-lg mb-8">MyPiggyBox treats contribution data, payment redirects, and account access as security-sensitive by default.</p>

            <div class="grid md:grid-cols-2 gap-5">
                @foreach([
                    ['Secure payments', 'Payments are processed through supported payment providers. MyPiggyBox does not ask contributors to share card credentials directly with us.'],
                    ['Account protection', 'Account areas are protected by authentication, verification flows, and administrative access controls.'],
                    ['Operational controls', 'We monitor key payment states and keep audit-friendly records for contributions, gifts, and withdrawals.'],
                    ['Responsible reporting', 'If you believe you found a vulnerability, email us with clear reproduction steps and impact.'],
                ] as [$title, $body])
                    <div class="bg-white border border-[#E6E3DC] rounded-[10px] p-6">
                        <h2 class="text-lg font-semibold text-[#15140F]">{{ $title }}</h2>
                        <p class="text-[#6B6862] text-[14px] mt-2">{{ $body }}</p>
                    </div>
                @endforeach
            </div>

            <p class="mt-8 text-[#6B6862]">Security reports can be sent to <a class="text-[#1B6B4E] font-medium" href="mailto:support@mypiggybox.com">support@mypiggybox.com</a>.</p>
        </div>
    </section>
</x-layouts.guest>
