<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->boolean('isBuy');
            /*
                $table->integer('costPerItem');
                $table->integer('Quantity');
                $table->boolean('priceChanged')->nullable();
                $table->foreignId('item_id');
            */

            $table->foreignId('person_id');

            $table->string('notes')->nullable();
            
            $table->softDeletes();
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
        Schema::dropIfExists('transactions');
    }
}
