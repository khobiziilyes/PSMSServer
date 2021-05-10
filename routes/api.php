<?php
    /*
        - Fix type_id.
        
        - https://laravel.com/docs/8.x/authorization
        - Enable onlyJsonMiddleware.
        - Think about doing calculations on client side.
        - sell under requriedMinimumPrice auth.
        - change sell price auth.
        - https://github.com/leshawn-rice/grabaphone
    */

    use Illuminate\Support\Facades\Route;
    
    Illuminate\Support\Facades\Auth::loginUsingId(1);
    
    Route::prefix('devices')->group(function() {
        Route::get('search/{term}', [App\Http\Controllers\PhonesScrapController::class, 'searchDevices']);
        Route::get('specs/{endPoint}', [App\Http\Controllers\PhonesScrapController::class, 'getDeviceSpecs']);
    });

    Route::get('test', [App\Http\Controllers\PhonesScrapController::class, 'getDeviceSpecs']);

    Route::prefix('auth')->group(function () {
        Route::post('login', 'AuthController@login');
        //Route::post('register', 'AuthController@register');
        
        Route::middleware('auth:api')->group(function() {
            Route::get('user', 'AuthController@user');
            Route::get('logout', 'AuthController@logout');
        });
    });

    //Route::middleware('auth:api')->group(function() {
        Route::apiResources([
            'vendors' => VendorsController::class,
            'customers' => CustomersController::class,
        ]);

        Route::apiResource('accessories', PhonesController::class);
        Route::apiResource('phones', PhonesController::class)->except(['store', 'update', 'destroy']);
        Route::post('/phones', [App\Http\Controllers\PhonesController::class, 'search']);

        Route::prefix('transactions')->group(function() {
            Route::apiResources([
                'buy' => BuyController::class,
                'sell' => SellController::class
            ], ['except' => ['update']]);

            Route::get('/', [App\Http\Controllers\TransactionsController::class, 'index']);
        });

        Route::apiResource('items', ItemsController::class)->except(['store']);
        Route::post('items/{type}/{Itemable}',
            [App\Http\Controllers\ItemsController::class, 'storeItemable'])
        ->where(['type' => '(?:phone|accessory)', 'Itemable' => '[0-9]+']);
    //});