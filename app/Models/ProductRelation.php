<?php

namespace App\Models;

use App\Models\baseModel;

class ProductRelation extends baseModel {    
    protected $fillable = ['phone_id', 'accessory_id'];
    protected $table = 'accessory_phone';
}