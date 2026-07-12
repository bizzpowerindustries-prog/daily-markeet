<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\SellerController;
use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\PaymentController;

// Public routes
Route::get('/homepage', [App\Http\Controllers\Api\HomepageController::class, 'index']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/category/{slug}', [CategoryController::class, 'show']);
Route::get('/product/{slug}', [ProductController::class, 'show']);
Route::get('/search', [ProductController::class, 'search']);
Route::get('/order/track/{trackingNumber}', [TrackingController::class, 'track']);

// Payment webhooks (public)
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');

// Protected routes - Firebase Auth required
Route::middleware(['firebase.auth'])->group(function () {
    
    // Auth
    Route::post('/auth/verify', [AuthController::class, 'verifyFirebaseToken']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    
    // Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    
    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::put('/cart/update/{id}', [CartController::class, 'update']);
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove']);
    Route::delete('/cart/clear', [CartController::class, 'clear']);
    
    // Orders
    Route::post('/order/place', [OrderController::class, 'placeOrder']);
    Route::get('/orders', [OrderController::class, 'getOrders']);
    Route::get('/order/{id}', [OrderController::class, 'getOrder']);
    Route::post('/order/return/{id}', [OrderController::class, 'requestReturn']);
    
    // Wallet
    Route::get('/wallet', [WalletController::class, 'index']);
    Route::post('/wallet/add-money', [WalletController::class, 'addMoney']);
    Route::post('/wallet/pay', [WalletController::class, 'pay']);
    Route::get('/wallet/transactions', [WalletController::class, 'transactions']);
    
    // Reviews
    Route::post('/review', [App\Http\Controllers\Api\ReviewController::class, 'store']);
    Route::get('/reviews/product/{productId}', [App\Http\Controllers\Api\ReviewController::class, 'productReviews']);
    
    // Chat
    Route::post('/chat/send', [App\Http\Controllers\Api\ChatController::class, 'send']);
    Route::get('/chat/conversations', [App\Http\Controllers\Api\ChatController::class, 'conversations']);
    Route::get('/chat/{id}/messages', [App\Http\Controllers\Api\ChatController::class, 'messages']);
    
    // Addresses
    Route::get('/addresses', [App\Http\Controllers\Api\AddressController::class, 'index']);
    Route::post('/address', [App\Http\Controllers\Api\AddressController::class, 'store']);
    Route::put('/address/{id}', [App\Http\Controllers\Api\AddressController::class, 'update']);
    Route::delete('/address/{id}', [App\Http\Controllers\Api\AddressController::class, 'destroy']);
    
    // Wishlist
    Route::get('/wishlist', [App\Http\Controllers\Api\WishlistController::class, 'index']);
    Route::post('/wishlist/add', [App\Http\Controllers\Api\WishlistController::class, 'add']);
    Route::delete('/wishlist/remove/{id}', [App\Http\Controllers\Api\WishlistController::class, 'remove']);
    
    // Notifications
    Route::get('/notifications', [App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::post('/notifications/read/{id}', [App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
});

// Seller routes
Route::prefix('seller')->middleware(['firebase.auth', 'seller.auth'])->group(function () {
    
    // Products
    Route::get('/products', [SellerController::class, 'products']);
    Route::post('/product', [ProductController::class, 'store']);
    Route::put('/product/{id}', [ProductController::class, 'update']);
    Route::delete('/product/{id}', [ProductController::class, 'destroy']);
    
    // Orders
    Route::get('/orders', [SellerController::class, 'orders']);
    Route::put('/order/{id}/status', [SellerController::class, 'updateOrderStatus']);
    Route::post('/order/{id}/tracking', [SellerController::class, 'addTrackingEvent']);
    
    // Wallet
    Route::get('/wallet', [SellerController::class, 'wallet']);
    Route::post('/wallet/withdraw', [SellerController::class, 'requestWithdraw']);
    Route::get('/wallet/transactions', [SellerController::class, 'walletTransactions']);
    
    // Dashboard
    Route::get('/dashboard', [SellerController::class, 'dashboard']);
    
    // Analytics
    Route::get('/analytics', [SellerController::class, 'analytics']);
    
    // Ads
    Route::get('/ads', [SellerController::class, 'ads']);
    Route::post('/ad/create', [SellerController::class, 'createAd']);
    Route::put('/ad/{id}', [SellerController::class, 'updateAd']);
    
    // Profile
    Route::get('/profile', [SellerController::class, 'profile']);
    Route::put('/profile', [SellerController::class, 'updateProfile']);
});

// Admin routes
Route::prefix('admin')->middleware(['firebase.auth', 'admin.auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index']);
    Route::get('/stats', [App\Http\Controllers\Admin\DashboardController::class, 'stats']);
    
    // Categories
    Route::get('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'index']);
    Route::post('/category', [App\Http\Controllers\Admin\CategoryController::class, 'store']);
    Route::put('/category/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'update']);
    Route::delete('/category/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'destroy']);
    
    // Products
    Route::get('/products', [App\Http\Controllers\Admin\ProductController::class, 'index']);
    Route::put('/product/{id}/approve', [App\Http\Controllers\Admin\ProductController::class, 'approve']);
    Route::put('/product/{id}/reject', [App\Http\Controllers\Admin\ProductController::class, 'reject']);
    Route::delete('/product/{id}', [App\Http\Controllers\Admin\ProductController::class, 'destroy']);
    
    // Sellers
    Route::get('/sellers', [App\Http\Controllers\Admin\SellerController::class, 'index']);
    Route::put('/seller/{id}/approve', [App\Http\Controllers\Admin\SellerController::class, 'approve']);
    Route::put('/seller/{id}/suspend', [App\Http\Controllers\Admin\SellerController::class, 'suspend']);
    Route::put('/seller/{id}/ban', [App\Http\Controllers\Admin\SellerController::class, 'ban']);
    
    // Payment Gateways
    Route::get('/payment-gateways', [App\Http\Controllers\Admin\PaymentGatewayController::class, 'index']);
    Route::put('/payment-gateway/{id}', [App\Http\Controllers\Admin\PaymentGatewayController::class, 'update']);
    Route::post('/payment-gateway/switch', [App\Http\Controllers\Admin\PaymentGatewayController::class, 'switch']);
    
    // Withdrawals
    Route::get('/withdrawals', [App\Http\Controllers\Admin\WithdrawalController::class, 'index']);
    Route::put('/withdrawal/{id}/approve', [App\Http\Controllers\Admin\WithdrawalController::class, 'approve']);
    Route::put('/withdrawal/{id}/reject', [App\Http\Controllers\Admin\WithdrawalController::class, 'reject']);
    
    // Commissions
    Route::get('/commissions', [App\Http\Controllers\Admin\CommissionController::class, 'index']);
    Route::put('/commission', [App\Http\Controllers\Admin\CommissionController::class, 'update']);
    
    // Flash Sales
    Route::get('/flash-sales', [App\Http\Controllers\Admin\FlashSaleController::class, 'index']);
    Route::post('/flash-sale', [App\Http\Controllers\Admin\FlashSaleController::class, 'store']);
    Route::put('/flash-sale/{id}', [App\Http\Controllers\Admin\FlashSaleController::class, 'update']);
    Route::delete('/flash-sale/{id}', [App\Http\Controllers\Admin\FlashSaleController::class, 'destroy']);
    
    // Coupons
    Route::get('/coupons', [App\Http\Controllers\Admin\CouponController::class, 'index']);
    Route::post('/coupon', [App\Http\Controllers\Admin\CouponController::class, 'store']);
    Route::put('/coupon/{id}', [App\Http\Controllers\Admin\CouponController::class, 'update']);
    Route::delete('/coupon/{id}', [App\Http\Controllers\Admin\CouponController::class, 'destroy']);
    
    // Orders
    Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index']);
    Route::get('/order/{id}', [App\Http\Controllers\Admin\OrderController::class, 'show']);
    Route::put('/order/{id}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus']);
    Route::put('/order/{id}/cancel', [App\Http\Controllers\Admin\OrderController::class, 'cancel']);
    
    // Returns
    Route::get('/returns', [App\Http\Controllers\Admin\ReturnController::class, 'index']);
    Route::put('/return/{id}/approve', [App\Http\Controllers\Admin\ReturnController::class, 'approve']);
    Route::put('/return/{id}/reject', [App\Http\Controllers\Admin\ReturnController::class, 'reject']);
    
    // Reviews
    Route::get('/reviews', [App\Http\Controllers\Admin\ReviewController::class, 'index']);
    Route::put('/review/{id}/approve', [App\Http\Controllers\Admin\ReviewController::class, 'approve']);
    Route::put('/review/{id}/reject', [App\Http\Controllers\Admin\ReviewController::class, 'reject']);
    Route::delete('/review/{id}', [App\Http\Controllers\Admin\ReviewController::class, 'destroy']);
    
    // Settings
    Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index']);
    Route::put('/settings', [App\Http\Controllers\Admin\SettingController::class, 'update']);
    Route::post('/settings/upload-logo', [App\Http\Controllers\Admin\SettingController::class, 'uploadLogo']);
    Route::post('/settings/upload-favicon', [App\Http\Controllers\Admin\SettingController::class, 'uploadFavicon']);
    
    // Legal Pages
    Route::get('/legal', [App\Http\Controllers\Admin\LegalController::class, 'index']);
    Route::put('/legal/{id}', [App\Http\Controllers\Admin\LegalController::class, 'update']);
    
    // Email Logs
    Route::get('/email-logs', [App\Http\Controllers\Admin\EmailLogController::class, 'index']);
    Route::post('/email-log/{id}/resend', [App\Http\Controllers\Admin\EmailLogController::class, 'resend']);
    
    // Reports
    Route::get('/reports/sales', [App\Http\Controllers\Admin\ReportController::class, 'sales']);
    Route::get('/reports/revenue', [App\Http\Controllers\Admin\ReportController::class, 'revenue']);
    Route::get('/reports/commission', [App\Http\Controllers\Admin\ReportController::class, 'commission']);
    Route::get('/reports/top-products', [App\Http\Controllers\Admin\ReportController::class, 'topProducts']);
    Route::get('/reports/top-sellers', [App\Http\Controllers\Admin\ReportController::class, 'topSellers']);
    
    // Error Logs
    Route::get('/error-logs', [App\Http\Controllers\Admin\ErrorLogController::class, 'index']);
    Route::delete('/error-logs/clear', [App\Http\Controllers\Admin\ErrorLogController::class, 'clear']);
    
    // Notifications (Broadcast)
    Route::post('/notifications/broadcast', [App\Http\Controllers\Admin\NotificationController::class, 'broadcast']);
});
