<div>
    <section class="w-full">
        @include('partials.settings-heading')

        <x-settings.layout :heading="__('Withdrawal Accounts')" :subheading="__('Manage your bank and mobile money accounts for withdrawals')">
            <div class="my-6 w-full space-y-6">

                @if (session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Verification Check --}}
                @if(!auth()->user()->isVerified() && !auth()->user()->idVerifications()->where('status', 'pending')->exists())
                    <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-6 mb-6">
                        <div class="flex items-center space-x-3">
                            <svg class="w-8 h-8 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h3 class="text-lg font-semibold text-yellow-900">ID Verification Required</h3>
                                <p class="text-sm text-yellow-700">You must verify your identity before adding withdrawal accounts.</p>
                                <a href="{{ route('settings.verification') }}" class="inline-flex items-center mt-2 text-sm font-medium text-yellow-900 hover:text-yellow-700">
                                    Verify your ID now →
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Add Account Button --}}
                    @if(!$showForm && $accounts->count() < 5)
                        <div class="flex justify-end mb-6">
                            <flux:button wire:click="showAddForm" variant="primary">
                                Add Withdrawal Account
                            </flux:button>
                        </div>
                    @endif

                    {{-- Add/Edit Form --}}
                    @if($showForm)
                        <div class="bg-white rounded-lg shadow-lg p-6 mb-6 border-2 border-green-500">
                            <flux:heading size="lg" class="mb-4">
                                {{ $editingId ? 'Edit Withdrawal Account' : 'Add Withdrawal Account' }}
                            </flux:heading>

                            <form wire:submit.prevent="save" class="space-y-4">
                                {{-- Account Type --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Account Type *</label>
                                    <select wire:model.live="accountType" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600">
                                        <option value="">Select account type...</option>
                                        @foreach($accountTypes as $type)
                                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                        @endforeach
                                    </select>
                                    @error('accountType') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                @if($accountType === 'mobile_money')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Mobile Network *</label>
                                        <select wire:model="mobileNetwork" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600">
                                            <option value="">Select network...</option>
                                            @foreach($mobileNetworks as $network)
                                                <option value="{{ $network->value }}">{{ $network->label() }}</option>
                                            @endforeach
                                        </select>
                                        @error('mobileNetwork') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <flux:input wire:model="accountNumber" label="Mobile Number *" type="text" placeholder="0XXXXXXXXX" />
                                    <flux:input wire:model="accountName" label="Account Name *" type="text" placeholder="Name registered on mobile money" />
                                @elseif($accountType === 'bank_account')
                                    <flux:input wire:model="bankName" label="Bank Name *" type="text" placeholder="e.g., GCB Bank, Ecobank Ghana" />
                                    <flux:input wire:model="accountNumber" label="Account Number *" type="text" placeholder="Enter bank account number" />
                                    <flux:input wire:model="accountName" label="Account Holder Name *" type="text" placeholder="Name as shown on account" />
                                    <flux:input wire:model="bankBranch" label="Branch (Optional)" type="text" placeholder="e.g., Accra Main Branch" />
                                @endif

                                @if($accountType)
                                    <div class="flex items-center space-x-2">
                                        <input type="checkbox" wire:model="isDefault" id="isDefault" class="rounded border-gray-300 text-green-600 focus:ring-green-600">
                                        <label for="isDefault" class="text-sm text-gray-700">Set as default withdrawal account</label>
                                    </div>
                                @endif

                                <div class="flex justify-end space-x-3 pt-4">
                                    <flux:button type="button" wire:click="cancel" variant="ghost">Cancel</flux:button>
                                    <flux:button type="submit" variant="primary">
                                        {{ $editingId ? 'Update Account' : 'Add Account' }}
                                    </flux:button>
                                </div>
                            </form>
                        </div>
                    @endif

                    {{-- Accounts List --}}
                    @if($accounts->count() > 0)
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <flux:heading size="lg">Your Withdrawal Accounts</flux:heading>
                                    <p class="text-sm text-gray-600 mt-1">{{ $accounts->count() }} {{ Str::plural('account', $accounts->count()) }} added</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($accounts as $account)
                                    <div class="relative bg-gradient-to-br {{ $account->account_type->value === 'mobile_money' ? 'from-blue-50 to-indigo-50' : 'from-green-50 to-emerald-50' }} rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border-2 {{ $account->is_default ? 'border-green-500' : 'border-transparent' }} {{ !$account->canBeModified() ? 'opacity-95' : '' }}">
                                        {{-- Badges --}}
                                        <div class="absolute top-0 right-0 flex flex-col items-end gap-1">
                                            @if($account->is_default)
                                                <div class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg">
                                                    ★ DEFAULT
                                                </div>
                                            @endif
                                            @if(!$account->canBeModified())
                                                <div class="bg-gray-600 text-white text-xs font-bold px-3 py-1 rounded-bl-lg flex items-center space-x-1">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span>LOCKED</span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="p-6">
                                            {{-- Icon and Type --}}
                                            <div class="flex items-center space-x-4 mb-4">
                                                <div class="flex-shrink-0 w-14 h-14 rounded-2xl flex items-center justify-center {{ $account->account_type->value === 'mobile_money' ? 'bg-blue-100' : 'bg-green-100' }} shadow-sm">
                                                    @if($account->account_type->value === 'mobile_money')
                                                        <svg class="w-7 h-7 {{ $account->account_type->value === 'mobile_money' ? 'text-blue-600' : 'text-green-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                        </svg>
                                                    @else
                                                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                        </svg>
                                                    @endif
                                                </div>

                                                <div class="flex-1 min-w-0">
                                                    <h3 class="text-lg font-bold text-gray-900 truncate">
                                                        @if($account->account_type->value === 'mobile_money')
                                                            {{ $account->mobile_network->label() }}
                                                        @else
                                                            {{ $account->bank_name }}
                                                        @endif
                                                    </h3>
                                                    <p class="text-xs text-gray-600 font-medium uppercase tracking-wide">
                                                        {{ $account->account_type->label() }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Account Details --}}
                                            <div class="space-y-2 mb-4">
                                                <div class="flex items-center space-x-2">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                                    </svg>
                                                    <span class="text-base font-mono font-semibold text-gray-700">{{ $account->maskAccountNumber() }}</span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    <span class="text-sm text-gray-600 truncate">{{ $account->account_name }}</span>
                                                </div>
                                            </div>

                                            {{-- Action Buttons --}}
                                            <div class="flex flex-wrap gap-2 pt-4 border-t border-gray-200">
                                                @if(!$account->is_default)
                                                    <button
                                                        wire:click="setAsDefault({{ $account->id }})"
                                                        wire:confirm="Set this as your default withdrawal account?"
                                                        class="flex-1 min-w-[100px] px-3 py-2 text-xs font-medium text-green-700 bg-white border border-green-300 rounded-lg hover:bg-green-50 transition-colors"
                                                    >
                                                        <span class="flex items-center justify-center space-x-1">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                            <span>Set Default</span>
                                                        </span>
                                                    </button>
                                                @endif

                                                @if($account->canBeModified())
                                                    <button
                                                        wire:click="edit({{ $account->id }})"
                                                        class="flex-1 min-w-[80px] px-3 py-2 text-xs font-medium text-blue-700 bg-white border border-blue-300 rounded-lg hover:bg-blue-50 transition-colors"
                                                    >
                                                        <span class="flex items-center justify-center space-x-1">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                            <span>Edit</span>
                                                        </span>
                                                    </button>

                                                    <button
                                                        wire:click="delete({{ $account->id }})"
                                                        wire:confirm="Are you sure you want to delete this account?"
                                                        class="flex-1 min-w-[80px] px-3 py-2 text-xs font-medium text-red-700 bg-white border border-red-300 rounded-lg hover:bg-red-50 transition-colors"
                                                    >
                                                        <span class="flex items-center justify-center space-x-1">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                            <span>Delete</span>
                                                        </span>
                                                    </button>
                                                @else
                                                    <div class="flex-1 px-3 py-2 text-xs font-medium text-gray-500 bg-gray-100 border border-gray-200 rounded-lg text-center">
                                                        <span class="flex items-center justify-center space-x-1">
                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                            </svg>
                                                            <span>Account Locked</span>
                                                        </span>
                                                        <p class="text-[10px] mt-1 text-gray-400">Has disbursement history</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="bg-white rounded-lg shadow p-12 text-center">
                            <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <flux:heading size="lg" class="mb-2">No Withdrawal Accounts</flux:heading>
                            <p class="text-gray-600 mb-6">Add a bank account or mobile money account to receive withdrawals</p>
                            <flux:button wire:click="showAddForm" variant="primary">
                                Add Your First Account
                            </flux:button>
                        </div>
                    @endif
                @endif

            </div>
        </x-settings.layout>
    </section>
</div>
