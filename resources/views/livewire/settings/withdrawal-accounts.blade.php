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
                        <div class="bg-white rounded-lg shadow">
                            <div class="p-6 border-b border-gray-200">
                                <flux:heading size="lg">Your Withdrawal Accounts</flux:heading>
                                <p class="text-sm text-gray-600 mt-1">You have {{ $accounts->count() }} withdrawal {{ Str::plural('account', $accounts->count()) }}</p>
                            </div>
                            
                            <div class="divide-y divide-gray-200">
                                @foreach($accounts as $account)
                                    <div class="p-6 hover:bg-gray-50 transition-colors">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-3 mb-2">
                                                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center {{ $account->account_type->value === 'mobile_money' ? 'bg-blue-100' : 'bg-green-100' }}">
                                                        @if($account->account_type->value === 'mobile_money')
                                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                            </svg>
                                                        @else
                                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                            </svg>
                                                        @endif
                                                    </div>

                                                    <div>
                                                        <div class="flex items-center space-x-2">
                                                            <h4 class="text-base font-semibold text-gray-900">
                                                                @if($account->account_type->value === 'mobile_money')
                                                                    {{ $account->mobile_network->label() }}
                                                                @else
                                                                    {{ $account->bank_name }}
                                                                @endif
                                                            </h4>
                                                            @if($account->is_default)
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                                    Default
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <p class="text-sm text-gray-600">{{ $account->maskAccountNumber() }}</p>
                                                        <p class="text-sm text-gray-500">{{ $account->account_name }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex items-center space-x-2">
                                                @if(!$account->is_default)
                                                    <flux:button 
                                                        size="sm" 
                                                        variant="ghost"
                                                        wire:click="setAsDefault({{ $account->id }})"
                                                        wire:confirm="Set this as your default withdrawal account?"
                                                    >
                                                        Set Default
                                                    </flux:button>
                                                @endif
                                                
                                                <flux:button 
                                                    size="sm" 
                                                    variant="ghost"
                                                    wire:click="edit({{ $account->id }})"
                                                >
                                                    Edit
                                                </flux:button>
                                                
                                                <flux:button 
                                                    size="sm" 
                                                    variant="danger"
                                                    wire:click="delete({{ $account->id }})"
                                                    wire:confirm="Are you sure you want to delete this account?"
                                                >
                                                    Delete
                                                </flux:button>
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
