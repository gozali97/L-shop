<?php

// use App\Models\Product;
use Illuminate\Support\Facades\Route;

/*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */

// CACHE CLEAR ROUTE
Route::get('cache-clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    request()->session()->flash('success', 'Successfully cache cleared.');
    return redirect()->back();
})->name('cache.clear');


Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware(['guest'])->name('verification.notice');

Route::post('/email/resend', 'FrontendController@resend')->name('verification.resend');
Route::get('/email/verify/{id}/{hash}', [\App\Http\Controllers\FrontendController::class, 'verify'])->middleware(['user'])->name('verification.verify');

Route::get('/request-forgot-password', function () {
    return view('auth.passwords.request');
})->middleware('guest')->name('password.request.reset');

Route::post('/submit-forgot-password/submit', [\App\Http\Controllers\FrontendController::class, 'sendMailReset'])->middleware('guest')->name('password.email.submit');

Route::get('/confirm-reset-password/{token}', function (string $token) {
    return view('auth.passwords.custom-reset', ['token' => $token]);
})->middleware('guest')->name('password.reset.confirm');

// STORAGE LINKED ROUTE
// Route::get('storage-link', [\App\Http\Controllers\AdminController::class, 'storageLink'])->name('storage.link');

// Auth Section
Auth::routes(['register' => false]);
Route::get('/admin/login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])->name('admin.login');
/// User Login
Route::get('/login', 'FrontendController@login')->name('login');
// User Login Submitted
Route::post('user/login', 'FrontendController@loginSubmit')->name('login.submit');
// User Logout
Route::post('user/logout', 'FrontendController@logout')->name('user.logout');
// User Register
Route::get('user/register', 'FrontendController@register')->name('register.form');
// User Register Submitted
Route::post('user/register', 'FrontendController@registerSubmit')->name('register.submit');
// Reset password
//    Route::get('password-reset', 'FrontendController@showResetForm')->name('password.reset');
Route::post('/forgot-password/send', 'FrontendController@sendMailReset')->name('password.sendMailReset');
// Socialite
Route::get('login/{provider}/', 'Auth\LoginController@redirect')->name('login.redirect');
Route::get('login/{provider}/callback/', 'Auth\LoginController@Callback')->name('login.callback');
Route::get('/', 'FrontendController@home')->name('home');
// Frontend section start
Route::get('/home', 'FrontendController@index');
// About Us
Route::get('/about-us', 'FrontendController@aboutUs')->name('about-us');
// Contact
Route::get('/contact', 'FrontendController@contact')->name('contact');
Route::post('/contact/message', 'MessageController@store')->name('contact.store');
// Product Detail
Route::get('product-detail/{slug}', 'FrontendController@productDetail')->name('product-detail');
// Product Search
Route::post('/product/search', 'FrontendController@productSearch')->name('product.search');
// Product Category
Route::get('/product-cat/{slug}', 'FrontendController@productCat')->name('product-cat');
// Product Sub-Category
Route::get('/product-sub-cat/{slug}/{sub_slug}', 'FrontendController@productSubCat')->name('product-sub-cat');
//  Brand
Route::get('/product-brand/{slug}', 'FrontendController@productBrand')->name('product-brand');
// Cart section
Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart');

// Delete Wishlist
Route::get('wishlist-delete/{id}', 'WishlistController@wishlistDelete')->name('wishlist-delete');
Route::get('/income', 'OrderController@incomeChart')->name('product.order.income');
// Route::get('/user/chart','AdminController@userPieChart')->name('user.piechart');

// Product Grid
Route::get('/product-grids', 'FrontendController@productGrids')->name('product-grids');
// Product List
Route::get('/product-lists', 'FrontendController@productLists')->name('product-lists');
// Product Filter
Route::match(['get', 'post'], '/filter', 'FrontendController@productFilter')->name('shop.filter');
// Order Track
Route::get('/product/track', 'OrderController@orderTrack')->name('order.track');
Route::post('product/track/order', 'OrderController@productTrackOrder')->name('product.track.order');
// Blog
Route::get('/blog', 'FrontendController@blog')->name('blog');
// Get blog detail
Route::get('/blog-detail/{slug}', 'FrontendController@blogDetail')->name('blog.detail');
// Blog Search
Route::get('/blog/search', 'FrontendController@blogSearch')->name('blog.search');
// Filter Blog
Route::post('/blog/filter', 'FrontendController@blogFilter')->name('blog.filter');
// Blog Category
Route::get('blog-cat/{slug}', 'FrontendController@blogByCategory')->name('blog.category');
// Blog Tag
Route::get('blog-tag/{slug}', 'FrontendController@blogByTag')->name('blog.tag');
// NewsLetter
Route::post('/subscribe', 'FrontendController@subscribe')->name('subscribe');
// Product Review
Route::resource('/review', 'ProductReviewController');
Route::post('product/{slug}/review', 'ProductReviewController@store')->name('review.store');
// Post Comment
Route::post('post/{slug}/comment', 'PostCommentController@store')->name('post-comment.store');
Route::resource('/comment', 'PostCommentController');
// Coupon
Route::post('/coupon-store', 'CouponController@couponStore')->name('coupon-store');
// Payment
Route::get('payment', 'PayPalController@payment')->name('payment');
Route::get('cancel', 'PayPalController@cancel')->name('payment.cancel');
Route::get('payment/success', 'PayPalController@success')->name('payment.success');
// Backend section start
Route::group(['prefix' => '/admin', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/', 'AdminController@index')->name('admin');
    Route::get('/file-manager', function () {
        return view('backend.layouts.file-manager');
    })->name('file-manager');
    // user route
    Route::resource('users', 'UsersController');
    // Banner
    Route::resource('banner', 'BannerController');
    // Brand
    Route::resource('brand', 'BrandController');
    // Profile
    Route::get('/profile', 'AdminController@profile')->name('admin-profile');
    Route::post('/profile/{id}', 'AdminController@profileUpdate')->name('profile-update');
    // Category
    Route::resource('/category', 'CategoryController');
    // Product
    Route::resource('/product', 'ProductController');
    // Ajax for sub category
    Route::post('/category/{id}/child', 'CategoryController@getChildByParent');
    // POST category
    Route::resource('/post-category', 'PostCategoryController');
    // Post tag
    Route::resource('/post-tag', 'PostTagController');
    // Post
    Route::resource('/post', 'PostController');
    // Message
    Route::resource('/message', 'MessageController');
    Route::get('/message/five', 'MessageController@messageFive')->name('messages.five');
    // Order
    Route::resource('/order', 'OrderController');
    Route::post('order/add-resi/{id}', [\App\Http\Controllers\OrderController::class, 'addresi'])->name('order.addResi');
    Route::get('order/view-tracking/{id}', [\App\Http\Controllers\OrderController::class, 'viewTracking'])->name('order.viewTracking');
    Route::post('order/print-pdf/{id}', [\App\Http\Controllers\OrderController::class, 'pdf'])->name('order.pdf');
    Route::get('order/print-invoice/{id}', [\App\Http\Controllers\OrderController::class, 'invoice'])->name('order.printInvoice');
    Route::get('order/print-item-list/{id}', [\App\Http\Controllers\OrderController::class, 'itemListPdf'])->name('order.PrintItemListPdf');
    Route::get('order/confirm/{id}', [\App\Http\Controllers\OrderController::class, 'confirm'])->name('order.confirm');
    Route::post('order/confirm-store/{id}', [\App\Http\Controllers\OrderController::class, 'confirmStore'])->name('order.confirmStore');
    Route::post('order/confirm-refund/{id}', [\App\Http\Controllers\OrderController::class, 'confirmRefund'])->name('order.confirmRefund');
    Route::post('order/refund-finish/{id}', [\App\Http\Controllers\OrderController::class, 'refundFinish'])->name('order.refund.finish');
    // Shipping
    Route::get('/shipping', [\App\Http\Controllers\ShippingFeeController::class, 'index'])->name('shipping.index');
    Route::post('/shipping/update/{id}', [\App\Http\Controllers\ShippingFeeController::class, 'update'])->name('shipping.update');
    // Coupon
    Route::resource('/coupon', 'CouponController');
    // Bank
    Route::resource('/bank', \App\Http\Controllers\BankController::class)->except(['show']);
    // Settings
    Route::get('settings', 'AdminController@settings')->name('settings');
    Route::post('setting/update', 'AdminController@settingsUpdate')->name('settings.update');
    // Notification
    Route::get('/notification/{id}', 'NotificationController@show')->name('admin.notification');
    Route::get('/notifications', 'NotificationController@index')->name('all.notification');
    Route::delete('/notification/{id}', 'NotificationController@delete')->name('notification.delete');
    // Password Change
    Route::get('change-password', 'AdminController@changePassword')->name('change.password.form');
    Route::post('change-password', 'AdminController@changPasswordStore')->name('change.password');
});
// User section start
Route::group(['prefix' => '/user', 'middleware' => ['auth', 'user',  'verified']], function () {
    Route::get('/add-to-cart/{slug}', 'CartController@addToCart')->name('add-to-cart');

    Route::post('/add-to-cart', 'CartController@singleAddToCart')->name('single-add-to-cart');

    Route::get('cart-delete/{id}', 'CartController@cartDelete')->name('cart-delete');
    // Update Cart
    Route::post('cart-update', 'CartController@cartUpdate')->name('cart.update');
    // Checkout Section
    Route::get('/checkout', 'CartController@checkout')->name('checkout');
    // Checkout Get City
    Route::get('/checkout/getCity', 'CartController@getCity')->name('checkout.getCity');
    // Checkout Get District
    Route::get('/checkout/getDistrict', 'CartController@getDistrict')->name('checkout.getDistrict');
    // Checkout Get Shipping Cost
    Route::get('/checkout/getCost', 'CartController@getCost')->name('checkout.getCost');
    Route::post('cart-order', 'CartController@store')->name('cart.order');

    Route::get('/wishlist', function () {
        return view('frontend.pages.wishlist');
    })->name('wishlist');

    Route::get('/wishlist/{slug}', 'WishlistController@wishlist')->name('add-to-wishlist');

    Route::get('/', 'HomeController@index')->name('user');
    // Profile
    Route::get('/profile', 'HomeController@profile')->name('user-profile');
    Route::post('/profile/{id}', 'HomeController@profileUpdate')->name('user-profile-update');
    //  Order
    Route::get('/order', "HomeController@orderIndex")->name('user.order.index');
    Route::get('/order/confirm/{id}', "HomeController@confirm")->name('user.order.confirm');
    Route::get('/order/refund/{id}', "HomeController@refund")->name('user.order.refund');
    Route::get('/order/refund/shipping/{id}', "HomeController@refundShipping")->name('user.order.refund.shipping');
    Route::post('/order/refund-store/{id}', "HomeController@refundStore")->name('user.order.refundStore');
    Route::post('/order/refund/shipping-store/{id}', "HomeController@refundShippingStore")->name('user.order.refund.shippingStore');
    Route::post('/order/confirm-store/{id}', "HomeController@confirmStore")->name('user.order.confirmStore');
    Route::get('/order/show/{id}', "HomeController@orderShow")->name('user.order.show');
    Route::get('/order/print-invoice/{id}', [\App\Http\Controllers\OrderController::class, 'invoice'])->name('user.order.printInvoice');
    Route::get('/order/view-tracking/{id}', "HomeController@viewTracking")->name('user.order.viewTracking');
    Route::post('/order/confirm-completed/{id}', 'HomeController@confirmCompleted')->name('user.order.userConfirmCompleted');
    Route::delete('/order/delete/{id}', 'HomeController@userOrderDelete')->name('user.order.delete');
    // Product Review
    Route::get('/user-review', 'HomeController@productReviewIndex')->name('user.productreview.index');
    Route::delete('/user-review/delete/{id}', 'HomeController@productReviewDelete')->name('user.productreview.delete');
    Route::get('/user-review/edit/{id}', 'HomeController@productReviewEdit')->name('user.productreview.edit');
    Route::patch('/user-review/update/{id}', 'HomeController@productReviewUpdate')->name('user.productreview.update');
    // Post comment
    Route::get('user-post/comment', 'HomeController@userComment')->name('user.post-comment.index');
    Route::delete('user-post/comment/delete/{id}', 'HomeController@userCommentDelete')->name('user.post-comment.delete');
    Route::get('user-post/comment/edit/{id}', 'HomeController@userCommentEdit')->name('user.post-comment.edit');
    Route::patch('user-post/comment/udpate/{id}', 'HomeController@userCommentUpdate')->name('user.post-comment.update');
    // Post comment
    Route::get('user-address/address', 'HomeController@userAddress')->name('user.address.index');
    Route::get('user-address/address/create', 'HomeController@userAddAddress')->name('user.address.create');
    Route::post('user-address/address/store', 'HomeController@userStoreAddress')->name('user.address.store');
    Route::delete('user-post/address/delete/{id}', 'HomeController@userAddressDelete')->name('user.address.delete');
    Route::get('user-post/address/edit/{id}', 'HomeController@userAddressEdit')->name('user.address.edit');
    Route::patch('user-post/address/udpate/{id}', 'HomeController@userAddressUpdate')->name('user.address.update');
    Route::get('user-post/address/getCity', 'HomeController@getCity')->name('user.address.getCity');
    Route::get('user-post/address/getDistrict', 'HomeController@getDistrict')->name('user.address.getDistrict');
    // Password Change
    Route::get('change-password', 'HomeController@changePassword')->name('user.change.password.form');
    Route::post('change-password', 'HomeController@changPasswordStore')->name('change.password');

    Route::get('bank', [\App\Http\Controllers\UserBankController::class, 'index'])->name('user.bank.index');
    Route::get('bank/create', [\App\Http\Controllers\UserBankController::class, 'create'])->name('user.bank.create');
    Route::post('bank/store', [\App\Http\Controllers\UserBankController::class, 'store'])->name('user.bank.store');
    Route::post('bank/update/{id}', [\App\Http\Controllers\UserBankController::class, 'update'])->name('user.bank.update');
});
