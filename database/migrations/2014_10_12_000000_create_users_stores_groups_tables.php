<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// https://app.dbdesigner.net/designer/schema/424960

class CreateUsersStoresGroupsTables extends Migration {
    public function up() {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->nullable()->default(null);
            
            $table->string('name');
            $table->string('phone_number');
            $table->string('password');

            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id');

            $table->string('name');
            $table->string('location');
            $table->date('pay_date');

            $table->timestamps();
        });

        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id');

            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('users');
        Schema::dropIfExists('stores');
        Schema::dropIfExists('group');
    }
}