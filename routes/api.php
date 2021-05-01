<?php

    /*
        - person_type - good_id .... Cannot be edited.
        - Decrease/Increase currentQuantity.
        - Re-write tests.
        - Check quantity before sell.
        - Multi transactions in one.
        - Cannot double sell same IMEI.
        - Fix Delta & its validation.
        - use morph.
        - Transactions "destroy" && Deal with IMEI specially ...
        - https://laravel.com/docs/8.x/authorization
        - Flexy.
        - Enable onlyJsonMiddleware in "Kernel.php".
    */

    use Illuminate\Support\Facades\Route;
    use App\Models\Buy;

    Illuminate\Support\Facades\Auth::loginUsingId(1);

    Route::prefix('auth')->group(function () {
        Route::post('login', 'AuthController@login');
        //Route::post('register', 'AuthController@register');
        
        Route::middleware('auth:api')->group(function() {
            Route::get('user', 'AuthController@user');
            Route::get('logout', 'AuthController@logout');
        });
    });

    //Route::middleware('auth:api')->group(function() {
        Route::prefix('people')->group(function () {
            Route::apiResources([
                'vendors' => VendorsController::class,
                'customers' => CustomersController::class
            ]);
        });

        Route::prefix('goods')->group(function () {
            Route::apiResources([
                'phones' => PhonesController::class,
                'accessories' => AccessoriesController::class
            ]);
        });

        Route::apiResource('items', ItemsController::class);
        
        Route::prefix('transactions')->group(function() {
            Route::apiResources([
                'buy' => BuyController::class,
                'sell' => SellController::class
            ], ['except' => 'update']);
        });

        Route::apiResource('imei', imeiController::class);
    //});