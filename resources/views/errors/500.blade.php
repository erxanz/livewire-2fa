<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg w-full text-center">
            <!-- Icon -->
            <div
                class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-red-100 dark:bg-red-900/20 mb-8">
                <svg class="h-12 w-12 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>

            <!-- Error Code -->
            <h1 class="text-9xl font-extrabold text-gray-900 dark:text-white tracking-widest">
                500
            </h1>

            <!-- Error Title -->
            <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-300 mt-4">
                Terjadi Kesalahan Server
            </h2>

            <!-- Error Message -->
            <p class="text-gray-500 dark:text-gray-400 mt-4 mb-8">
                Maaf, terjadi kesalahan pada server kami. Tim teknis kami telah diberitahu dan sedang bekerja untuk
                memperbaikinya.
            </p>

            <!-- Status Info -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-8 text-left">
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Error ID: {{ uniqid('ERR_') }}</span>
                </div>
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mt-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Waktu: {{ now()->format('d M Y H:i:s') }}</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button onclick="window.location.reload()"
                    class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Coba Lagi
                </button>
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>
            </div>

            <!-- Contact Info -->
            <div class="mt-10 pt-8 border-t border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Jika masalah berlanjut, silakan hubungi
                    <a href="mailto:{{ settings('contact.email', 'support@example.com') }}"
                        class="text-indigo-600 dark:text-indigo-400 hover:underline">
                        {{ settings('contact.email', 'support@example.com') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>

</html>
