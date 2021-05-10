<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeopleTable extends Migration {
    public function up() {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            
            $table->string('name');
            $table->boolean('isVendor');
            $table->string('address')->nullable();
            $table->string('phone1');
            $table->string('phone2')->nullable();
            $table->string('fax')->nullable();
            $table->notes();

            $table->foreignId('store_id');
            $table->usersAndStamps();
        });
    }

    public function down() {
        Schema::dropIfExists('people');
    }
}
