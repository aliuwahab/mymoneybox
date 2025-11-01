<x-layouts.guest>
    <div class="min-h-screen bg-gray-50">
        <!-- Header with your branding -->
        <div class="bg-white border-b shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('home') }}" class="text-2xl font-bold text-primary-600">
                            MyPiggyBox
                        </a>
                        <span class="text-gray-400">|</span>
                        <span class="text-gray-600">Secure Checkout</span>
                    </div>
                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>Secured by TrendiPay</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment iframe container -->
        <div class="max-w-5xl mx-auto px-4 py-6">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden" style="height: calc(100vh - 150px);">
                <iframe 
                    src="{{ $paymentUrl }}" 
                    class="w-full h-full border-0"
                    title="Secure Payment"
                    sandbox="allow-same-origin allow-scripts allow-forms allow-top-navigation"
                    allow="payment"
                ></iframe>
            </div>
            
            <!-- Trust indicators -->
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-500 flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Your payment is protected with bank-level security</span>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Listen for messages from the iframe (if TrendiPay supports postMessage)
        window.addEventListener('message', function(event) {
            // Verify the origin for security
            if (event.origin === 'https://test-checkout.trendipay.com' || event.origin === 'https://checkout.trendipay.com') {
                if (event.data.type === 'payment_complete') {
                    // Redirect to success page
                    window.location.href = '{{ route("home") }}';
                }
            }
        });
    </script>
</x-layouts.guest>
