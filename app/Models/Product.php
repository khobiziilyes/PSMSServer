<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use App\Models\baseModel;
use App\Models\Item;

class Product extends baseModel {
    static $storeIdScope = false;
    
    protected $fillable = ['name', 'brand', 'notes', 'type_id'];
    protected $appends = ['isPhone'];
    protected $_hidden = ['isPhone', 'pivot'];

    public function getIsPhoneAttribute() {
        return static::$isPhone;
    }

    public function Items() {
        return $this->morphMany(Item::class, 'itemable');
    }

    public function modelFilter() {
        return $this->provideFilter(\App\ModelFilters\ProductFilter::class);
    }
}

class Phone extends Product {
    static $isPhone = true;
    
    public function Accessories() {
        return $this->belongsToMany(Accessory::class);
    }
}

class Accessory extends Product {
    static $isPhone = false;
    
    public function Phones() {
        return $this->belongsToMany(Phone::class);
    }
}