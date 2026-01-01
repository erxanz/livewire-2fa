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
        // Tabel permissions
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');                      // Nama permission (View Users)
            $table->string('slug')->unique();            // Slug untuk pengecekan (view-users)
            $table->string('group')->nullable();         // Grup permission (users, roles, settings)
            $table->string('description')->nullable();   // Deskripsi permission
            $table->timestamps();
        });

        // Pivot table: role_permission
        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['role_id', 'permission_id']);
        });

        // Pivot table: user_permission (direct permission assignment)
        Schema::create('user_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_permission');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
    }
};
