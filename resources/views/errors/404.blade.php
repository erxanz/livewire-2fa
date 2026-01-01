<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg w-full text-center">
            <!-- Icon -->
            <div
                class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-yellow-100 dark:bg-yellow-900/20 mb-8">
                <svg class="h-12 w-12 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <!-- Error Code -->
            <h1 class="text-9xl font-extrabold text-gray-900 dark:text-white tracking-widest">
                404
            </h1>

            <!-- Error Title -->
            <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-300 mt-4">
                Halaman Tidak Ditemukan
            </h2>

            <!-- Error Message -->
            <p class="text-gray-500 dark:text-gray-400 mt-4 mb-8">
                Maaf, halaman yang Anda cari tidak ada atau telah dipindahkan.
            </p>

            <!-- Search Box -->
            <div class="max-w-md mx-auto mb-8">
                <form action="{{ route('dashboard') }}" method="GET" class="flex gap-2">
                    <input type="text" name="search" placeholder="Cari sesuatu..."
                        class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <button type="submit"
                        class="px-4 py-3 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>
            </div>

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

            <!-- Popular Links -->
            <div class="mt-10 pt-8 border-t border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Mungkin Anda mencari:</p>
                <div class="flex flex-wrap justify-center gap-2">
                    <a href="{{ route('dashboard') }}"
                        class="px-3 py-1 text-sm bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">Dashboard</a>
                    @auth
                        <a href="{{ route('settings.profile') }}"
                            class="px-3 py-1 text-sm bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">Profil</a>
                        @admin
                            <a href="{{ route('admin.users') }}"
                                class="px-3 py-1 text-sm bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">Users</a>
                        @endadmin
                    @endauth
                </div>
            </div>
        </div>
    </div>
</body>

</html>
