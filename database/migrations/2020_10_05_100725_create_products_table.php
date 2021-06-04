<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration {
    public function up() {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            
            $table->string('name');
            $table->string('brand');
            $table->notes();
            $table->integer('type_id');
            
            $table->foreignId('store_id');
            $table->usersAndStamps();
        });
    }
    
    public function down() {
        Schema::dropIfExists('products');
    }
}
