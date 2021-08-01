<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use App\Models\baseModel;
use App\Models\Item;

class Product extends baseModel {
    static $storeIdScope = false;
    
    protected $fillable = ['name', 'brand', 'notes', 'type_id', 'is_public'];
    protected $hidden = ['pivot', 'is_public'];
    
    static $case = ['name', 'brand'];
    
    public function getIsPhoneAttribute() {
        return static::$isPhone;
    }

    public function Items() {
        return $this->morphMany(Item::class, 'itemable');
    }

    public function modelFilter() {
        return $this->provideFilter(\App\ModelFilters\ProductFilter::class);
    }

    public function getIsWritableAttribute() {
        return $this->getIsWritableAttributeInit() && !$this->is_public;
    }
}

class Phone extends Product { static $isPhone = true; }
class Accessory extends Product { static $isPhone = false; }