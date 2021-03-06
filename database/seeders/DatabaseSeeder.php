<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    public function run() {        
        $time = new \DateTime(now());

        DB::statement('SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";');

        $atby = [
            'created_at' => $time,
            'updated_at' => $time,
            'created_by_id' => 0,
            'updated_by_id' => 0
        ];

        $this->createGroup([
            'id' => 0,
            'phone_number' => '',
            'name' => 'PSMS'
        ], [
            'id' => 0,
            'name' => 'PSMS',
            'location' => '',
            'pay_date' => date_create('2021-01-01')
        ], [
            'id' => 0,
            'maxUsers' => 0
        ])[0];

        $store1 = $this->createGroup([
            'phone_number' => '0667809272',
            'name' => 'Khobizi Ilyes'
        ],[
            'name' => "Ilyes's Store",
            'location' => 'SEG, Bouira',
            'pay_date' => date_create('2021-01-01')
        ])[0];

        DB::table('users')->insert([
            [
                'phone_number' => '0799244496',
                'name' => 'Mohammed Chelbeb',
                'store_id' => $store1,
                'password' => Hash::make('0')
            ],
            [
                'phone_number' => '0559451776',
                'name' => 'Mahdi Mameche',
                'store_id' => $store1,
                'password' => Hash::make('0')
            ]
        ]);

        $IDs = $this->createGroup([
            'phone_number' => '0667809272',
            'name' => 'Bouterfes Abdellah',
        ],[
            'name' => "Abdou's Store",
            'location' => 'Ain Defla, Ain Defla',
            'pay_date' => date_create('2021-01-01')
        ]);

        $store2 = $IDs[0];

        DB::table('users')->insert([
            [
                'phone_number' => '0559510225',
                'name' => 'Oussama Kassed',
                'store_id' => $store2,
                'password' => Hash::make('0')
            ]
        ]);

        $group2 = $IDs[1];

        $store3 = DB::table('stores')->insertGetId([
            'name' => "Mido's Store",
            'location' => 'Stawali, Alger',
            'pay_date' => date_create('2021-01-01'),
            'group_id' => $group2
        ]);

        DB::table('users')->insert([
            [
                'phone_number' => '0559510226',
                'name' => 'Moha',
                'store_id' => $store3,
                'password' => Hash::make('0')
            ]
        ]);

        $this->seedProducts($atby);
        $this->seedItems($atby);
        $this->seedPeople($atby);
    }

    public function seedProducts($atby) {
        DB::table('phones')->insert(array_merge([
            'name' => 'Redmi Note 7',
            'brand' => 'Xiaomi',
            'store_id' => 0
        ], $atby));

        DB::table('accessories')->insert(array_merge([
            'name' => 'AntiShock',
            'brand' => 'Gorilla',
            'type_id' => 1,
            'notes' => 'Some notes to test',
            'store_id' => 0
        ], $atby));
    }

    public function seedItems($atby) {
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
            'itemable_id' => 1,
            'delta' => 0,
            'currentQuantity' => 5,
            'defaultPrice' => 1000,
            'totalBuyCost' => 0,
            'totalSellCost' => 0,
            'totalBuys' => 0,
            'totalSells' => 0,
            'store_id' => 1
        ], $atby));
    }

    public function seedPeople($atby) {
        $this->seedPerson(1, false, $atby);
        $this->seedPerson(2, true, $atby);
    }

    public function seedPerson($id, $isVendor, $atby) {
        DB::table('people')->insert(array_merge([
            'id' => $id,
            'isVendor' => $isVendor,
            'name' => 'UNKNOWN',
            'address' => null,
            'phone1' => '',
            'phone2' => null,
            'fax' => null,
            'store_id' => 0
        ], $atby));
    }

    public function createGroup($owner, $store, $group = []) {
        $owner_id = DB::table('users')->insertGetId(array_merge($owner, [
            'password' => Hash::make('0')
        ]));

        $group_id = DB::table('groups')->insertGetId(array_merge([
            'owner_id' => $owner_id,
            'maxUsers' => 1
        ], $group));

        $default_store_id = DB::table('stores')->insertGetId(array_merge($store, [
            'group_id' => $group_id
        ]));

        DB::table('users')->where('id', $owner_id)->update(['store_id' => $default_store_id]);

        return [$default_store_id, $group_id];
    }
}