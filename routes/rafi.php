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
use App\Http\Controllers\CouponController;
use App\Http\Controllers\ReportMatrixController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

 Route::get('/test', [UpdateController::class, 'test']);

Route::post('password/email', [AuthController::class, 'sendResetOTP']);
Route::post('password/verify-otp', [AuthController::class, 'verifyResetOTP'])->name('password.verify-otp');
Route::post('password/forget', [AuthController::class, 'sendPasswordResetLink'])->name('password.forget');
Route::post('password/reset', [AuthController::class, 'resetPassword'])->name('password.reset');


Route::get('me', [AuthController::class, 'me'])->middleware('auth:api');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');


Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::put('/update-password', [AuthController::class, 'updatePassword']);
});

Route::middleware(['auth:api', 'role:user'])->group(function () {
    Route::get('/user-only', function () {
        return response()->json(['message' => 'Hello User!']);
    });
});

Route::middleware(['auth:api', 'role:admin,user'])->group(function () {
    Route::get('/both-roles', function () {
        return response()->json(['message' => 'Hello Admin or User!']);
    });
});



Route::get('/services', [ApiController::class, 'services']);
Route::get('/balance', [ApiController::class, 'balance']);
Route::post('/order', [ApiController::class, 'order']);
Route::get('/status/{id}', [ApiController::class, 'status']);
Route::post('/multi-status', [ApiController::class, 'multiStatus']);
Route::post('/refill/{id}', [ApiController::class, 'refill']);
Route::post('/multi-refill', [ApiController::class, 'multiRefill']);
Route::get('/refill-status/{id}', [ApiController::class, 'refillStatus']);
Route::post('/multi-refill-status', [ApiController::class, 'multiRefillStatus']);
Route::post('/cancel', [ApiController::class, 'cancel']);


//........Provider Routes..........................................................................

// List all providers
Route::get('/providers', [ProviderController::class, 'index'])->name('providers.index');
// Create a new provider
Route::post('/providers', [ProviderController::class, 'store'])->name('providers.store');
// Show a single provider by ID
Route::get('/providers/{id}', [ProviderController::class, 'show'])->name('providers.show');
// Update a provider by ID
Route::put('/providers/{id}', [ProviderController::class, 'update'])->name('providers.update');
// Delete a provider by ID
Route::delete('/providers/{id}', [ProviderController::class, 'destroy'])->name('providers.destroy');


//..........New Subscription Routes..................................................


Route::get('/new-subscriptions', [NewSubscriptionController::class, 'index'])->name('new_subscriptions.index');
Route::post('/new-subscriptions', [NewSubscriptionController::class, 'store'])->name('new_subscriptions.store');
Route::get('/new-subscriptions/{id}', [NewSubscriptionController::class, 'show'])->name('new_subscriptions.show');
Route::put('/new-subscriptions/{id}', [NewSubscriptionController::class, 'update'])->name('new_subscriptions.update');
Route::delete('/new-subscriptions/{id}', [NewSubscriptionController::class, 'destroy'])->name('new_subscriptions.destroy');

//create new route for bulkfollows
Route::get('/bulkfollows', [UpdateController::class, 'bulkfollows']);


//Service management
Route::prefix('services')->group(function () {
    // Route::get('/', [ServiceController::class, 'index']);
    Route::post('/', [ServiceController::class, 'store']);
    Route::get('/{id}', [ServiceController::class, 'show']);
    Route::put('/{id}', [ServiceController::class, 'update']);
    Route::delete('/{id}', [ServiceController::class, 'destroy']);
});

//Update management
Route::prefix('updates')->group(function () {
    Route::get('/', [UpdateController::class, 'index']);
    Route::post('/', [UpdateController::class, 'store']);
    Route::get('/{id}', [UpdateController::class, 'show']);
    Route::put('/{id}', [UpdateController::class, 'update']);
    Route::delete('/{id}', [UpdateController::class, 'destroy']);
});

//Country management
Route::prefix('countries')->group(function () {
    Route::get('/', [CountryController::class, 'index']);
    Route::post('/', [CountryController::class, 'store']);
    Route::get('/{id}', [CountryController::class, 'show']);
    Route::post('/{id}', [CountryController::class, 'update']);
    Route::delete('/{id}', [CountryController::class, 'destroy']);
});

//Country management
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::get('/{id}', [CategoryController::class, 'show']);
    Route::put('/{id}', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
});




//Orders management
Route::prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'store']);
    Route::get('/{id}', [OrderController::class, 'show']);
    Route::put('/{id}', [OrderController::class, 'update']);
    Route::delete('/{id}', [OrderController::class, 'destroy']);
});

//Payments management
Route::prefix('payments')->group(function () {
    Route::get('/', [PaymentController::class, 'index']);
    Route::post('/', [PaymentController::class, 'store']);
    Route::get('/{id}', [PaymentController::class, 'show']);
    Route::put('/{id}', [PaymentController::class, 'update']);
    Route::delete('/{id}', [PaymentController::class, 'destroy']);
});


//Referrals management
Route::prefix('referrals')->group(function () {
    Route::get('/', [ReferralController::class, 'index']);
    Route::post('/', [ReferralController::class, 'store']);
    Route::get('/{id}', [ReferralController::class, 'show']);
    Route::put('/{id}', [ReferralController::class, 'update']);
    Route::delete('/{id}', [ReferralController::class, 'destroy']);
});


//Referral Payouts management
Route::prefix('referral-payouts')->group(function () {
    Route::get('/', [ReferralPayoutController::class, 'index']);
    Route::post('/', [ReferralPayoutController::class, 'store']);
    Route::get('/{id}', [ReferralPayoutController::class, 'show']);
    Route::put('/{id}', [ReferralPayoutController::class, 'update']);
    Route::delete('/{id}', [ReferralPayoutController::class, 'destroy']);
});
//..................client....................................................

// routes/api.php

Route::get   ('/clients',       [ClientController::class, 'index']);
Route::get   ('/clients/{id}',  [ClientController::class, 'show']);
Route::post  ('/clients',       [ClientController::class, 'store']);
Route::put   ('/clients/{id}',  [ClientController::class, 'update']);
Route::delete('/clients/{id}',  [ClientController::class, 'destroy']);


//....................................ticket............................................................




Route::prefix('tickets')->name('tickets.')->group(function () {
    Route::get   ('/',         [TicketController::class, 'index'])->name('index');
    Route::post  ('/',         [TicketController::class, 'store'])->name('store');
    Route::get   ('/{id}',     [TicketController::class, 'show'])->whereNumber('id')->name('show');
    Route::put   ('/{id}',     [TicketController::class, 'update'])->whereNumber('id')->name('update');
    Route::delete('/{id}',     [TicketController::class, 'destroy'])->whereNumber('id')->name('destroy');

    Route::get('status/{status}', [TicketController::class, 'byStatus'])
        ->where('status', '[A-Za-z0-9_\-]+')
        ->name('byStatus');
        
       // Convenience routes for common statuses
    Route::get('pending',  [TicketController::class, 'byStatus'])->defaults('status', 'pending')->name('pending');
    Route::get('answered', [TicketController::class, 'byStatus'])->defaults('status', 'answered')->name('answered');
    Route::get('closed',   [TicketController::class, 'byStatus'])->defaults('status', 'closed')->name('closed');    
});

//................................coupon............................................................



Route::prefix('coupons')->name('coupons.')->group(function () {
    Route::get   ('/',        [CouponController::class, 'index'])->name('index');
    Route::post  ('/',        [CouponController::class, 'store'])->name('store');
    Route::get   ('/{id}',    [CouponController::class, 'show'])->whereNumber('id')->name('show');
    Route::put   ('/{id}',    [CouponController::class, 'update'])->whereNumber('id')->name('update');
    Route::delete('/{id}',    [CouponController::class, 'destroy'])->whereNumber('id')->name('destroy');
});



//.........................Report......................................................

Route::get('/reports/orders-matrix', [ReportMatrixController::class, 'ordersMatrix'])
    ->name('reports.orders.matrix');
 
Route::get('/reports/orders-counts', [ReportMatrixController::class, 'ordersCounts'])
    ->name('reports.orders.counts');

Route::get('/reports/payments-matrix', [ReportMatrixController::class, 'paymentsMatrix'])
    ->name('reports.payments.matrix');
