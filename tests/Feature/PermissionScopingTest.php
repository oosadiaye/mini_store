<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PermissionScopingTest extends TestCase
{
    use RefreshDatabase;

    public function test_roles_and_permissions_are_scoped_to_tenant()
    {
        $tenant1 = Tenant::create(['id' => 't1', 'slug' => 't1', 'name' => 'T1', 'email' => 't1@example.com']);
        $tenant2 = Tenant::create(['id' => 't2', 'slug' => 't2', 'name' => 'T2', 'email' => 't2@example.com']);

        // Enable Spatie teams AND set tenant container
        setPermissionsTeamId($tenant1->id);
        app()->instance('tenant', $tenant1);
        config(['app.tenant_id' => $tenant1->id]);

        $role1 = Role::create(['name' => 'editor', 'guard_name' => 'web']);
        $permission1 = Permission::create(['name' => 'edit-posts', 'guard_name' => 'web']);
        $role1->givePermissionTo($permission1);

        // dd($role1->toArray());

        // Switch team
        setPermissionsTeamId($tenant2->id);
        app()->instance('tenant', $tenant2);
        config(['app.tenant_id' => $tenant2->id]);

        // This should not find the role/permission from team 1 if scoping works
        $this->assertCount(0, Role::where('name', 'editor')->get());
        
        $role2 = Role::create(['name' => 'editor', 'guard_name' => 'web']);
        $this->assertEquals($tenant2->id, $role2->tenant_id);

        // Switch back
        setPermissionsTeamId($tenant1->id);
        app()->instance('tenant', $tenant1);
        config(['app.tenant_id' => $tenant1->id]);

        $this->assertCount(1, Role::where('name', 'editor')->get());
        $this->assertEquals($tenant1->id, Role::where('name', 'editor')->first()->tenant_id);
    }
}
