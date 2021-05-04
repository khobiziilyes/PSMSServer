<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
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
                $perPage = request()->query('perPage');
                $defaultPerPage = 10;
                $maxPerPage = 100;

                $perPage = (filter_var($perPage, FILTER_VALIDATE_INT) === false || (int) $perPage < 1)
                ? $defaultPerPage
                : min((int) $perPage, $maxPerPage);
            }
            
            $paginator = $this->paginate($perPage, ...$args);

            return [
                'currentPage' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'perPage' => $paginator->count(),
                'total' => $paginator->total(),
                'data' => $paginator->items()
            ];
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
