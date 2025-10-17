<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->foreignId('role_id')
                    ->nullable()
                    ->after('role')
                    ->constrained('roles')
                    ->nullOnDelete();
            }
        });

        // Migrate legacy role column values into the new relationship
        if (Schema::hasColumn('users', 'role')) {
            $roles = DB::table('roles')->pluck('id', 'name');

            DB::table('users')
                ->whereNull('role_id')
                ->whereNotNull('role')
                ->orderBy('id')
                ->lazy()
                ->each(function ($user) use ($roles) {
                    $roleName = $user->role;
                    $roleId = $roles[$roleName] ?? null;

                    if ($roleId) {
                        DB::table('users')
                            ->where('id', $user->id)
                            ->update(['role_id' => $roleId]);
                    }
                });
        }

        // Ensure every user has a role_id; default to admin if available
        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        if ($adminRoleId) {
            DB::table('users')
                ->whereNull('role_id')
                ->update(['role_id' => $adminRoleId]);
        }

        // Drop legacy enum role column if it exists
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'staff', 'viewer'])
                    ->default('staff')
                    ->after('phone');
            }
        });

        // Restore legacy role column values from role_id
        $roles = DB::table('roles')->pluck('name', 'id');

        DB::table('users')
            ->orderBy('id')
            ->lazy()
            ->each(function ($user) use ($roles) {
            $roleName = $roles[$user->role_id] ?? 'staff';

            DB::table('users')
                ->where('id', $user->id)
                ->update(['role' => $roleName]);
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropConstrainedForeignId('role_id');
            }
        });
    }
};
