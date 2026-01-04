<x-layouts.app :title="'My Piggy Box'">
    <div class="min-h-screen bg-gray-50 py-8" x-data="{
        showToast: false,
        toastMessage: '',
        shareUrl: '{{ $shareUrl }}',
        piggyCode: '{{ auth()->user()->piggy_code }}',
        userName: '{{ auth()->user()->name }}',
        copyLink() {
            navigator.clipboard.writeText(this.shareUrl).then(() => {
                this.showToastMessage('✅ Link copied to clipboard!');
            }).catch(() => {
                this.showToastMessage('❌ Failed to copy');
            });
        },
        shareViaWhatsApp() {
            const text = `🎁 ${this.userName} is collecting gifts!\n\nMy Piggy Code: ${this.piggyCode}\n\nOr use this link: ${this.shareUrl}`;
            const encodedText = encodeURIComponent(text);
            window.open(`https://wa.me/?text=${encodedText}`, '_blank');
        },
        copyPiggyCode() {
            const text = `🎁 ${this.userName} is collecting gifts!\n\nMy Piggy Code: ${this.piggyCode}\n\nOr use this link: ${this.shareUrl}`;
            navigator.clipboard.writeText(text).then(() => {
                this.showToastMessage('✅ Piggy message copied to clipboard!');
            }).catch(() => {
                this.showToastMessage('❌ Failed to copy');
            });
        },
        showToastMessage(message) {
            this.toastMessage = message;
            this.showToast = true;
            setTimeout(() => { this.showToast = false; }, 3000);
        }
    }" x-init="
        @if(session('success'))
            showToastMessage('{{ session('success') }}');
        @endif
    ">
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
                        @if(auth()->user()->isVerified() && $piggyBox->getAvailableBalance() >= config('withdrawal.min_amount', 10) && auth()->user()->withdrawalAccounts()->active()->count() > 0)
                            <a
                                href="{{ route('piggy.withdraw.create') }}"
                                class="flex-1 sm:flex-none text-center px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 hover:shadow-md transition-all cursor-pointer transform hover:scale-105"
                            >
                                💰 Withdraw Funds
                            </a>
                        @elseif(!auth()->user()->isVerified())
                            <a
                                href="{{ route('settings.verification') }}"
                                class="flex-1 sm:flex-none text-center px-4 py-2 text-sm font-medium text-yellow-700 bg-yellow-100 border border-yellow-300 rounded-lg hover:bg-yellow-200 transition"
                            >
                                🔒 Verify ID to Withdraw
                            </a>
                        @elseif(auth()->user()->withdrawalAccounts()->active()->count() === 0)
                            <a
                                href="{{ route('settings.withdrawal-accounts') }}"
                                class="flex-1 sm:flex-none text-center px-4 py-2 text-sm font-medium text-blue-700 bg-blue-100 border border-blue-300 rounded-lg hover:bg-blue-200 transition"
                            >
                                🏦 Add Account to Withdraw
                            </a>
                        @elseif($piggyBox->getAvailableBalance() < config('withdrawal.min_amount', 10))
                            <button
                                type="button"
                                disabled
                                class="flex-1 sm:flex-none text-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed"
                            >
                                💰 Insufficient Balance
                            </button>
                        @endif
                        <button
                            @click="shareViaWhatsApp()"
                            class="flex-1 sm:flex-none text-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 hover:shadow-md transition-all cursor-pointer transform hover:scale-105 flex items-center justify-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.076 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421-7.403h-.004a9.87 9.87 0 00-4.946 1.196c-1.54.92-2.846 2.454-3.297 4.12 1.357-2.119 3.2-3.913 5.408-5.028 1.265-.72 2.88-1.288 2.835 0 0 0 0 0zM12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0z"/></svg>
                            Share via WhatsApp
                        </button>
                        <button
                            @click="copyPiggyCode()"
                            class="flex-1 sm:flex-none text-center px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-lg hover:bg-yellow-700 hover:shadow-md transition-all cursor-pointer transform hover:scale-105"
                        >
                            📋 Copy Piggy Code
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Stats Overview -->
                <div class="lg:col-span-3">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="text-sm font-medium text-gray-600 mb-1">Total Received</div>
                            <div class="text-3xl font-bold text-gray-900">
                                {{ $piggyBox->formatAmount($piggyBox->total_received) }}
                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                                all time
                            </div>
                        </div>

                        <div class="bg-green-50 border-2 border-green-200 rounded-lg shadow p-6">
                            <div class="text-sm font-medium text-green-700 mb-1">Available Balance</div>
                            <div class="text-3xl font-bold text-green-600">
                                {{ $piggyBox->formatAmount($piggyBox->getAvailableBalance()) }}
                            </div>
                            <div class="text-sm text-green-600 mt-1">
                                ready to withdraw
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

                <!-- QR Code Section -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">🔲 Your QR Code</h2>
                        @if($piggyBox->hasQrCode())
                            <div class="flex flex-col lg:flex-row gap-6">
                                <!-- QR Code Display -->
                                <div class="flex flex-col items-center lg:w-1/3">
                                    <img
                                        src="{{ $piggyBox->getQrCodeUrl() }}"
                                        alt="QR Code for Piggy Box"
                                        class="w-64 h-64 border-2 border-gray-200 rounded-lg mb-3"
                                    />
                                    <div class="w-full max-w-xs space-y-2">
                                        <a
                                            href="{{ route('piggy.download-qr') }}"
                                            class="flex items-center justify-center space-x-2 w-full px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            <span>Download QR Code</span>
                                        </a>
                                        <a
                                            href="https://wa.me/?text={{ urlencode('🎁 Gift me! Scan my QR code or visit: ' . $shareUrl) }}"
                                            target="_blank"
                                            class="flex items-center justify-center space-x-2 w-full px-4 py-2 text-sm font-medium text-white bg-green-500 rounded-lg hover:bg-green-600 transition"
                                        >
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                            </svg>
                                            <span>Share QR via WhatsApp</span>
                                        </a>
                                    </div>
                                </div>
                                
                                <!-- Instructions -->
                                <div class="flex-1">
                                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-lg p-6 border-2 border-yellow-200 h-full">
                                        <h3 class="text-md font-semibold text-gray-900 mb-3">📢 How to Share Your QR Code</h3>
                                        <div class="space-y-3">
                                            <div class="flex items-start space-x-3">
                                                <div class="flex-shrink-0 w-8 h-8 bg-yellow-500 text-white rounded-full flex items-center justify-center font-bold text-sm">
                                                    1
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">Download the QR Code</p>
                                                    <p class="text-sm text-gray-600">Click the download button above to save the QR code to your device.</p>
                                                </div>
                                            </div>
                                            <div class="flex items-start space-x-3">
                                                <div class="flex-shrink-0 w-8 h-8 bg-yellow-500 text-white rounded-full flex items-center justify-center font-bold text-sm">
                                                    2
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">Share Anywhere</p>
                                                    <p class="text-sm text-gray-600">Share the QR code on social media, WhatsApp, email, or print it on flyers, invitations, etc.</p>
                                                </div>
                                            </div>
                                            <div class="flex items-start space-x-3">
                                                <div class="flex-shrink-0 w-8 h-8 bg-yellow-500 text-white rounded-full flex items-center justify-center font-bold text-sm">
                                                    3
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">People Scan & Gift</p>
                                                    <p class="text-sm text-gray-600">Anyone who scans your QR code will be taken directly to your gift page to send you money!</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="mb-4 p-8 bg-gray-50 rounded-lg inline-block">
                                    <svg class="mx-auto h-20 w-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Generate Your QR Code</h3>
                                <p class="text-gray-600 mb-6 max-w-md mx-auto">Create a QR code that people can scan to send you gifts instantly!</p>
                                <form action="{{ route('piggy.generate-qr') }}" method="POST" class="inline">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-sm transition inline-flex items-center space-x-2"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        <span>Generate QR Code</span>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Share Information -->
                <div class="lg:col-span-3">
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-lg shadow p-6 border-2 border-yellow-200">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">📢 Other Ways to Share</h2>
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
                                    <div class="flex flex-col sm:flex-row gap-2 justify-center">
                                        <button
                                            @click="shareViaWhatsApp()"
                                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 hover:shadow-md transition-all cursor-pointer transform hover:scale-105 flex items-center justify-center gap-2"
                                        >
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.076 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421-7.403h-.004a9.87 9.87 0 00-4.946 1.196c-1.54.92-2.846 2.454-3.297 4.12 1.357-2.119 3.2-3.913 5.408-5.028 1.265-.72 2.88-1.288 2.835 0 0 0 0 0zM12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0z"/></svg>
                                            Share via WhatsApp
                                        </button>
                                        <button
                                            @click="copyPiggyCode()"
                                            class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 hover:shadow-md transition-all cursor-pointer transform hover:scale-105"
                                        >
                                            📋 Copy Piggy Code
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Withdrawal Requests -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <h2 class="text-lg font-semibold text-gray-900">Withdrawal Requests</h2>
                            @if($withdrawals->count() > 0)
                                <span class="text-sm text-gray-600">
                                    Total: {{ $withdrawals->count() }}
                                </span>
                            @endif
                        </div>
                        <div class="p-6">
                            @if($withdrawals->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead>
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Amount</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($withdrawals as $withdrawal)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-4 whitespace-nowrap">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $withdrawal->reference }}
                                                        </div>
                                                        @if($withdrawal->user_note)
                                                            <div class="text-xs text-gray-500 mt-1">
                                                                {{ Str::limit($withdrawal->user_note, 30) }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $piggyBox->formatAmount($withdrawal->amount) }}
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                                        - {{ $piggyBox->formatAmount($withdrawal->fee) }}
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                                        {{ $piggyBox->formatAmount($withdrawal->net_amount) }}
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        @if($withdrawal->withdrawalAccount)
                                                            <div class="text-sm">{{ $withdrawal->withdrawalAccount->getDisplayName() }}</div>
                                                        @else
                                                            <span class="text-gray-400">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap">
                                                        @php
                                                            $statusClasses = match($withdrawal->status->value) {
                                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                                'in_review' => 'bg-blue-100 text-blue-800',
                                                                'approved' => 'bg-green-100 text-green-800',
                                                                'disbursed' => 'bg-emerald-100 text-emerald-800',
                                                                'rejected' => 'bg-red-100 text-red-800',
                                                                'failed' => 'bg-orange-100 text-orange-800',
                                                                default => 'bg-gray-100 text-gray-800',
                                                            };
                                                        @endphp
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses }}">
                                                            {{ $withdrawal->status->label() }}
                                                        </span>
                                                        @if($withdrawal->rejection_reason)
                                                            <div class="text-xs text-red-600 mt-1">
                                                                {{ Str::limit($withdrawal->rejection_reason, 20) }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        <div>{{ $withdrawal->created_at->format('M d, Y') }}</div>
                                                        <div class="text-xs text-gray-400">{{ $withdrawal->created_at->format('h:i A') }}</div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No withdrawal requests yet</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        When you request withdrawals, they will appear here.
                                    </p>
                                    @if($piggyBox->getAvailableBalance() >= config('withdrawal.min_amount', 10))
                                        <div class="mt-4">
                                            <a
                                                href="{{ route('piggy.withdraw.create') }}"
                                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 transition"
                                            >
                                                Request Withdrawal
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-layouts.app>
