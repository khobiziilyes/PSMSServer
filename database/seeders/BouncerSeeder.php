<?php

namespace Database\Seeders;

use App\Models\Group;
use Bouncer;
use Illuminate\Database\Seeder;

class BouncerSeeder extends Seeder {
    public function run() {
 		$BASIC_PERMISSIONS = config('app.BASIC_PERMISSIONS');
 		$ULTRA_PERMISSIONS = config('app.ULTRA_PERMISSIONS');
 		
 		Bouncer::allow('owner')->to($BASIC_PERMISSIONS);
 		Bouncer::allow('trusted')->to($ULTRA_PERMISSIONS);

 		$owners_ids = Group::pluck('owner_id')->toArray();
 		Bouncer::assign('owner')->to($owners_ids);
    }
}