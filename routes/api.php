<?php
    /*
        - Generate PDFs for tables.
        - Should log updates ?.
        
        - Stats (Item - User - Product - Time).
        
        - Receipt.
        - Barcode.

        - Debts.
        - Loyalty System.
        - Easy pay.
        
        - Flexy.
        - Reparation.
        
        - Social Media Share.
        - Online store view.

        - My Control Panel.
        - onlyJsonMiddleware.
        - Bouncer cache.
        
        https://laravel.com/docs/8.x/deployment
        https://laravel.com/docs/8.x/passport#deploying-passport
        php artisan migrate:fresh --seed && php artisan passport:install && php artisan db:seed --class=BouncerSeeder
        COMPOSER_MEMORY_LIMIT = -1
    */

    use Illuminate\Http\Request;

    use App\Http\Middleware\isAdminMiddleware;
    use App\Http\Middleware\isOwnerMiddleware;

    use Illuminate\Support\Facades\Route;
    use App\Models\Phone;
    use App\Models\User;

    // Illuminate\Support\Facades\Auth::loginUsingId(config('app.FAKE_LOGIN_ID'));

    Route::prefix('auth')->group(function () {
        Route::post('login', 'AuthController@login');
        
        Route::middleware('auth:api')->group(function() {
            Route::get('user', 'AuthController@user');
            Route::get('logout', 'AuthController@logout');
        });
    });
    
    //Route::middleware('auth:api')->group(function() {
        Route::apiResource('users', UsersController::class)->middleware(isOwnerMiddleware::class)->except(['show', 'index']);
        
        Route::get('/users', 'UsersController@index');
        Route::put('/users/permissions/{user}', 'UsersController@updatePermissions');
        Route::post('/store', 'UsersController@switchStore');

        Route::apiResources([
            'vendors' => VendorsController::class,
            'customers' => CustomersController::class,
            'accessories' => AccessoriesController::class,
            'phones' => PhonesController::class
        ], ['except' => ['show']]);
        
        Route::get('/buy', ['uses' => 'TransactionsController@index', 'isBuy' => true]);
        Route::get('/sell', ['uses' => 'TransactionsController@index', 'isBuy' => false]);
        
        Route::post('/buy', ['uses' => 'TransactionsController@store', 'isBuy' => true]);
        Route::post('/sell', ['uses' => 'TransactionsController@store', 'isBuy' => false]);

        Route::apiResource('items', ItemsController::class)->except(['store', 'show']);
        
        Route::post('items/{type}/{Itemable}',
            'ItemsController@storeItemable')
        ->where(['type' => '(phone|accessory)', 'Itemable' => '[0-9]+']);
        
        Route::prefix('/search')->group(function() {
            Route::prefix('/people')->group(function() {
                Route::post('/vendor', 'SearchController@searchForVendor');
                Route::post('/customer', 'SearchController@searchForCustomer');
            });

            Route::prefix('/products')->group(function() {
                Route::post('/all', 'SearchController@searchForProducts');    

                Route::post('/phone', 'SearchController@searchForPhone');
                Route::post('/accessory', 'SearchController@searchForAccessory');
            });
            
            Route::prefix('/items')->group(function() {
                Route::post('/all', 'SearchController@searchForItems');    

                Route::post('/phone', 'SearchController@searchForPhoneWithItems');
                Route::post('/accessory', 'SearchController@searchForAccessoryWithItems');
            });
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