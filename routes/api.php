<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\NewSubscriptionController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\ReferralPayoutController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\ReportMatrixController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All routes here are loaded by RouteServiceProvider under the "api" group.
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/* ---- Auth ---- */
Route::post('register', [AuthController::class, 'register']);
Route::post('login',    [AuthController::class, 'login']);
Route::post('password/email',       [AuthController::class, 'sendResetOTP']);
Route::post('password/verify-otp',  [AuthController::class, 'verifyResetOTP'])->name('password.verify-otp');
Route::post('password/forget',      [AuthController::class, 'sendPasswordResetLink'])->name('password.forget');
Route::post('password/reset',       [AuthController::class, 'resetPassword'])->name('password.reset');

Route::get('me',     [AuthController::class, 'me'])->middleware('auth:api');
Route::post('logout',[AuthController::class, 'logout'])->middleware('auth:api');

Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::put('/update-password', [AuthController::class, 'updatePassword']);
});

Route::middleware(['auth:api', 'role:user'])->group(function () {
    Route::get('/user-only', fn () => response()->json(['message' => 'Hello User!']));
});

Route::middleware(['auth:api', 'role:admin,user'])->group(function () {
    Route::get('/both-roles', fn () => response()->json(['message' => 'Hello Admin or User!']));
});

/* ---- API endpoints (legacy) ---- */
Route::get('/services',        [ApiController::class, 'services']);
Route::get('/balance',         [ApiController::class, 'balance']);
Route::post('/order',          [ApiController::class, 'order']);
Route::get('/status/{id}',     [ApiController::class, 'status']);
Route::post('/multi-status',   [ApiController::class, 'multiStatus']);
Route::post('/refill/{id}',    [ApiController::class, 'refill']);
Route::post('/multi-refill',   [ApiController::class, 'multiRefill']);
Route::get('/refill-status/{id}', [ApiController::class, 'refillStatus']);
Route::post('/multi-refill-status', [ApiController::class, 'multiRefillStatus']);
Route::post('/cancel',         [ApiController::class, 'cancel']);
Route::get('/test',            [UpdateController::class, 'test']);

/* ---- Providers ---- */
Route::get   ('/providers',      [ProviderController::class, 'index'])->name('providers.index');
Route::post  ('/providers',      [ProviderController::class, 'store'])->name('providers.store');
Route::get   ('/providers/{id}', [ProviderController::class, 'show'])->name('providers.show');
Route::put   ('/providers/{id}', [ProviderController::class, 'update'])->name('providers.update');
Route::delete('/providers/{id}', [ProviderController::class, 'destroy'])->name('providers.destroy');

/* ---- New Subscriptions ---- */
Route::get   ('/new-subscriptions',      [NewSubscriptionController::class, 'index'])->name('new_subscriptions.index');
Route::post  ('/new-subscriptions',      [NewSubscriptionController::class, 'store'])->name('new_subscriptions.store');
Route::get   ('/new-subscriptions/{id}', [NewSubscriptionController::class, 'show'])->name('new_subscriptions.show');
Route::put   ('/new-subscriptions/{id}', [NewSubscriptionController::class, 'update'])->name('new_subscriptions.update');
Route::delete('/new-subscriptions/{id}', [NewSubscriptionController::class, 'destroy'])->name('new_subscriptions.destroy');

/* ---- Bulk follows ---- */
Route::get('/bulkfollows', [UpdateController::class, 'bulkfollows']);

/* ---- Services CRUD ---- */
Route::prefix('services')->group(function () {
    Route::get('/', [ServiceController::class, 'index']);
    Route::post('/',    [ServiceController::class, 'store']);
    Route::get ('/{id}',[ServiceController::class, 'show']);
    Route::put ('/{id}',[ServiceController::class, 'update']);
    Route::delete('/{id}',[ServiceController::class, 'destroy']);

});

/* ---- Updates CRUD ---- */
Route::prefix('updates')->group(function () {
    Route::get   ('/',     [UpdateController::class, 'index']);
    Route::post  ('/',     [UpdateController::class, 'store']);
    Route::get   ('/{id}', [UpdateController::class, 'show']);
    Route::put   ('/{id}', [UpdateController::class, 'update']);
    Route::delete('/{id}', [UpdateController::class, 'destroy']);
});

/* ---- Countries CRUD ---- */
Route::prefix('countries')->group(function () {
    Route::get   ('/',     [CountryController::class, 'index']);
    Route::post  ('/',     [CountryController::class, 'store']);
    Route::get   ('/{id}', [CountryController::class, 'show']);
    Route::post  ('/{id}', [CountryController::class, 'update']); // (kept as POST if intentional)
    Route::delete('/{id}', [CountryController::class, 'destroy']);
});

//Country management
Route::prefix('categories')->group(function () {
    Route::get   ('/',     [CategoryController::class, 'index']);
    Route::post  ('/',     [CategoryController::class, 'store']);
    Route::get   ('/{id}', [CategoryController::class, 'show']);
    Route::put   ('/{id}', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
});

/* ---- Orders CRUD ---- */
Route::prefix('orders')->group(function () {
    Route::get   ('/',     [OrderController::class, 'index']);
    Route::post  ('/',     [OrderController::class, 'store']);
    Route::get   ('/{id}', [OrderController::class, 'show']);
    Route::put   ('/{id}', [OrderController::class, 'update']);
    Route::delete('/{id}', [OrderController::class, 'destroy']);
});

/* ---- Payments CRUD ---- */
Route::prefix('payments')->group(function () {
    Route::get   ('/',     [PaymentController::class, 'index']);
    Route::post  ('/',     [PaymentController::class, 'store']);
    Route::get   ('/{id}', [PaymentController::class, 'show']);
    Route::put   ('/{id}', [PaymentController::class, 'update']);
    Route::delete('/{id}', [PaymentController::class, 'destroy']);
});

/* ---- Referrals CRUD ---- */
Route::prefix('referrals')->group(function () {
    Route::get   ('/',     [ReferralController::class, 'index']);
    Route::post  ('/',     [ReferralController::class, 'store']);
    Route::get   ('/{id}', [ReferralController::class, 'show']);
    Route::put   ('/{id}', [ReferralController::class, 'update']);
    Route::delete('/{id}', [ReferralController::class, 'destroy']);
});

/* ---- Referral Payouts CRUD ---- */
Route::prefix('referral-payouts')->group(function () {
    Route::get   ('/',     [ReferralPayoutController::class, 'index']);
    Route::post  ('/',     [ReferralPayoutController::class, 'store']);
    Route::get   ('/{id}', [ReferralPayoutController::class, 'show']);
    Route::put   ('/{id}', [ReferralPayoutController::class, 'update']);
    Route::delete('/{id}', [ReferralPayoutController::class, 'destroy']);
});

/* ---- Clients CRUD ---- */
Route::get   ('/clients',      [ClientController::class, 'index']);
Route::get   ('/clients/{id}', [ClientController::class, 'show']);
Route::post  ('/clients',      [ClientController::class, 'store']);
Route::put   ('/clients/{id}', [ClientController::class, 'update']);
Route::delete('/clients/{id}', [ClientController::class, 'destroy']);

/* ---- Tickets ---- */
Route::prefix('tickets')->name('tickets.')->group(function () {
    Route::get   ('/',       [TicketController::class, 'index'])->name('index');
    Route::post  ('/',       [TicketController::class, 'store'])->name('store');
    Route::get   ('/{id}',   [TicketController::class, 'show'])->whereNumber('id')->name('show');
    Route::put   ('/{id}',   [TicketController::class, 'update'])->whereNumber('id')->name('update');
    Route::delete('/{id}',   [TicketController::class, 'destroy'])->whereNumber('id')->name('destroy');

    Route::get('status/{status}', [TicketController::class, 'byStatus'])
        ->where('status', '[A-Za-z0-9_\\-]+')
        ->name('byStatus');

    Route::get('pending',  [TicketController::class, 'byStatus'])->defaults('status', 'pending')->name('pending');
    Route::get('answered', [TicketController::class, 'byStatus'])->defaults('status', 'answered')->name('answered');
    Route::get('closed',   [TicketController::class, 'byStatus'])->defaults('status', 'closed')->name('closed');
});

/* ---- Promotions CRUD ---- */
Route::prefix('promotions')->group(function () {
    Route::get   ('/',     [PromotionController::class, 'index']);
    Route::post  ('/',     [PromotionController::class, 'store']);
    Route::get   ('/{id}', [PromotionController::class, 'show']);
    Route::put   ('/{id}', [PromotionController::class, 'update']);
    Route::delete('/{id}', [PromotionController::class, 'destroy']);
});
//................................coupon............................................................

/* ---- Coupons CRUD ---- */
Route::prefix('coupons')->name('coupons.')->group(function () {
    Route::get   ('/',     [CouponController::class, 'index'])->name('index');
    Route::post  ('/',     [CouponController::class, 'store'])->name('store');
    Route::get   ('/{id}', [CouponController::class, 'show'])->whereNumber('id')->name('show');   // fixed path
    Route::put   ('/{id}', [CouponController::class, 'update'])->whereNumber('id')->name('update');
    Route::delete('/{id}', [CouponController::class, 'destroy'])->whereNumber('id')->name('destroy');
});



//.........................Report......................................................

Route::get('/reports/orders-matrix', [ReportMatrixController::class, 'ordersMatrix'])
    ->name('reports.orders.matrix');
 
Route::get('/reports/orders-counts', [ReportMatrixController::class, 'ordersCounts'])
    ->name('reports.orders.counts');

Route::get('/reports/payments-matrix', [ReportMatrixController::class, 'paymentsMatrix'])
    ->name('reports.payments.matrix');

