<?php

namespace Database\Seeders;

use App\Models\Group;
use Bouncer;
use Illuminate\Database\Seeder;

class BouncerSeeder extends Seeder {
    public function run() {
 		$ALL_PERMISSIONS = config('app.ALL_PERMISSIONS');
        foreach ($ALL_PERMISSIONS as $permission) Bouncer::allow('owner')->to($permission);
        
        foreach (Group::pluck('owner_id')->toArray() as $owner_id) Bouncer::assign('owner')->to($owner_id);
    }
}