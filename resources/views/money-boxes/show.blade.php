<x-layouts.app>
    <div class="min-h-screen bg-gray-50 py-8" x-data="{ showToast: false, toastMessage: '' }" x-init="
        @if(session('success'))
            showToast = true;
            toastMessage = '{{ session('success') }}';
            setTimeout(() => showToast = false, 5000);
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
            class="fixed top-4 right-4 z-50 bg-blue-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2"
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
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-3">{{ $moneyBox->title }}</h1>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs sm:text-sm font-medium {{ $moneyBox->visibility->value === 'public' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $moneyBox->visibility->value === 'public' ? 'Public' : 'Private' }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs sm:text-sm font-medium {{ $moneyBox->is_active ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800' }}">
                                {{ $moneyBox->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            @if($moneyBox->category)
                                <span class="text-xs sm:text-sm text-gray-600">
                                    {{ $moneyBox->category->icon }} {{ $moneyBox->category->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                        <a
                            href="{{ route('box.show', $moneyBox->slug) }}"
                            target="_blank"
                            class="flex-1 sm:flex-none text-center px-4 py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                        >
                            View Public Page
                        </a>
                        <a
                            href="{{ route('money-boxes.share', $moneyBox) }}"
                            class="flex-1 sm:flex-none text-center px-4 py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                        >
                            Share
                        </a>
                        @if(auth()->user()->isVerified() && $moneyBox->getAvailableBalance() >= config('withdrawal.min_amount', 10) && auth()->user()->withdrawalAccounts()->active()->count() > 0)
                            <a
                                href="{{ route('money-boxes.withdraw.create', $moneyBox) }}"
                                class="flex-1 sm:flex-none text-center px-4 py-2 text-xs sm:text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition"
                            >
                                💰 Withdraw Funds
                            </a>
                        @elseif(!auth()->user()->isVerified())
                            <a
                                href="{{ route('settings.verification') }}"
                                class="flex-1 sm:flex-none text-center px-4 py-2 text-xs sm:text-sm font-medium text-yellow-700 bg-yellow-100 border border-yellow-300 rounded-lg hover:bg-yellow-200 transition"
                            >
                                🔒 Verify ID to Withdraw
                            </a>
                        @elseif(auth()->user()->withdrawalAccounts()->active()->count() === 0)
                            <a
                                href="{{ route('settings.withdrawal-accounts') }}"
                                class="flex-1 sm:flex-none text-center px-4 py-2 text-xs sm:text-sm font-medium text-blue-700 bg-blue-100 border border-blue-300 rounded-lg hover:bg-blue-200 transition"
                            >
                                🏦 Add Account to Withdraw
                            </a>
                        @elseif($moneyBox->getAvailableBalance() < config('withdrawal.min_amount', 10))
                            <button
                                type="button"
                                disabled
                                class="flex-1 sm:flex-none text-center px-4 py-2 text-xs sm:text-sm font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed"
                            >
                                💰 Insufficient Balance
                            </button>
                        @endif
                        <a
                            href="{{ route('money-boxes.edit', $moneyBox) }}"
                            class="flex-1 sm:flex-none text-center px-4 py-2 text-xs sm:text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition"
                        >
                            Edit
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Stats Overview -->
                <div class="lg:col-span-3">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="text-sm font-medium text-gray-600 mb-1">Total Raised</div>
                            <div class="text-3xl font-bold text-gray-900">
                                {{ $moneyBox->formatAmount($moneyBox->total_contributions) }}
                            </div>
                            @if($moneyBox->goal_amount)
                                <div class="text-sm text-gray-500 mt-1">
                                    of {{ $moneyBox->formatAmount($moneyBox->goal_amount) }}
                                </div>
                            @endif
                        </div>

                        <div class="bg-green-50 border-2 border-green-200 rounded-lg shadow p-6">
                            <div class="text-sm font-medium text-green-700 mb-1">Available Balance</div>
                            <div class="text-3xl font-bold text-green-600">
                                {{ $moneyBox->formatAmount($moneyBox->getAvailableBalance()) }}
                            </div>
                            <div class="text-sm text-green-600 mt-1">
                                ready to withdraw
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="text-sm font-medium text-gray-600 mb-1">Contributors</div>
                            <div class="text-3xl font-bold text-gray-900">
                                {{ $moneyBox->contribution_count }}
                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                                {{ Str::plural('person', $moneyBox->contribution_count) }}
                            </div>
                        </div>

                        @if($moneyBox->goal_amount)
                            <div class="bg-white rounded-lg shadow p-6">
                                <div class="text-sm font-medium text-gray-600 mb-1">Progress</div>
                                <div class="text-3xl font-bold text-gray-900">
                                    {{ number_format($moneyBox->getProgressPercentage(), 1) }}%
                                </div>
                                <div class="text-sm text-gray-500 mt-1">
                                    of goal
                                </div>
                            </div>
                        @endif

                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="text-sm font-medium text-gray-600 mb-1">Status</div>
                            <div class="text-xl font-bold {{ $moneyBox->canAcceptContributions() ? 'text-blue-600' : 'text-red-600' }}">
                                {{ $moneyBox->canAcceptContributions() ? 'Accepting' : 'Not Accepting' }}
                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                                contributions
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Image & Gallery -->
                @if($moneyBox->hasMedia('main') || $moneyBox->hasMedia('gallery'))
                    <div class="lg:col-span-3">
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-lg font-semibold text-gray-900">Images</h2>
                                <a href="{{ route('money-boxes.edit', $moneyBox) }}" 
                                   class="text-sm text-blue-600 hover:text-blue-700">
                                    Manage Images
                                </a>
                            </div>
                            
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                @if($moneyBox->hasMedia('main'))
                                    <div class="lg:col-span-2">
                                        <h3 class="text-sm font-medium text-gray-700 mb-2">Main Image</h3>
                                        <img src="{{ $moneyBox->getMainImageUrl() }}" 
                                             alt="Main image"
                                             class="w-full h-64 object-cover rounded-lg shadow-sm">
                                    </div>
                                @endif
                                
                                @if($moneyBox->hasMedia('gallery'))
                                    <div class="{{ $moneyBox->hasMedia('main') ? 'lg:col-span-1' : 'lg:col-span-3' }}">
                                        <h3 class="text-sm font-medium text-gray-700 mb-2">Gallery ({{ $moneyBox->getMedia('gallery')->count() }})</h3>
                                        <div class="grid grid-cols-2 {{ $moneyBox->hasMedia('main') ? 'lg:grid-cols-1' : 'lg:grid-cols-4' }} gap-2">
                                            @foreach($moneyBox->getGalleryImageUrls() as $imageUrl)
                                                <img src="{{ $imageUrl }}" 
                                                     alt="Gallery image"
                                                     class="w-full h-32 object-cover rounded-lg shadow-sm hover:shadow-md transition cursor-pointer">
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Recent Contributions -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <h2 class="text-lg font-semibold text-gray-900">Recent Contributions</h2>
                            <a
                                href="{{ route('money-boxes.statistics', $moneyBox) }}"
                                class="text-sm text-blue-600 hover:text-blue-700"
                            >
                                View All
                            </a>
                        </div>
                        <div class="p-6">
                            @if($moneyBox->contributions->count() > 0)
                                <div class="space-y-4">
                                    @foreach($moneyBox->contributions as $contribution)
                                        <div class="flex items-start space-x-3 pb-4 border-b border-gray-200 last:border-0">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <span class="text-blue-700 font-semibold">
                                                        {{ substr($contribution->getDisplayName(), 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $contribution->getDisplayName() }}
                                                    </p>
                                                    <span class="text-sm font-semibold text-gray-900">
                                                        {{ $moneyBox->formatAmount($contribution->amount) }}
                                                    </span>
                                                </div>
                                                @if($contribution->message)
                                                    <p class="text-sm text-gray-600 mt-1">{{ $contribution->message }}</p>
                                                @endif
                                                <div class="flex items-center space-x-2 mt-1">
                                                    <p class="text-xs text-gray-500">
                                                        {{ $contribution->created_at->diffForHumans() }}
                                                    </p>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $contribution->payment_status->value === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                        {{ ucfirst($contribution->payment_status->value) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No contributions yet</h3>
                                    <p class="mt-1 text-sm text-gray-500">Share your piggy box to start receiving contributions.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Withdrawal Requests -->
                <div class="lg:col-span-3">
                    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <h2 class="text-lg font-semibold text-gray-900">Withdrawal Requests</h2>
                            @if($moneyBox->withdrawals->count() > 0)
                                <span class="text-sm text-gray-600">
                                    Total: {{ $moneyBox->withdrawals->count() }}
                                </span>
                            @endif
                        </div>
                        <div class="p-6">
                            @if($moneyBox->withdrawals->count() > 0)
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
                                            @foreach($moneyBox->withdrawals as $withdrawal)
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
                                                        {{ $moneyBox->formatAmount($withdrawal->amount) }}
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                                        - {{ $moneyBox->formatAmount($withdrawal->fee) }}
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                                        {{ $moneyBox->formatAmount($withdrawal->net_amount) }}
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
                                    @if($moneyBox->getAvailableBalance() >= config('withdrawal.min_amount', 10))
                                        <div class="mt-4">
                                            <a
                                                href="{{ route('money-boxes.withdraw.create', $moneyBox) }}"
                                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition"
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

                <!-- Piggy Box Info Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Quick Share -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Quick Share</h3>
                        <div class="space-y-2">
                            <button
                                type="button"
                                @click="navigator.clipboard.writeText('{{ route('box.show', $moneyBox->slug) }}').then(() => { toastMessage = 'Link copied!'; showToast = true; setTimeout(() => showToast = false, 3000); })"
                                class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition cursor-pointer"
                            >
                                Copy Link
                            </button>
                            <a
                                href="{{ route('money-boxes.share', $moneyBox) }}"
                                class="block w-full px-4 py-2 text-center text-sm font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition"
                            >
                                Share Options
                            </a>
                        </div>
                    </div>

                    <!-- QR Code -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">QR Code</h3>
                        @if($moneyBox->hasQrCode())
                            <div class="flex flex-col items-center">
                                <img
                                    src="{{ $moneyBox->getQrCodeUrl() }}"
                                    alt="QR Code"
                                    class="w-full aspect-square border-2 border-gray-200 rounded-lg mb-3"
                                />
                                <div class="w-full space-y-2">
                                    <a
                                        href="{{ route('money-boxes.download-qr', $moneyBox) }}"
                                        class="flex items-center justify-center space-x-2 w-full px-3 py-2 text-xs font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        <span>Download QR</span>
                                    </a>
                                    <a
                                        href="https://wa.me/?text={{ urlencode($moneyBox->title . "\n\nScan my QR code or visit: " . route('box.show', $moneyBox->slug)) }}"
                                        target="_blank"
                                        class="flex items-center justify-center space-x-2 w-full px-3 py-2 text-xs font-medium text-white bg-green-500 rounded-lg hover:bg-green-600 transition"
                                    >
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                        </svg>
                                        <span>Share via WhatsApp</span>
                                    </a>
                                </div>
                                <p class="text-xs text-gray-500 mt-2 text-center">
                                    Share this QR code to receive contributions
                                </p>
                            </div>
                        @else
                            <div class="text-center">
                                <div class="mb-3 p-4 bg-gray-50 rounded-lg">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                </div>
                                <form action="{{ route('money-boxes.generate-qr', $moneyBox) }}" method="POST" class="inline">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="w-full px-3 py-2 text-xs font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition"
                                    >
                                        Generate QR Code
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <!-- Details -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Details</h3>
                        <dl class="space-y-3 text-sm">
                            <div>
                                <dt class="text-gray-600">Created</dt>
                                <dd class="text-gray-900 font-medium">{{ $moneyBox->created_at->format('M d, Y') }}</dd>
                            </div>
                            @if($moneyBox->start_date)
                                <div>
                                    <dt class="text-gray-600">Start Date</dt>
                                    <dd class="text-gray-900 font-medium">{{ $moneyBox->start_date->format('M d, Y') }}</dd>
                                </div>
                            @endif
                            @if($moneyBox->end_date)
                                <div>
                                    <dt class="text-gray-600">End Date</dt>
                                    <dd class="text-gray-900 font-medium">{{ $moneyBox->end_date->format('M d, Y') }}</dd>
                                </div>
                            @elseif($moneyBox->is_ongoing)
                                <div>
                                    <dt class="text-gray-600">Duration</dt>
                                    <dd class="text-gray-900 font-medium">Ongoing</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-gray-600">Amount Type</dt>
                                <dd class="text-gray-900 font-medium">{{ ucfirst($moneyBox->amount_type->value) }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-600">Currency</dt>
                                <dd class="text-gray-900 font-medium">{{ $moneyBox->currency_code }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-layouts.app>
