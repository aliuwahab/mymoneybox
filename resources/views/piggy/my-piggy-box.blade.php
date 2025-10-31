<x-layouts.app :title="'My Piggy Box'">
    <div class="min-h-screen bg-gray-50 py-8" x-data="{
        showToast: false,
        toastMessage: '',
        shareUrl: '{{ $shareUrl }}',
        piggyCode: '{{ auth()->user()->piggy_code }}',
        copyLink() {
            navigator.clipboard.writeText(this.shareUrl).then(() => {
                this.showToastMessage('✅ Link copied to clipboard!');
            }).catch(() => {
                this.showToastMessage('❌ Failed to copy');
            });
        },
        copyCode() {
            const text = `🎁 Send me a gift!\n\nMy Piggy Code: ${this.piggyCode}\n\nOr use this link: ${this.shareUrl}`;
            
            if (navigator.share) {
                navigator.share({
                    title: 'My Piggy Box',
                    text: text
                }).catch((err) => {
                    if (err.name !== 'AbortError') {
                        this.fallbackCopy(text);
                    }
                });
            } else {
                this.fallbackCopy(text);
            }
        },
        fallbackCopy(text) {
            navigator.clipboard.writeText(text).then(() => {
                this.showToastMessage('✅ Copied to clipboard!');
            }).catch(() => {
                this.showToastMessage('❌ Failed to copy');
            });
        },
        showToastMessage(message) {
            this.toastMessage = message;
            this.showToast = true;
            setTimeout(() => { this.showToast = false; }, 3000);
        }
    }">
        <!-- Toast Notification -->
        <div
            x-show="showToast"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed top-4 right-4 z-50 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2"
            style="display: none;"
        >
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span x-text="toastMessage"></span>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header with Actions -->
            <div class="bg-white rounded-lg shadow mb-6 p-4 sm:p-6">
                <div class="flex flex-col space-y-4">
                    <div>
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-3">
                            🎁 {{ $piggyBox->title }}
                        </h1>
                        <p class="text-gray-600 mb-3">
                            Your personal piggy box for receiving gifts from friends and family!
                        </p>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                Code: <span class="ml-1 font-bold">{{ auth()->user()->piggy_code }}</span>
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $piggyBox->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $piggyBox->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                        <a
                            href="{{ $shareUrl }}"
                            target="_blank"
                            class="flex-1 sm:flex-none text-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:shadow-md transition-all cursor-pointer transform hover:scale-105"
                        >
                            🔗 View Donation Page
                        </a>
                        <button
                            @click="copyLink()"
                            class="flex-1 sm:flex-none text-center px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-lg hover:bg-yellow-700 hover:shadow-md transition-all cursor-pointer transform hover:scale-105"
                        >
                            📋 Copy Share Link
                        </button>
                        <button
                            @click="copyCode()"
                            class="flex-1 sm:flex-none text-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 hover:shadow-md transition-all cursor-pointer transform hover:scale-105"
                        >
                            📤 Share My Code
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Stats Overview -->
                <div class="lg:col-span-3">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="text-sm font-medium text-gray-600 mb-1">Total Received</div>
                            <div class="text-3xl font-bold text-gray-900">
                                {{ $piggyBox->formatAmount($piggyBox->total_received) }}
                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                                all time
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="text-sm font-medium text-gray-600 mb-1">Total Gifts</div>
                            <div class="text-3xl font-bold text-gray-900">
                                {{ $piggyBox->donation_count }}
                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                                {{ Str::plural('gift', $piggyBox->donation_count) }} received
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="text-sm font-medium text-gray-600 mb-1">Status</div>
                            <div class="text-xl font-bold {{ $piggyBox->canReceiveDonations() ? 'text-green-600' : 'text-red-600' }}">
                                {{ $piggyBox->canReceiveDonations() ? 'Accepting' : 'Not Accepting' }}
                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                                gifts
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Share Information -->
                <div class="lg:col-span-3">
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-lg shadow p-6 border-2 border-yellow-200">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">📢 How to Share Your Piggy Box</h2>
                        <div class="space-y-3">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-yellow-500 text-white rounded-full flex items-center justify-center font-bold">
                                    1
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Share Your Code</p>
                                    <p class="text-sm text-gray-600">Tell friends to visit the homepage and click "Piggy Someone", then enter your code: <span class="font-bold text-yellow-700">{{ auth()->user()->piggy_code }}</span></p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-yellow-500 text-white rounded-full flex items-center justify-center font-bold">
                                    2
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Share Your Direct Link</p>
                                    <p class="text-sm text-gray-600">Or send them this direct link:</p>
                                    <div class="mt-2 p-2 bg-white rounded border border-yellow-300">
                                        <code class="text-xs break-all text-gray-700">{{ $shareUrl }}</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Gifts -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Recent Gifts</h2>
                        </div>
                        <div class="p-6">
                            @if($recentDonations->count() > 0)
                                <div class="space-y-4">
                                    @foreach($recentDonations as $donation)
                                        <div class="flex items-start space-x-3 pb-4 border-b border-gray-200 last:border-0">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                                    <span class="text-yellow-700 font-semibold">
                                                        {{ substr($donation->getDisplayName(), 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $donation->getDisplayName() }}
                                                    </p>
                                                    <span class="text-sm font-semibold text-gray-900">
                                                        {{ $piggyBox->formatAmount($donation->amount) }}
                                                    </span>
                                                </div>
                                                @if($donation->message)
                                                    <div class="mt-2 p-2 bg-gray-50 rounded text-sm text-gray-700">
                                                        "{{ $donation->message }}"
                                                    </div>
                                                @endif
                                                <div class="flex items-center space-x-2 mt-2">
                                                    <p class="text-xs text-gray-500">
                                                        {{ $donation->created_at->diffForHumans() }}
                                                    </p>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $donation->payment_status->value === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                        {{ ucfirst($donation->payment_status->value) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <div class="text-6xl mb-4">🎁</div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No gifts yet</h3>
                                    <p class="text-gray-600 mb-4">Share your piggy code with friends to start receiving gifts!</p>
                                    <button
                                        @click="copyCode()"
                                        class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 hover:shadow-md transition-all cursor-pointer transform hover:scale-105"
                                    >
                                        Share My Code
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-layouts.app>
