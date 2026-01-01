<?php

use App\Models\Role;
use App\Models\Permission;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $description = '';
    public array $permissions = [];
    public bool $showModal = false;
    public ?int $editingRoleId = null;

    public function getRoles()
    {
        return Role::with('permissions')->get();
    }

    public function getPermissions()
    {
        return Permission::all();
    }

    public function openCreateModal(): void
    {
        $this->reset(['name', 'description', 'permissions', 'editingRoleId']);
        $this->showModal = true;
    }

    public function openEditModal(int $roleId): void
    {
        $role = Role::with('permissions')->find($roleId);
        if ($role) {
            $this->editingRoleId = $role->id;
            $this->name = $role->name;
            $this->description = $role->description ?? '';
            $this->permissions = $role->permissions->pluck('id')->toArray();
            $this->showModal = true;
        }
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['name', 'description', 'permissions', 'editingRoleId']);
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:roles,name' . ($this->editingRoleId ? ',' . $this->editingRoleId : ''),
            'description' => 'nullable|string|max:500',
            'permissions' => 'array',
        ]);

        if ($this->editingRoleId) {
            $role = Role::find($this->editingRoleId);
            $role->update([
                'name' => $this->name,
                'description' => $this->description,
            ]);
            $role->permissions()->sync($this->permissions);
            session()->flash('message', 'Role berhasil diperbarui.');
        } else {
            $role = Role::create([
                'name' => $this->name,
                'slug' => strtolower(str_replace(' ', '-', $this->name)),
                'description' => $this->description,
            ]);
            $role->permissions()->attach($this->permissions);
            session()->flash('message', 'Role berhasil dibuat.');
        }

        $this->closeModal();
    }

    public function deleteRole(int $roleId): void
    {
        if ($roleId <= 3) {
            session()->flash('error', 'Tidak dapat menghapus role bawaan sistem.');
            return;
        }

        Role::destroy($roleId);
        session()->flash('message', 'Role berhasil dihapus.');
    }
}; ?>

<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                Manajemen Role
            </h2>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                Kelola peran dan izin pengguna sistem
            </p>
        </div>
        <button wire:click="openCreateModal"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-zinc-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200 w-full sm:w-auto">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Role
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

    {{-- Stats Section --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700/50 dark:bg-zinc-800/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Roles</p>
                    <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-zinc-100">
                        {{ count($this->getRoles()) }}
                    </p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 12H9m0 0l3-3m-3 3l-3-3m3 3l3 3m-3-3l-3 3" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700/50 dark:bg-zinc-800/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Permissions</p>
                    <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-zinc-100">
                        {{ count($this->getPermissions()) }}
                    </p>
                </div>
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/30">
                    <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700/50 dark:bg-zinc-800/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">System Roles</p>
                    <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-zinc-100">3</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/30">
                    <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Roles Grid --}}
    @if ($this->getRoles()->isNotEmpty())
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($this->getRoles() as $role)
                <div
                    class="flex flex-col rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700/50 dark:bg-zinc-800/50">
                    {{-- Role Header --}}
                    <div class="flex items-start justify-between gap-3 pb-4">
                        <div class="min-w-0 flex-1">
                            <h3 class="truncate font-semibold text-zinc-900 dark:text-zinc-100">{{ $role->name }}</h3>
                            <p class="mt-1 truncate text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $role->description ?? 'Tidak ada deskripsi' }}
                            </p>
                        </div>
                        <span
                            class="inline-flex shrink-0 items-center rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-700/50 dark:text-zinc-300">
                            {{ count($role->permissions) }} izin
                        </span>
                    </div>

                    {{-- Permissions List --}}
                    @if ($role->permissions->isNotEmpty())
                        <div class="mb-4 flex flex-wrap gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-700/50">
                            @foreach ($role->permissions->take(3) as $permission)
                                <span
                                    class="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                    {{ $permission->name }}
                                </span>
                            @endforeach
                            @if (count($role->permissions) > 3)
                                <span
                                    class="inline-flex items-center rounded-full bg-zinc-100 px-2 py-1 text-xs font-medium text-zinc-600 dark:bg-zinc-700/50 dark:text-zinc-400">
                                    +{{ count($role->permissions) - 3 }} lagi
                                </span>
                            @endif
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="mt-auto flex gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-700/50">
                        <button wire:click="openEditModal({{ $role->id }})"
                            class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700/50 dark:bg-zinc-700/50 dark:text-zinc-300 dark:hover:bg-zinc-600/50">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </button>
                        @if ($role->id > 3)
                            <button wire:click="deleteRole({{ $role->id }})"
                                onclick="return confirm('Yakin ingin menghapus role ini?')"
                                class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-100 dark:border-red-700/50 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- Empty State --}}
        <div
            class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-zinc-200 bg-zinc-50 p-12 dark:border-zinc-700/50 dark:bg-zinc-800/30">
            <svg class="h-12 w-12 text-zinc-400 dark:text-zinc-600" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <h3 class="mt-4 text-lg font-semibold text-zinc-900 dark:text-zinc-100">Tidak ada role</h3>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Mulai dengan membuat role baru untuk sistem Anda
            </p>
            <button wire:click="openCreateModal"
                class="mt-4 inline-flex items-center gap-2 rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Role Pertama
            </button>
        </div>
    @endif

    {{-- Modal --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-lg bg-white shadow-xl dark:bg-zinc-800">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between border-b border-zinc-200 p-6 dark:border-zinc-700/50">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ $editingRoleId ? 'Edit Role' : 'Buat Role Baru' }}
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
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Nama Role</label>
                        <input type="text" wire:model="name" placeholder="Contoh: Manager"
                            class="mt-1 block w-full rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-zinc-900 placeholder-zinc-500 focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 dark:border-zinc-700/50 dark:bg-zinc-700/50 dark:text-zinc-100 dark:placeholder-zinc-400 dark:focus:border-zinc-400 dark:focus:ring-zinc-400" />
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description Textarea --}}
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Deskripsi</label>
                        <textarea wire:model="description" placeholder="Deskripsi role..." rows="3"
                            class="mt-1 block w-full rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-zinc-900 placeholder-zinc-500 focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 dark:border-zinc-700/50 dark:bg-zinc-700/50 dark:text-zinc-100 dark:placeholder-zinc-400 dark:focus:border-zinc-400 dark:focus:ring-zinc-400"></textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Permissions Checkboxes --}}
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Izin</label>
                        <div class="mt-2 space-y-2 max-h-48 overflow-y-auto">
                            @foreach ($this->getPermissions() as $permission)
                                <label
                                    class="flex items-center gap-3 rounded-lg border border-zinc-200 p-3 hover:bg-zinc-50 dark:border-zinc-700/50 dark:hover:bg-zinc-700/30">
                                    <input type="checkbox" wire:model="permissions" value="{{ $permission->id }}"
                                        class="h-4 w-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900 dark:border-zinc-600 dark:bg-zinc-700 dark:text-zinc-100" />
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $permission->name }}
                                        </p>
                                        @if ($permission->description)
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                                {{ $permission->description }}
                                            </p>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('permissions')
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
