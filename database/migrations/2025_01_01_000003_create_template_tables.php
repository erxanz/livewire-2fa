<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Global Settings
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();            // Kunci setting (app.name, app.logo)
            $table->text('value')->nullable();           // Nilai setting
            $table->string('type')->default('string');   // Tipe: string, boolean, integer, float, json
            $table->string('group')->default('general'); // Grup: general, mail, appearance
            $table->string('label')->nullable();         // Label untuk form
            $table->text('description')->nullable();     // Deskripsi setting
            $table->boolean('is_public')->default(false); // Apakah bisa diakses publik
            $table->timestamps();
        });

        // Modules / Feature Toggle
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');                      // Nama module (Laporan, Inventory)
            $table->string('slug')->unique();            // Slug untuk pengecekan (laporan)
            $table->text('description')->nullable();     // Deskripsi module
            $table->boolean('is_active')->default(true); // Status aktif
            $table->json('settings')->nullable();        // Konfigurasi tambahan per module
            $table->integer('order')->default(0);        // Urutan tampilan
            $table->timestamps();
        });

        // Activity Log / Audit Log
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');                    // create, update, delete, login, logout
            $table->string('model_type')->nullable();    // App\Models\User
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('old_values')->nullable();      // Nilai sebelum perubahan
            $table->json('new_values')->nullable();      // Nilai setelah perubahan
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('description')->nullable();     // Deskripsi aktivitas
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
            $table->index('action');
            $table->index('created_at');
        });

        // In-App Notifications
        Schema::create('in_app_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');                      // info, success, warning, error
            $table->string('title');
            $table->text('message');
            $table->string('action_url')->nullable();    // URL untuk action
            $table->string('action_label')->nullable();  // Label tombol action
            $table->json('data')->nullable();            // Data tambahan
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
        });

        // Backups
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('disk')->default('local');
            $table->string('path');
            $table->unsignedBigInteger('size');          // Size in bytes
            $table->string('type')->default('full');     // full, database, files
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();           // Catatan backup
            $table->timestamps();
        });

        // Add is_active column to users
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('role_id');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'last_login_at', 'last_login_ip']);
        });

        Schema::dropIfExists('backups');
        Schema::dropIfExists('in_app_notifications');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('settings');
    }
};
