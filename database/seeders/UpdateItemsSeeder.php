<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateItemsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('items')->update([
            'image' => 'default.png',   // bisa ganti sesuai kebutuhan
            'price' => 10000            // contoh harga default Rp10.000
        ]);
    }
}
