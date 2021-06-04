<?php

namespace App\Providers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    public function boot() {
        Schema::defaultStringLength(191);
        
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
            if ($perPage === null) {
                $perPage = request()->query('perPage');
                $defaultPerPage = 10;
                $maxPerPage = 100;

                $perPage = (filter_var($perPage, FILTER_VALIDATE_INT) === false || (int) $perPage < 1)
                ? $defaultPerPage
                : min((int) $perPage, $maxPerPage);
            }
            
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