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
            ->paginate(15);
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
        <flux:button wire:click="openCreateModal" variant="primary" icon="plus" class="w-full sm:w-auto">
            Tambah User
        </flux:button>
    </div>

    {{-- Alert Messages --}}
    @if (session()->has('message'))
        <flux:card class="border-l-4 border-l-emerald-500 bg-emerald-50/50 dark:bg-emerald-900/20">
            <div class="flex items-start gap-3">
                <svg class="h-5 w-5 shrink-0 text-emerald-600 dark:text-emerald-400" fill="currentColor"
                    viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <p class="text-sm font-medium text-emerald-800 dark:text-emerald-200">{{ session('message') }}</p>
            </div>
        </flux:card>
    @endif

    @if (session()->has('error'))
        <flux:card class="border-l-4 border-l-red-500 bg-red-50/50 dark:bg-red-900/20">
            <div class="flex items-start gap-3">
                <svg class="h-5 w-5 shrink-0 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
                <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
            </div>
        </flux:card>
    @endif

    {{-- Search & Filter --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <flux:input wire:model.live.debounce="search" icon="magnifying-glass" placeholder="Cari nama atau email..."
            class="w-full sm:max-w-xs" />
        <p class="text-sm text-zinc-500 dark:text-zinc-400">
            <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $this->getUsers()->total() }}</span>
            {{ $this->getUsers()->total() == 1 ? 'user' : 'users' }}
        </p>
    </div>

    {{-- Users Table Card --}}
    <flux:card>
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
                                    <flux:button wire:click="openEditModal({{ $user->id }})" icon="pencil"
                                        size="sm" variant="ghost" />
                                    @if (auth()->id() !== $user->id)
                                        <flux:button wire:click="deleteUser({{ $user->id }})" icon="trash"
                                            size="sm" variant="danger"
                                            wire:confirm="Apakah Anda yakin ingin menghapus user ini?" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="flex h-16 w-16 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-700/50">
                                        <svg class="h-8 w-8 text-zinc-400 dark:text-zinc-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <p class="mt-4 text-sm font-medium text-zinc-900 dark:text-zinc-100">Tidak ada user
                                        ditemukan</p>
                                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Coba ubah kata kunci
                                        pencarian Anda</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="divide-y divide-zinc-100 md:hidden dark:divide-zinc-700/30">
            @forelse ($this->getUsers() as $user)
                <div class="p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-zinc-600 to-zinc-800 text-sm font-semibold text-white dark:from-zinc-500 dark:to-zinc-700">
                                {{ $user->initials() }}
                            </div>
                            <div class="min-w-0">
                                <p class="truncate font-medium text-zinc-900 dark:text-zinc-100">{{ $user->name }}
                                </p>
                                <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:button wire:click="openEditModal({{ $user->id }})" icon="pencil" size="sm"
                                variant="ghost" />
                            @if (auth()->id() !== $user->id)
                                <flux:button wire:click="deleteUser({{ $user->id }})" icon="trash" size="sm"
                                    variant="danger" wire:confirm="Apakah Anda yakin ingin menghapus user ini?" />
                            @endif
                        </div>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-2">
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
                        @endif
                        @if ($user->email_verified_at)
                            <span
                                class="inline-flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                Terverifikasi
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-4 py-12 text-center">
                    <div class="flex flex-col items-center">
                        <div
                            class="flex h-16 w-16 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-700/50">
                            <svg class="h-8 w-8 text-zinc-400 dark:text-zinc-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <p class="mt-4 text-sm font-medium text-zinc-900 dark:text-zinc-100">Tidak ada user ditemukan
                        </p>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Coba ubah kata kunci pencarian</p>
                    </div>
                </div>
            @endforelse
        </div>
    </flux:card>

    {{-- Pagination --}}
    @if ($this->getUsers()->hasPages())
        <div class="flex items-center justify-center">
            {{ $this->getUsers()->links() }}
        </div>
    @endif

    {{-- Modal Create/Edit User --}}
    <flux:modal wire:model="showModal" class="w-full max-w-lg">
        <flux:card>
            <div class="mb-6">
                <h3 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">
                    {{ $editingUserId ? 'Edit User' : 'Tambah User Baru' }}
                </h3>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ $editingUserId ? 'Perbarui informasi user yang sudah ada' : 'Buat akun user baru untuk sistem' }}
                </p>
            </div>

            <form wire:submit="save" class="space-y-5">
                <flux:input wire:model="name" label="Nama Lengkap" placeholder="Masukkan nama lengkap" />
                @error('name')
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror

                <flux:input wire:model="email" type="email" label="Email" placeholder="contoh@email.com" />
                @error('email')
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror

                <flux:input wire:model="password" type="password" label="Password"
                    placeholder="{{ $editingUserId ? 'Kosongkan jika tidak ingin mengubah' : 'Minimal 8 karakter' }}" />
                @error('password')
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror

                <flux:select wire:model="role_id" label="Role">
                    <option value="">-- Pilih Role --</option>
                    @foreach ($this->getRoles() as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </flux:select>
                @error('role_id')
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror

                <div class="flex items-center justify-end gap-3 pt-4">
                    <flux:button type="button" wire:click="closeModal" variant="ghost">
                        Batal
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $editingUserId ? 'Simpan Perubahan' : 'Tambah User' }}
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </flux:modal>
</div>
