<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('good_id');
            $table->integer('delta');
            $table->integer('currentQuantity')->default(0);
            $table->integer('defaultPrice');
            
            $table->unique(['good_id', 'delta']);            
            /*
            $table->integer('totalBuyCost')->default(0);
            $table->integer('totalSellCost')->default(0);

            $table->integer('totalBuys')->default(0);
            $table->integer('totalSells')->default(0);

            $table->integer('totalReturns')->default(0);
            */
            
            $table->string('notes')->nullable();
            
            $table->foreignId('store_id');
            $table->usersAndStamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('items');
    }
}