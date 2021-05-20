<?php
    /*
        - Separete Phones and Goods table.
        - Maybe remove buy/sell models.
        - Accessories should have "for_phone".
        
        - Edit users scopes.
        - Think about doing calculations on client side.
        
        - Social Media Share
        - Store Website

        - Enable onlyJsonMiddleware.
        
        - https://laravel.com/docs/8.x/deployment
        - https://laravel.com/docs/8.x/passport#deploying-passport
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

        Route::apiResource('accessories', AccessoriesController::class);
        Route::apiResource('phones', PhonesController::class)->except(['store', 'update', 'destroy']);
        Route::post('/phones', [App\Http\Controllers\PhonesController::class, 'search']);

        Route::prefix('transactions')->group(function() {
            Route::apiResources([
                'buy' => BuyController::class,
                'sell' => SellController::class
            ], ['except' => ['update']]);

            Route::get('/', [App\Http\Controllers\TransactionsController::class, 'index']);
            Route::get('/item/{item}', [App\Http\Controllers\TransactionsController::class, 'indexItem']);
            Route::get('/phone/{phone}', [App\Http\Controllers\TransactionsController::class, 'indexPhone']);
            Route::get('/accessory/{accessory}', [App\Http\Controllers\TransactionsController::class, 'indexAccessory']);
        });

        Route::apiResource('items', ItemsController::class)->except(['store']);
        Route::post('items/{type}/{Itemable}',
            [App\Http\Controllers\ItemsController::class, 'storeItemable'])
        ->where(['type' => '(?:phone|accessory)', 'Itemable' => '[0-9]+']);
    //});