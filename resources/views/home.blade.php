<x-layouts.guest>
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-br from-green-600 via-green-700 to-green-900 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <svg class="absolute w-full h-full" xmlns="http://www.w3.org/2000/svg">
                <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                    <circle cx="20" cy="20" r="1" fill="white"/>
                </pattern>
                <rect width="100%" height="100%" fill="url(#grid)"/>
            </svg>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Hero Content -->
                <div class="text-white">
                    <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                        Collect Contributions <span class="text-green-200">Made Easy</span>
                    </h1>
                    <p class="text-xl md:text-2xl mb-8 text-green-100">
                        Create a money box for any occasion. Share with friends and family. Track contributions in real-time.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-green-700 font-bold rounded-lg hover:bg-green-50 transition text-center shadow-lg">
                            Create Your Money Box
                        </a>
                        <a href="{{ route('browse') }}" class="px-8 py-4 bg-green-500/20 backdrop-blur-sm text-white font-bold rounded-lg hover:bg-green-500/30 transition text-center border-2 border-white/30">
                            Browse Money Boxes
                        </a>
                    </div>

                    <!-- Stats -->
                    <div class="mt-12 grid grid-cols-3 gap-8">
                        <div>
                            <div class="text-3xl font-bold text-white">{{ \App\Models\MoneyBox::count() }}+</div>
                            <div class="text-green-200">Money Boxes</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-white">{{ \App\Models\Contribution::count() }}+</div>
                            <div class="text-green-200">Contributions</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-white">{{ \App\Models\User::count() }}+</div>
                            <div class="text-green-200">Users</div>
                        </div>
                    </div>
                </div>

                <!-- Hero Image/Illustration -->
                <div class="relative">
                    <div class="relative z-10 bg-white/10 backdrop-blur-md rounded-2xl p-8 shadow-2xl border border-white/20">
                        <div class="bg-white rounded-xl p-6 shadow-lg">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-600">Example Money Box</div>
                                    <div class="text-xs text-gray-500">Wedding Gift for Sarah</div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="text-gray-600">Progress</span>
                                    <span class="font-bold text-gray-900">75%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-green-500 to-green-600 h-3 rounded-full" style="width: 75%"></div>
                                </div>
                            </div>
                            <div class="flex justify-between items-end">
                                <div>
                                    <div class="text-2xl font-bold text-gray-900">$7,500</div>
                                    <div class="text-sm text-gray-500">of $10,000 goal</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-gray-900">42</div>
                                    <div class="text-sm text-gray-500">contributors</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Decorative Elements -->
                    <div class="absolute -top-4 -right-4 w-24 h-24 bg-green-400 rounded-full blur-3xl opacity-50"></div>
                    <div class="absolute -bottom-4 -left-4 w-32 h-32 bg-green-300 rounded-full blur-3xl opacity-50"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Why Choose MyMoneyBox?</h2>
                <p class="text-xl text-gray-600">Everything you need to collect contributions seamlessly</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white rounded-xl p-8 shadow-lg hover:shadow-xl transition">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Quick Setup</h3>
                    <p class="text-gray-600">Create your money box in minutes. No complicated forms or lengthy processes.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white rounded-xl p-8 shadow-lg hover:shadow-xl transition">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Secure Payments</h3>
                    <p class="text-gray-600">Accept contributions through trusted payment providers with bank-level security.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white rounded-xl p-8 shadow-lg hover:shadow-xl transition">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Easy Sharing</h3>
                    <p class="text-gray-600">Share via link, QR code, or social media. Reach your contributors effortlessly.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Money Boxes -->
    <div id="featured" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-12">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Featured Money Boxes</h2>
                    <p class="text-xl text-gray-600">Support these amazing causes</p>
                </div>
                <a href="{{ route('browse') }}" class="text-green-600 hover:text-green-700 font-semibold flex items-center">
                    View All
                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            @if($featuredMoneyBoxes->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($featuredMoneyBoxes as $moneyBox)
                        <x-money-box-card :moneyBox="$moneyBox" />
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-gray-50 rounded-lg">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No money boxes yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Be the first to create one!</p>
                    <div class="mt-6">
                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            Create Money Box
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 py-16">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Ready to Get Started?
            </h2>
            <p class="text-xl text-green-100 mb-8">
                Join thousands of people collecting contributions for their special moments
            </p>
            <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-4 bg-white text-green-700 font-bold rounded-lg hover:bg-green-50 transition shadow-lg">
                Create Your Free Account
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </a>
        </div>
    </div>
</x-layouts.guest>
