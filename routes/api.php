<?php
    /*
        - Profit in Transaction details.
        - Stats should be casted to int.
        
        - Rename 'Good' to 'Product'
        
        - add global search field.
        - use WhereIn instead in search to search by request query string.
        
        - Should add a Gate in updating && deleting (user.store_id === item.store_id).
        - Accessories should have "for_phones", Therefor separete Phones and Goods table.
        
        - Update users scopes.
        - Think about doing calculations on client side.
        
        - Social Media Share.
        - Store Website.

        - Enable onlyJsonMiddleware.
        
        - https://laravel.com/docs/8.x/deployment
        - https://laravel.com/docs/8.x/passport#deploying-passport
    */
    
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
        Route::apiResource('phones', PhonesController::class)->except(['store', 'update', 'destroy']);
        Route::post('/phones', [App\Http\Controllers\PhonesController::class, 'search']);

        Route::apiResource('transactions', TransactionsController::class)->except(['update']);
        Route::apiResource('items', ItemsController::class)->except(['store']);
        
        Route::post('items/{type}/{Itemable}',
            [App\Http\Controllers\ItemsController::class, 'storeItemable'])
        ->where(['type' => '(?:phone|accessory)', 'Itemable' => '[0-9]+']);
    //});