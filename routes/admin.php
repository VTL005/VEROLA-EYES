<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\ProductVariantController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware([
        'auth',
        'admin',
    ])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [DashboardController::class, 'index']
        )->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | CATEGORIES
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'categories',
            CategoryController::class
        )->except([
            'show',
        ]);


        /*
        |--------------------------------------------------------------------------
        | PRODUCTS
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'products',
            ProductController::class
        );


        /*
        |--------------------------------------------------------------------------
        | PRODUCT ACTIVATION
        |--------------------------------------------------------------------------
        |
        | Sản phẩm sau khi được tạo vẫn ở trạng thái chưa kinh doanh.
        | Chỉ khi đủ điều kiện thì Admin mới kích hoạt để đưa ra website.
        |
        */

        Route::patch(
            'products/{product}/activate',
            [ProductController::class, 'activate']
        )->name('products.activate');


        /*
        |--------------------------------------------------------------------------
        | PRODUCT IMAGES
        |--------------------------------------------------------------------------
        */

        Route::post(
            'products/{product}/images',
            [ProductImageController::class, 'store']
        )->name('products.images.store');


        Route::patch(
            'products/{product}/images/{image}/primary',
            [ProductImageController::class, 'setPrimary']
        )->name('products.images.primary');


        Route::delete(
            'products/{product}/images/{image}',
            [ProductImageController::class, 'destroy']
        )->name('products.images.destroy');


        /*
        |--------------------------------------------------------------------------
        | PRODUCT VARIANTS
        |--------------------------------------------------------------------------
        */

        Route::get(
            'products/{product}/variants/create',
            [ProductVariantController::class, 'create']
        )->name('products.variants.create');


        Route::post(
            'products/{product}/variants',
            [ProductVariantController::class, 'store']
        )->name('products.variants.store');


        Route::get(
            'products/{product}/variants/{variant}/edit',
            [ProductVariantController::class, 'edit']
        )->name('products.variants.edit');


        Route::put(
            'products/{product}/variants/{variant}',
            [ProductVariantController::class, 'update']
        )->name('products.variants.update');


        Route::delete(
            'products/{product}/variants/{variant}',
            [ProductVariantController::class, 'destroy']
        )->name('products.variants.destroy');


        /*
        |--------------------------------------------------------------------------
        | INVENTORY
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/inventory',
            [InventoryController::class, 'index']
        )->name('inventory.index');

    });