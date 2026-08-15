<?php

use App\Http\Controllers\Staff\CategoryController;
use App\Http\Controllers\Staff\DashboardController;
use App\Http\Controllers\Staff\ProductController;
use App\Http\Controllers\Staff\ProductImageController;
use App\Http\Controllers\Staff\ProductVariantController;
use App\Http\Controllers\Staff\InventoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('staff')
    ->name('staff.')
    ->middleware([
        'auth',
        'staff',
    ])
    ->group(function () {

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
        )->only([
            'index',
            'create',
            'store',
            'edit',
            'update',
        ]);


        /*
        |--------------------------------------------------------------------------
        | PRODUCTS
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'products',
            ProductController::class
        )->only([
            'index',
            'create',
            'store',
            'show',
            'edit',
            'update',
        ]);


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


        Route::patch(
            'products/{product}/variants/{variant}/deactivate',
            [ProductVariantController::class, 'deactivate']
        )->name('products.variants.deactivate');


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