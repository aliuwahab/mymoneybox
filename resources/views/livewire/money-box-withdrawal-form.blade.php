<div>
    {{-- Withdraw Button --}}
    @if($this->canWithdraw)
        <button
            wire:click="openModal"
            type="button"
            class="flex-1 sm:flex-none text-center px-4 py-2 text-xs sm:text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition"
        >
            💰 Withdraw Funds
        </button>
    @else
        @if(!auth()->user()->isVerified())
            <a
                href="{{ route('settings.verification') }}"
                class="flex-1 sm:flex-none text-center px-4 py-2 text-xs sm:text-sm font-medium text-yellow-700 bg-yellow-100 border border-yellow-300 rounded-lg hover:bg-yellow-200 transition"
            >
                🔒 Verify ID to Withdraw
            </a>
        @elseif($this->withdrawalAccounts->count() === 0)
            <a
                href="{{ route('settings.withdrawal-accounts') }}"
                class="flex-1 sm:flex-none text-center px-4 py-2 text-xs sm:text-sm font-medium text-blue-700 bg-blue-100 border border-blue-300 rounded-lg hover:bg-blue-200 transition"
            >
                🏦 Add Account to Withdraw
            </a>
        @elseif($this->availableBalance < config('withdrawal.min_amount', 10))
            <button
                type="button"
                disabled
                class="flex-1 sm:flex-none text-center px-4 py-2 text-xs sm:text-sm font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed"
            >
                💰 Insufficient Balance
            </button>
        @endif
    @endif

    {{-- Withdrawal Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showModal') }">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Backdrop --}}
                <div 
                    class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                    wire:click="closeModal"
                ></div>

                {{-- Modal --}}
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="submit">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                        Withdraw Funds from {{ $moneyBox->title }}
                                    </h3>

                                    <div class="space-y-4">
                                        {{-- Available Balance --}}
                                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                            <div class="text-sm text-green-700">Available Balance</div>
                                            <div class="text-2xl font-bold text-green-600">
                                                {{ $moneyBox->formatAmount($this->availableBalance) }}
                                            </div>
                                        </div>

                                        {{-- Amount Input --}}
                                        <div>
                                            <flux:input 
                                                wire:model.live="amount" 
                                                label="Withdrawal Amount" 
                                                type="number" 
                                                step="0.01"
                                                placeholder="Enter amount"
                                            />
                                            <p class="mt-1 text-xs text-gray-500">
                                                Minimum: {{ $moneyBox->formatAmount(config('withdrawal.min_amount', 10)) }}
                                            </p>
                                        </div>

                                        {{-- Fee Breakdown --}}
                                        @if($feeData)
                                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm space-y-1">
                                                <div class="flex justify-between text-gray-700">
                                                    <span>Amount:</span>
                                                    <span>{{ $moneyBox->formatAmount($feeData->amount) }}</span>
                                                </div>
                                                <div class="flex justify-between text-gray-700">
                                                    <span>Fee ({{ $feeData->feePercentage }}%):</span>
                                                    <span>- {{ $moneyBox->formatAmount($feeData->fee) }}</span>
                                                </div>
                                                <div class="flex justify-between font-bold text-blue-700 pt-1 border-t border-blue-300">
                                                    <span>You'll Receive:</span>
                                                    <span>{{ $moneyBox->formatAmount($feeData->netAmount) }}</span>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Withdrawal Account --}}
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Withdrawal Account</label>
                                            <select wire:model="withdrawalAccountId" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600">
                                                <option value="">Select account...</option>
                                                @foreach($this->withdrawalAccounts as $account)
                                                    <option value="{{ $account->id }}">
                                                        {{ $account->getDisplayName() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('withdrawalAccountId') 
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                            @enderror
                                            <a href="{{ route('settings.withdrawal-accounts') }}" class="text-xs text-blue-600 hover:underline mt-1 inline-block">
                                                Add new account
                                            </a>
                                        </div>

                                        {{-- Optional Note --}}
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Note (Optional)</label>
                                            <textarea 
                                                wire:model="note" 
                                                rows="2" 
                                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600"
                                                placeholder="Add a note about this withdrawal..."
                                            ></textarea>
                                            @error('note') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                            <flux:button type="submit" variant="primary">
                                Submit Withdrawal
                            </flux:button>
                            <flux:button type="button" wire:click="closeModal" variant="ghost">
                                Cancel
                            </flux:button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
