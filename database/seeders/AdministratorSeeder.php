<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

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
        $administrator->username = "dekikurnia";
        $administrator->name = "Deki Kurnia";
        $administrator->email = "dekikurnia@gmail.com";
        $administrator->password = \Hash::make("rahasia0909");

        $administrator->save();
    }
}
