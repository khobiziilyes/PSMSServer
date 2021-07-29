<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhonesTable extends Migration {
    public function up() {
        Schema::create('phones', function (Blueprint $table) {
            $table->id();
            
            $table->string('name');
            $table->string('brand');
            $table->boolean('is_public')->default(false);
            $table->notes();
            
            $table->foreignId('store_id');
            $table->usersAndStamps();
        });
    }

    public function down() {
        Schema::dropIfExists('phones');
    }
}