# Laravel Starter Template

Template aplikasi Laravel yang reusable, scalable, dan aman. Cocok untuk berbagai kebutuhan seperti sistem desa, akademik, UMKM, atau internal perusahaan.

## 🚀 Fitur Utama

### 1. Sistem Role & Permission

-   **Dynamic Roles**: Role disimpan di database (bukan hardcode)
-   **Granular Permissions**: Permission terpisah dari role
-   **Role-Permission Assignment**: Assign multiple permissions ke role
-   **User-Permission Assignment**: Assign permission langsung ke user (bypass role)
-   **Middleware Protection**: `role`, `permission`, `module`

### 2. User Management

-   CRUD user lengkap
-   Assign role & permission
-   Activate/Deactivate user
-   Reset password
-   Track last login

### 3. Global Settings

-   Key-value settings system
-   Grouped settings (general, contact, features, etc.)
-   Type casting (string, boolean, integer, json)
-   Public/private settings
-   Helper function: `settings('key', 'default')`

### 4. Module/Feature Toggle

-   Enable/disable fitur tanpa ubah kode
-   Module-specific settings
-   Middleware `module:slug`
-   Helper function: `module_active('slug')`

### 5. Activity Log / Audit Trail

-   Track semua aktivitas user
-   Log create, update, delete
-   Track old & new values
-   IP address & user agent
-   Trait `LogsActivity` untuk auto-logging

### 6. In-App Notification

-   Notification system built-in
-   Multiple types: info, success, warning, error
-   Action URL support
-   Read/unread status

### 7. Error Pages

-   Custom 403 (Forbidden)
-   Custom 404 (Not Found)
-   Custom 500 (Server Error)
-   Custom 503 (Maintenance)

### 8. API Ready

-   RESTful API dengan Sanctum
-   Token authentication
-   Versioned API (`/api/v1`)
-   Permission-based access control

### 9. Backup System

-   Database backup support
-   File backup support
-   Track backup history
-   Download & restore support

### 10. Two-Factor Authentication

-   TOTP-based 2FA
-   QR Code support
-   Recovery codes

## 📋 Requirements

-   PHP 8.2+
-   Laravel 12.x
-   Node.js 18+
-   SQLite / MySQL / PostgreSQL

## 🛠️ Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/your-repo/laravel-starter-template.git
cd laravel-starter-template
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Setup

```bash
# Untuk SQLite
touch database/database.sqlite

# Edit .env
# DB_CONNECTION=sqlite
# DB_DATABASE=/absolute/path/to/database.sqlite

php artisan migrate
php artisan db:seed
```

### 5. Build Assets

```bash
npm run build
# atau untuk development
npm run dev
```

### 6. Run Application

```bash
php artisan serve
```

## 👤 Default Users

| Email             | Password | Role  |
| ----------------- | -------- | ----- |
| admin@example.com | password | Admin |
| test@example.com  | password | User  |

## 🔐 Role & Permission

### Blade Directives

```blade
{{-- Check role --}}
@role('admin')
    Admin only content
@endrole

{{-- Check multiple roles --}}
@hasanyrole(['admin', 'editor'])
    Admin or Editor content
@endhasanyrole

{{-- Check admin --}}
@admin
    Admin only
@endadmin

{{-- Check permission --}}
@permission('create-users')
    Can create users
@endpermission

{{-- Check multiple permissions --}}
@permissions(['create-users', 'edit-users'])
    Has all permissions
@endpermissions

{{-- Check module --}}
@module('backup')
    Backup module is active
@endmodule
```

### Route Middleware

```php
// Single role
Route::middleware('role:admin')->group(function () {
    // Admin routes
});

// Multiple roles
Route::middleware('role:admin,editor')->group(function () {
    // Admin or Editor routes
});

// Single permission
Route::middleware('permission:create-users')->group(function () {
    // Users with create-users permission
});

// Multiple permissions (any)
Route::middleware('permission:create-users|edit-users')->group(function () {
    // Users with any permission
});

// All permissions required
Route::middleware('permission:create-users|edit-users,all')->group(function () {
    // Users with all permissions
});

// Module check
Route::middleware('module:backup')->group(function () {
    // Only when backup module is active
});
```

### Programmatic Check

```php
// Check role
$user->hasRole('admin');
$user->hasAnyRole(['admin', 'editor']);
$user->isAdmin();

// Check permission
$user->hasPermission('create-users');
$user->hasAnyPermission(['create-users', 'edit-users']);
$user->hasAllPermissions(['create-users', 'edit-users']);

// Get all permissions
$user->getAllPermissions();

// Assign role
$user->assignRole('editor');
$user->removeRole();

// Assign permission directly
$user->givePermission('create-users');
$user->revokePermission('create-users');
$user->syncPermissions([1, 2, 3]);
```

## ⚙️ Settings

### Helper Functions

```php
// Get setting
$appName = settings('app.name', 'Default Name');

// Set setting
set_setting('app.name', 'New Name');

// Get all settings
$all = Setting::getAllAsArray();

// Get public settings only
$public = Setting::getPublicAsArray();

// Get by group
$general = Setting::getByGroup('general');
```

### Available Settings

| Key                      | Type    | Description             |
| ------------------------ | ------- | ----------------------- |
| app.name                 | string  | Application name        |
| app.description          | string  | Application description |
| app.logo                 | string  | Logo URL/path           |
| app.favicon              | string  | Favicon URL/path        |
| app.timezone             | string  | Default timezone        |
| contact.email            | string  | Contact email           |
| features.registration    | boolean | Allow registration      |
| features.two_factor_auth | boolean | Enable 2FA              |
| maintenance.enabled      | boolean | Maintenance mode        |

## 📦 Modules

### Available Modules

| Slug                  | Name                  | Default  |
| --------------------- | --------------------- | -------- |
| user-management       | User Management       | Active   |
| role-management       | Role Management       | Active   |
| permission-management | Permission Management | Active   |
| settings              | Settings              | Active   |
| activity-log          | Activity Log          | Active   |
| notifications         | Notifications         | Active   |
| backup                | Backup System         | Active   |
| api                   | API Access            | Active   |
| reports               | Reports               | Inactive |

### Check Module Status

```php
// Helper function
if (module_active('backup')) {
    // Backup is enabled
}

// Get module
$module = module('backup');
$setting = $module->getSetting('retention_count');

// Toggle module
$module->activate();
$module->deactivate();
$module->toggle();
```

## 📝 Activity Logging

### Auto-Logging dengan Trait

```php
use App\Traits\LogsActivity;

class Post extends Model
{
    use LogsActivity;

    // Optional: Specify which events to log
    protected static array $loggedEvents = ['created', 'updated', 'deleted'];

    // Optional: Exclude attributes from log
    protected static array $excludedFromLog = ['password'];
}
```

### Manual Logging

```php
use App\Models\ActivityLog;

// Using helper
log_activity('custom_action', $model, $oldValues, $newValues, 'Description');

// Using model
ActivityLog::log('login', $user, null, null, 'User logged in');
```

## 🔔 Notifications

### Send Notification

```php
use App\Models\InAppNotification;

// Using helper
notify_user($user, 'Welcome!', 'Your account has been created.', 'success');

// Using model
InAppNotification::send(
    user: $user,
    title: 'New Message',
    message: 'You have a new message.',
    type: 'info',
    actionUrl: '/messages/1',
    actionLabel: 'View Message'
);

// Send to multiple users
InAppNotification::sendToMany(
    userIds: [1, 2, 3],
    title: 'Announcement',
    message: 'System update scheduled.',
    type: 'warning'
);
```

### Get Notifications

```php
// Get unread count
$count = $user->getUnreadNotificationsCount();

// Get notifications
$notifications = $user->inAppNotifications()->unread()->get();

// Mark as read
$notification->markAsRead();

// Mark all as read
$user->markAllNotificationsAsRead();
```

## 🌐 API Usage

### Authentication

```bash
# Login
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# Response
{
  "message": "Login successful",
  "user": {...},
  "token": "1|xxxxx",
  "token_type": "Bearer"
}
```

### Using Token

```bash
curl -X GET http://localhost:8000/api/v1/users \
  -H "Authorization: Bearer 1|xxxxx"
```

### Available Endpoints

| Method | Endpoint            | Permission       |
| ------ | ------------------- | ---------------- |
| POST   | /api/v1/login       | Public           |
| POST   | /api/v1/register    | Public           |
| GET    | /api/v1/user        | Auth             |
| POST   | /api/v1/logout      | Auth             |
| GET    | /api/v1/users       | view-users       |
| POST   | /api/v1/users       | create-users     |
| GET    | /api/v1/users/{id}  | view-users       |
| PUT    | /api/v1/users/{id}  | edit-users       |
| DELETE | /api/v1/users/{id}  | delete-users     |
| GET    | /api/v1/roles       | view-roles       |
| GET    | /api/v1/permissions | view-permissions |

## 📁 Struktur File

```
app/
├── Helpers/
│   └── helpers.php           # Global helper functions
├── Http/
│   ├── Controllers/
│   │   └── Api/V1/           # API Controllers
│   └── Middleware/
│       ├── RoleMiddleware.php
│       ├── PermissionMiddleware.php
│       ├── ModuleMiddleware.php
│       └── EnsureUserIsActive.php
├── Livewire/
│   └── Admin/                # Admin Livewire components
├── Models/
│   ├── User.php
│   ├── Role.php
│   ├── Permission.php
│   ├── Setting.php
│   ├── Module.php
│   ├── ActivityLog.php
│   ├── InAppNotification.php
│   └── Backup.php
├── Providers/
│   └── AppServiceProvider.php
└── Traits/
    └── LogsActivity.php

database/
├── migrations/
│   ├── create_roles_table.php
│   ├── create_permissions_table.php
│   └── create_template_tables.php
└── seeders/
    ├── RoleSeeder.php
    ├── PermissionSeeder.php
    ├── SettingSeeder.php
    └── ModuleSeeder.php

resources/views/
├── errors/
│   ├── 403.blade.php
│   ├── 404.blade.php
│   ├── 500.blade.php
│   └── 503.blade.php
└── livewire/admin/
    ├── user-management.blade.php
    └── role-management.blade.php
```

## 🔧 Konfigurasi

### Environment Variables

```env
# Application
APP_NAME="Laravel Template"
APP_ENV=production
APP_DEBUG=false

# Database
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/database.sqlite

# Mail (untuk notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
```

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=UserTest
```

## 📜 License

MIT License

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📞 Support

-   Email: support@example.com
-   Documentation: [Wiki](https://github.com/your-repo/wiki)
-   Issues: [GitHub Issues](https://github.com/your-repo/issues)
