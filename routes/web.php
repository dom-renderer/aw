<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::redirect('admin', 'admin/login');

Route::prefix('admin')->group(function () {
    Route::match(['GET', 'POST'], 'login', [LoginController::class, 'login'])->name('admin.login');
    Route::post('logout', [LoginController::class, 'logout'])->name('admin.logout');
});

Route::prefix('admin')->middleware(['auth', 'permission'])->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', \App\Http\Controllers\UserController::class);
    Route::resource('roles', \App\Http\Controllers\RoleController::class);
    Route::resource('customers', \App\Http\Controllers\CustomerController::class);
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);
    Route::resource('locations', \App\Http\Controllers\WarehouseLocationController::class);
    Route::resource('customer-locations', \App\Http\Controllers\LocationController::class);
    Route::resource('warehouses', \App\Http\Controllers\WarehouseController::class);
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    Route::resource('products', \App\Http\Controllers\ProductController::class);
    Route::resource('brands', \App\Http\Controllers\BrandController::class);

    Route::any('product-management/{type?}/{step?}/{id?}', [\App\Http\Controllers\ProductController::class, 'steps'])->name('product-management');
    Route::match(['GET', 'POST'], 'get-variant-stock-history', [\App\Http\Controllers\VariableProductController::class, 'getVariantStockHistory'])->name('products.get-variant-stock-history');
    Route::match(['GET', 'POST'], 'adjust-stock', [\App\Http\Controllers\VariableProductController::class, 'adjustStock'])->name('products.adjust-stock');

    Route::post('brand-list', [\App\Helpers\Helper::class, 'getBrands'])->name('brand-list');
    Route::post('product-image-delete', [\App\Http\Controllers\ProductController::class, 'deleteImage'])->name('product-image-delete');

    Route::get('settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

    Route::get('home-page-settings', [App\Http\Controllers\HomePageSettingController::class, 'index'])->name('home-page-settings.index');
    Route::post('home-page-settings/reorder', [App\Http\Controllers\HomePageSettingController::class, 'reorder'])->name('home-page-settings.reorder');
    Route::post('home-page-settings/{key}', [App\Http\Controllers\HomePageSettingController::class, 'update'])->name('home-page-settings.update');
});

    Route::post('state-list', [\App\Helpers\Helper::class, 'getStatesByCountry'])->name('state-list');
    Route::post('city-list', [\App\Helpers\Helper::class, 'getCitiesByState'])->name('city-list');