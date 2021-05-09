<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category1 = new \App\Models\Category;
        $category1->nama = "Elektrik";
        $category1->save();

        $category2 = new \App\Models\Category;
        $category2->nama = "Perdana";
        $category2->save();

        $category3 = new \App\Models\Category;
        $category3->nama = "Voucher";
        $category3->save();
    }
}
