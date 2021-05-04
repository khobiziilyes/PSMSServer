<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use App\Models\baseModel;
use App\Models\Item;

class Cart extends baseModel {
    protected $fillable = ['name', 'brand', 'notes', 'type_id'];
    protected $table = 'goods';
    protected $appendAppends = ['isPhone'];

    public function getIsPhoneAttribute() {
        return static::$isPhone;
    }
}