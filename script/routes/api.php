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
    'middleware' => [AvalogyMiddleware::class],
], function () {
    // Club-wise Online Ticket products (seller/product Type = Online Ticket).
    // Query: ?club_id=hello-tester-club
    Route::get('/get-ticket-product', 'Api\TicketProductController@index');
});

Route::group([
    'prefix'     => '/storedata',
    'middleware' => [AvalogyMiddleware::class,StoreSettingCheckMiddleware::class, InitializeTenancyByRequestData::class, 'tenantenvironment','page.cache'],
], function () {
    Route::get('/categories', 'Api\ProductController@categoryList');
    Route::get('/products', 'Api\ProductController@productList');
    Route::get('/product/{id}', 'Api\ProductController@productDetail');
    Route::get('/productbyslug/{id}', 'Api\ProductController@productDetailBySlug');
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

    Route::post('/add_product_form','Api\ProductController@addProductForm');

    Route::get('/products_list', 'Api\ProductController@productDropdownList');
    
    Route::post('/partner/register-payment-domain', 'Api\ProductController@registerPaymentMethodDomain');

    // PosApiController

    Route::post('/get_pos_category_list','Api\PosApiController@getPosCategoryList');
    Route::post('/get_pos_product_list', 'Api\PosApiController@posProductList');
    Route::post('/cart/pos_add_to_cart', 'Api\PosApiController@posAddToCart');
    Route::post('/cart/pos_get_cart', 'Api\PosApiController@posGetCart');
    Route::post('/cart/pos_update_cart', 'Api\PosApiController@posCartQty');
    Route::post('/cart/pos_remove_from_cart/{id}', 'Api\PosApiController@posRemoveCart');
    Route::get('/pos-product/{id}', 'Api\PosApiController@posProductDetail');
    Route::post('/pos-make-order', 'Api\PosApiController@posMakeOrder');

    // Additive: POS Quick Sale module (separate from regular product inventory).
    Route::post('/pos-quick-sale-add-descriptor', 'Api\PosQuickSaleApiController@addDescriptor');
    Route::post('/pos-quick-sale-update-descriptor', 'Api\PosQuickSaleApiController@updateDescriptor');
    Route::post('/pos-quick-sale-delete-descriptor', 'Api\PosQuickSaleApiController@deleteDescriptor');
    Route::post('/pos-quick-sale-get-descriptors', 'Api\PosQuickSaleApiController@getDescriptors');

    Route::post('/pos-get-store-details', 'Api\PosApiController@posGetStoreDetails');

    Route::post('/pos-order-info', 'Api\PosApiController@posOrderInfo');
    Route::post('/pos-order-list', 'Api\PosApiController@posOrderList');
    Route::post('/pos-parent-category-product', 'Api\PosApiController@posParentCategoryProduct');
    Route::post('/pos-email-send', 'Api\PosApiController@posEmailSend');
    Route::get('/pos-stripe-reader-connection-token', 'Api\PosApiController@stipeCardReaderConnectionToken');
    Route::post('/pos-stripe-reader-client-secret', 'Api\PosApiController@stipeCardReaderClientSecret');
    
    Route::post('/stripe/publishable-key','Api\PosApiController@stripePublishableKey');
    Route::post('/order/create','Api\PosApiController@makeOrderCreate');

    // Additive: apply/remove coupon on existing cartId (before /order/create).
    Route::post('/coupon/apply', 'Api\CouponApiController@apply');
    Route::post('/coupon/remove', 'Api\CouponApiController@remove');

    // Additive: POS refund (full / item / dollar) — POS orders only (order_from 4/5).
    Route::post('/pos-refund-payment', 'Api\PosRefundApiController@refundPayment');

    // Additive: POS Quick Sale refund (full / item / dollar) — separate from regular product refunds.
    Route::post('/pos-quick-sale-refund-payment', 'Api\PosQuickSaleRefundApiController@refundPayment');
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
    Route::get('checkout-form-redirect/{cartid}/{redirect_url}', 'Store\CheckoutController@redirect_to_checkout_form');

    Route::get('cron/product-price-reset', 'Seller\CronController@ProductPriceReset');
});

Route::group([
    'prefix'     => '/storedata',
    'middleware' => [
        InitializeTenancyByRequestData::class,
        'tenantenvironment',
        'auth:web'
    ],
], function () {
    Route::get('/summary', 'Api\ProductController@summary');
});
