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
        DB::table('stores')->truncate();
        DB::table('users')->truncate();
        
        $theStoreId = DB::table('stores')->insertGetId([
            'name' => 'Walido Store',
            'location' => 'SEG, Bouira',
            'pay_date' => date_create('2021-01-01')
        ]);

        DB::table('users')->insert([
            'phone_number' => '0667809272',
            'name' => 'Khobizi Ilyes',
            'store_id' => $theStoreId,
            'password' => Hash::make('0')
        ]);
    }
}
