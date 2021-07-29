<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use App\Models\baseModel;
use App\Models\Transaction;

class Person extends baseModel {    
    protected $fillable = ['name', 'address', 'phone1', 'phone2', 'fax', 'notes'];
    protected $table = 'people';
    protected $hidden = ['isVendor'];
    
    public function modelFilter() {
        return $this->provideFilter(\App\ModelFilters\PersonFilter::class);
    }

    public static function boot() {
        parent::boot();

        if (isset(static::$isVendor)) {
            static::addGlobalScope('isVendor', function (Builder $builder) {
                $builder->where('isVendor', static::$isVendor);
            });
        }
        
        static::creating(function($model) {
            $model->isVendor = static::$isVendor;
        });
    }

    public function Transactions() {
        return $this->hasMany(Transaction::class, 'person_id');
    }
}

class Customer extends Person { static $isVendor = false; }
class Vendor extends Person { static $isVendor = true; }