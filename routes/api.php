<?php

    /*
        - WHAT ABOUT CONVERTING Cart TO A WHOLE NEW TABLE !
        - Transactions "destroy" ...
    
        - "Phone" on people is unique.
        - Create common validation rules like: quantity - price ...
        - Fix type_id & its validation.
        - https://laravel.com/docs/8.x/authorization
        - Flexy.
        - Enable onlyJsonMiddleware in "Kernel.php".
    */

    use Illuminate\Support\Facades\Route;
    use App\Models\Buy;
    use App\Models\Phone;

    //Illuminate\Support\Facades\Auth::loginUsingId(1);
    
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
        ], ['except' => 'store']);

        Route::apiResources([
            'buy' => BuyController::class,
            'sell' => SellController::class
        ], ['except' => ['update', 'destroy']]);

        Route::delete('buy/{Transaction}', [App\Http\Controllers\BuyController::class, 'destroyBuy']);
        Route::delete('sell/{Transaction}', [App\Http\Controllers\SellController::class, 'destroySell']);

        Route::apiResource('items', ItemsController::class)->except(['store']);
        Route::post('items/phone/{Itemable}', [App\Http\Controllers\ItemsController::class, 'storePhoneItem']);
        Route::post('items/accessory/{Itemable}', [App\Http\Controllers\ItemsController::class, 'storeAccessoryItem']);
    //});