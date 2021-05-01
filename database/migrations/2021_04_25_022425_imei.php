<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Imei extends Migration {
    public function up() {
        Schema::create('imei', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id'); // u sure ?

            $table->string('imei')->unique();

            $table->foreignId('buy_payment_id');
            $table->foreignId('sell_payment_id')->nullable();
            
            $table->foreignId('store_id');
            $table->usersAndStamps();
        });
    }

    public function down() {
        Schema::dropIfExists('imei');
    }
}
