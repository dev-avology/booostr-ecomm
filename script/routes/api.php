<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;
use App\Http\Middleware\AvalogyMiddleware;
use Stancl\Tenancy\Middleware\InitializeTenancyByRequestData;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group([
    'middleware' => [AvalogyMiddleware::class],
], function () {
    Route::post('/partner/create', 'Api\MerchantController@createmerchant');
    Route::post('/partner/login', 'Api\MerchantController@login');
    Route::post('/partner/createstore', 'Api\MerchantController@createstore');
});

// Route::post('/partner/createstore', 'Api\MerchantController@createstore')->middleware('auth:api');

Route::group([
    'prefix'     => '/storedata',
    'middleware' => [AvalogyMiddleware::class, InitializeTenancyByRequestData::class, 'tenantenvironment'],
], function () {
    Route::get('/products', 'Api\ProductController@productList');
    Route::get('/product/{id}', 'Api\ProductController@productDetail');
    Route::post('/product/search', 'Api\ProductController@search');
    Route::get('/cart/getcart', 'Api\ProductController@getcart');
    Route::post('/cart/add_to_cart', 'Api\ProductController@addtocart');
    Route::post('/cart/remove_from_cart/{id}', 'Api\ProductController@removecart');
    Route::post('/cart/update_cart', 'Api\ProductController@CartQty');
    Route::post('/checkout/order', 'Api\ProductController@CartQty');
    Route::post('/resend_invoice','Api\ProductController@resend_invoice');
});



Route::group([
    'middleware' => [AvalogyMiddleware::class],
    'prefix'     => '/partner/store/{tenant}',
], function () {
});

Route::group([
    'prefix'     => '/store/{tenant}',
    'middleware' => [InitializeTenancyByPath::class, 'tenantenvironment'],
], function () {
    Route::get('checkout-redirect/{cartid}/{redirect_url}', 'Store\CheckoutController@redirect_to_checkout');
    Route::get('cron/product-price-reset', 'Seller\CronController@ProductPriceReset');
});
