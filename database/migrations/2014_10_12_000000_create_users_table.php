<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration {
    public function up() {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            $table->string('phone_number');
            $table->string('name');
            $table->string('password');
            $table->boolean('isAdmin')->default(0);

            $table->foreignId('store_id');
            $table->rememberToken();
            $table->timestamps();

            foreach (['Accessory', 'Item', 'Customer', 'Vendor', 'Phone', 'Buy', 'Sell'] as $model) {
                foreach (['Read', 'Write', 'Update'] as $method) {
                    $table->boolean('can' . $method . $model)->default(false);
                }
            }
        });
    }

    public function down() {
        Schema::dropIfExists('users');
    }
}
