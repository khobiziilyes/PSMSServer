<?php

    /*
        - Multi transactions in one.
        - Transactions "destroy" && Deal with IMEI specially ...
    
        - Create common validation rules like: quantity - price ...
        - person_type - good_id .... Cannot be edited.
        - Fix type_id & its validation.
        - https://laravel.com/docs/8.x/authorization
        - Flexy.
        - Enable onlyJsonMiddleware in "Kernel.php".
    */

    use Illuminate\Support\Facades\Route;
    use App\Models\Buy;
    use App\Models\Phone;

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
            'imei' => imeiController::class
        ]);


        Route::apiResources([
            'phones' => PhonesController::class,
            'accessories' => AccessoriesController::class,
        ], ['except' => 'store']);

        Route::apiResource('items', ItemsController::class)->except(['store']);

        Route::apiResources([
            'buy' => BuyController::class,
            'sell' => SellController::class
        ], ['except' => ['store', 'update', 'destroy']]);

        Route::post('items/phone/{Itemable}', [App\Http\Controllers\ItemsController::class, 'storePhoneItem']);
        Route::post('items/accessory/{Itemable}', [App\Http\Controllers\ItemsController::class, 'storeAccessoryItem']);

        Route::get('buyx/{Item}/{Vendor}', [App\Http\Controllers\BuyController::class, 'storeBuy']);
        Route::get('sellx/{Item}/{Customer}', [App\Http\Controllers\SellController::class, 'storeSell']);
    //});