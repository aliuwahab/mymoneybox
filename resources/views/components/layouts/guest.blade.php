<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MyMoneyBox') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2">
                        <x-app-logo-icon class="w-8 h-8 text-primary-600" />
                        <span class="text-xl font-bold text-gray-900">{{ config('app.name', 'MyMoneyBox') }}</span>
                    </a>
                </div>

                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-primary-600 font-medium transition">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-primary-600 font-medium transition">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition">
                            Get Started
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-2 mb-4">
                        <x-app-logo-icon class="w-8 h-8 text-primary-400" />
                        <span class="text-xl font-bold">{{ config('app.name') }}</span>
                    </div>
                    <p class="text-gray-400 mb-4">
                        Create and share money boxes for any occasion. Collect contributions easily and securely.
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold mb-4">Platform</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('browse') }}" class="hover:text-white transition">Browse Money Boxes</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition">Create Account</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold mb-4">Support</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Help Center</a></li>
                        <li><a href="#" class="hover:text-white transition">Contact Us</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
