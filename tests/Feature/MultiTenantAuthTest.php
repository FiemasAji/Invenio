<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MultiTenantAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('INVENIO');
        $response->assertSee('Masuk');
    }

    public function test_superadmin_can_login_and_access_dashboard(): void
    {
        $superadmin = User::create([
            'name'      => 'Super Admin',
            'email'     => 'superadmin@invenio.app',
            'password'  => Hash::make('password'),
            'role'      => 'superadmin',
            'tenant_id' => null,
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email'    => 'superadmin@invenio.app',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($superadmin);
        $response->assertRedirect('/dashboard');

        $dashResponse = $this->actingAs($superadmin)->get('/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Super Admin');
    }

    public function test_tenant_admin_is_scoped_to_their_tenant(): void
    {
        $tenant = Tenant::create([
            'name'      => 'PT Maju Bersama',
            'slug'      => 'maju-bersama',
            'is_active' => true,
        ]);

        $admin = User::create([
            'name'      => 'Admin Maju',
            'email'     => 'admin@maju.com',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'tenant_id' => $tenant->id,
            'is_active' => true,
        ]);

        $dashResponse = $this->actingAs($admin)->get('/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('PT Maju Bersama');
        $dashResponse->assertSee('Tenant Admin');
    }

    public function test_tenant_user_is_scoped_to_their_tenant(): void
    {
        $tenant = Tenant::create([
            'name'      => 'PT Maju Bersama',
            'slug'      => 'maju-bersama',
            'is_active' => true,
        ]);

        $user = User::create([
            'name'      => 'Staff Maju',
            'email'     => 'staff@maju.com',
            'password'  => Hash::make('password'),
            'role'      => 'user',
            'tenant_id' => $tenant->id,
            'is_active' => true,
        ]);

        $dashResponse = $this->actingAs($user)->get('/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('PT Maju Bersama');
        $dashResponse->assertSee('User (Staff Tenant)');
    }

    public function test_inactive_tenant_user_is_blocked_from_dashboard(): void
    {
        $inactiveTenant = Tenant::create([
            'name'      => 'PT Nonaktif',
            'slug'      => 'nonaktif',
            'is_active' => false,
        ]);

        $user = User::create([
            'name'      => 'User Nonaktif',
            'email'     => 'user@nonaktif.com',
            'password'  => Hash::make('password'),
            'role'      => 'user',
            'tenant_id' => $inactiveTenant->id,
            'is_active' => true,
        ]);

        $dashResponse = $this->actingAs($user)->get('/dashboard');
        $dashResponse->assertRedirect('/login');
    }

    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }
}
