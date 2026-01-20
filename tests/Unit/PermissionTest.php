<?php

namespace Tests\Unit;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_user_has_users_create_permission_through_wildcard()
    {
        // Arrange: Seed the roles and permissions
        $this->seed(RolePermissionSeeder::class);

        // Create an admin user
        $adminUser = User::factory()->create();
        $adminRole = Role::findByName('Admin');
        $adminUser->assignRole($adminRole);

        // Act & Assert: Check if the user has a specific permission included in the wildcard
        $this->assertTrue($adminUser->hasPermissionTo('users.create'));
        $this->assertTrue($adminUser->hasPermissionTo('users.view'));
        $this->assertTrue($adminUser->hasPermissionTo('users.delete'));
    }
}
