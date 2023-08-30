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
    Route::post('/partner/create','Api\MerchantController@createmerchant');
    Route::post('/partner/login','Api\MerchantController@login');
});

Route::post('/partner/createstore', 'Api\MerchantController@createstore')->middleware('auth:api');

Route::group([
    'middleware' => [AvalogyMiddleware::class],
    'prefix'     => '/partner/store/{tenant}',
], function () {
    
});
Route::group([
    'prefix'     => '/storedata',
    'middleware' => [InitializeTenancyByRequestData::class,'tenantenvironment'],
], function () {
    Route::get('/products', 'Api\ProductController@productList');
    
});



Route::group([
    'prefix'     => '/store/{tenant}',
    'middleware' => [InitializeTenancyByPath::class,'tenantenvironment'],
], function () {

    Route::get('cron/product-price-reset','Seller\CronController@ProductPriceReset');
    
});