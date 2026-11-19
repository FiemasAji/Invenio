<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
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

        $this->command->info('Super Admin created: ' . env('SUPERADMIN_EMAIL', 'superadmin@invenio.app'));
    }
}
