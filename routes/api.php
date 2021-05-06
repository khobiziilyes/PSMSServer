<?php
    /*
        - Updated getValidationRule in baseController to include isUpdate Argument.
        - Check destroy() property, if useful.
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
        ], ['except' => 'store']);

        Route::prefix('transactions')->group(function() {
            Route::apiResources([
                'buy' => BuyController::class,
                'sell' => SellController::class
            ], ['except' => ['update', 'destroy']]);

            Route::delete('buy/{Transaction}', [App\Http\Controllers\BuyController::class, 'destroyBuy']);
            Route::delete('sell/{Transaction}', [App\Http\Controllers\SellController::class, 'destroySell']);
            Route::get('/', [App\Http\Controllers\TransactionsController::class, 'index']);
        });

        Route::apiResource('items', ItemsController::class)->except(['store']);
        Route::post('items/phone/{Itemable}', [App\Http\Controllers\ItemsController::class, 'storePhoneItem']);
        Route::post('items/accessory/{Itemable}', [App\Http\Controllers\ItemsController::class, 'storeAccessoryItem']);
    //});