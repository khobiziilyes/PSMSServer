<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use App\Models\baseModel;
use App\Models\Item;

class Good extends baseModel {
    protected $fillable = ['name', 'brand', 'notes', 'type_id'];
    protected $table = 'goods';
    protected $appends = ['isPhone'];

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        
        $this->makeHiddenIf(static::$isPhone, [
            'type_id',
            'created_by',
            'created_at',
            'updated_by',
            'updated_at',
            'notes'
        ]);
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
}

class Phone extends Good { static $isPhone = true; }
class Accessory extends Good { static $isPhone = false; }