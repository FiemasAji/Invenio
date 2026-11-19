<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Super Admin (Hanya satu, tidak terikat ke tenant manapun)
        User::updateOrCreate(
            ['email' => env('SUPERADMIN_EMAIL', 'superadmin@invenio.app')],
            [
                'name'      => 'Super Admin',
                'password'  => Hash::make(env('SUPERADMIN_PASSWORD', 'password')),
                'role'      => 'superadmin',
                'tenant_id' => null,
                'is_active' => true,
            ]
        );

        // 2. Sample Tenant 1: PT Nusantara Tech
        $tenant1 = Tenant::updateOrCreate(
            ['slug' => 'nusantara-tech'],
            [
                'name'      => 'PT Nusantara Tech',
                'email'     => 'contact@nusantaratech.id',
                'phone'     => '021-5551234',
                'address'   => 'Jl. Sudirman Kav. 25, Jakarta Selatan',
                'is_active' => true,
            ]
        );

        // Tenant 1 Admin
        User::updateOrCreate(
            ['email' => 'admin@nusantaratech.id'],
            [
                'name'      => 'Budi (Admin Nusantara Tech)',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'tenant_id' => $tenant1->id,
                'is_active' => true,
            ]
        );

        // Tenant 1 User (Staff)
        User::updateOrCreate(
            ['email' => 'user@nusantaratech.id'],
            [
                'name'      => 'Siti (Staff Nusantara Tech)',
                'password'  => Hash::make('password'),
                'role'      => 'user',
                'tenant_id' => $tenant1->id,
                'is_active' => true,
            ]
        );

        // 3. Sample Tenant 2: CV Karya Mandiri (untuk uji isolasi multi-tenant)
        $tenant2 = Tenant::updateOrCreate(
            ['slug' => 'karya-mandiri'],
            [
                'name'      => 'CV Karya Mandiri',
                'email'     => 'info@karyamandiri.co.id',
                'phone'     => '031-7775678',
                'address'   => 'Jl. Pemuda No. 10, Surabaya',
                'is_active' => true,
            ]
        );

        // Tenant 2 Admin
        User::updateOrCreate(
            ['email' => 'admin@karyamandiri.co.id'],
            [
                'name'      => 'Agus (Admin Karya Mandiri)',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'tenant_id' => $tenant2->id,
                'is_active' => true,
            ]
        );

        // Tenant 2 User (Staff)
        User::updateOrCreate(
            ['email' => 'user@karyamandiri.co.id'],
            [
                'name'      => 'Dewi (Staff Karya Mandiri)',
                'password'  => Hash::make('password'),
                'role'      => 'user',
                'tenant_id' => $tenant2->id,
                'is_active' => true,
            ]
        );
    }
}
