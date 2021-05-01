<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use \App\Models\baseModel;

class Person extends baseModel {
    protected $fillable = ['name', 'address', 'phone1', 'phone2', 'fax', 'notes'];
    protected $table = 'people';

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->hidden[] = 'isVendor';
    }

    public static function boot() {
        parent::boot();

        if (isset(static::$isVendor)) {
            static::addGlobalScope('isVendor', function (Builder $builder) {
                $builder->where('isVendor', static::$isVendor);
            });
        }

        static::creating(function($model){
            $model->isVendor = static::$isVendor;
        });
    }
}

class Customer extends Person { static $isVendor = false; }
class Vendor extends Person { static $isVendor = true; }