<div>
    {{-- Withdraw Button --}}
    @if($this->canWithdraw)
        <button
            wire:click="openModal"
            type="button"
            class="btn btn-primary"
        >
            Withdraw Funds
        </button>
    @else
        @if(!auth()->user()->isVerified())
            <a
                href="{{ route('settings.verification') }}"
                class="btn"
                style="background:#FEF3C7;color:#92400E;border-color:#FDE68A"
            >
                Verify ID to Withdraw
            </a>
        @elseif($this->withdrawalAccounts->count() === 0)
            <a
                href="{{ route('settings.withdrawal-accounts') }}"
                class="btn"
            >
                Add Account to Withdraw
            </a>
        @else
            <button type="button" disabled class="btn opacity-40 cursor-not-allowed">
                Insufficient Balance
            </button>
        @endif
    @endif

    {{-- Withdrawal Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showModal') }">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div class="fixed inset-0 bg-black/40" wire:click="closeModal"></div>

                <div class="relative bg-white rounded-[16px] shadow-2xl w-full max-w-md overflow-hidden">
                    <form wire:submit.prevent="submit">
                        <div class="px-6 pt-6 pb-2">
                            <div class="flex items-center justify-between mb-5">
                                <h3 class="text-[16px] font-semibold text-[#15140F]">
                                    Withdraw from Piggy Wallet
                                </h3>
                                <button type="button" wire:click="closeModal" class="text-[#9C998F] hover:text-[#15140F] transition">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            <div class="space-y-4">
                                {{-- Available Balance --}}
                                <div class="bg-primary-50 border border-primary-200/60 rounded-[10px] px-4 py-3">
                                    <div class="text-[11.5px] text-primary-700 mb-0.5">Available balance</div>
                                    <div class="text-[24px] font-semibold tracking-tight tnum text-primary-700">
                                        {{ $this->piggyBox?->formatAmount($this->availableBalance) }}
                                    </div>
                                </div>

                                {{-- Amount --}}
                                <div class="grid gap-1.5">
                                    <label class="text-[13px] font-medium text-[#6B6862]">Amount</label>
                                    <input
                                        type="number"
                                        wire:model.live="amount"
                                        step="0.01"
                                        min="{{ config('withdrawal.min_amount', 10) }}"
                                        placeholder="0.00"
                                        class="@error('amount') border-red-400 ring-1 ring-red-400/20 @enderror"
                                    />
                                    <p class="text-[11.5px] text-[#9C998F]">
                                        Min {{ $this->piggyBox?->formatAmount(config('withdrawal.min_amount', 10)) }}
                                    </p>
                                    @error('amount')
                                        <p class="text-[12px] text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Live Fee Breakdown --}}
                                @if($feeData)
                                    <div class="bg-[#F3F1EB] border border-[#E6E3DC] rounded-[10px] px-4 py-3 text-[13px] space-y-1.5">
                                        <div class="flex justify-between text-[#6B6862]">
                                            <span>Withdrawal amount</span>
                                            <span class="tnum">{{ $this->piggyBox?->formatAmount($feeData->amount) }}</span>
                                        </div>
                                        <div class="flex justify-between text-[#6B6862]">
                                            <span>Platform fee ({{ $feeData->feePercentage }}%)</span>
                                            <span class="tnum text-red-600">− {{ $this->piggyBox?->formatAmount($feeData->fee) }}</span>
                                        </div>
                                        <div class="flex justify-between font-semibold text-[#15140F] pt-1.5 border-t border-[#E6E3DC]">
                                            <span>You'll receive</span>
                                            <span class="tnum text-primary-700">{{ $this->piggyBox?->formatAmount($feeData->netAmount) }}</span>
                                        </div>
                                    </div>
                                @endif

                                {{-- Account --}}
                                <div class="grid gap-1.5">
                                    <label class="text-[13px] font-medium text-[#6B6862]">Withdrawal account</label>
                                    <select wire:model="withdrawalAccountId" class="@error('withdrawalAccountId') border-red-400 ring-1 ring-red-400/20 @enderror">
                                        <option value="">Select account…</option>
                                        @foreach($this->withdrawalAccounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->getDisplayName() }}</option>
                                        @endforeach
                                    </select>
                                    @error('withdrawalAccountId')
                                        <p class="text-[12px] text-red-600">{{ $message }}</p>
                                    @enderror
                                    <a href="{{ route('settings.withdrawal-accounts') }}" class="text-[12px] text-primary-600 hover:underline">Add new account</a>
                                </div>

                                {{-- Note --}}
                                <div class="grid gap-1.5">
                                    <label class="text-[13px] font-medium text-[#6B6862]">Note <span class="font-normal text-[#9C998F]">(optional)</span></label>
                                    <textarea wire:model="note" rows="2" placeholder="Add a note…" class="@error('note') border-red-400 ring-1 ring-red-400/20 @enderror"></textarea>
                                    @error('note') <p class="text-[12px] text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-[#FAFAF7] border-t border-[#E6E3DC] flex items-center justify-end gap-2.5">
                            <button type="button" wire:click="closeModal" class="btn">Cancel</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="submit">
                                <span wire:loading.remove wire:target="submit">Submit request</span>
                                <span wire:loading wire:target="submit">Submitting…</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
