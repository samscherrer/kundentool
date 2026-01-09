<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'internal_admin',
            'internal_agent',
            'internal_billing',
            'customer_admin',
            'customer_user',
        ] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}
