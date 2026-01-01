<x-layouts.app :title="__('Manajemen Role')">
    <div class="space-y-6">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.dashboard') }}" wire:navigate
                class="text-zinc-500 transition-colors hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200">
                Admin
            </a>
            <svg class="h-4 w-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="font-medium text-zinc-900 dark:text-zinc-100">Roles</span>
        </nav>

        <livewire:admin.role-management />
    </div>
</x-layouts.app>
