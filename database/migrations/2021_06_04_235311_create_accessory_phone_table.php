<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessoryPhoneTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accessory_phone', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('phone_id');
            $table->foreignId('accessory_id');
            
            $table->foreignId('store_id');
            $table->usersAndStamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accessory_phone');
    }
}
