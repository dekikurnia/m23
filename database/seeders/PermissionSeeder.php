<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'menu-master',
            'menu-pindah-barang',
            'menu-pembelian',
            'menu-penjualan-gudang',
            'menu-penjualan-retail',
            'menu-penjualan-grosir',
            'menu-piutang-penjualan',
            'menu-laporan',
            'menu-manajemen-pengguna'
         ];
 
         foreach ($permissions as $permission) {
              Permission::create(['name' => $permission]);
         }
    }
}
