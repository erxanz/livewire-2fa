<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - {{ config('app.name', 'Laravel') }}</title>
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
                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>

            <!-- Error Code -->
            <h1 class="text-9xl font-extrabold text-gray-900 dark:text-white tracking-widest">
                403
            </h1>

            <!-- Error Title -->
            <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-300 mt-4">
                Akses Ditolak
            </h2>

            <!-- Error Message -->
            <p class="text-gray-500 dark:text-gray-400 mt-4 mb-8">
                {{ $exception->getMessage() ?: 'Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.' }}
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url()->previous() }}"
                    class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>
            </div>

            <!-- Help Text -->
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-8">
                Jika Anda merasa ini adalah kesalahan, silakan hubungi administrator.
            </p>
        </div>
    </div>
</body>

</html>
