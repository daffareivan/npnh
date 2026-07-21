<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminConversionController;
use App\Http\Controllers\Admin\AppSettingsController;
use App\Http\Controllers\Admin\CommunityAdminController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\CreditSettingsController;
use App\Http\Controllers\Admin\SubscriptionAdminController;
use App\Http\Controllers\App\RobloxIntegrationController;
use App\Http\Controllers\App\CommunityController;
use App\Http\Controllers\App\SubscriptionController;
use App\Http\Controllers\App\UserAppController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::view('/privacy', 'legal.privacy', ['title' => __('pages.privacy')])->name('privacy');
Route::view('/terms', 'legal.terms', ['title' => __('pages.terms')])->name('terms');
Route::post('/preferences/theme', [PreferenceController::class, 'theme'])->name('preferences.theme');
Route::post('/preferences/locale', [PreferenceController::class, 'locale'])->name('preferences.locale');
Route::post('/payment/webhook/mustika', [WebhookController::class, 'mustika'])->name('payment.mustika.webhook');

Route::middleware('guest')->group(function (): void {
    Route::redirect('/login', '/signin')->name('login');
    Route::get('/signin', [AuthenticatedSessionController::class, 'create'])->name('signin');
    Route::post('/signin', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:login')->name('signin.store');
    Route::get('/signup', [RegisteredUserController::class, 'create'])->name('signup');
    Route::post('/signup', [RegisteredUserController::class, 'store'])->middleware('throttle:register')->name('signup.store');
    Route::get('/forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'email'])->middleware('throttle:password.email')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/verify-email', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])->middleware('throttle:6,1')->name('verification.send');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::delete('/profile/google', [ProfileController::class, 'unlinkGoogle'])->name('profile.google.unlink');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'active', 'role:user|admin'])->group(function (): void {
    Route::get('/dashboard', function () {
        return auth()->user()->can('admin.access')
            ? redirect()->route('admin.dashboard.show')
            : redirect()->route('app.dashboard');
    })->name('dashboard');
});

Route::middleware(['auth', 'active', 'verified.config', 'role:user|admin', 'permission:converter.upload|converter.convert'])->prefix('app')->name('app.')->group(function (): void {
    Route::get('/', [UserAppController::class, 'dashboard'])->name('dashboard');
    Route::get('/converter', [UserAppController::class, 'converter'])->name('converter');
    Route::get('/history', [UserAppController::class, 'history'])->name('history');
    Route::get('/pricing', [SubscriptionController::class, 'pricing'])->name('pricing');
    Route::post('/pricing/{plan}/checkout', [SubscriptionController::class, 'checkout'])->name('plans.checkout');
    Route::get('/orders/{order}', [SubscriptionController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{order}/confirm', [SubscriptionController::class, 'confirmManualPayment'])->name('orders.confirm');
    Route::get('/documentation', fn () => view('app.static', ['title' => __('pages.documentation'), 'heading' => __('app.documentation_heading'), 'copy' => __('app.documentation_copy')]))->name('documentation');
    Route::get('/integrations', fn () => view('app.integrations.index', ['title' => __('pages.integrations')]))->name('integrations');
    Route::get('/settings/integrations', fn () => view('app.integrations.settings', ['title' => __('pages.integration_settings')]))->name('integrations.settings');
    Route::get('/integrations/roblox', [RobloxIntegrationController::class, 'show'])->name('integrations.roblox');
    Route::get('/profile', [UserAppController::class, 'profile'])->name('profile');
});

Route::middleware(['auth', 'active', 'verified.config'])->group(function (): void {
    Route::post('/payment/create', [PaymentController::class, 'create'])->name('payment.create');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/pending', [PaymentController::class, 'pending'])->name('payment.pending');
    Route::get('/payment/failed', [PaymentController::class, 'failed'])->name('payment.failed');
    Route::get('/payment/orders/{order}/status', [PaymentController::class, 'status'])->name('payment.orders.status');
    Route::post('/payment/orders/{order}/cancel', [PaymentController::class, 'cancel'])->name('payment.orders.cancel');
    Route::get('/user/payment/history', [InvoiceController::class, 'paymentHistory'])->name('payment.history');
    Route::get('/user/invoices', [InvoiceController::class, 'invoices'])->name('payment.invoices');
});

Route::middleware(['auth', 'active', 'verified.config'])->prefix('integrations/roblox')->name('roblox.')->group(function (): void {
    Route::get('/connect', [RobloxIntegrationController::class, 'redirect'])->middleware('throttle:6,1')->name('connect');
    Route::post('/switch', [RobloxIntegrationController::class, 'switch'])->middleware('throttle:6,1')->name('switch');
    Route::get('/callback', [RobloxIntegrationController::class, 'callback'])->middleware('throttle:12,1')->name('callback');
    Route::get('/disconnect', fn () => redirect()->route('app.integrations.roblox')->with('status', 'Use the Disconnect button to remove the connected Roblox account.'))->name('disconnect.notice');
    Route::delete('/disconnect', [RobloxIntegrationController::class, 'disconnect'])->middleware('throttle:6,1')->name('disconnect');
});

Route::redirect('/converter', '/app/converter')->middleware(['auth', 'active', 'verified.config'])->name('converter.shortcut');

Route::middleware(['auth', 'active', 'role:admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', [AdminDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard.show');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/credits', [AdminUserController::class, 'addCredits'])->name('users.credits.add');
    Route::post('/users/{user}/credits/set', [AdminUserController::class, 'setCredits'])->name('users.credits.set');
    Route::post('/users/{user}/plan', [AdminUserController::class, 'changePlan'])->name('users.plan.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::get('/history', [AdminConversionController::class, 'history'])->name('history');
    Route::get('/analytics', [AdminConversionController::class, 'analytics'])->name('analytics');
    Route::get('/queue', [AdminConversionController::class, 'queue'])->name('queue');
    Route::get('/activity', [AdminConversionController::class, 'activity'])->name('activity');
    Route::get('/content/homepage-reviews', [CommunityAdminController::class, 'reviews'])->name('content.homepage-reviews');
    Route::put('/content/homepage-reviews/{review}', [CommunityAdminController::class, 'updateReview'])->name('content.homepage-reviews.update');
    Route::get('/community/comments', [CommunityAdminController::class, 'comments'])->name('community.comments');
    Route::put('/community/comments/{comment}', [CommunityAdminController::class, 'updateComment'])->name('community.comments.update');
    Route::get('/community/reports', [CommunityAdminController::class, 'reports'])->name('community.reports');
    Route::get('/community/badges', [CommunityAdminController::class, 'badges'])->name('community.badges');
    Route::post('/community/badges', [CommunityAdminController::class, 'storeBadge'])->name('community.badges.store');
    Route::post('/community/badges/assign', [CommunityAdminController::class, 'assignBadge'])->name('community.badges.assign');
    Route::post('/community/users/{user}/ban', [CommunityAdminController::class, 'toggleBan'])->name('community.users.ban');
    Route::get('/settings', fn () => view('pages.converter.settings', [
        'title' => __('pages.settings'),
        'presets' => \App\Models\ConversionPreset::query()->orderBy('speed')->get(),
        'settings' => config('converter'),
    ]))->name('settings');
    Route::get('/app-settings', [AppSettingsController::class, 'edit'])->name('app-settings.edit');
    Route::put('/app-settings', [AppSettingsController::class, 'update'])->name('app-settings.update');
    Route::get('/credit-settings', [CreditSettingsController::class, 'edit'])->name('credit-settings.edit');
    Route::put('/credit-settings', [CreditSettingsController::class, 'update'])->name('credit-settings.update');
    Route::get('/subscription/plans', [SubscriptionAdminController::class, 'plans'])->name('subscription.plans');
    Route::put('/subscription/plans/{plan}', [SubscriptionAdminController::class, 'updatePlan'])->name('subscription.plans.update');
    Route::get('/subscription/orders', [SubscriptionAdminController::class, 'orders'])->name('subscription.orders');
    Route::post('/subscription/orders/{order}/paid', [SubscriptionAdminController::class, 'markOrderPaid'])->name('subscription.orders.paid');
    Route::get('/subscription/transactions', [SubscriptionAdminController::class, 'transactions'])->name('subscription.transactions');
    Route::get('/subscription/contact-settings', [SubscriptionAdminController::class, 'contactSettings'])->name('subscription.contact-settings');
    Route::put('/subscription/contact-settings', [SubscriptionAdminController::class, 'updateContactSettings'])->name('subscription.contact-settings.update');
    Route::get('/profile', fn () => view('pages.profile', ['title' => __('pages.admin_profile')]))->name('profile');
});
