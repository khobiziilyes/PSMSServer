<?php
    /*
        - Switch store.
        - $this->whiteListOrderBy
        - Flexy Support.
        
        - Generate PDFs
        - Devices Specs.
        - Auto add Accessories for new phones.
        
        - Do i need show resource?

        - Social Media Share.
        - Store Website.

        - Enable onlyJsonMiddleware.
        
        - https://laravel.com/docs/8.x/deployment
        - https://laravel.com/docs/8.x/passport#deploying-passport
    */
    
    use App\Http\Middleware\isAdminMiddleware;
    use Illuminate\Support\Facades\Route;
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
        ]);

        Route::apiResource('accessories', AccessoriesController::class);
        
        Route::apiResource('phones', PhonesController::class)->only(['index']);
        Route::post('/phones', [App\Http\Controllers\PhonesController::class, 'search']);

        Route::apiResource('transactions', TransactionsController::class)->only(['show']);
        
        Route::get('/buy', ['uses' => 'TransactionsController@index', 'isBuy' => true]);
        Route::get('/sell', ['uses' => 'TransactionsController@index', 'isBuy' => false]);
        
        Route::post('/buy', ['uses' => 'TransactionsController@store', 'isBuy' => true]);
        Route::post('/sell', ['uses' => 'TransactionsController@store', 'isBuy' => false]);

        Route::apiResource('items', ItemsController::class)->except(['store']);
        
        Route::post('items/{type}/{Itemable}',
            [App\Http\Controllers\ItemsController::class, 'storeItemable'])
        ->where(['type' => '(?:phone|accessory)', 'Itemable' => '[0-9]+']);

        Route::middleware(isAdminMiddleware::class)->group(function() {
            Route::apiResource('users', UsersController::class)->except(['show']);
        });
    //});


/*
    - BUY
        2 PHONE 35000
        2 ACCES 1000

    - BUY
        1 PHONE 36000
        1 PHONE 35000

    - SELL
        1 ACCES 1200

    - BUY
        1 PHONE 34000
    
    - SELL
        1 ACCES 900
        1 PHONE 35000

    - BUY
        3 ACCES 1000
        2 ACCES 900
        1 PHONE 35000
        1 PHONE 33000
*/