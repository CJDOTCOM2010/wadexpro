<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name', 100)->unique();
                $table->string('label')->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_system')->default(false);
                $table->timestamps();
            });
        } else {
            // Ensure our extra columns exist on the existing roles table
            Schema::table('roles', function (Blueprint $table) {
                if (!Schema::hasColumn('roles', 'label'))       $table->string('label')->nullable()->after('name');
                if (!Schema::hasColumn('roles', 'description')) $table->text('description')->nullable()->after('label');
                if (!Schema::hasColumn('roles', 'is_system'))   $table->boolean('is_system')->default(false)->after('description');
            });
        }

        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name', 100)->unique();
                $table->string('module', 50);
                $table->string('label')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('permissions', function (Blueprint $table) {
                if (!Schema::hasColumn('permissions', 'module')) $table->string('module', 50)->default('general')->after('name');
                if (!Schema::hasColumn('permissions', 'label'))  $table->string('label')->nullable()->after('module');
            });
        }

        if (!Schema::hasTable('role_permission')) {
            Schema::create('role_permission', function (Blueprint $table) {
                $table->uuid('role_id');
                $table->uuid('permission_id');
                $table->primary(['role_id', 'permission_id']);
                $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
                $table->foreign('permission_id')->references('id')->on('permissions')->cascadeOnDelete();
            });
        }

        if (!Schema::hasTable('user_role')) {
            Schema::create('user_role', function (Blueprint $table) {
                $table->uuid('user_id');
                $table->uuid('role_id');
                $table->primary(['user_id', 'role_id']);
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
                $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_role');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
