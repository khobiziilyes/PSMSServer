<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run() {        
        $time = new \DateTime(now());

        $atby = [
            'created_at' => $time,
            'updated_at' => $time,
            'created_by_id' => 1,
            'updated_by_id' => 1
        ];

        DB::table('stores')->insertGetId([
            'name' => 'Walido Store',
            'location' => 'SEG, Bouira',
            'pay_date' => date_create('2021-01-01')
        ]);

        DB::table('users')->insert([
            'phone_number' => '0667809272',
            'name' => 'Khobizi Ilyes',
            'store_id' => 1,
            'password' => Hash::make('0')
        ]);

        DB::table('users')->insert([
            'phone_number' => '0559451776',
            'name' => 'Mohamed Chelbeb',
            'store_id' => 1,
            'password' => Hash::make('0')
        ]);

        DB::table('goods')->insert(array_merge([
            'name' => 'Redmi Note 7',
            'brand' => 'Xiaomi',
            'type_id' => 0,
            'store_id' => 0
        ], $atby));

        DB::table('goods')->insert(array_merge([
            'name' => 'AntiShock',
            'brand' => 'Gorilla',
            'type_id' => 1,
            'notes' => 'Some notes to test',
            'store_id' => 0
        ], $atby));

        DB::table('items')->insert(array_merge([
            'itemable_type' => 'App\Models\Phone',
            'itemable_id' => 1,
            'delta' => 0,
            'currentQuantity' => 3,
            'defaultPrice' => 40000,
            'totalBuyCost' => 0,
            'totalSellCost' => 0,
            'totalBuys' => 0,
            'totalSells' => 0,
            'store_id' => 1
        ], $atby));

        DB::table('items')->insert(array_merge([
            'itemable_type' => 'App\Models\Accessory',
            'itemable_id' => 2,
            'delta' => 0,
            'currentQuantity' => 5,
            'defaultPrice' => 1000,
            'totalBuyCost' => 0,
            'totalSellCost' => 0,
            'totalBuys' => 0,
            'totalSells' => 0,
            'store_id' => 1
        ], $atby));

        DB::table('people')->insert(array_merge([
            'name' => 'First Vendor',
            'isVendor' => true,
            'address' => 'Sour El Ghozlane',
            'phone1' => '0799244496',
            'phone2' => null,
            'fax' => null,
            'store_id' => 0
        ], $atby));

        DB::table('people')->insert(array_merge([
            'name' => 'First Customer',
            'isVendor' => false,
            'address' => 'Sour El Ghozlane',
            'phone1' => '0799244496',
            'phone2' => null,
            'fax' => null,
            'store_id' => 0
        ], $atby));
    }
}
