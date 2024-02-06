<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;
use App\Http\Middleware\AvalogyMiddleware;
use App\Http\Middleware\StoreSettingCheckMiddleware;
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
    'middleware' => [AvalogyMiddleware::class,StoreSettingCheckMiddleware::class, InitializeTenancyByRequestData::class, 'tenantenvironment'],
], function () {
    Route::get('/categories', 'Api\ProductController@categoryList');
    Route::get('/products', 'Api\ProductController@productList');
    Route::get('/product/{id}', 'Api\ProductController@productDetail');
    Route::post('/product/search', 'Api\ProductController@search');
    Route::get('/cart/getcart', 'Api\ProductController@getcart');
    Route::post('/cart/add_to_cart', 'Api\ProductController@addtocart');
    Route::post('/cart/remove_from_cart/{id}', 'Api\ProductController@removecart');
    Route::post('/cart/update_cart', 'Api\ProductController@CartQty');
    Route::post('/checkout/order', 'Api\ProductController@CartQty');
    Route::post('/resend_invoice','Api\ProductController@resend_invoice');
    Route::post('/get_invoice_info','Api\ProductController@getInvoiceInfo');
    Route::get('/get_banner_image','Api\ProductController@getBannerImage');
    Route::get('/get_footer_links','Api\ProductController@getFooterLinks');


    // PosApiController

    Route::post('/get_pos_category_list','Api\PosApiController@getPosCategoryList');
    Route::post('/get_pos_product_list', 'Api\PosApiController@posProductList');
    Route::post('/cart/pos_add_to_cart', 'Api\PosApiController@posAddToCart');
    Route::post('/cart/pos_get_cart', 'Api\PosApiController@posGetCart');
    Route::post('/cart/pos_update_cart', 'Api\PosApiController@posCartQty');
    Route::post('/cart/pos_remove_from_cart/{id}', 'Api\PosApiController@posRemoveCart');
    Route::get('/pos-product/{id}', 'Api\PosApiController@posProductDetail');
    Route::post('/pos-make-order', 'Api\PosApiController@posMakeOrder');

    Route::post('/pos-get-store-details', 'Api\PosApiController@posGetStoreDetails');

    Route::post('/pos-order-info', 'Api\PosApiController@posOrderInfo');
    Route::post('/pos-order-list', 'Api\PosApiController@posOrderList');

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
