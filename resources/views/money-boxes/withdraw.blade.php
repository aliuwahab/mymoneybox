<x-layouts.app>
    @php
        $sym = auth()->user()->country?->currency_symbol ?? '₵';
        $feePercent = $moneyBox->getEffectiveFeePercentage();
        $minFee = config('withdrawal.min_fee', 2);
        $maxFee = config('withdrawal.max_fee', 20);
        $minAmount = config('withdrawal.min_amount', 10);
    @endphp

    <div class="page-wrap max-w-[720px] mx-auto w-full">

        {{-- Back --}}
        <div class="mb-6">
            <a href="{{ route('money-boxes.show', $moneyBox) }}"
               class="inline-flex items-center gap-1.5 text-[13px] text-[#6B6862] hover:text-[#15140F] transition-colors duration-100"
               wire:navigate>
                <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5m0 0 7 7m-7-7 7-7"/>
                </svg>
                Back to box
            </a>
        </div>

        {{-- Header card --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 grid place-items-center flex-none">
                        <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 6v12M9 9l3-3 3 3M9 15l3 3 3-3"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h1 class="text-[16px] font-semibold text-[#15140F] mb-0.5">Withdraw Funds</h1>
                        <p class="tiny">From: {{ $moneyBox->title }}</p>
                    </div>
                </div>

                {{-- Balance --}}
                <div class="mt-4 bg-primary-50 border border-primary-200/60 rounded-[8px] px-4 py-3.5">
                    <div class="tiny mb-0.5 text-primary-700">Available balance</div>
                    <div class="text-[26px] font-semibold tracking-tight tnum text-primary-700">
                        {{ $sym }}{{ number_format($availableBalance, 2) }}
                    </div>
                    <div class="text-[11.5px] text-primary-600 mt-0.5">ready to withdraw</div>
                </div>
            </div>
        </div>

        {{-- Form card --}}
        <div class="card">
            <div class="card-body">
                <form
                    action="{{ route('money-boxes.withdraw.store', $moneyBox) }}"
                    method="POST"
                    class="space-y-5"
                    x-data="{
                        amount: '{{ old('amount', '') }}',
                        feePercent: {{ $feePercent }},
                        minFee: {{ $minFee }},
                        maxFee: {{ $maxFee }},
                        sym: '{{ $sym }}',
                        get fee() {
                            const a = parseFloat(this.amount);
                            if (!a || a <= 0) return null;
                            let f = (a * this.feePercent) / 100;
                            f = Math.max(this.minFee, Math.min(this.maxFee, f));
                            return Math.round(f * 100) / 100;
                        },
                        get net() {
                            const a = parseFloat(this.amount);
                            if (!a || a <= 0 || this.fee === null) return null;
                            return Math.round((a - this.fee) * 100) / 100;
                        },
                        fmt(n) { return this.sym + n.toLocaleString('en', {minimumFractionDigits:2, maximumFractionDigits:2}); }
                    }"
                >
                    @csrf

                    {{-- Amount --}}
                    <div class="grid gap-1.5">
                        <label for="amount" class="text-[13px] font-medium text-[#6B6862]">
                            Amount <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            id="amount"
                            name="amount"
                            step="0.01"
                            min="{{ $minAmount }}"
                            max="{{ $availableBalance }}"
                            x-model="amount"
                            value="{{ old('amount') }}"
                            required
                            placeholder="0.00"
                            class="@error('amount') border-red-400 ring-1 ring-red-400/20 @enderror"
                        />
                        <p class="text-[11.5px] text-[#9C998F]">
                            Min {{ $sym }}{{ number_format($minAmount, 2) }} ·
                            Max {{ $sym }}{{ number_format($availableBalance, 2) }}
                        </p>
                        @error('amount')
                            <p class="text-[12px] text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Live fee breakdown --}}
                    <template x-if="fee !== null">
                        <div class="bg-[#F3F1EB] border border-[#E6E3DC] rounded-[10px] px-4 py-3 text-[13px] space-y-1.5">
                            <div class="flex justify-between text-[#6B6862]">
                                <span>Withdrawal amount</span>
                                <span class="tnum" x-text="fmt(parseFloat(amount))"></span>
                            </div>
                            <div class="flex justify-between text-[#6B6862]">
                                <span>Platform fee (<span x-text="feePercent"></span>%)</span>
                                <span class="tnum text-red-600" x-text="'− ' + fmt(fee)"></span>
                            </div>
                            <div class="flex justify-between font-semibold text-[#15140F] pt-1.5 border-t border-[#E6E3DC]">
                                <span>You'll receive</span>
                                <span class="tnum text-primary-700" x-text="fmt(net)"></span>
                            </div>
                        </div>
                    </template>

                    {{-- Static hint when no amount yet --}}
                    <template x-if="fee === null">
                        <div class="bg-[#ECEAE3] rounded-[8px] px-4 py-3 text-[12.5px] text-[#6B6862]">
                            A platform fee of <strong class="text-[#15140F]">{{ $feePercent }}%</strong> applies
                            (min {{ $sym }}{{ number_format($minFee, 2) }}, max {{ $sym }}{{ number_format($maxFee, 2) }}).
                            Enter an amount above to see the exact breakdown.
                        </div>
                    </template>

                    {{-- Account --}}
                    <div class="grid gap-1.5">
                        <label for="withdrawal_account_id" class="text-[13px] font-medium text-[#6B6862]">
                            Withdrawal account <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="withdrawal_account_id"
                            name="withdrawal_account_id"
                            required
                            class="@error('withdrawal_account_id') border-red-400 ring-1 ring-red-400/20 @enderror"
                        >
                            <option value="">Select account…</option>
                            @foreach($withdrawalAccounts as $account)
                                <option value="{{ $account->id }}" {{ old('withdrawal_account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->getDisplayName() }}
                                </option>
                            @endforeach
                        </select>
                        @error('withdrawal_account_id')
                            <p class="text-[12px] text-red-600">{{ $message }}</p>
                        @enderror
                        <a href="{{ route('settings.withdrawal-accounts') }}" class="text-[12px] text-primary-600 hover:underline" wire:navigate>
                            Add new account
                        </a>
                    </div>

                    {{-- Note --}}
                    <div class="grid gap-1.5">
                        <label for="note" class="text-[13px] font-medium text-[#6B6862]">
                            Note <span class="text-[#9C998F] font-normal">(optional)</span>
                        </label>
                        <textarea
                            id="note"
                            name="note"
                            rows="3"
                            placeholder="Add a note about this withdrawal…"
                            class="@error('note') border-red-400 ring-1 ring-red-400/20 @enderror"
                        >{{ old('note') }}</textarea>
                        @error('note')
                            <p class="text-[12px] text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2.5 pt-1">
                        <button type="submit" class="btn btn-primary">
                            Submit withdrawal request
                        </button>
                        <a href="{{ route('money-boxes.show', $moneyBox) }}" class="btn" wire:navigate>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
