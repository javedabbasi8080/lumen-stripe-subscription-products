<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Role::create([
            'name' => "superadmin",
            "guard_name" => "api"
        ]);

        Role::create([
            'name' => "customer",
            "guard_name" => "api"
        ]);
    }
}
