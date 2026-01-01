<x-layouts.app :title="__('Admin Dashboard')">
    <div class="space-y-8">
        {{-- Welcome Header --}}
        <div
            class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-zinc-900 via-zinc-800 to-zinc-900 p-8 dark:from-zinc-800 dark:via-zinc-700 dark:to-zinc-800">
            {{-- Background Pattern --}}
            <div class="absolute inset-0 opacity-10">
                <svg class="h-full w-full" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="grid" width="32" height="32" patternUnits="userSpaceOnUse">
                            <path d="M 32 0 L 0 0 0 32" fill="none" stroke="white" stroke-width="0.5" />
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#grid)" />
                </svg>
            </div>

            <div class="relative">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-medium text-zinc-400">Selamat datang kembali,</p>
                        <h1 class="mt-1 text-3xl font-bold text-white">{{ auth()->user()->name }} 👋</h1>
                        <div class="mt-3 flex items-center gap-2">
                            <span
                                class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-sm font-medium text-white backdrop-blur-sm">
                                {{ auth()->user()->getRoleName() ?? 'User' }}
                            </span>
                            <span class="text-sm text-zinc-400">•</span>
                            <span class="text-sm text-zinc-400">{{ now()->format('l, d F Y') }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.users') }}" wire:navigate
                            class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-zinc-900 shadow-lg transition-all hover:bg-zinc-100">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Kelola Users
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        @php
            $totalUsers = \App\Models\User::count();
            $totalRoles = \App\Models\Role::count();
            $adminCount = \App\Models\User::whereHas('role', fn($q) => $q->where('slug', 'admin'))->count();
            $verifiedCount = \App\Models\User::whereNotNull('email_verified_at')->count();
        @endphp

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Total Users --}}
            <div
                class="group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-6 transition-all hover:border-zinc-300 hover:shadow-lg dark:border-zinc-700/50 dark:bg-zinc-800/50 dark:hover:border-zinc-600">
                <div
                    class="absolute -right-4 -top-4 h-20 w-20 rounded-full bg-gradient-to-br from-blue-500 to-cyan-500 opacity-10 blur-2xl transition-all group-hover:opacity-20">
                </div>
                <div class="relative flex items-center gap-4">
                    <div
                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-600 shadow-lg shadow-blue-500/25">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Users</p>
                        <p class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($totalUsers) }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Total Roles --}}
            <div
                class="group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-6 transition-all hover:border-zinc-300 hover:shadow-lg dark:border-zinc-700/50 dark:bg-zinc-800/50 dark:hover:border-zinc-600">
                <div
                    class="absolute -right-4 -top-4 h-20 w-20 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 opacity-10 blur-2xl transition-all group-hover:opacity-20">
                </div>
                <div class="relative flex items-center gap-4">
                    <div
                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/25">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Roles</p>
                        <p class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($totalRoles) }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Admin Users --}}
            <div
                class="group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-6 transition-all hover:border-zinc-300 hover:shadow-lg dark:border-zinc-700/50 dark:bg-zinc-800/50 dark:hover:border-zinc-600">
                <div
                    class="absolute -right-4 -top-4 h-20 w-20 rounded-full bg-gradient-to-br from-violet-500 to-purple-500 opacity-10 blur-2xl transition-all group-hover:opacity-20">
                </div>
                <div class="relative flex items-center gap-4">
                    <div
                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-500 to-purple-600 shadow-lg shadow-violet-500/25">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Admin Users</p>
                        <p class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($adminCount) }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Verified Users --}}
            <div
                class="group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-6 transition-all hover:border-zinc-300 hover:shadow-lg dark:border-zinc-700/50 dark:bg-zinc-800/50 dark:hover:border-zinc-600">
                <div
                    class="absolute -right-4 -top-4 h-20 w-20 rounded-full bg-gradient-to-br from-amber-500 to-orange-500 opacity-10 blur-2xl transition-all group-hover:opacity-20">
                </div>
                <div class="relative flex items-center gap-4">
                    <div
                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 shadow-lg shadow-amber-500/25">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Terverifikasi</p>
                        <p class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ number_format($verifiedCount) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content Grid --}}
        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Role Distribution --}}
            <div class="lg:col-span-2">
                <div
                    class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-700/50 dark:bg-zinc-800/50">
                    <div class="mb-6 flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Distribusi Role</h2>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">Persentase user berdasarkan role</p>
                        </div>
                        <a href="{{ route('admin.roles') }}" wire:navigate
                            class="text-sm font-medium text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100">
                            Lihat Semua →
                        </a>
                    </div>

                    <div class="space-y-5">
                        @foreach (\App\Models\Role::withCount('users')->orderByDesc('users_count')->get() as $role)
                            @php
                                $percentage = $totalUsers > 0 ? round(($role->users_count / $totalUsers) * 100, 1) : 0;
                                $roleColors = [
                                    'admin' => 'bg-red-500',
                                    'editor' => 'bg-blue-500',
                                    'moderator' => 'bg-amber-500',
                                    'user' => 'bg-zinc-500',
                                ];
                                $barColor = $roleColors[$role->slug] ?? 'bg-zinc-500';
                            @endphp
                            <div>
                                <div class="mb-2 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="h-3 w-3 rounded-full {{ $barColor }}"></span>
                                        <span
                                            class="font-medium text-zinc-900 dark:text-zinc-100">{{ $role->name }}</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="text-sm text-zinc-500 dark:text-zinc-400">{{ $role->users_count }}
                                            users</span>
                                        <span
                                            class="w-12 text-right text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $percentage }}%</span>
                                    </div>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-700/50">
                                    <div class="h-full rounded-full {{ $barColor }} transition-all duration-500"
                                        style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach

                        @if (\App\Models\Role::count() === 0)
                            <div class="py-8 text-center">
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">Belum ada role yang dibuat</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div>
                <div
                    class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-700/50 dark:bg-zinc-800/50">
                    <h2 class="mb-6 text-lg font-semibold text-zinc-900 dark:text-zinc-100">Quick Actions</h2>

                    <div class="space-y-3">
                        <a href="{{ route('admin.users') }}" wire:navigate
                            class="group flex items-center gap-4 rounded-xl border border-zinc-200 p-4 transition-all hover:border-blue-200 hover:bg-blue-50 dark:border-zinc-700/50 dark:hover:border-blue-800/50 dark:hover:bg-blue-900/10">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600 transition-colors group-hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">Manajemen User</p>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">Tambah, edit, atau hapus user</p>
                            </div>
                        </a>

                        <a href="{{ route('admin.roles') }}" wire:navigate
                            class="group flex items-center gap-4 rounded-xl border border-zinc-200 p-4 transition-all hover:border-emerald-200 hover:bg-emerald-50 dark:border-zinc-700/50 dark:hover:border-emerald-800/50 dark:hover:bg-emerald-900/10">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 transition-colors group-hover:bg-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">Manajemen Role</p>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">Atur hak akses pengguna</p>
                            </div>
                        </a>

                        <a href="{{ route('profile.edit') }}" wire:navigate
                            class="group flex items-center gap-4 rounded-xl border border-zinc-200 p-4 transition-all hover:border-violet-200 hover:bg-violet-50 dark:border-zinc-700/50 dark:hover:border-violet-800/50 dark:hover:bg-violet-900/10">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-violet-100 text-violet-600 transition-colors group-hover:bg-violet-200 dark:bg-violet-900/30 dark:text-violet-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">Pengaturan Profil</p>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">Ubah informasi akun Anda</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Users --}}
        <div class="rounded-2xl border border-zinc-200 bg-white dark:border-zinc-700/50 dark:bg-zinc-800/50">
            <div class="flex items-center justify-between border-b border-zinc-100 p-6 dark:border-zinc-700/50">
                <div>
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">User Terbaru</h2>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">5 user yang baru terdaftar</p>
                </div>
                <a href="{{ route('admin.users') }}" wire:navigate
                    class="text-sm font-medium text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100">
                    Lihat Semua →
                </a>
            </div>

            <div class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                @foreach (\App\Models\User::with('role')->latest()->take(5)->get() as $user)
                    <div
                        class="flex items-center justify-between p-4 transition-colors hover:bg-zinc-50/50 dark:hover:bg-zinc-700/20">
                        <div class="flex items-center gap-4">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-zinc-600 to-zinc-800 text-sm font-semibold text-white dark:from-zinc-500 dark:to-zinc-700">
                                {{ $user->initials() }}
                            </div>
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $user->name }}</p>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            @if ($user->role)
                                @php
                                    $roleColors = [
                                        'admin' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                        'editor' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                        'moderator' =>
                                            'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                        'user' => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-700/50 dark:text-zinc-300',
                                    ];
                                    $roleColor = $roleColors[$user->role->slug] ?? $roleColors['user'];
                                @endphp
                                <span
                                    class="hidden rounded-full px-2.5 py-1 text-xs font-medium sm:inline-flex {{ $roleColor }}">
                                    {{ $user->role->name }}
                                </span>
                            @endif
                            <span class="text-sm text-zinc-400 dark:text-zinc-500">
                                {{ $user->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.app>
