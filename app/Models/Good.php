<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use App\Models\baseModel;
use App\Models\Item;

class Good extends baseModel {
    protected $fillable = ['name', 'brand', 'notes', 'type_id'];
    protected $table = 'goods';
    protected $appends = ['isPhone'];
    protected $_hidden = ['isPhone'];

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->makeHiddenIf(static::$isPhone, ['type_id']);
    }

    public static function boot() {
        parent::boot();
         
        static::addGlobalScope('type_id', function (Builder $builder) {
            $builder->where('type_id', (static::$isPhone ? '' : '!' ) . '=', 0);
        });    
    }

    public function getIsPhoneAttribute() {
        return static::$isPhone;
    }

    public function Items() {
        return $this->morphMany(Item::class, 'itemable');
    }

    public function modelFilter() {
        return $this->provideFilter(\App\ModelFilters\GoodFilter::class);
    }
}

class Phone extends Good { static $isPhone = true; }
class Accessory extends Good { static $isPhone = false; }