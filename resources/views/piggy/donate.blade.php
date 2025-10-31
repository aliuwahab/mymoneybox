<x-layouts.guest>
    <div class="min-h-screen bg-gradient-to-br from-yellow-50 via-orange-50 to-pink-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">
            <!-- User Info Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
                <div class="flex items-center space-x-6 mb-6">
                    <!-- Profile Photo Placeholder -->
                    <div class="flex-shrink-0">
                        <div class="w-20 h-20 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg">
                            <span class="text-3xl font-bold text-white">{{ $user->initials() }}</span>
                        </div>
                    </div>
                    
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                        <p class="text-gray-600">Send them a gift!</p>
                        <div class="mt-2 inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                            <span class="mr-1">🎁</span> Code: <span class="ml-1 font-bold">{{ $user->piggy_code }}</span>
                        </div>
                    </div>
                </div>

                <!-- Piggy Box Stats -->
                <div class="grid grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg">
                    <div>
                        <div class="text-2xl font-bold text-gray-900">
                            {{ $piggyBox->formatAmount($piggyBox->total_received) }}
                        </div>
                        <div class="text-sm text-gray-600">Total Received</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">
                            {{ $piggyBox->donation_count }}
                        </div>
                        <div class="text-sm text-gray-600">{{ Str::plural('Gift', $piggyBox->donation_count) }}</div>
                    </div>
                </div>
            </div>

            <!-- Donation Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Send a Gift</h2>

                @if (session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('piggy.donate', $user) }}" class="space-y-6">
                    @csrf

                    <!-- Amount -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Amount ({{ $piggyBox->getCurrencySymbol() }}) <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            name="amount"
                            id="amount"
                            step="0.01"
                            min="0.01"
                            required
                            placeholder="0.00"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent text-lg"
                            value="{{ old('amount') }}"
                        />
                        @error('amount')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Donor Name -->
                    <div>
                        <label for="donor_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Your Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="donor_name"
                            id="donor_name"
                            required
                            placeholder="John Doe"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                            value="{{ old('donor_name', auth()->user()->name ?? '') }}"
                        />
                        @error('donor_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Hidden Email Field (required by validation but not shown) -->
                    <input type="hidden" name="donor_email" value="noreply@mymoneybox.com">

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full bg-gradient-to-r from-yellow-500 to-orange-500 text-white font-bold py-4 px-6 rounded-lg hover:from-yellow-600 hover:to-orange-600 transition shadow-lg transform hover:scale-105"
                    >
                        🎁 Send Gift
                    </button>
                </form>
            </div>

            <!-- Back Link -->
            <div class="mt-6 text-center">
                <a href="{{ route('piggy.lookup') }}" class="text-gray-600 hover:text-gray-900">
                    ← Try Different Code
                </a>
            </div>
        </div>
    </div>
</x-layouts.guest>
