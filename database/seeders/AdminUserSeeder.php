<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();
        $email = env('ADMIN_EMAIL', 'admin@example.com');
        $password = env('ADMIN_PASSWORD', 'password');

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Admin',
                'password' => Hash::make($password),
                'is_internal' => true,
            ]
        );

        $role = Role::where('name', 'internal_admin')->first();
        if ($role) {
            $user->roles()->sync([$role->id]);
        }
    }
}
