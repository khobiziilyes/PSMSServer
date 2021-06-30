<?php
    /*
        - Notes are required, but can be empty (not null).
        - Why isPhone hidden in product?
        - Temporarily enable addPhone.
        - Brand in Accessory shouldn't be required.
        - Add isUpdatable to data.
        - Goods Stats.
        - User Stats.
        - $this->whiteListOrderBy
        - Maybe Add "forceSearch" for liveSearch.
        
        - Auto add Accessories for new phones.
        - Flexy.
        
        - Generate PDFs for tables.
        - Devices Specs.
        - Do i need show resource?

        - Social Media Share.
        - Store Website.

        - Enable onlyJsonMiddleware.
        - Bouncer cache.

        - My Control Panel.
        - https://laravel.com/docs/8.x/deployment
        - https://laravel.com/docs/8.x/passport#deploying-passport
    */

    use Illuminate\Http\Request;

    use App\Http\Middleware\isAdminMiddleware;
    use App\Http\Middleware\isOwnerMiddleware;

    use Illuminate\Support\Facades\Route;
    use App\Models\Phone;
    use App\Models\User;

    Illuminate\Support\Facades\Auth::loginUsingId(config('app.FAKE_LOGIN_ID'));

    Route::prefix('auth')->group(function () {
        Route::post('login', 'AuthController@login');
        //Route::post('register', 'AuthController@register');
        
        Route::middleware('auth:api')->group(function() {
            Route::get('user', 'AuthController@user');
            Route::get('logout', 'AuthController@logout');
        });
    });
    
    //Route::middleware('auth:api')->group(function() {
        Route::prefix('owner')->middleware(isOwnerMiddleware::class)->group(function () {
            Route::get('/stores', function(Request $request) {
                return $request->user()->Stores;
            });

            Route::post('/store', function(Request $request) {
                return $request->user()->setWorkingStore($request->input('store_id'));
            });

            Route::get('/users', 'UsersController@index');
            Route::patch('/users/{user}/permissions', 'UsersController@updatePermissions');
            Route::patch('/users/{user}', 'UsersController@update');
        });

        Route::apiResources([
            'vendors' => VendorsController::class,
            'customers' => CustomersController::class,
            'accessories' => AccessoriesController::class
        ]);
        
        Route::apiResource('phones', PhonesController::class)->only(['index']);
        Route::post('/search/{type}', 'SearchController@index')->where('type', '(all|phone|accessory)');

        // Route::apiResource('transactions', TransactionsController::class)->only(['show']);
        
        Route::get('/buy', ['uses' => 'TransactionsController@index', 'isBuy' => true]);
        Route::get('/sell', ['uses' => 'TransactionsController@index', 'isBuy' => false]);
        
        Route::post('/buy', ['uses' => 'TransactionsController@store', 'isBuy' => true]);
        Route::post('/sell', ['uses' => 'TransactionsController@store', 'isBuy' => false]);

        Route::apiResource('items', ItemsController::class)->except(['store']);
        
        Route::post('items/{type}/{Itemable}',
            'ItemsController@storeItemable')
        ->where(['type' => '(phone|accessory)', 'Itemable' => '[0-9]+']);

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