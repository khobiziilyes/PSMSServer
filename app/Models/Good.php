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
        $this->hidden[] = 'type_id';
    }

    public static function boot() {
        parent::boot();

        if (isset(static::$isPhone)) {
            static::addGlobalScope('type_id', function (Builder $builder) {
                $builder->where('type_id', (static::$isPhone ? '' : '!' ) . '=', 0);
            });
        }
    }

    public function getIsPhoneAttribute() {
        return (intval($this->type_id) === 0);
    }

    public function Items() {
        return $this->hasMany(Item::class, 'good_id');
    }
}

class Phone extends Good { static $isPhone = true; }

class Accessory extends Good { static $isPhone = false; }