<?php

namespace App\Providers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    public function register() {
    
    }

    public function boot() {
        Schema::defaultStringLength(191);
        
        Blueprint::macro('users', function() {
            $this->foreignId('created_by_id');
            $this->foreignId('updated_by_id');
        });

        Blueprint::macro('usersAndStamps', function() {
            $this->timestamps();
            $this->users();
        });

        Validator::extendImplicit('emptyOrValid', function ($attribute, $value, $parameters, $validator) {
            return ($value === '' || $value === null || preg_match($parameters[0], $value));
        });

        Validator::extend('notes', function ($attribute, $value, $parameters, $validator) {
            return ($value === '' || $value === null || preg_match('/HERE THE NOTES PATTERN/', $value));
        });

        Builder::macro('paginateAuto', function ($perPage = null, ...$args) {
            if ($perPage === null) {
                $perPage = request()->query('per_page');
                $defaultPerPage = 10;
                $maxPerPage = 100;

                $perPage = (filter_var($perPage, FILTER_VALIDATE_INT) === false || (int) $perPage < 1)
                ? $defaultPerPage
                : min((int) $perPage, $maxPerPage);
            }
            
            $currentPage = request()->query('page', null);
            if (filter_var($currentPage, FILTER_VALIDATE_INT) === false || (int) $currentPage < 0) $currentPage = 1;
            if ((int) $currentPage === 0) $currentPage = floor($this->count() / $perPage);

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