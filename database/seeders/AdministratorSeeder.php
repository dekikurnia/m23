<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdministratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $administrator = new \App\Models\User;

        $technician = new \App\Models\User;
        $technician->username = "dekikurnia";
        $technician->name = "Deki Kurnia";
        $technician->password = \Hash::make("rahasia0909");

        $role = Role::create(['name' => 'Technician']);

        $permissions = Permission::pluck('id','id')->all();
        $role->syncPermissions($permissions);
        $administrator->assignRole([$role->id]);

        $administrator->save();
    }
}
