<x-layouts.app>
    <div class="page-wrap max-w-[720px] mx-auto w-full">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('money-boxes.show', $moneyBox) }}"
               class="w-8 h-8 rounded-full border border-[#E6E3DC] bg-white grid place-items-center text-[#6B6862] hover:text-[#15140F] hover:border-[#D9D6CE] transition-colors duration-100"
               wire:navigate>
                <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5m0 0 7 7m-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="page-title" style="font-size:1.75rem;">Share</h1>
                <p class="tiny mt-0.5">{{ $moneyBox->title }}</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-primary-50 border border-primary-200/60 text-primary-700 text-[13px] px-4 py-3 rounded-[8px]">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-4">

            {{-- Direct link --}}
            <div class="card">
                <div class="card-head">
                    <span class="card-title">Direct link</span>
                </div>
                <div class="card-body">
                    <div class="flex gap-2" x-data="{ copied: false }">
                        <input
                            type="text"
                            readonly
                            value="{{ route('box.show', $moneyBox->slug) }}"
                            id="share-link"
                            class="flex-1 bg-[#F3F1EB] text-[#6B6862] border-[#E6E3DC]"
                        />
                        <button
                            @click="navigator.clipboard.writeText('{{ route('box.show', $moneyBox->slug) }}').then(() => { copied = true; setTimeout(() => copied = false, 2000); })"
                            class="btn btn-primary"
                            x-text="copied ? 'Copied!' : 'Copy'">
                            Copy
                        </button>
                    </div>
                </div>
            </div>

            {{-- QR code --}}
            <div class="card">
                <div class="card-head">
                    <span class="card-title">QR code</span>
                </div>
                <div class="card-body">
                    @if($moneyBox->hasQrCode())
                        <div class="flex flex-col sm:flex-row items-start gap-6">
                            <img
                                src="{{ $moneyBox->getQrCodeUrl() }}"
                                alt="QR Code"
                                class="w-52 h-52 border border-[#E6E3DC] rounded-[8px]"
                            />
                            <div class="space-y-3">
                                <p class="text-[13px] text-[#6B6862] leading-relaxed">
                                    Download and share this QR code. Anyone who scans it can contribute to your box.
                                </p>
                                <div class="flex flex-col gap-2">
                                    <a href="{{ route('money-boxes.download-qr', $moneyBox) }}" class="btn" wire:navigate>
                                        <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 15V3m0 12-4-4m4 4 4-4"/><path d="M2 17l.621 2.485A2 2 0 0 0 4.561 21h14.878a2 2 0 0 0 1.94-1.515L22 17"/>
                                        </svg>
                                        Download QR
                                    </a>
                                    <a href="https://wa.me/?text={{ urlencode($moneyBox->title . "\n\nScan QR or visit: " . route('box.show', $moneyBox->slug)) }}"
                                       target="_blank" class="btn">
                                        <svg viewBox="0 0 24 24" class="w-4 h-4" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                        Share via WhatsApp
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-12 h-12 rounded-xl bg-[#F3F1EB] grid place-items-center mx-auto mb-3">
                                <svg viewBox="0 0 24 24" class="w-6 h-6 text-[#9C998F]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path d="M12 3h.01"/><path d="M12 16v.01"/><path d="M16 12h1"/><path d="M21 12v.01"/><path d="M12 21v-1"/>
                                </svg>
                            </div>
                            <h3 class="text-[14px] font-semibold text-[#15140F] mb-1">QR code not generated yet</h3>
                            <form action="{{ route('money-boxes.generate-qr', $moneyBox) }}" method="POST" class="mt-3">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                                    Generate QR code
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Social media --}}
            <div class="card">
                <div class="card-head">
                    <span class="card-title">Share on social media</span>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <a href="https://wa.me/?text={{ urlencode($moneyBox->title . "\n\n" . route('box.show', $moneyBox->slug)) }}"
                           target="_blank"
                           class="btn justify-center bg-[#25D366] border-[#25D366] text-white hover:bg-[#22C55E] hover:border-[#22C55E]">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            WhatsApp
                        </a>

                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('box.show', $moneyBox->slug)) }}"
                           target="_blank"
                           class="btn justify-center bg-[#1877F2] border-[#1877F2] text-white hover:bg-[#166FE5] hover:border-[#166FE5]">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            Facebook
                        </a>

                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($moneyBox->title) }}&url={{ urlencode(route('box.show', $moneyBox->slug)) }}"
                           target="_blank"
                           class="btn justify-center bg-[#1DA1F2] border-[#1DA1F2] text-white hover:bg-[#1a91da] hover:border-[#1a91da]">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            Twitter
                        </a>
                    </div>
                </div>
            </div>

            {{-- Embed (coming soon) --}}
            <div class="card">
                <div class="card-head">
                    <span class="card-title">Embed code</span>
                    <span class="pill pill-muted">Coming soon</span>
                </div>
                <div class="card-body">
                    <p class="text-[13px] text-[#6B6862]">Embed this money box on your website with a simple code snippet.</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>