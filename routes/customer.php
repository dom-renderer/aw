<?php

use Illuminate\Support\Facades\Route;

Route::match(['GET', 'POST'], 'login', [\App\Http\Controllers\FrontendController::class, 'login'])->name('login');
Route::match(['GET', 'POST'], 'register', [\App\Http\Controllers\FrontendController::class, 'register'])->name('register');
Route::get('verify-email/{token}', [\App\Http\Controllers\FrontendController::class, 'verifyEmail'])->name('verification.verify');
Route::post('logout', [\App\Http\Controllers\FrontendController::class, 'logout'])->name('logout');

Route::get('/', [\App\Http\Controllers\FrontendController::class, 'index'])->name('home');
Route::get('cart', [\App\Http\Controllers\FrontendController::class, 'cart'])->name('cart');
Route::post('cart/add', [\App\Http\Controllers\FrontendController::class, 'addToCart'])->name('cart.add');
Route::post('cart/update', [\App\Http\Controllers\FrontendController::class, 'updateCartItem'])->name('cart.update');
Route::post('cart/remove', [\App\Http\Controllers\FrontendController::class, 'removeFromCart'])->name('cart.remove');
Route::get('cart/count', [\App\Http\Controllers\FrontendController::class, 'getCartCount'])->name('cart.count');
Route::post('cart/item-quantity', [\App\Http\Controllers\FrontendController::class, 'getCartItemQuantity'])->name('cart.item-quantity');
Route::post('order/place', [\App\Http\Controllers\FrontendController::class, 'placeOrder'])->name('order.place');
Route::get('order/confirmation/{order_number}', [\App\Http\Controllers\FrontendController::class, 'orderConfirmation'])->name('order.confirmation');
Route::get('order/invoice/{order_number}', [\App\Http\Controllers\FrontendController::class, 'downloadInvoice'])->name('order.invoice');

Route::get('/c/{category_slug}/{short_url}', [\App\Http\Controllers\FrontendController::class, 'category'])->name('category.index');
Route::get('/p/{product_slug}/{short_url}/{variant?}', [\App\Http\Controllers\FrontendController::class, 'product'])->name('product.index');
Route::get('/s', [\App\Http\Controllers\FrontendController::class, 'search'])->name('search');
Route::post('p-ref', [\App\Http\Controllers\FrontendController::class, 'getVariantByAttributes'])->name('product.getVariant');
Route::post('product/pricing', [\App\Http\Controllers\FrontendController::class, 'getProductPricingData'])->name('product.pricing');
Route::get('switch-account', [\App\Http\Controllers\FrontendController::class, 'switchAccount'])->name('switch-account');
Route::get('remove-account/{id}', [\App\Http\Controllers\FrontendController::class, 'removeAccount'])->name('remove-account');
Route::get('add-new-account', [\App\Http\Controllers\FrontendController::class, 'addNewAccount'])->name('add-new-account');
Route::get('wishlist/status', [\App\Http\Controllers\FrontendController::class, 'wishlistStatus'])->name('wishlist.status');

Route::middleware(['auth:customer'])->group(function () {    
    Route::get('wishlist', [\App\Http\Controllers\FrontendController::class, 'wishlist'])->name('wishlist');
    Route::post('wishlist/add', [\App\Http\Controllers\FrontendController::class, 'addToWishlist'])->name('wishlist.add');
    Route::delete('wishlist/{id}', [\App\Http\Controllers\FrontendController::class, 'removeFromWishlist'])->name('wishlist.remove');
    Route::post('wishlist/toggle', [\App\Http\Controllers\FrontendController::class, 'toggleWishlist'])->name('wishlist.toggle');
    Route::post('wishlist/merge', [\App\Http\Controllers\FrontendController::class, 'mergeWishlist'])->name('wishlist.merge');
    
    Route::get('addresses', [\App\Http\Controllers\FrontendController::class, 'addresses'])->name('addresses');
    Route::post('addresses', [\App\Http\Controllers\FrontendController::class, 'storeAddress'])->name('addresses.store');
    Route::put('addresses/{id}', [\App\Http\Controllers\FrontendController::class, 'updateAddress'])->name('addresses.update');
    Route::delete('addresses/{id}', [\App\Http\Controllers\FrontendController::class, 'deleteAddress'])->name('addresses.delete');
    
    Route::get('orders', [\App\Http\Controllers\FrontendController::class, 'orders'])->name('orders');
    Route::post('orders/reorder', [\App\Http\Controllers\FrontendController::class, 'reorder'])->name('orders.reorder');
    Route::get('orders/export', [\App\Http\Controllers\FrontendController::class, 'exportOrders'])->name('orders.export');
});

Route::post('state-list', [\App\Helpers\Helper::class, 'getStatesByCountry'])->name('state-list');
Route::post('city-list', [\App\Helpers\Helper::class, 'getCitiesByState'])->name('city-list');