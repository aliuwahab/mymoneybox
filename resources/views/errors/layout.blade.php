<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title') - {{ config('app.name', 'MyPiggyBox') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100 dark:from-gray-900 dark:to-gray-800 px-4">
            <div class="max-w-2xl w-full text-center">
                <!-- Logo -->
                <div class="mb-8 flex justify-center">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
                        <!-- Logo SVG -->
                        <svg class="w-12 h-12 text-primary-600 dark:text-primary-400" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Piggy box body -->
                            <rect x="20" y="35" width="60" height="40" rx="8" fill="currentColor" opacity="0.2"/>
                            <!-- Coin slot -->
                            <rect x="45" y="30" width="10" height="3" rx="1" fill="currentColor"/>
                            <!-- M letter -->
                            <path d="M30 45 L30 65 L35 60 L40 65 L40 45" stroke="currentColor" stroke-width="3" fill="none"/>
                            <!-- Dollar sign -->
                            <text x="55" y="65" font-size="24" font-weight="bold" fill="currentColor">$</text>
                        </svg>
                        <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">MyPiggyBox</span>
                    </a>
                </div>

                <!-- Error Code -->
                <div class="mb-6">
                    <h1 class="text-9xl font-extrabold text-primary-600 dark:text-primary-400">
                        @yield('code')
                    </h1>
                </div>

                <!-- Error Message -->
                <div class="mb-4">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        @yield('title')
                    </h2>
                    <p class="text-lg text-gray-600 dark:text-gray-400">
                        @yield('message')
                    </p>
                </div>

                <!-- Additional Help Text -->
                @hasSection('help')
                    <div class="mb-8">
                        <p class="text-base text-gray-600 dark:text-gray-400">
                            @yield('help')
                        </p>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition duration-150 ease-in-out">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Go Home
                    </a>
                    <button onclick="window.history.back()" class="inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded-lg border border-gray-300 dark:border-gray-600 transition duration-150 ease-in-out">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Go Back
                    </button>
                </div>
            </div>
        </div>
    </body>
</html>
