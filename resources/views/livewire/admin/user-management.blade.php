<?php

use App\Models\Role;
use App\Models\User;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Hash;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public ?int $role_id = null;
    public bool $showModal = false;
    public ?int $editingUserId = null;
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function getRoles()
    {
        return Role::all();
    }

    public function getUsers()
    {
        return User::with('role')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);
    }

    public function openCreateModal(): void
    {
        $this->reset(['name', 'email', 'password', 'role_id', 'editingUserId']);
        $this->showModal = true;
    }

    public function openEditModal(int $userId): void
    {
        $user = User::find($userId);
        if ($user) {
            $this->editingUserId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role_id = $user->role_id;
            $this->password = '';
            $this->showModal = true;
        }
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['name', 'email', 'password', 'role_id', 'editingUserId']);
        $this->resetValidation();
    }

    public function save(): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email' . ($this->editingUserId ? ',' . $this->editingUserId : ''),
            'role_id' => 'nullable|exists:roles,id',
        ];

        if (!$this->editingUserId) {
            $rules['password'] = 'required|string|min:8';
        } else {
            $rules['password'] = 'nullable|string|min:8';
        }

        $this->validate($rules);

        if ($this->editingUserId) {
            $user = User::find($this->editingUserId);
            $user->name = $this->name;
            $user->email = $this->email;
            $user->role_id = $this->role_id;

            if ($this->password) {
                $user->password = Hash::make($this->password);
            }

            $user->save();
            session()->flash('message', 'User berhasil diperbarui.');
        } else {
            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role_id' => $this->role_id,
                'email_verified_at' => now(),
            ]);
            session()->flash('message', 'User berhasil dibuat.');
        }

        $this->closeModal();
    }

    public function deleteUser(int $userId): void
    {
        if (auth()->id() === $userId) {
            session()->flash('error', 'Anda tidak dapat menghapus akun sendiri.');
            return;
        }

        User::destroy($userId);
        session()->flash('message', 'User berhasil dihapus.');
    }
}; ?>

<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                Manajemen User
            </h2>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                Kelola semua pengguna sistem dalam satu tempat
            </p>
        </div>
        <button wire:click="openCreateModal"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-zinc-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200 w-full sm:w-auto">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah User
        </button>
    </div>

    {{-- Alert Messages --}}
    @if (session()->has('message'))
        <div
            class="flex items-start gap-3 rounded-lg border-l-4 border-l-emerald-500 bg-emerald-50 p-4 dark:bg-emerald-950/30">
            <svg class="h-5 w-5 shrink-0 text-emerald-600 dark:text-emerald-400" fill="currentColor"
                viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" />
            </svg>
            <p class="text-sm font-medium text-emerald-800 dark:text-emerald-200">{{ session('message') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="flex items-start gap-3 rounded-lg border-l-4 border-l-red-500 bg-red-50 p-4 dark:bg-red-950/30">
            <svg class="h-5 w-5 shrink-0 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd" />
            </svg>
            <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Search & Filter --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div class="relative w-full sm:max-w-xs">
            <svg class="absolute left-3 top-3 h-5 w-5 text-zinc-400 dark:text-zinc-500" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" wire:model.live.debounce="search" placeholder="Cari nama atau email..."
                class="block w-full rounded-lg border border-zinc-200 bg-white py-2.5 pl-10 pr-4 text-zinc-900 placeholder-zinc-500 focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 dark:border-zinc-700/50 dark:bg-zinc-700/50 dark:text-zinc-100 dark:placeholder-zinc-400 dark:focus:border-zinc-400 dark:focus:ring-zinc-400" />
        </div>
        <p class="text-sm text-zinc-500 dark:text-zinc-400">
            <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $this->getUsers()->total() }}</span>
            {{ $this->getUsers()->total() == 1 ? 'user' : 'users' }}
        </p>
    </div>

    {{-- Users Table Card --}}
    <div class="rounded-lg border border-zinc-200 bg-white dark:border-zinc-700/50 dark:bg-zinc-800/50">
        {{-- Desktop Table --}}
        <div class="hidden overflow-x-auto md:block">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700/50">
                <thead>
                    <tr class="bg-zinc-50/50 dark:bg-zinc-800/50">
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            User
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            Role
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            Status
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/30">
                    @forelse ($this->getUsers() as $user)
                        <tr class="transition-colors hover:bg-zinc-50/50 dark:hover:bg-zinc-700/20">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-zinc-600 to-zinc-800 text-sm font-semibold text-white dark:from-zinc-500 dark:to-zinc-700">
                                        {{ $user->initials() }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $user->name }}</p>
                                        <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ $user->email }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($user->role)
                                    @php
                                        $roleColors = [
                                            'admin' =>
                                                'bg-red-100 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-400 dark:ring-red-500/30',
                                            'editor' =>
                                                'bg-blue-100 text-blue-700 ring-blue-600/20 dark:bg-blue-900/30 dark:text-blue-400 dark:ring-blue-500/30',
                                            'moderator' =>
                                                'bg-amber-100 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-400 dark:ring-amber-500/30',
                                            'user' =>
                                                'bg-zinc-100 text-zinc-700 ring-zinc-600/20 dark:bg-zinc-700/50 dark:text-zinc-300 dark:ring-zinc-500/30',
                                        ];
                                        $colorClass = $roleColors[$user->role->slug] ?? $roleColors['user'];
                                    @endphp
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $colorClass }}">
                                        {{ $user->role->name }}
                                    </span>
                                @else
                                    <span class="text-sm text-zinc-400 dark:text-zinc-500">Tidak ada</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($user->email_verified_at)
                                    <span
                                        class="inline-flex items-center gap-1.5 text-sm text-emerald-600 dark:text-emerald-400">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                        Terverifikasi
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 text-sm text-zinc-400 dark:text-zinc-500">
                                        <span class="h-2 w-2 rounded-full bg-zinc-300 dark:bg-zinc-600"></span>
                                        Belum verifikasi
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="openEditModal({{ $user->id }})"
                                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700/50 dark:bg-zinc-700/50 dark:text-zinc-300 dark:hover:bg-zinc-600/50">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </button>
                                    @if (auth()->id() !== $user->id)
                                        <button wire:click="deleteUser({{ $user->id }})"
                                            onclick="return confirm('Yakin ingin menghapus user ini?')"
                                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-100 dark:border-red-700/50 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">Tidak ada user ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Card View --}}
        <div class="space-y-3 p-4 md:hidden">
            @forelse ($this->getUsers() as $user)
                <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700/50 dark:bg-zinc-800/50">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-zinc-600 to-zinc-800 text-xs font-semibold text-white dark:from-zinc-500 dark:to-zinc-700">
                                {{ $user->initials() }}
                            </div>
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $user->name }}</p>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $user->email }}</p>
                                @if ($user->role)
                                    <span
                                        class="mt-2 inline-flex items-center rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                        {{ $user->role->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-1">
                            <button wire:click="openEditModal({{ $user->id }})"
                                class="rounded-lg border border-zinc-200 bg-white p-2 text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700/50 dark:bg-zinc-700/50 dark:text-zinc-300 dark:hover:bg-zinc-600/50">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            @if (auth()->id() !== $user->id)
                                <button wire:click="deleteUser({{ $user->id }})"
                                    onclick="return confirm('Yakin ingin menghapus user ini?')"
                                    class="rounded-lg border border-red-200 bg-red-50 p-2 text-red-700 hover:bg-red-100 dark:border-red-700/50 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Tidak ada user ditemukan</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($this->getUsers()->hasPages())
            <div class="border-t border-zinc-200 px-6 py-4 dark:border-zinc-700/50">
                {{ $this->getUsers()->links() }}
            </div>
        @endif
    </div>

    {{-- Modal --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-lg bg-white shadow-xl dark:bg-zinc-800">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between border-b border-zinc-200 p-6 dark:border-zinc-700/50">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ $editingUserId ? 'Edit User' : 'Buat User Baru' }}
                    </h3>
                    <button wire:click="closeModal"
                        class="text-zinc-400 hover:text-zinc-600 dark:text-zinc-500 dark:hover:text-zinc-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <form wire:submit.prevent="save" class="space-y-4 p-6">
                    {{-- Name Input --}}
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Nama</label>
                        <input type="text" wire:model="name" placeholder="Nama lengkap"
                            class="mt-1 block w-full rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-zinc-900 placeholder-zinc-500 focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 dark:border-zinc-700/50 dark:bg-zinc-700/50 dark:text-zinc-100 dark:placeholder-zinc-400 dark:focus:border-zinc-400 dark:focus:ring-zinc-400" />
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email Input --}}
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Email</label>
                        <input type="email" wire:model="email" placeholder="user@example.com"
                            class="mt-1 block w-full rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-zinc-900 placeholder-zinc-500 focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 dark:border-zinc-700/50 dark:bg-zinc-700/50 dark:text-zinc-100 dark:placeholder-zinc-400 dark:focus:border-zinc-400 dark:focus:ring-zinc-400" />
                        @error('email')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password Input --}}
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Password
                            @if ($editingUserId)
                                <span class="text-zinc-500">(opsional)</span>
                            @endif
                        </label>
                        <input type="password" wire:model="password" placeholder="Minimal 8 karakter"
                            class="mt-1 block w-full rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-zinc-900 placeholder-zinc-500 focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 dark:border-zinc-700/50 dark:bg-zinc-700/50 dark:text-zinc-100 dark:placeholder-zinc-400 dark:focus:border-zinc-400 dark:focus:ring-zinc-400" />
                        @error('password')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Role Select --}}
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Role</label>
                        <select wire:model="role_id"
                            class="mt-1 block w-full rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-zinc-900 focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 dark:border-zinc-700/50 dark:bg-zinc-700/50 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-400">
                            <option value="">Pilih Role</option>
                            @foreach ($this->getRoles() as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex gap-3 border-t border-zinc-200 pt-6 dark:border-zinc-700/50">
                        <button type="button" wire:click="closeModal"
                            class="flex-1 rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700/50 dark:bg-zinc-700/50 dark:text-zinc-300 dark:hover:bg-zinc-600/50">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 rounded-lg bg-zinc-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
