<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>503 - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg w-full text-center">
            <!-- Icon -->
            <div
                class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-blue-100 dark:bg-blue-900/20 mb-8">
                <svg class="h-12 w-12 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>

            <!-- Error Code -->
            <h1 class="text-9xl font-extrabold text-gray-900 dark:text-white tracking-widest">
                503
            </h1>

            <!-- Error Title -->
            <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-300 mt-4">
                Sedang Dalam Pemeliharaan
            </h2>

            <!-- Error Message -->
            <p class="text-gray-500 dark:text-gray-400 mt-4 mb-8">
                {!! $exception->getMessage() ?:
                    settings(
                        'maintenance.message',
                        'Kami sedang melakukan pemeliharaan terjadwal. Silakan kembali beberapa saat lagi.',
                    ) !!}
            </p>

            <!-- Progress Animation -->
            <div class="max-w-xs mx-auto mb-8">
                <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full animate-pulse"
                        style="width: 70%"></div>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Estimasi waktu: beberapa menit</p>
            </div>

            <!-- Status Checks -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-8">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Status Sistem:</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Database</span>
                        <span class="flex items-center text-yellow-600 dark:text-yellow-400">
                            <svg class="w-4 h-4 mr-1 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Updating
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Server</span>
                        <span class="flex items-center text-green-600 dark:text-green-400">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            Online
                        </span>
                    </div>
                </div>
            </div>

            <!-- Refresh Button -->
            <button onclick="window.location.reload()"
                class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh Halaman
            </button>

            <!-- Auto Refresh Notice -->
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-6">
                Halaman akan otomatis refresh dalam <span id="countdown">60</span> detik
            </p>
        </div>
    </div>

    <script>
        // Auto refresh countdown
        let seconds = 60;
        const countdownEl = document.getElementById('countdown');

        setInterval(() => {
            seconds--;
            countdownEl.textContent = seconds;
            if (seconds <= 0) {
                window.location.reload();
            }
        }, 1000);
    </script>
</body>

</html>
