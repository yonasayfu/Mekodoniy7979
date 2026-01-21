<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds for roles and permissions.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissionGroups = config('rbac.permissions', []);
        $wildcardPermissions = config('rbac.wildcards', []);

        $allPermissions = collect($permissionGroups)->flatten()->unique()->values()->all();

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        foreach ($wildcardPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $roles = config('rbac.roles', []);

        foreach ($roles as $roleName => $roleConfig) {
            $permissions = $this->normalizePermissions(
                $roleConfig['permissions'] ?? [],
                $allPermissions,
                $wildcardPermissions
            );

            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($permissions);
        }

        // Clear cache again
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Normalize a role's permission list, expanding '*' or wildcard entries.
     */
    private function normalizePermissions(array $permissions, array $allPermissions, array $wildcards): array
    {
        $resolved = [];

        foreach ($permissions as $permission) {
            if ($permission === '*') {
                $resolved = array_merge($resolved, $allPermissions, $wildcards);
                continue;
            }

            if (in_array($permission, $wildcards, true)) {
                $resolved[] = $permission;
                continue;
            }

            $resolved[] = $permission;
        }

        return array_unique($resolved);
    }
}
