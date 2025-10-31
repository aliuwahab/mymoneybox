<x-layouts.guest>
    <div class="min-h-screen bg-gradient-to-br from-yellow-50 via-orange-50 to-pink-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-yellow-500 rounded-full mb-4 shadow-lg">
                    <span class="text-4xl">🎁</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Piggy Someone!</h1>
                <p class="text-gray-600">Enter their piggy code to send them a gift</p>
            </div>

            <!-- Error Message -->
            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Lookup Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <form action="{{ route('piggy.find') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6">
                        <label for="piggy_code" class="block text-sm font-medium text-gray-700 mb-2">
                            Piggy Code
                        </label>
                        <input 
                            type="text" 
                            name="piggy_code" 
                            id="piggy_code"
                            placeholder="e.g., ABCD5"
                            maxlength="10"
                            required
                            class="w-full px-4 py-3 text-center text-2xl font-bold uppercase border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent tracking-wider"
                            oninput="this.value = this.value.toUpperCase()"
                        >
                        @error('piggy_code')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button 
                        type="submit" 
                        class="w-full bg-gradient-to-r from-yellow-500 to-orange-500 text-white font-bold py-4 px-6 rounded-lg hover:from-yellow-600 hover:to-orange-600 transition shadow-lg"
                    >
                        Find Piggy Box
                    </button>
                </form>

                <!-- Info -->
                <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
                    <p class="text-sm text-gray-600 text-center">
                        <span class="font-semibold">💡 Tip:</span> Ask your friend for their unique piggy code to send them a gift!
                    </p>
                </div>
            </div>

            <!-- Back Link -->
            <div class="mt-6 text-center">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900">
                    ← Back to Home
                </a>
            </div>
        </div>
    </div>
</x-layouts.guest>
