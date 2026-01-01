# Sistem Autentikasi Multi-User dengan Role Dinamis

Sistem autentikasi multi-user dengan role yang dinamis menggunakan Laravel 12 dan Livewire Starter Kit. Role tidak di-hardcode dan bisa ditambah/dikurangi melalui database tanpa perlu mengubah banyak file.

## 📋 Fitur

-   ✅ Role dinamis (disimpan di database)
-   ✅ User memiliki satu role (one-to-many relationship)
-   ✅ Middleware role untuk membatasi akses route
-   ✅ Blade directives untuk pengecekan role di view
-   ✅ Helper methods untuk pengecekan role di model & controller
-   ✅ Livewire components untuk manajemen user & role
-   ✅ Admin dashboard dengan statistik

## 🚀 Instalasi

### 1. Jalankan Migration

```bash
php artisan migrate
```

### 2. Jalankan Seeder

```bash
php artisan db:seed
```

Atau untuk menjalankan RoleSeeder saja:

```bash
php artisan db:seed --class=RoleSeeder
```

### 3. Default Users

Setelah menjalankan seeder, sistem akan membuat:

| Email             | Password | Role  |
| ----------------- | -------- | ----- |
| admin@example.com | password | Admin |
| test@example.com  | password | User  |

## 📁 Struktur File

```
app/
├── Http/
│   └── Middleware/
│       └── RoleMiddleware.php          # Middleware untuk cek role
├── Models/
│   ├── Role.php                        # Model Role
│   └── User.php                        # Model User (updated)
└── Providers/
    └── AppServiceProvider.php          # Blade directives

bootstrap/
└── app.php                             # Register middleware alias

database/
├── migrations/
│   └── 2025_01_01_000001_create_roles_table.php
└── seeders/
    ├── DatabaseSeeder.php
    └── RoleSeeder.php

resources/views/
├── admin/
│   ├── dashboard.blade.php             # Admin dashboard
│   ├── users.blade.php                 # Halaman manajemen user
│   └── roles.blade.php                 # Halaman manajemen role
└── livewire/admin/
    ├── user-management.blade.php       # Livewire component user
    └── role-management.blade.php       # Livewire component role

routes/
└── web.php                             # Routes dengan middleware role
```

## 📖 Cara Penggunaan

### 1. Menambah Role Baru

#### Via Seeder

Edit file `database/seeders/RoleSeeder.php`:

```php
$roles = [
    [
        'name' => 'Admin',
        'slug' => 'admin',
        'description' => 'Administrator dengan akses penuh',
    ],
    [
        'name' => 'Manager',  // Role baru
        'slug' => 'manager',
        'description' => 'Manager dengan akses terbatas',
    ],
    // Tambahkan role lainnya...
];
```

Lalu jalankan:

```bash
php artisan db:seed --class=RoleSeeder
```

#### Via Tinker

```bash
php artisan tinker
```

```php
App\Models\Role::create([
    'name' => 'Manager',
    'slug' => 'manager',
    'description' => 'Manager dengan akses terbatas'
]);
```

#### Via Admin Panel

Akses `/admin/roles` dan klik tombol "Tambah Role".

### 2. Menghapus Role

#### Via Tinker

```bash
php artisan tinker
```

```php
App\Models\Role::where('slug', 'manager')->delete();
```

#### Via Admin Panel

Akses `/admin/roles` dan klik tombol "Hapus" pada role yang ingin dihapus.

> ⚠️ **Perhatian:** Role tidak bisa dihapus jika masih digunakan oleh user. Pindahkan user ke role lain terlebih dahulu.

### 3. Assign Role ke User

#### Via Code

```php
use App\Models\User;
use App\Models\Role;

// Cara 1: Menggunakan slug
$user = User::find(1);
$user->assignRole('admin');

// Cara 2: Menggunakan Role instance
$role = Role::findBySlug('admin');
$user->assignRole($role);

// Cara 3: Langsung saat create user
User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => Hash::make('password'),
    'role_id' => Role::findBySlug('admin')->id,
]);
```

### 4. Membatasi Akses Route (Middleware)

#### Single Role

```php
// Hanya admin yang bisa akses
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
});
```

#### Multiple Roles

```php
// Admin ATAU editor bisa akses
Route::middleware(['auth', 'role:admin,editor'])->group(function () {
    Route::get('/content', [ContentController::class, 'index']);
});

// Admin, editor, ATAU moderator bisa akses
Route::middleware(['auth', 'role:admin,editor,moderator'])->group(function () {
    Route::get('/moderation', [ModerationController::class, 'index']);
});
```

### 5. Cek Role di Controller

```php
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // Cek single role
        if ($request->user()->hasRole('admin')) {
            // User adalah admin
        }

        // Cek multiple roles
        if ($request->user()->hasAnyRole(['admin', 'editor'])) {
            // User adalah admin ATAU editor
        }

        // Cek apakah admin (shorthand)
        if ($request->user()->isAdmin()) {
            // User adalah admin
        }

        // Dapatkan nama/slug role
        $roleName = $request->user()->getRoleName();  // "Admin"
        $roleSlug = $request->user()->getRoleSlug();  // "admin"
    }
}
```

### 6. Cek Role di Blade View

#### Menggunakan Blade Directives

```blade
{{-- Cek single role --}}
@role('admin')
    <p>Ini hanya tampil untuk Admin</p>
@endrole

{{-- Cek multiple roles --}}
@hasanyrole(['admin', 'editor'])
    <p>Ini tampil untuk Admin atau Editor</p>
@endhasanyrole

{{-- Shorthand untuk admin --}}
@admin
    <a href="/admin">Admin Dashboard</a>
@endadmin
```

#### Menggunakan @if

```blade
@if(auth()->user()->hasRole('admin'))
    <p>Anda adalah admin</p>
@endif

@if(auth()->user()->hasAnyRole(['admin', 'editor']))
    <p>Anda memiliki akses editing</p>
@endif
```

### 7. Cek Role di Livewire Component

```php
<?php

use Livewire\Component;

class MyComponent extends Component
{
    public function mount()
    {
        // Cek role
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }
    }

    public function someAction()
    {
        // Cek role sebelum aksi
        if (!auth()->user()->hasAnyRole(['admin', 'editor'])) {
            session()->flash('error', 'Anda tidak memiliki akses.');
            return;
        }

        // Lakukan aksi...
    }

    public function render()
    {
        return view('livewire.my-component', [
            'isAdmin' => auth()->user()->isAdmin(),
            'userRole' => auth()->user()->getRoleName(),
        ]);
    }
}
```

## 🔧 Kustomisasi

### Menambah Method ke User Model

Anda bisa menambah helper methods ke `User.php`:

```php
// Cek role spesifik
public function isEditor(): bool
{
    return $this->hasRole('editor');
}

public function isModerator(): bool
{
    return $this->hasRole('moderator');
}

// Cek apakah user memiliki role yang lebih tinggi
public function hasHigherRoleThan(User $user): bool
{
    $hierarchy = ['user' => 1, 'editor' => 2, 'moderator' => 3, 'admin' => 4];

    return ($hierarchy[$this->getRoleSlug()] ?? 0) > ($hierarchy[$user->getRoleSlug()] ?? 0);
}
```

### Menambah Blade Directive Baru

Di `AppServiceProvider.php`:

```php
// Directive untuk editor
Blade::if('editor', function () {
    return auth()->check() && auth()->user()->hasRole('editor');
});

// Directive untuk moderator
Blade::if('moderator', function () {
    return auth()->check() && auth()->user()->hasRole('moderator');
});
```

Penggunaan:

```blade
@editor
    <p>Konten khusus editor</p>
@endeditor
```

### Menambah Middleware untuk Role Spesifik

Di `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'role' => RoleMiddleware::class,
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
    ]);
})
```

Buat file `app/Http/Middleware/AdminMiddleware.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()?->isAdmin()) {
            abort(403);
        }

        return $next($request);
    }
}
```

## 📊 Database Schema

### Tabel `roles`

| Column      | Type              | Description                      |
| ----------- | ----------------- | -------------------------------- |
| id          | bigint            | Primary key                      |
| name        | string            | Nama role (unik)                 |
| slug        | string            | Slug untuk URL/middleware (unik) |
| description | string (nullable) | Deskripsi role                   |
| created_at  | timestamp         |                                  |
| updated_at  | timestamp         |                                  |

### Tabel `users` (kolom tambahan)

| Column  | Type              | Description                |
| ------- | ----------------- | -------------------------- |
| role_id | bigint (nullable) | Foreign key ke tabel roles |

## 🛡️ Best Practices

### 1. Selalu Gunakan Middleware di Route

```php
// ✅ Benar
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Routes admin
});

// ❌ Hindari cek role di setiap controller method
```

### 2. Gunakan Blade Directive untuk UI

```blade
{{-- ✅ Benar --}}
@admin
    <button>Delete</button>
@endadmin

{{-- ❌ Hindari --}}
@if(auth()->user() && auth()->user()->role && auth()->user()->role->slug === 'admin')
    <button>Delete</button>
@endif
```

### 3. Eager Load Role

```php
// ✅ Benar - eager load untuk menghindari N+1 query
$users = User::with('role')->paginate(10);

// ❌ Hindari - menyebabkan N+1 query
$users = User::paginate(10);
foreach ($users as $user) {
    echo $user->role->name; // Query setiap iterasi
}
```

### 4. Cache Role untuk Performa

```php
// Di User model atau helper
public function getCachedRole()
{
    return cache()->remember("user_{$this->id}_role", 3600, function () {
        return $this->role;
    });
}
```

### 5. Gunakan Policy untuk Authorization Complex

```php
// app/Policies/PostPolicy.php
public function update(User $user, Post $post)
{
    return $user->isAdmin() || $user->id === $post->user_id;
}
```

## 🔗 Links

-   [Laravel Livewire Starter Kit](https://github.com/laravel/livewire-starter-kit)
-   [Laravel Authorization](https://laravel.com/docs/authorization)
-   [Livewire Documentation](https://livewire.laravel.com/docs)

## 📝 License

MIT License
