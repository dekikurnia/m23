<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stock1 = new \App\Models\Stock;
        $stock1 ->item_id = 1;
        $stock1 ->stok_gudang = 0;
        $stock1 ->stok_toko = 84;

        $stock1->save();

        $stock2 = new \App\Models\Stock;
        $stock2 ->item_id = 2;
        $stock2 ->stok_gudang = 24250;
        $stock2 ->stok_toko = -48;

        $stock2->save();

        $stock3 = new \App\Models\Stock;
        $stock3 ->item_id = 3;
        $stock3 ->stok_gudang = 300;
        $stock3 ->stok_toko = 151;

        $stock3->save();

        $stock4 = new \App\Models\Stock;
        $stock4 ->item_id = 4;
        $stock4 ->stok_gudang = 100;
        $stock4 ->stok_toko = 29;

        $stock4->save();
    }
}
