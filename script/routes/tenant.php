<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;
use App\Jobs\TenantMailJob;
use App\Http\Controllers\Store\TicketScanController;
/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

// tenant routes

Route::group(['middleware' => ['Isinstalled','InitializeTenancyByDomain','PreventAccessFromCentralDomains','web','tenantenvironment']], function () {

    Auth::routes(['register' => false, 'reset' => false]);

    Route::get('/pwa',fn() => redirect('/'));

    // Ticket routes
    Route::get('/ticket/scan/{uuid}', [TicketScanController::class, 'scan']);
    Route::get('/tickets/qr_{uuid}.png', [TicketScanController::class, 'ticketQrPng']);
    Route::get('/ticket/{uuid}/calendar', [TicketScanController::class, 'calendar'])->name('ticket.calendar');
    Route::get('/ticket/google-wallet/{uuid}', [TicketScanController::class, 'googleWallet']);
    Route::get('/create-google-wallet-class', [TicketScanController::class, 'createGoogleWalletClass']);
    Route::get('/apple-wallet/{uuid}/ticket.pkpass', [TicketScanController::class, 'appleWallet']);
    Route::get('/ticket/{uuid}/print', [TicketScanController::class, 'print'])->name('ticket.print');

    // Standalone Storefront Routes (Gated by standalone_store_status setting)
    Route::group(['middleware' => ['standalone.store']], function () {
        Route::get('/', 'Store\PageController@home');
        Route::get('/products', 'Store\PageController@products');
        Route::get('/brand/{slug}', 'Store\PageController@brand');
        Route::get('/category/{slug}', 'Store\PageController@category');
        Route::get('/deals', 'Store\PageController@deals');
        Route::get('/featured/{slug}', 'Store\PageController@featured');
        Route::get('/tag/{slug}', 'Store\PageController@tag');
        Route::get('/product/{slug}', 'Store\PageController@productView');
        Route::get('/page/{slug}', 'Store\PageController@page')->name('store.page');
        Route::get('/menu', 'Store\PageController@menu');
        Route::get('/cart', 'Store\PageController@cart');
        Route::get('/wishlist', 'Store\PageController@wishlist');
        Route::get('/checkout', 'Store\CheckoutController@subdomainCheckout')->name('store.checkout');
        Route::get('/thanks', 'Store\PageController@thanks');
        Route::get('/checkout/payment/{order_id}', 'Store\CheckoutController@paymentPage')->name('checkout.payment');
        Route::get('/contact', 'Store\PageController@contact');
        Route::post('/contact/send', 'Store\PageController@contact_send')->middleware('throttle:2,1');
        Route::get('locale/lang', 'LocalizationController@store')->name('language.set');
        Route::get('category/product/show','Store\PageController@category_product')->name('category.product');
        Route::get('blog', 'Store\BlogController@index');
        Route::get('blog/{slug}', 'Store\BlogController@show');

        // Cart routes for standalone storefront
        Route::post('add-to-cart','Store\CartController@addtocart')->name('add.tocart');
        Route::post('add-to-wishlist','Store\CartController@addtowishlist')->name('add.wishlist');
        Route::post('makediscount','Store\CartController@makediscount')->name('makediscount');
        Route::get('remove-wishlist/{id}','Store\CartController@removeWishlist');
        Route::get('/express','Store\CartController@express');
        Route::get('remove-cart/{id}','Store\CartController@removecart')->name('remove.cart');
        Route::post('cart-qty','Store\CartController@CartQty')->name('cart.qty');

        // Customer routes for standalone storefront
        Route::group(['prefix'=>'customer'],function(){
            Route::post('/make-subscribe','Store\CustomerController@subscribe')->name('customer.subscribe');
            Route::get('/login', 'Store\CustomerController@login')->middleware('guest');
            Route::post('/login-customer', 'Store\CustomerController@loginCustomer')->name('customer.login')->middleware('guest');
            Route::get('/register', 'Store\CustomerController@register')->middleware('guest');
            Route::post('/register-customer', 'Store\CustomerController@registerCustomer')->name('customer.register')->middleware('guest');
            
            // Booostr Custom SSO Routes
            Route::post('/sso/check-identity', 'Store\CustomerController@ssoCheckIdentity')->name('customer.sso.check')->middleware('guest');
            Route::post('/sso/verify-credentials', 'Store\CustomerController@ssoVerifyCredentials')->name('customer.sso.credentials')->middleware('guest');
            Route::post('/sso/verify-otp', 'Store\CustomerController@ssoVerifyOtp')->name('customer.sso.otp')->middleware('guest');
            Route::post('/sso/resend-otp', 'Store\CustomerController@ssoResendOtp')->name('customer.sso.resend')->middleware('guest');
            Route::post('/sso/complete', 'Store\CustomerController@ssoComplete')->name('customer.sso.complete')->middleware('guest');
            Route::get('/dashboard', 'Store\CustomerController@dashboard')->name('customer.dashboard')->middleware(['auth','user']);
            Route::get('/orders', 'Store\CustomerController@orders')->name('customer.orders')->middleware(['auth','user']);
            Route::get('/order/{id}', 'Store\CustomerController@orderview')->name('customer.order.view')->middleware(['auth','user']);
            Route::get('/settings', 'Store\CustomerController@settings')->name('customer.settings')->middleware(['auth','user']);
            Route::get('/reviews', 'Store\CustomerController@reviews')->name('customer.reviews')->middleware(['auth','user']);
            Route::post('profile/update','Store\CustomerController@profileUpdate')->name('customer.profile.update')->middleware(['auth','user']);
            Route::post('profile/password-update','Store\CustomerController@profilePasswordUpdate')->name('customer.password.update')->middleware(['auth','user']);
            Route::post('order-make-rating/{orderid}/{termid}/{optionid}','Store\CustomerController@makeReview')->middleware(['auth','user']);

            // Site Builder Standard Dashboard Aliases
            Route::get('/edit-profile', 'Store\CustomerController@settings')->name('customer.edit.profile')->middleware(['auth','user']);
            Route::get('/change-password', 'Store\CustomerController@changePassword')->name('customer.change.password')->middleware(['auth','user']);
            Route::get('/purchase-history', 'Store\CustomerController@orders')->name('customer.purchase.history')->middleware(['auth','user']);
        });

        // Customer Forgot & Reset Password Routes (Official URLs)
        Route::get('/customer/forget-password', 'Store\CustomerController@showForgetPasswordForm')->name('customer.forget.password')->middleware('guest');
        Route::post('/customer/forget-password', 'Store\CustomerController@sendUserForgetPasswordMail')->name('customer.forget.password.post')->middleware('guest');
        Route::get('/customer/reset-password/{username}/{token}', 'Store\CustomerController@showResetPasswordForm')->name('customer.reset.password')->middleware('guest');
        Route::post('/customer/reset-password', 'Store\CustomerController@UserResetPassword')->name('customer.reset.password.post')->middleware('guest');

        // Clean Redirects for root /login and /register to customer storefront
        Route::get('/login', fn() => redirect('/customer/login'))->name('login');

        Route::get('/register', fn() => redirect('/customer/register'))->name('register');

        // Backward-compatible aliases for legacy / email reset links
        Route::get('/login/forget-password', fn() => redirect()->route('customer.forget.password'))->middleware('guest');
        Route::get('/login/reset-password/{username}/{token}', fn($username, $token) => redirect()->route('customer.reset.password', ['username' => $username, 'token' => $token]))->middleware('guest');
        Route::get('/reset-password/{user}/{token}', fn($user, $token) => redirect()->route('customer.reset.password', ['username' => $user, 'token' => $token]))->name('tenant.user.reset.password')->middleware('guest');
    
    });

    // Direct / Embed Checkout Routes (Bypasses standalone_store check so Website Builder and Club Profiles always work)
    Route::get('/direct_checkout/{cartid}/{redirect_url}', 'Store\CheckoutController@direct_checkout_to');
    Route::get('/direct_checkout_form/{cartid}/{redirect_url}', 'Store\CheckoutController@direct_checkout_form');
    Route::get('/direct/checkout/{cartid}/{redirect_url}', 'Store\CheckoutController@direct_new_checkout')->name('direct.checkout');
    Route::get('/direct/checkout_form/{cartid}/{redirect_url}', 'Store\CheckoutController@direct_checkout_form_to')->name('direct.checkout_form');
    Route::get('/direct/new-checkout/{cartid}/{redirect_url}', 'Store\CheckoutController@direct_new_checkout')->name('direct.new_checkout');

    Route::get('/get-stripe-paymentIntent', 'Store\CheckoutController@get_stripe_paymentIntent')->name('checkout.get_stripe_paymentIntent');
    Route::post('/applyTax', 'Store\CheckoutController@applyTax')->name('checkout.applyTax');
    Route::post('/checkout/make-order', 'Store\CheckoutController@makeOrder')->name('checkout.makeorder');
    Route::post('/checkout/new-make-order', 'Store\CheckoutController@newMakeOrder')->name('checkout.newmakeorder');
    Route::post('/checkout/process-payment/{order_id}', 'Store\CheckoutController@processPayment')->name('checkout.processPayment');
    Route::get('/checkout/process-payment/{order_id}', 'Store\CheckoutController@processPayment')->name('checkout.processPayment');
    Route::get('/payment/success', 'Store\CheckoutController@success')->name('checkout.success');
    Route::post('make-order', 'Store\OrderController@makeOrder')->name('make.order');

    Route::post('product-search','Store\CartController@search')->name('pos.search');
    Route::get('product-varidation/{id}','Store\CartController@varidation')->name('pos.varidation');
    Route::resource('order', 'Store\OrderController');
    Route::post('orders/destroy','Store\OrderController@destroy')->name('order.multipledelete');

    // Routes for response data & POS / APIs
    Route::get('/databack', 'Store\DataController@databack');
    Route::get('/get-databack', 'Store\DataController@getData')->name('callback.data');
    Route::get('/get-product','Store\DataController@getproducts');
    Route::get('/get-product-reviews/{id}','Store\DataController@getProductReviews');
    Route::get('/product-details/{id}','Store\DataController@productDetails');
    Route::get('product_search','Store\DataController@productSearch')->name('product.search');

    Route::get('/order-success','Store\OrderController@success');
    Route::get('/order-fail','Store\OrderController@fail');

    Route::post('apply-coupon-code','CouponController@applyCoupon');
    Route::get('get-coupon-type/{type}','CouponController@getCouponType');
    Route::get('generate-coupon-code','CouponController@generateCode');
    


   



    Route::post('save-token','Seller\FirebaseController@saveToken')->name('save-token')->middleware(['auth','user']);

    Route::get('/oops', fn() => "this modules not support for this request");
    Route::get('/reset/product-price','CronController@ProductPriceReset');

    Route::get('/mysettings', 'Admin\UserController@index')->middleware(['auth','user']);
    Route::post('genup', 'Admin\UserController@genUpdate')->name('admin.users.genupdate')->middleware(['auth','user']);
    Route::post('passup', 'Admin\UserController@updatePassword')->name('admin.users.passup')->middleware(['auth','user']); 
    Route::get('/make-login/{token}','Seller\LoginController@tokenBasedLogin')->middleware('throttle:2,1');
    Route::get('redirect/login', 'Seller\LoginController@login');

    // Seller Routes
    Route::group(['as' => 'seller.', 'prefix' => 'seller', 'namespace' => 'Seller', 'middleware' => ['auth','seller','user']], function () {
        Route::get('/dashboard', 'DashboardController@dashboard')->name('dashboard');
        Route::get('/dashboard/static','DashboardController@staticData');
        Route::get('/dashboard/perfomance/{period}','DashboardController@perfomance');
        Route::get('/dashboard/order-perfomance/{period}','DashboardController@orderPerfomace');
        
        Route::get('/dashboard/deposit/perfomance/{period}','DashboardController@depositPerfomance');
        Route::get('/dashboard/order_statics/{month}','DashboardController@order_statics');
        Route::get('/dashboard/neworders','DashboardController@getCurrentOrders')->name('orders.new');
        Route::get('/clear-cache','DashboardController@cacheClear');
        Route::get('/subscription-status','DashboardController@subscriptionStatus');

        Route::resource('category', 'CategoryController');
        Route::resource('brand', 'BrandController');
        Route::resource('tag', 'TagController');
        Route::post('add-jquery-tag','TagController@addJqueryTag');
        Route::post('add-ok-checkist-val','TagController@addOkChecklist');
        Route::post('checklist-store-launch','TagController@checkListStoreLaunch');
        Route::post('add-jquery-brand','BrandController@addJqueryBrand');
        Route::resource('orderstatus', 'OrderstatusController');
        Route::resource('coupon', 'CouponController');

        Route::resource('tax', 'TaxController');
        Route::resource('location', 'LocationController');
        Route::resource('shipping', 'ShippingController');
        Route::resource('features', 'FeaturesController');
        Route::resource('product', 'ProductController');

        Route::post('product-import', 'ProductController@import')->name('product.import');
        Route::post('product-order-update', 'ProductController@updateProductOrder')->name('product.reorder');

        Route::get('product/edit/{id}/{type}', 'ProductController@edit');
        Route::get('product/clone/{id}', 'ProductController@clone')->name('product.clone');
        
        // add route sales product history
        Route::get('product/{id}/sales-history', 'ProductController@salesHistory')->name('product.sales.history');
        Route::get('product/{id}/crm-sync/status', 'ProductController@crmSyncStatus')->name('product.crm.sync.status');
        Route::post('product/{id}/crm-sync/enable-continuous', 'ProductController@crmSyncEnableContinuous')->name('product.crm.sync.enable');
        Route::post('product/{id}/crm-sync/process-batch', 'ProductController@crmSyncProcessBatch')->name('product.crm.sync.process');
        Route::post('product/{id}/crm-sync/stop', 'ProductController@crmSyncStop')->name('product.crm.sync.stop');
        Route::post('product/{id}/crm-sync/pause', 'ProductController@crmSyncPause')->name('product.crm.sync.pause');
        Route::post('product/{id}/crm-sync/restart', 'ProductController@crmSyncRestart')->name('product.crm.sync.restart');
        Route::post('product/{id}/crm-sync/record-one-time', 'ProductController@crmSyncRecordOneTime')->name('product.crm.sync.record_one_time');
        Route::post('product/{id}/crm-sync/contact-groups', 'ProductController@crmSyncCreateContactGroup')->name('product.crm.sync.contact_groups.create');
        Route::post('product/ticket-status-update', 'ProductController@ticketStatusUpdate')->name('product.ticket.status.update');
        
        Route::get('product/remove-price/{id}', 'ProductController@removeVariationPrice');
        Route::get('product/remove-variation-attribute/{id}', 'ProductController@removeVariationAttribute');

        Route::post('products/destroys', 'ProductController@multiDelete')->name('products.destroys');
        Route::resource('attribute', 'AttributeController');
        Route::resource('media', 'MediaController');
        Route::resource('mediacompress', 'ImagecompressController');
        Route::resource('table','TableController');
        Route::resource('barcode','BarcodeController');
        Route::get('barcodes/reset','BarcodeController@reset')->name('barcode.reset');
        Route::resource('user','UserController');
        Route::get('/user/login/{id}','UserController@login')->name('user.login');
        Route::resource('rider','RiderController');
        Route::get('settings','SettingsController@index');
        Route::post('settings/update','SettingsController@update')->name('settings.update');
        Route::get('medias','MedialistController@index');
        Route::post('media/delete','MedialistController@delete')->name('medias.delete');
        Route::get('media/create','MedialistController@create')->name('medias.create');

        Route::get('payment/gateway','PaymentgatewayController@index')->name('payment.gateway');
        Route::post('payment/custom/gateway','PaymentgatewayController@custom_payment')->name('custom.payment');
        Route::get('payment/custom/gateway/create','PaymentgatewayController@custom_payment_create')->name('custom.payment.create');
        Route::post('payment/gateway/{id}','PaymentgatewayController@store')->name('payment.gateway.store');
        Route::get('payment/gateway/{payment}','PaymentgatewayController@payment_edit')->name('payment.edit');
        Route::get('payment/install/{payment}','PaymentgatewayController@install')->name('payment.install');

        Route::get('theme','ThemeController@index')->name('theme.index');
        Route::get('theme/install/{theme}','ThemeController@install')->name('theme.install');

        Route::resource('language','LanguageController');
        Route::post('language-addkey/{id}','LanguageController@addKey')->name('language.addkey');
        Route::delete('language-remove-key/{id}','LanguageController@keyRemove')->name('language.keyremove');

        Route::get('calender','CalenderController@index')->name('calender.index');
        Route::get('upcominOrders','CalenderController@upcoming_orders')->name('seller.order.upcoming');

        Route::post('product/barcode/search','BarcodeController@search')->name('barcode.search');
        Route::post('barcode/generate','BarcodeController@generate')->name('barcode.generate');

        // Reviews Route
        Route::get('review','ReviewController@index')->name('review.index');
        Route::post('review/destroy','ReviewController@destroy')->name('review.destroy');

        //pos routes
        Route::resource('pos', 'PosController');
        Route::get('products','PosController@productList')->name('product.json');
        Route::post('add-to-cart','PosController@addtocart')->name('add.tocart');
        Route::get('remove-cart/{id}','PosController@removecart')->name('remove.cart');
        Route::post('cart-qty','PosController@CartQty')->name('cart.qty');
        Route::post('product-search','PosController@search')->name('pos.search');
        Route::get('product-varidation/{id}','PosController@varidation')->name('pos.varidation');
        Route::post('check-customer','PosController@checkcustomer');
        Route::post('make-order','PosController@makeorder')->name('pos.order');
        Route::post('make-customer','PosController@makeCustomer')->name('pos.customer.store');
        Route::get('apply-tax','PosController@applyTax');
        Route::resource('order', 'OrderController');

        Route::post('orders/destroy','OrderController@destroy')->name('order.multipledelete');
        Route::post('order/capture/{id}','OrderController@capture')->name('order.capture');
        Route::post('order/refund/{id}','OrderController@refund')->name('order.refund');
        Route::post('order/refund-cash/{id}','OrderController@refundCashCheck')->name('order.refund.cash');
        Route::get('order/print/{id}','OrderController@print')->name('order.print');

        Route::post('/save-token', 'FirebaseController@saveToken')->name('save-token');
        Route::post('/send-notification',  'FirebaseController@sendNotification')->name('send.notification');
        Route::resource('notification','FirebaseController');
        Route::post('notifications/destroy','FirebaseController@destroy')->name('notification.destroys');

        Route::resource('site-settings','SitesettingsController');
        Route::resource('google-analytics','GoogleanalyticsController');
        
        // Menu Route
        Route::resource('menu', 'MenuController');
        Route::post('/menus/destroy', 'MenuController@destroy')->name('menus.destroy');
        Route::post('menues/node', 'MenuController@MenuNodeStore')->name('menus.MenuNodeStore');
        //role routes
        Route::resource('role', 'RoleController');
        Route::post('roles/destroy', 'RoleController@destroy')->name('roles.destroy');
        // Admin Route
        Route::resource('admin', 'AdminController');
        Route::post('/admins/destroy', 'AdminController@destroy')->name('admins.destroy');

        Route::resource('page','PageController');
        Route::resource('blog','BlogController');
        Route::resource('slider','SliderController');
        Route::resource('banner','BannerController');
       
        Route::resource('special-menu','SpecialmenuController');

        Route::get('/store-settings','SiteController@index');
        Route::get('/get-banner-types/{type}','SiteController@getBannerType');
        Route::post('/theme-data-update/{type}','SiteController@updatethemesettings')->name('themeoption.update');

        Route::get('settings/seo','SeoController@index')->name('seo.index');
        Route::post('seo/{page}','SeoController@update')->name('seo.update');
        Route::get('settings/pwa','SettingsController@pwa')->name('pwa.index');
        Route::post('settings/pwa','SettingsController@pwa_update')->name('pwa.update');
        Route::get('settings/custom_css_js','SettingsController@custom_css_js')->name('custom_css_js.index');
        Route::post('settings/custom_css_js','SettingsController@custom_css_js_update')->name('custom_css_js.update');
    });

    // Rider Routes
    Route::group(['prefix'=>'rider', 'as' => 'rider.', 'namespace' => 'Rider','middleware'=>['auth','rider','user']], function(){
        Route::get('dashboard','DashboardController@dashboard')->name('dashboard');
        Route::get('order','OrderController@index')->name('order.index');
        Route::get('settings','SettingsController@index')->name('settings.index');
        Route::post('settings','SettingsController@update')->name('settings.update');
        Route::get('order/{id}','OrderController@show')->name('order.show');
        Route::post('order/delivered','OrderController@delivered')->name('order.delivered');
        Route::get('order/cancel/{id}','OrderController@cancelled')->name('order.cancelled');
        Route::get('live/orders','DashboardController@live_orders')->name('live.orders');
    });
   

   

    
});


