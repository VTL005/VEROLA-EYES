<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EyePrescriptionController as AdminEyePrescriptionController;
use App\Http\Controllers\Admin\InventoryController as AdminInventoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProductImageController as AdminProductImageController;
use App\Http\Controllers\Admin\ProductVariantController as AdminProductVariantController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\StaffController as AdminStaffController;
use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\Admin\WarrantyController as AdminWarrantyController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerChatController;
use App\Http\Controllers\EyePrescriptionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\Staff\AppointmentController as StaffAppointmentController;
use App\Http\Controllers\Staff\CategoryController as StaffCategoryController;
use App\Http\Controllers\Staff\ChatController as StaffChatController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\EyePrescriptionController as StaffEyePrescriptionController;
use App\Http\Controllers\Staff\InventoryController as StaffInventoryController;
use App\Http\Controllers\Staff\OrderController as StaffOrderController;
use App\Http\Controllers\Staff\ProductController as StaffProductController;
use App\Http\Controllers\Staff\ProductImageController as StaffProductImageController;
use App\Http\Controllers\Staff\ProductVariantController as StaffProductVariantController;
use App\Http\Controllers\Staff\ReviewController as StaffReviewController;
use App\Http\Controllers\Staff\WarrantyController as StaffWarrantyController;
use App\Http\Controllers\WarrantyController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

Route::get(
    '/',
    [HomeController::class, 'index']
)->name('home');

/*
|--------------------------------------------------------------------------
| PRODUCT
|--------------------------------------------------------------------------
*/

Route::get(
    '/products',
    [ProductController::class, 'index']
)->name('products.index');

Route::get(
    '/products/{product}',
    [ProductController::class, 'show']
)->name('products.show');

/*
|--------------------------------------------------------------------------
| CATEGORY
|--------------------------------------------------------------------------
*/

Route::get(
    '/categories/{category:slug}',
    [CategoryController::class, 'show']
)->name('categories.show');

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| CUSTOMER CHAT
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get(
        '/chat',
        [CustomerChatController::class, 'index']
    )->name('customer.chat.index');

    Route::post(
        '/chat',
        [CustomerChatController::class, 'store']
    )->name('customer.chat.store');

    Route::patch(
        '/chat/{conversation}/read',
        [CustomerChatController::class, 'markRead']
    )->name('customer.chat.read');
});

Route::middleware('guest')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | GOOGLE LOGIN
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/auth/google',
        [SocialAuthController::class, 'redirectToGoogle']
    )->name('social.google.redirect');

    Route::get(
        '/auth/google/callback',
        [SocialAuthController::class, 'handleGoogleCallback']
    )->name('social.google.callback');

    Route::get(
        '/auth/google/complete-profile',
        [SocialAuthController::class, 'showGoogleCompleteProfile']
    )->name('social.google.complete');

    Route::post(
        '/auth/google/complete-profile',
        [SocialAuthController::class, 'completeGoogleProfile']
    )->name('social.google.complete.store');

    /*
    |--------------------------------------------------------------------------
    | FORGOT PASSWORD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/forgot-password',
        [PasswordResetController::class, 'showForgotPasswordForm']
    )->name('password.request');

    Route::post(
        '/forgot-password',
        [PasswordResetController::class, 'sendResetLink']
    )->name('password.email');

    /*
    |--------------------------------------------------------------------------
    | RESET PASSWORD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/reset-password/{token}',
        [PasswordResetController::class, 'showResetPasswordForm']
    )->name('password.reset');

    Route::post(
        '/reset-password',
        [PasswordResetController::class, 'resetPassword']
    )->name('password.update');

    Route::get(
        '/register',
        [AuthController::class, 'showRegister']
    )->name('register');

    Route::post(
        '/register',
        [AuthController::class, 'register']
    )->name('register.store');

    Route::get(
        '/login',
        [AuthController::class, 'showLogin']
    )->name('login');

    Route::post(
        '/login',
        [AuthController::class, 'login']
    )->name('login.store');

});

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

Route::post(
    '/logout',
    [AuthController::class, 'logout']
)
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| WARRANTY LOOKUP
|--------------------------------------------------------------------------
*/

Route::get(
    '/warranty-lookup',
    [WarrantyController::class, 'lookupForm']
)->name('warranties.lookup-form');

Route::post(
    '/warranty-lookup',
    [WarrantyController::class, 'lookup']
)->name('warranties.lookup');
/*
|--------------------------------------------------------------------------
| CUSTOMER
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'customer',
])->group(function () {

    Route::post(
        '/checkout/prepare',
        [CheckoutController::class, 'prepare']
    )->name('checkout.prepare');

    /*
    |--------------------------------------------------------------------------
    | CUSTOMER REVIEWS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/products/{product}/review/create',
        [ReviewController::class, 'create']
    )->name('reviews.create');

    Route::post(
        '/products/{product}/reviews',
        [ReviewController::class, 'store']
    )->name('reviews.store');

    /*
    |--------------------------------------------------------------------------
    | CUSTOMER WARRANTIES
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/warranties',
        [WarrantyController::class, 'index']
    )->name('warranties.index');

    Route::get(
        '/warranties/{warranty}',
        [WarrantyController::class, 'show']
    )->name('warranties.show');

    /*
    |--------------------------------------------------------------------------
    | CUSTOMER EYE PRESCRIPTIONS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/eye-prescriptions',
        [EyePrescriptionController::class, 'index']
    )->name('eye-prescriptions.index');

    Route::get(
        '/eye-prescriptions/{eyePrescription}',
        [EyePrescriptionController::class, 'show']
    )->name('eye-prescriptions.show');

    /*
    |--------------------------------------------------------------------------
    | CUSTOMER APPOINTMENTS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/appointments',
        [AppointmentController::class, 'index']
    )->name('appointments.index');

    Route::get(
        '/appointments/create',
        [AppointmentController::class, 'create']
    )->name('appointments.create');

    Route::post(
        '/appointments',
        [AppointmentController::class, 'store']
    )->name('appointments.store');

    Route::get(
        '/appointments/{appointment}',
        [AppointmentController::class, 'show']
    )->name('appointments.show');

    Route::patch(
        '/appointments/{appointment}/cancel',
        [AppointmentController::class, 'cancel']
    )->name('appointments.cancel');

    /*
|--------------------------------------------------------------------------
| CUSTOMER CHANGE PASSWORD
|--------------------------------------------------------------------------
*/

    Route::get(
        '/profile/change-password',
        [PasswordController::class, 'edit']
    )->name('profile.password.edit');

    Route::patch(
        '/profile/change-password',
        [PasswordController::class, 'update']
    )->name('profile.password.update');

    /*
    |--------------------------------------------------------------------------
    | WISHLIST
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/wishlist',
        [WishlistController::class, 'index']
    )->name('wishlist.index');

    Route::post(
        '/wishlist/{product}',
        [WishlistController::class, 'store']
    )->name('wishlist.store');

    Route::delete(
        '/wishlist/{product}',
        [WishlistController::class, 'destroy']
    )->name('wishlist.destroy');

    /*
    |--------------------------------------------------------------------------
    | CART
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/cart',
        [CartController::class, 'index']
    )->name('cart.index');

    Route::post(
        '/cart',
        [CartController::class, 'store']
    )->name('cart.store');

    Route::patch(
        '/cart/{variant}',
        [CartController::class, 'update']
    )->name('cart.update');

    Route::delete(
        '/cart/{variant}',
        [CartController::class, 'destroy']
    )->name('cart.destroy');

    Route::delete(
        '/cart',
        [CartController::class, 'clear']
    )->name('cart.clear');

    /*
    |--------------------------------------------------------------------------
    | CART VOUCHER
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/cart/voucher/apply',
        [CartController::class, 'applyVoucher']
    )->name('cart.voucher.apply');

    Route::delete(
        '/cart/voucher/remove',
        [CartController::class, 'removeVoucher']
    )->name('cart.voucher.remove');

    /*
    |--------------------------------------------------------------------------
    | ADDRESS
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'addresses',
        AddressController::class
    )->except([
        'show',
        'index',
    ]);

    Route::get(
        '/addresses',
        [AddressController::class, 'index']
    )->name('addresses.index');

    /*
    |--------------------------------------------------------------------------
    | CHECKOUT
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/checkout',
        [CheckoutController::class, 'index']
    )->name('checkout.index');

    Route::post(
        '/checkout',
        [CheckoutController::class, 'store']
    )->name('checkout.store');

    Route::get(
        '/checkout/success/{order}',
        [CheckoutController::class, 'success']
    )->name('checkout.success');

    /*
    |--------------------------------------------------------------------------
    | CUSTOMER ORDERS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/orders',
        [OrderController::class, 'index']
    )->name('orders.index');

    Route::get(
        '/orders/{order}',
        [OrderController::class, 'show']
    )->name('orders.show');

    Route::patch(
        '/orders/{order}/cancel',
        [OrderController::class, 'cancel']
    )->name('orders.cancel');

    /*
|--------------------------------------------------------------------------
| PAYMENT QR
|--------------------------------------------------------------------------
*/

    Route::get(
        '/payments/qr/{order}',
        [PaymentController::class, 'showQr']
    )->name('payments.qr.show');

    Route::post(
        '/payments/qr/{order}/confirm',
        [PaymentController::class, 'confirmQr']
    )->name('payments.qr.confirm');

    /*
    |--------------------------------------------------------------------------
    | PAYMENT VNPAY
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/payments/vnpay/{order}',
        [PaymentController::class, 'showVnpay']
    )->name('payments.vnpay.show');

    Route::post(
        '/payments/vnpay/{order}/confirm',
        [PaymentController::class, 'confirmVnpay']
    )->name('payments.vnpay.confirm');
    /*
    |--------------------------------------------------------------------------
    | CUSTOMER PROFILE
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/profile',
        [ProfileController::class, 'show']
    )->name('profile.show');

    Route::get(
        '/profile/edit',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

});

/*
|--------------------------------------------------------------------------
| STAFF
|--------------------------------------------------------------------------
*/

Route::prefix('staff')
    ->name('staff.')
    ->middleware([
        'auth',
        'staff',
    ])
    ->group(function () {

        /*
|--------------------------------------------------------------------------
| STAFF DASHBOARD
|--------------------------------------------------------------------------
*/

        Route::get(
            '/dashboard',
            [StaffDashboardController::class, 'index']
        )->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | STAFF CHAT
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/chat',
            [StaffChatController::class, 'index']
        )->name('chat.index');

        Route::get(
            '/chat/{conversation}',
            [StaffChatController::class, 'show']
        )->name('chat.show');

        Route::post(
            '/chat/{conversation}/accept',
            [StaffChatController::class, 'accept']
        )->name('chat.accept');

        Route::post(
            '/chat/{conversation}/messages',
            [StaffChatController::class, 'store']
        )->name('chat.messages.store');

        Route::post(
            '/chat/{conversation}/products',
            [StaffChatController::class, 'storeProducts']
        )->name('chat.products.store');

        Route::patch(
            '/chat/{conversation}/close',
            [StaffChatController::class, 'close']
        )->name('chat.close');
        Route::patch(
            '/chat/{conversation}/read',
            [StaffChatController::class, 'markRead']
        )->name('chat.read');

        /*
        |--------------------------------------------------------------------------
        | STAFF CATEGORIES
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/categories',
            [StaffCategoryController::class, 'index']
        )->name('categories.index');

        Route::get(
            '/categories/create',
            [StaffCategoryController::class, 'create']
        )->name('categories.create');

        Route::post(
            '/categories',
            [StaffCategoryController::class, 'store']
        )->name('categories.store');

        Route::get(
            '/categories/{category}/edit',
            [StaffCategoryController::class, 'edit']
        )->name('categories.edit');

        Route::put(
            '/categories/{category}',
            [StaffCategoryController::class, 'update']
        )->name('categories.update');

        /*
        |--------------------------------------------------------------------------
        | STAFF PRODUCTS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/products',
            [StaffProductController::class, 'index']
        )->name('products.index');

        Route::get(
            '/products/create',
            [StaffProductController::class, 'create']
        )->name('products.create');

        Route::post(
            '/products',
            [StaffProductController::class, 'store']
        )->name('products.store');

        Route::get(
            '/products/{product}',
            [StaffProductController::class, 'show']
        )->name('products.show');

        Route::get(
            '/products/{product}/edit',
            [StaffProductController::class, 'edit']
        )->name('products.edit');

        Route::put(
            '/products/{product}',
            [StaffProductController::class, 'update']
        )->name('products.update');

        /*
        |--------------------------------------------------------------------------
        | STAFF PRODUCT IMAGES
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/products/{product}/images',
            [StaffProductImageController::class, 'store']
        )->name('products.images.store');

        Route::patch(
            '/products/{product}/images/{image}/primary',
            [StaffProductImageController::class, 'setPrimary']
        )->name('products.images.set-primary');

        Route::delete(
            '/products/{product}/images/{image}',
            [StaffProductImageController::class, 'destroy']
        )->name('products.images.destroy');

        /*
        |--------------------------------------------------------------------------
        | STAFF PRODUCT VARIANTS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/products/{product}/variants/create',
            [StaffProductVariantController::class, 'create']
        )->name('products.variants.create');

        Route::post(
            '/products/{product}/variants',
            [StaffProductVariantController::class, 'store']
        )->name('products.variants.store');

        Route::get(
            '/products/{product}/variants/{variant}/edit',
            [StaffProductVariantController::class, 'edit']
        )->name('products.variants.edit');

        Route::put(
            '/products/{product}/variants/{variant}',
            [StaffProductVariantController::class, 'update']
        )->name('products.variants.update');

        Route::patch(
            '/products/{product}/variants/{variant}/deactivate',
            [StaffProductVariantController::class, 'deactivate']
        )->name('products.variants.deactivate');

        /*
        |--------------------------------------------------------------------------
        | STAFF INVENTORY
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/inventory',
            [StaffInventoryController::class, 'index']
        )->name('inventory.index');

        /*
        |--------------------------------------------------------------------------
        | STAFF REVIEWS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/reviews',
            [StaffReviewController::class, 'index']
        )->name('reviews.index');

        Route::get(
            '/reviews/{review}',
            [StaffReviewController::class, 'show']
        )->name('reviews.show');

        Route::patch(
            '/reviews/{review}/visibility',
            [StaffReviewController::class, 'toggleVisibility']
        )->name('reviews.toggle-visibility');

        /*
|--------------------------------------------------------------------------
| STAFF WARRANTIES
|--------------------------------------------------------------------------
*/

        Route::get(
            '/order-details/{orderDetail}/warranty/create',
            [StaffWarrantyController::class, 'create']
        )->name('warranties.create');

        Route::post(
            '/order-details/{orderDetail}/warranty',
            [StaffWarrantyController::class, 'store']
        )->name('warranties.store');

        Route::get(
            '/warranties/{warranty}',
            [StaffWarrantyController::class, 'show']
        )->name('warranties.show');

        /*
|--------------------------------------------------------------------------
| STAFF EYE PRESCRIPTIONS
|--------------------------------------------------------------------------
*/

        Route::get(
            '/appointments/{appointment}/eye-prescriptions/create',
            [StaffEyePrescriptionController::class, 'create']
        )->name('eye-prescriptions.create');

        Route::post(
            '/appointments/{appointment}/eye-prescriptions',
            [StaffEyePrescriptionController::class, 'store']
        )->name('eye-prescriptions.store');

        Route::get(
            '/eye-prescriptions/{eyePrescription}',
            [StaffEyePrescriptionController::class, 'show']
        )->name('eye-prescriptions.show');

        /*
|--------------------------------------------------------------------------
| STAFF APPOINTMENTS
|--------------------------------------------------------------------------
*/

        Route::get(
            '/appointments',
            [StaffAppointmentController::class, 'index']
        )->name('appointments.index');

        Route::get(
            '/appointments/{appointment}',
            [StaffAppointmentController::class, 'show']
        )->name('appointments.show');

        Route::patch(
            '/appointments/{appointment}/status',
            [StaffAppointmentController::class, 'updateStatus']
        )->name('appointments.update-status');

        Route::get(
            '/orders',
            [StaffOrderController::class, 'index']
        )->name('orders.index');

        Route::get(
            '/orders/{order}',
            [StaffOrderController::class, 'show']
        )->name('orders.show');

        Route::patch(
            '/orders/{order}/status',
            [StaffOrderController::class, 'updateStatus']
        )->name('orders.update-status');

        Route::patch(
            '/orders/{order}/cancel',
            [StaffOrderController::class, 'cancel']
        )->name('orders.cancel');

    });
/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
|
| Phải đăng nhập và có role Admin.
|
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware([
        'auth',
        'admin',
    ])
    ->group(function () {

        Route::patch(
            '/payments/{payment}/refund',
            [AdminPaymentController::class, 'refund']
        )->name('payments.refund');

        /*
|--------------------------------------------------------------------------
| ADMIN - WARRANTIES
|--------------------------------------------------------------------------
*/

        Route::get(
            '/warranties',
            [AdminWarrantyController::class, 'index']
        )->name('warranties.index');

        Route::get(
            '/order-details/{orderDetail}/warranty/create',
            [AdminWarrantyController::class, 'create']
        )->name('warranties.create');

        Route::post(
            '/order-details/{orderDetail}/warranty',
            [AdminWarrantyController::class, 'store']
        )->name('warranties.store');

        Route::get(
            '/warranties/{warranty}',
            [AdminWarrantyController::class, 'show']
        )->name('warranties.show');

        /*
|--------------------------------------------------------------------------
| ADMIN - PAYMENTS
|--------------------------------------------------------------------------
*/

        Route::get(
            '/payments',
            [AdminPaymentController::class, 'index']
        )->name('payments.index');

        Route::get(
            '/payments/{payment}',
            [AdminPaymentController::class, 'show']
        )->name('payments.show');

        /*
|--------------------------------------------------------------------------
| ADMIN - EYE PRESCRIPTIONS
|--------------------------------------------------------------------------
*/

        Route::get(
            '/eye-prescriptions',
            [AdminEyePrescriptionController::class, 'index']
        )->name('eye-prescriptions.index');

        Route::get(
            '/appointments/{appointment}/eye-prescriptions/create',
            [AdminEyePrescriptionController::class, 'create']
        )->name('eye-prescriptions.create');

        Route::post(
            '/appointments/{appointment}/eye-prescriptions',
            [AdminEyePrescriptionController::class, 'store']
        )->name('eye-prescriptions.store');

        Route::get(
            '/eye-prescriptions/{eyePrescription}',
            [AdminEyePrescriptionController::class, 'show']
        )->name('eye-prescriptions.show');

        /*
|--------------------------------------------------------------------------
| ADMIN - APPOINTMENTS
|--------------------------------------------------------------------------
*/

        Route::get(
            '/appointments',
            [AdminAppointmentController::class, 'index']
        )->name('appointments.index');

        Route::get(
            '/appointments/{appointment}',
            [AdminAppointmentController::class, 'show']
        )->name('appointments.show');

        Route::patch(
            '/appointments/{appointment}/status',
            [AdminAppointmentController::class, 'updateStatus']
        )->name('appointments.update-status');
        /*
|--------------------------------------------------------------------------
| ADMIN CATEGORIES
|--------------------------------------------------------------------------
*/

        Route::get(
            '/categories',
            [AdminCategoryController::class, 'index']
        )->name('categories.index');

        Route::get(
            '/categories/create',
            [AdminCategoryController::class, 'create']
        )->name('categories.create');

        Route::post(
            '/categories',
            [AdminCategoryController::class, 'store']
        )->name('categories.store');

        Route::get(
            '/categories/{category}/edit',
            [AdminCategoryController::class, 'edit']
        )->name('categories.edit');

        Route::put(
            '/categories/{category}',
            [AdminCategoryController::class, 'update']
        )->name('categories.update');

        Route::delete(
            '/categories/{category}',
            [AdminCategoryController::class, 'destroy']
        )->name('categories.destroy');

        /*
        |--------------------------------------------------------------------------
        | ADMIN PRODUCTS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/products',
            [AdminProductController::class, 'index']
        )->name('products.index');

        Route::get(
            '/products/create',
            [AdminProductController::class, 'create']
        )->name('products.create');

        Route::post(
            '/products',
            [AdminProductController::class, 'store']
        )->name('products.store');

        Route::get(
            '/products/{product}',
            [AdminProductController::class, 'show']
        )->name('products.show');

        Route::get(
            '/products/{product}/edit',
            [AdminProductController::class, 'edit']
        )->name('products.edit');

        Route::put(
            '/products/{product}',
            [AdminProductController::class, 'update']
        )->name('products.update');

        Route::delete(
            '/products/{product}',
            [AdminProductController::class, 'destroy']
        )->name('products.destroy');

        /*
        |--------------------------------------------------------------------------
        | ADMIN PRODUCT IMAGES
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/products/{product}/images',
            [AdminProductImageController::class, 'store']
        )->name('products.images.store');

        Route::patch(
            '/products/{product}/images/{image}/primary',
            [AdminProductImageController::class, 'setPrimary']
        )->name('products.images.set-primary');

        Route::delete(
            '/products/{product}/images/{image}',
            [AdminProductImageController::class, 'destroy']
        )->name('products.images.destroy');

        /*
        |--------------------------------------------------------------------------
        | ADMIN PRODUCT VARIANTS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/products/{product}/variants/create',
            [AdminProductVariantController::class, 'create']
        )->name('products.variants.create');

        Route::post(
            '/products/{product}/variants',
            [AdminProductVariantController::class, 'store']
        )->name('products.variants.store');

        Route::get(
            '/products/{product}/variants/{variant}/edit',
            [AdminProductVariantController::class, 'edit']
        )->name('products.variants.edit');

        Route::put(
            '/products/{product}/variants/{variant}',
            [AdminProductVariantController::class, 'update']
        )->name('products.variants.update');

        Route::delete(
            '/products/{product}/variants/{variant}',
            [AdminProductVariantController::class, 'destroy']
        )->name('products.variants.destroy');

        /*
        |--------------------------------------------------------------------------
        | ADMIN INVENTORY
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/inventory',
            [AdminInventoryController::class, 'index']
        )->name('inventory.index');

        /*
|--------------------------------------------------------------------------
| ADMIN REPORTS
|--------------------------------------------------------------------------
*/

        Route::get(
            '/reports',
            [AdminReportController::class, 'index']
        )->name('reports.index');

        /*
|--------------------------------------------------------------------------
| ADMIN DASHBOARD
|--------------------------------------------------------------------------
*/

        Route::get(
            '/dashboard',
            [AdminDashboardController::class, 'index']
        )->name('dashboard');

        /*
|--------------------------------------------------------------------------
| ADMIN STAFF
|--------------------------------------------------------------------------
*/

        Route::get(
            '/staff',
            [AdminStaffController::class, 'index']
        )->name('staff.index');

        Route::get(
            '/staff/create',
            [AdminStaffController::class, 'create']
        )->name('staff.create');

        Route::post(
            '/staff',
            [AdminStaffController::class, 'store']
        )->name('staff.store');

        Route::get(
            '/staff/{staff}',
            [AdminStaffController::class, 'show']
        )->name('staff.show');

        Route::get(
            '/staff/{staff}/edit',
            [AdminStaffController::class, 'edit']
        )->name('staff.edit');

        Route::put(
            '/staff/{staff}',
            [AdminStaffController::class, 'update']
        )->name('staff.update');

        Route::patch(
            '/staff/{staff}/toggle-active',
            [AdminStaffController::class, 'toggleActive']
        )->name('staff.toggle-active');

        /*
|--------------------------------------------------------------------------
| ADMIN CUSTOMERS
|--------------------------------------------------------------------------
*/

        Route::get(
            '/customers',
            [AdminCustomerController::class, 'index']
        )->name('customers.index');

        Route::get(
            '/customers/{customer}',
            [AdminCustomerController::class, 'show']
        )->name('customers.show');

        Route::patch(
            '/customers/{customer}/toggle-active',
            [AdminCustomerController::class, 'toggleActive']
        )->name('customers.toggle-active');

        /*
|--------------------------------------------------------------------------
| ADMIN VOUCHERS
|--------------------------------------------------------------------------
*/

        Route::get(
            '/vouchers',
            [AdminVoucherController::class, 'index']
        )->name('vouchers.index');

        Route::get(
            '/vouchers/create',
            [AdminVoucherController::class, 'create']
        )->name('vouchers.create');

        Route::post(
            '/vouchers',
            [AdminVoucherController::class, 'store']
        )->name('vouchers.store');

        Route::get(
            '/vouchers/{voucher}/edit',
            [AdminVoucherController::class, 'edit']
        )->name('vouchers.edit');

        Route::put(
            '/vouchers/{voucher}',
            [AdminVoucherController::class, 'update']
        )->name('vouchers.update');

        Route::patch(
            '/vouchers/{voucher}/toggle-active',
            [AdminVoucherController::class, 'toggleActive']
        )->name('vouchers.toggle-active');

        /*
|--------------------------------------------------------------------------
| ADMIN REVIEWS
|--------------------------------------------------------------------------
*/

        Route::get(
            '/reviews',
            [AdminReviewController::class, 'index']
        )->name('reviews.index');

        Route::get(
            '/reviews/{review}',
            [AdminReviewController::class, 'show']
        )->name('reviews.show');

        Route::patch(
            '/reviews/{review}/visibility',
            [AdminReviewController::class, 'toggleVisibility']
        )->name('reviews.toggle-visibility');

        /*
        |--------------------------------------------------------------------------
        | ADMIN ORDERS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/orders',
            [AdminOrderController::class, 'index']
        )->name('orders.index');

        Route::get(
            '/orders/{order}',
            [AdminOrderController::class, 'show']
        )->name('orders.show');

        Route::patch(
            '/orders/{order}/status',
            [AdminOrderController::class, 'updateStatus']
        )->name('orders.update-status');

        Route::patch(
            '/orders/{order}/cancel',
            [AdminOrderController::class, 'cancel']
        )->name('orders.cancel');

    });
