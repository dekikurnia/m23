<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $provider1 = new \App\Models\Provider;
        $provider1->nama = "AXIS";
        $provider1->save();

        $provider2 = new \App\Models\Provider;
        $provider2->nama = "INDOSAT";
        $provider2->save();

        $provider3 = new \App\Models\Provider;
        $provider3->nama = "SIMPATI";
        $provider3->save();

        $provider4 = new \App\Models\Provider;
        $provider4->nama = "SMARTFREN";
        $provider4->save();

        $provider5 = new \App\Models\Provider;
        $provider5->nama = "THREE";
        $provider5->save();

        $provider6 = new \App\Models\Provider;
        $provider6->nama = "XL";
        $provider6->save();
    }
}
