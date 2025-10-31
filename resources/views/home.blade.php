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
                    <div class="inline-block mb-4 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-sm font-semibold border border-white/20">
                        ✨ The Modern Way to Collect & Give
                    </div>
                    <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                        Collect Gifts & Contributions <span class="text-green-200">The Easy Way</span>
                    </h1>
                    <p class="text-xl md:text-2xl mb-8 text-green-100 leading-relaxed">
                        From weddings to charities, birthdays to tithes—everyone contributes with just a link, QR code, or Piggy Number. <strong>Transparent. Accessible. Trusted by thousands.</strong>
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('piggy.lookup') }}" class="px-8 py-4 bg-yellow-500 text-white font-bold rounded-lg hover:bg-yellow-600 transition text-center shadow-lg">
                            🎁 Piggy Someone
                        </a>
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-green-700 font-bold rounded-lg hover:bg-green-50 transition text-center shadow-lg">
                            Create Your Piggy Box
                        </a>
                        <a href="{{ route('browse') }}" class="px-8 py-4 bg-green-500/20 backdrop-blur-sm text-white font-bold rounded-lg hover:bg-green-500/30 transition text-center border-2 border-white/30">
                            Browse Piggy Boxes
                        </a>
                    </div>

                    <!-- Stats -->
                    <div class="mt-12 grid grid-cols-3 gap-8">
                        <div>
                            <div class="text-3xl font-bold text-white">{{ \App\Models\MoneyBox::count() }}+</div>
                            <div class="text-green-200">Piggy Boxes</div>
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

                <!-- Hero Image/Illustration - 2x2 Grid -->
                <div class="relative">
                    <!-- Trending Header -->
                    <div class="mb-6 text-center">
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-400/90 backdrop-blur-sm rounded-full shadow-lg border-2 border-yellow-300">
                            <svg class="w-5 h-5 text-white animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="text-sm font-bold text-white">🔥 Trending Piggies</span>
                            <svg class="w-5 h-5 text-white animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Wedding Card -->
                        <div class="relative">
                            <div class="relative z-10 bg-white/10 backdrop-blur-md rounded-xl p-3 shadow-xl border border-white/20">
                                <div class="mb-2 flex justify-center">
                                    <div class="relative">
                                        <img 
                                            src="https://images.unsplash.com/photo-1511285560929-80b456fea0bc?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                                            alt="Happy bride"
                                            class="w-16 h-16 rounded-full object-cover border-3 border-white shadow-lg"
                                        />
                                        <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center border-2 border-white shadow-lg">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-white rounded-lg p-3 shadow-lg">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <div class="w-8 h-8 bg-pink-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-xs font-medium text-gray-600">Wedding</div>
                                            <div class="text-xs text-gray-500">Sarah & John</div>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="flex justify-between text-xs mb-1">
                                            <span class="text-gray-600">Progress</span>
                                            <span class="font-bold text-gray-900">75%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-gradient-to-r from-pink-500 to-pink-600 h-1.5 rounded-full" style="width: 75%"></div>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-end">
                                        <div>
                                            <div class="text-base font-bold text-gray-900">$7,500</div>
                                            <div class="text-xs text-gray-500">of $10,000</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-bold text-gray-900">42</div>
                                            <div class="text-xs text-gray-500">donors</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Church Tithing Card -->
                        <div class="relative">
                            <div class="relative z-10 bg-white/10 backdrop-blur-md rounded-xl p-3 shadow-xl border border-white/20">
                                <div class="mb-2 flex justify-center">
                                    <div class="relative">
                                        <img 
                                            src="https://images.unsplash.com/photo-1438232992991-995b7058bbb3?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                                            alt="Church building"
                                            class="w-16 h-16 rounded-full object-cover border-3 border-white shadow-lg"
                                        />
                                        <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-purple-500 rounded-full flex items-center justify-center border-2 border-white shadow-lg">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-white rounded-lg p-3 shadow-lg">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-xs font-medium text-gray-600">Tithing</div>
                                            <div class="text-xs text-gray-500">Grace Church</div>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="flex justify-between text-xs mb-1">
                                            <span class="text-gray-600">This Month</span>
                                            <span class="font-bold text-gray-900">65%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-1.5 rounded-full" style="width: 65%"></div>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-end">
                                        <div>
                                            <div class="text-base font-bold text-gray-900">$3,250</div>
                                            <div class="text-xs text-gray-500">of $5,000</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-bold text-gray-900">28</div>
                                            <div class="text-xs text-gray-500">givers</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Gift Appreciation Card -->
                        <div class="relative">
                            <div class="relative z-10 bg-white/10 backdrop-blur-md rounded-xl p-3 shadow-xl border border-white/20">
                                <div class="mb-2 flex justify-center">
                                    <div class="relative">
                                        <img 
                                            src="https://images.unsplash.com/photo-1513885535751-8b9238bd345a?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                                            alt="Gift box"
                                            class="w-16 h-16 rounded-full object-cover border-3 border-white shadow-lg"
                                        />
                                        <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-yellow-500 rounded-full flex items-center justify-center border-2 border-white shadow-lg">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M8 2a1 1 0 000 2h2a1 1 0 100-2H8z"/>
                                                <path d="M3 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v6h-4.586l1.293-1.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L10.414 13H15v3a2 2 0 01-2 2H5a2 2 0 01-2-2V5zM15 11h2a1 1 0 110 2h-2v-2z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-white rounded-lg p-3 shadow-lg">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M8 2a1 1 0 000 2h2a1 1 0 100-2H8z"/>
                                                <path d="M3 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v6h-4.586l1.293-1.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L10.414 13H15v3a2 2 0 01-2 2H5a2 2 0 01-2-2V5zM15 11h2a1 1 0 110 2h-2v-2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-xs font-medium text-gray-600">Gift</div>
                                            <div class="text-xs text-gray-500">For Emma</div>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="flex justify-between text-xs mb-1">
                                            <span class="text-gray-600">Collected</span>
                                            <span class="font-bold text-gray-900">45%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 h-1.5 rounded-full" style="width: 45%"></div>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-end">
                                        <div>
                                            <div class="text-base font-bold text-gray-900">$450</div>
                                            <div class="text-xs text-gray-500">of $1,000</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-bold text-gray-900">18</div>
                                            <div class="text-xs text-gray-500">givers</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Medical Care Card -->
                        <div class="relative">
                            <div class="relative z-10 bg-white/10 backdrop-blur-md rounded-xl p-3 shadow-xl border border-white/20">
                                <div class="mb-2 flex justify-center">
                                    <div class="relative">
                                        <img 
                                            src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                                            alt="Medical care"
                                            class="w-16 h-16 rounded-full object-cover border-3 border-white shadow-lg"
                                        />
                                        <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-red-500 rounded-full flex items-center justify-center border-2 border-white shadow-lg">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-white rounded-lg p-3 shadow-lg">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-xs font-medium text-gray-600">Medical</div>
                                            <div class="text-xs text-gray-500">For Michael</div>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="flex justify-between text-xs mb-1">
                                            <span class="text-gray-600">Raised</span>
                                            <span class="font-bold text-gray-900">82%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-gradient-to-r from-red-500 to-red-600 h-1.5 rounded-full" style="width: 82%"></div>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-end">
                                        <div>
                                            <div class="text-base font-bold text-gray-900">$16,400</div>
                                            <div class="text-xs text-gray-500">of $20,000</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-bold text-gray-900">156</div>
                                            <div class="text-xs text-gray-500">donors</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Decorative Elements -->
                    <div class="absolute -top-4 -right-4 w-24 h-24 bg-green-400 rounded-full blur-3xl opacity-50"></div>
                    <div class="absolute -bottom-4 -left-4 w-32 h-32 bg-purple-300 rounded-full blur-3xl opacity-50"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Why Thousands Choose MyPiggyBox</h2>
                <p class="text-xl text-gray-600">Modernize the age-old practice of communal contributions with complete transparency and security</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white rounded-xl p-8 shadow-lg hover:shadow-xl transition">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Lightning Fast Setup</h3>
                    <p class="text-gray-600">Create your PiggyBox in under 2 minutes. No complicated forms, no hassle—just simple, intuitive design.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white rounded-xl p-8 shadow-lg hover:shadow-xl transition">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Bank-Level Security</h3>
                    <p class="text-gray-600">Accept payments via cards and mobile money with enterprise-grade encryption. Your contributors' data is always protected.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white rounded-xl p-8 shadow-lg hover:shadow-xl transition">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Share Anywhere, Anytime</h3>
                    <p class="text-gray-600">QR codes for events, unique links for social media, or your personal Piggy Number—reach contributors on any platform instantly.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Use Cases Section -->
    <div class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Perfect For Every Occasion</h2>
                <p class="text-xl text-gray-600">From personal milestones to community causes—MyPiggyBox adapts to your needs</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Use Case 1 -->
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-pink-500 to-pink-600 rounded-2xl opacity-0 group-hover:opacity-10 transition-opacity"></div>
                    <div class="relative bg-white border-2 border-gray-100 rounded-2xl p-6 hover:border-pink-200 transition">
                        <div class="text-4xl mb-4">💍</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Weddings & Celebrations</h3>
                        <p class="text-gray-600">Guests scan QR codes at tables to send gifts instantly. Modern, seamless, and memorable.</p>
                    </div>
                </div>

                <!-- Use Case 2 -->
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl opacity-0 group-hover:opacity-10 transition-opacity"></div>
                    <div class="relative bg-white border-2 border-gray-100 rounded-2xl p-6 hover:border-purple-200 transition">
                        <div class="text-4xl mb-4">⛪</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Church & Tithes</h3>
                        <p class="text-gray-600">A permanent PiggyBox for offerings, tithes, and donations. Transparent and convenient for your congregation.</p>
                    </div>
                </div>

                <!-- Use Case 3 -->
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl opacity-0 group-hover:opacity-10 transition-opacity"></div>
                    <div class="relative bg-white border-2 border-gray-100 rounded-2xl p-6 hover:border-blue-200 transition">
                        <div class="text-4xl mb-4">🎓</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Group Events & Trips</h3>
                        <p class="text-gray-600">Friends split costs for dinners, trips, or activities. Everyone contributes their share hassle-free.</p>
                    </div>
                </div>

                <!-- Use Case 4 -->
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-red-600 rounded-2xl opacity-0 group-hover:opacity-10 transition-opacity"></div>
                    <div class="relative bg-white border-2 border-gray-100 rounded-2xl p-6 hover:border-red-200 transition">
                        <div class="text-4xl mb-4">🏥</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Medical Fundraisers</h3>
                        <p class="text-gray-600">Rally community support for medical needs. Track progress and thank donors in real-time.</p>
                    </div>
                </div>

                <!-- Use Case 5 -->
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-green-500 to-green-600 rounded-2xl opacity-0 group-hover:opacity-10 transition-opacity"></div>
                    <div class="relative bg-white border-2 border-gray-100 rounded-2xl p-6 hover:border-green-200 transition">
                        <div class="text-4xl mb-4">💰</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Tips & Gratuities</h3>
                        <p class="text-gray-600">Service workers receive tips via their Piggy Number or QR code. Digital tipping made simple.</p>
                    </div>
                </div>

                <!-- Use Case 6 -->
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-2xl opacity-0 group-hover:opacity-10 transition-opacity"></div>
                    <div class="relative bg-white border-2 border-gray-100 rounded-2xl p-6 hover:border-yellow-200 transition">
                        <div class="text-4xl mb-4">🎯</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Community Projects</h3>
                        <p class="text-gray-600">Political parties, clubs, and societies raise funds with clarity and accountability.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Piggy Boxes -->
    <div id="featured" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-12">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Featured Piggy Boxes</h2>
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
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No piggy boxes yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Be the first to create one!</p>
                    <div class="mt-6">
                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            Create Piggy Box
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- How It Works Section -->
    <div class="py-20 bg-gradient-to-br from-green-50 to-green-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Get Started in 3 Simple Steps</h2>
                <p class="text-xl text-gray-600">From creation to collection—it takes less than 5 minutes</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <!-- Step 1 -->
                <div class="text-center">
                    <div class="relative inline-flex items-center justify-center w-20 h-20 mb-6">
                        <div class="absolute inset-0 bg-green-200 rounded-full animate-pulse"></div>
                        <div class="relative bg-green-600 text-white text-3xl font-bold rounded-full w-16 h-16 flex items-center justify-center shadow-lg">
                            1
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Create Your PiggyBox</h3>
                    <p class="text-lg text-gray-600">Sign up free and set up your box in under 2 minutes. Add a title, goal, and description.</p>
                </div>

                <!-- Step 2 -->
                <div class="text-center">
                    <div class="relative inline-flex items-center justify-center w-20 h-20 mb-6">
                        <div class="absolute inset-0 bg-green-200 rounded-full animate-pulse animation-delay-150"></div>
                        <div class="relative bg-green-600 text-white text-3xl font-bold rounded-full w-16 h-16 flex items-center justify-center shadow-lg">
                            2
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Share With Anyone</h3>
                    <p class="text-lg text-gray-600">Get your unique link, QR code, or Piggy Number. Share on WhatsApp, Facebook, or print for events.</p>
                </div>

                <!-- Step 3 -->
                <div class="text-center">
                    <div class="relative inline-flex items-center justify-center w-20 h-20 mb-6">
                        <div class="absolute inset-0 bg-green-200 rounded-full animate-pulse animation-delay-300"></div>
                        <div class="relative bg-green-600 text-white text-3xl font-bold rounded-full w-16 h-16 flex items-center justify-center shadow-lg">
                            3
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Track & Collect</h3>
                    <p class="text-lg text-gray-600">Watch contributions come in real-time. Withdraw anytime. 100% transparent and secure.</p>
                </div>
            </div>

            <!-- CTA Button -->
            <div class="text-center mt-12">
                <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-4 bg-green-600 hover:bg-green-700 text-white text-lg font-bold rounded-lg shadow-xl transition">
                    Start Collecting Now - It's Free
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                <p class="mt-4 text-gray-600">No credit card required • Setup in 2 minutes • Free forever</p>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 py-16">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Join the Digital Giving Revolution
            </h2>
            <p class="text-xl text-green-100 mb-8">
                Whether it's a wedding gift, church offering, or community fundraiser—start collecting contributions the modern way. <strong>Free to create. Easy to share. Secure for everyone.</strong>
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
