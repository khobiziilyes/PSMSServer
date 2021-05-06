<?php
    /*
        - Cart model should use baseModel.
        - Update/Insert validation fields are not the same ... First one aren't required !

        - Create common validation rules like: quantity - price ...
        - Fix type_id.
        - https://laravel.com/docs/8.x/authorization
        - Flexy.
        - Enable onlyJsonMiddleware.
        - Think about doing calculations on client side.
        - Not able to sell under requriedMinimumPrice.
        - https://github.com/leshawn-rice/grabaphone
    */

    use Illuminate\Support\Facades\Route;
    
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
        Route::apiResources([
            'vendors' => VendorsController::class,
            'customers' => CustomersController::class,
        ]);

        Route::apiResources([
            'phones' => PhonesController::class,
            'accessories' => AccessoriesController::class,
        ]);

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