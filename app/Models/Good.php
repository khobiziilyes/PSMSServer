<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use App\Models\baseModel;
use App\Models\Item;

class Good extends baseModel {
    protected $fillable = ['name', 'brand', 'notes', 'type_id'];
    protected $table = 'goods';
    protected $appendAppends = ['isPhone'];

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
}

class Phone extends Good { static $isPhone = true; protected $appendHidden = ['type_id']; }
class Accessory extends Good { static $isPhone = false; }