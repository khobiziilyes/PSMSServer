<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration {
    public function up() {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            
            $table->morphs('itemable');
            $table->integer('delta');
            $table->integer('currentQuantity')->default(0);
            $table->integer('defaultPrice');
            
            $table->integer('totalBuyCost')->default(0);
            $table->integer('totalSellCost')->default(0);

            $table->integer('totalBuys')->default(0);
            $table->integer('totalSells')->default(0);

            //$table->integer('totalReturns')->default(0);
            
            $table->notes();
            $table->unique(['itemable_id', 'delta']);

            $table->foreignId('store_id');
            $table->usersAndStamps();
        });
    }

    public function down() {
        Schema::dropIfExists('items');
    }
}