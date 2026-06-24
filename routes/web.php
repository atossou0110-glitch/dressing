<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminEngagementController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\FlashNotificationController;
use App\Http\Controllers\NewsletterSubscriptionController;
use App\Http\Controllers\ProductCheckoutController;
use App\Http\Controllers\ProductInteractionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupportChatController;
use Illuminate\Support\Facades\Route;

// Catalog Routes
Route::get('/', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/dr-dressing', [CatalogController::class, 'drDressing'])->name('catalog.dr-dressing');
Route::get('/faq', fn() => view('faq'))->name('faq');
Route::get('/produit/{product}', [CatalogController::class, 'show'])->name('products.show');
Route::get('/produit/{product}/checkout', [ProductCheckoutController::class, 'show'])->name('products.checkout.show');
Route::post('/produit/{product}/checkout', [ProductCheckoutController::class, 'store'])
    ->middleware('throttle:20,1')
    ->name('products.checkout.store');
Route::get('/produit-a', [CatalogController::class, 'showProductA'])->name('products.show.a');
Route::get('/produit-b', [CatalogController::class, 'showProductB'])->name('products.show.b');
Route::get('/produit-c', [CatalogController::class, 'showProductC'])->name('products.show.c');
Route::get('/commandes/{order}', [ProductCheckoutController::class, 'showOrder'])->name('orders.show');
Route::get('/commandes/{order}/status', [ProductCheckoutController::class, 'status'])->name('orders.status');
Route::get('/paiements/fedapay/retour/{order}', [ProductCheckoutController::class, 'fedapayReturn'])->name('payments.fedapay.return');
Route::post('/paiements/fedapay/webhook', [ProductCheckoutController::class, 'fedapayWebhook'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('payments.fedapay.webhook');
Route::get('/catalog-media/commode/{filename}', [CatalogController::class, 'legacyCommodeImage'])
    ->where('filename', '[A-Za-z0-9\s\-_.()]+\.(png|jpg|jpeg|webp|gif|PNG|JFIF|jfif)$')
    ->name('catalog.media.commode');
Route::get('/catalog-media/{product}/{filename}', [CatalogController::class, 'productImage'])
    ->where('filename', '[A-Za-z0-9\s\-_.()]+\.(png|jpg|jpeg|webp|gif|PNG|JFIF|jfif)$')
    ->name('catalog.media.product');
Route::get('/assets/commode-etagere/{filename}', [CatalogController::class, 'commodEtagereImage'])
    ->where('filename', '.*\.(png|jpg|jpeg|webp|gif|PNG|JFIF|jfif)$')
    ->name('assets.commode-etagere');
Route::get('/assets/dressing/{filename}', [CatalogController::class, 'dressingImage'])
    ->where('filename', '.*\.(png|jpg|jpeg|webp|gif|PNG|JFIF|jfif)$')
    ->name('assets.dressing');

Route::prefix('products/{product}')->name('products.')->middleware('throttle:100,1')->group(function (): void {
    Route::post('/preorder', [ProductInteractionController::class, 'preorder'])->middleware('throttle:10,1')->name('preorder');
    Route::post('/reviews', [ProductInteractionController::class, 'storeReview'])->middleware('throttle:10,1')->name('reviews.store');
});

Route::post('/newsletter/subscribe', [NewsletterSubscriptionController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('newsletter.subscribe');
Route::post('/notifications/subscribe', [FlashNotificationController::class, 'subscribe'])
    ->middleware('throttle:20,1')
    ->name('notifications.subscribe');
Route::get('/notifications/latest', [FlashNotificationController::class, 'latest'])
    ->middleware('throttle:60,1')
    ->name('notifications.latest');
Route::post('/support/chat', [SupportChatController::class, 'store'])
    ->middleware('throttle:30,1')
    ->name('support.chat');

Route::middleware(['auth', 'verified', 'admin', 'admin.audit'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/products', [AdminDashboardController::class, 'products'])->name('dashboard.products');
    Route::get('/dashboard/products/a', [AdminDashboardController::class, 'productsA'])->name('dashboard.products.a');
    Route::get('/dashboard/products/b', [AdminDashboardController::class, 'productsB'])->name('dashboard.products.b');
    Route::get('/dashboard/audit-logs', [AdminDashboardController::class, 'auditLogs'])->name('dashboard.audit-logs');
    Route::get('/dashboard/export/products', [AdminDashboardController::class, 'exportProductsCsv'])->name('admin.export.products');
    Route::get('/dashboard/export/orders', [AdminDashboardController::class, 'exportOrdersCsv'])->name('admin.export.orders');
    Route::get('/dashboard/export/clients', [AdminDashboardController::class, 'exportClientsCsv'])->name('admin.export.clients');
    Route::get('/dashboard/export/audit-logs', [AdminDashboardController::class, 'exportAuditLogsCsv'])->name('admin.export.audit-logs');
    Route::get('/dashboard/reports/study.csv', [AdminDashboardController::class, 'exportStudyReportCsv'])->name('admin.reports.study.export');
    Route::post('/dashboard/products', [AdminDashboardController::class, 'storeProduct'])->name('admin.products.store');
    Route::put('/dashboard/products/{product}', [AdminDashboardController::class, 'updateProduct'])->name('admin.products.update');
    Route::post('/dashboard/products/{product}/reset-counters', [AdminDashboardController::class, 'resetProductCounters'])->name('admin.products.reset');
    Route::post('/dashboard/products/{product}/images', [AdminDashboardController::class, 'storeProductImages'])->name('admin.products.images.store');
    Route::delete('/dashboard/products/{product}/images', [AdminDashboardController::class, 'destroyProductImage'])->name('admin.products.images.destroy');
    Route::put('/dashboard/settings/whatsapp', [AdminDashboardController::class, 'updateWhatsApp'])->name('admin.settings.whatsapp');
    Route::delete('/dashboard/reviews/{review}', [AdminDashboardController::class, 'destroyReview'])->name('admin.reviews.destroy');
    Route::post('/dashboard/flash-campaigns', [AdminEngagementController::class, 'storeFlashCampaign'])->name('admin.flash-campaigns.store');
    Route::put('/dashboard/support/{supportConversation}', [AdminEngagementController::class, 'updateSupportConversation'])->name('admin.support.update');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
