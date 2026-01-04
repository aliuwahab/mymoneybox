<x-layouts.app>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('piggy.my-piggy-box') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to My Piggy Box
                </a>
            </div>

            <!-- Header -->
            <div class="bg-white rounded-lg shadow mb-6 p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="flex-shrink-0 h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Withdraw Funds</h1>
                        <p class="text-sm text-gray-600">From: {{ $piggyBox->title }}</p>
                    </div>
                </div>

                <!-- Available Balance -->
                <div class="bg-green-50 border-2 border-green-200 rounded-lg p-4">
                    <div class="text-sm text-green-700 mb-1">Available Balance</div>
                    <div class="text-3xl font-bold text-green-600">
                        {{ $piggyBox->formatAmount($availableBalance) }}
                    </div>
                    <div class="text-sm text-green-600 mt-1">
                        ready to withdraw
                    </div>
                </div>
            </div>

            <!-- Withdrawal Form -->
            <div class="bg-white rounded-lg shadow p-6">
                <form action="{{ route('piggy.withdraw.store') }}" method="POST">
                    @csrf

                    <div class="space-y-6">
                        <!-- Amount Input -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                                Withdrawal Amount <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="amount"
                                name="amount"
                                step="0.01"
                                min="{{ config('withdrawal.min_amount', 10) }}"
                                max="{{ $availableBalance }}"
                                value="{{ old('amount') }}"
                                required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 @error('amount') border-red-300 @enderror"
                                placeholder="Enter amount"
                            />
                            <p class="mt-1 text-xs text-gray-500">
                                Minimum: {{ $piggyBox->formatAmount(config('withdrawal.min_amount', 10)) }} |
                                Maximum: {{ $piggyBox->formatAmount($availableBalance) }}
                            </p>
                            @error('amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fee Information -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm">
                            <p class="text-gray-700">
                                <strong>Note:</strong> A withdrawal fee of {{ config('withdrawal.fee_percentage', 2) }}% will be deducted from your withdrawal amount.
                            </p>
                        </div>

                        <!-- Withdrawal Account -->
                        <div>
                            <label for="withdrawal_account_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Withdrawal Account <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="withdrawal_account_id"
                                name="withdrawal_account_id"
                                required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 @error('withdrawal_account_id') border-red-300 @enderror"
                            >
                                <option value="">Select account...</option>
                                @foreach($withdrawalAccounts as $account)
                                    <option value="{{ $account->id }}" {{ old('withdrawal_account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->getDisplayName() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('withdrawal_account_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <a href="{{ route('settings.withdrawal-accounts') }}" class="text-xs text-blue-600 hover:underline mt-1 inline-block">
                                Add new account
                            </a>
                        </div>

                        <!-- Optional Note -->
                        <div>
                            <label for="note" class="block text-sm font-medium text-gray-700 mb-2">
                                Note (Optional)
                            </label>
                            <textarea
                                id="note"
                                name="note"
                                rows="3"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 @error('note') border-red-300 @enderror"
                                placeholder="Add a note about this withdrawal..."
                            >{{ old('note') }}</textarea>
                            @error('note')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <button
                            type="submit"
                            class="flex-1 sm:flex-none px-6 py-3 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                        >
                            Submit Withdrawal Request
                        </button>
                        <a
                            href="{{ route('piggy.my-piggy-box') }}"
                            class="flex-1 sm:flex-none text-center px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                        >
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
