<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
 
        $item1 = new \App\Models\Item;
        $item1->provider_id = 5;
        $item1->nama= "P AON 3";
        $item1->category_id = 2;
        $item1->harga = 14000;

        $item1->save();
        
        $item2 = new \App\Models\Item;
        $item2->provider_id = 5;
        $item2->nama= "VC AON 1";
        $item2->category_id = 3;
        $item2->harga = 12700;

        $item2->save();

        $item3 = new \App\Models\Item;
        $item3->provider_id = 3;
        $item3->nama= "PERDANA SIMPATI REGULER";
        $item3->category_id = 2;
        $item3->harga = 2800;

        $item3->save();

        $item4 = new \App\Models\Item;
        $item4->provider_id = 6;
        $item4->nama= "VC XL 11GB";
        $item4->category_id = 3;
        $item4->harga = 12700;

        $item4->save();
    }
}
