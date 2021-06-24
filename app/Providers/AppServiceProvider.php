<?php

namespace App\Providers;

use Bouncer;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class AppServiceProvider extends ServiceProvider {
    public function boot() {
        Schema::defaultStringLength(191);
        Bouncer::dontCache();
        
        Blueprint::macro('notes', function(){
            $this->string('notes')->nullable()->default(null);
        });

        Blueprint::macro('users', function() {
            $this->foreignId('created_by_id');
            $this->foreignId('updated_by_id');
        });

        Blueprint::macro('usersAndStamps', function() {
            $this->timestamps();
            $this->users();
        });

        Validator::extend('name', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[\w\d\- ]{1,30}$/', $value);
        }, 'The field characters are invalid, Or too long.');
        
        Validator::extend('notes', function ($attribute, $value, $parameters, $validator) {
            return ($value === null || preg_match('/^[\w\d\s,.\-\n]{1,250}$/', $value));
        }, 'The field characters are invalid, Or too long.');

        Builder::macro('filterByMorph', function ($relationName, $morphRelations, $name, $value) {
            return $this->filterByMorph($relationName, $morphRelations, function($query) use($name, $value) {
                return $query->whereLike($name, $value);
            });
        });

        Builder::macro('paginateAuto', function ($perPage = null, ...$args) {
            $maxPerPage = 100;

            if ($perPage === null) {
                $perPage = request()->query('perPage');
                $defaultPerPage = 10;

                $perPage = (filter_var($perPage, FILTER_VALIDATE_INT) === false || (int) $perPage < 1)
                ? $defaultPerPage
                : min((int) $perPage, $maxPerPage);
            }
            
            if ($perPage === false) $perPage = $maxPerPage;

            $currentPage = request()->query('page', null);
            if (filter_var($currentPage, FILTER_VALIDATE_INT) === false || (int) $currentPage < 0) $currentPage = 1;

            LengthAwarePaginator::currentPageResolver(function () use ($currentPage) {
                return $currentPage;
            });

            return $this->paginate($perPage, ...$args);
        });

        Validator::extendImplicit('imei', function ($attribute, $value, $parameters, $validator) {
            if(strlen($value) != 15 || !ctype_digit($value)) return false;
            
            $digits = str_split($value);
            $imei_last = array_pop($digits);
            $log = array();
            
            foreach($digits as $key => $n) {
                if ($key & 1){
                    $double = str_split($n * 2);
                    $n = array_sum($double);
                }
                
                $log[] = $n;
            }

            $sum = array_sum($log) * 9;
            return substr($sum, -1) == $imei_last;
        }, 'The IMEI code is invalid.');
    }
}