<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Subscription\SubscriptionController;
use App\Http\Controllers\API\IVRSubscriptionController;
use App\Http\Controllers\API\UserController;

use App\Http\Controllers\API\AutoDebitSubscriptionController;
use App\Http\Controllers\API\LandingPageSubscription;
use App\Http\Controllers\API\ProductApiController;
use App\Http\Controllers\API\NetEntrollmentApiController;
use App\Http\Controllers\API\USSDSubscriptionController;
use App\Http\Controllers\SuperAgentL\CustomApiController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    Route::prefix('ivr')->group(function () {
        Route::post("/subscription", [IVRSubscriptionController::class, 'ivr_subscription'])
            ->name('subscription'); // Example route name

        Route::get("/getPlans", [IVRSubscriptionController::class, 'getPlans'])
            ->name('get_plans'); // Example route name

        Route::post("/getProducts", [IVRSubscriptionController::class, 'getProducts'])
            ->name('get_products'); // Example route name

        // Other routes related to IVR can be added here
    });
});


Route::prefix('v2')->group(function () {
    Route::prefix('ussd')->group(function () {
        Route::post("Ussdsub", [USSDSubscriptionController::class, 'ivr_subscription'])
            ->name('Ussdsub'); // Example route name

        Route::get("Ussdplan", [USSDSubscriptionController::class, 'getPlans'])
            ->name('Ussdplan'); // Example route name

        Route::post("Ussdproducts", [USSDSubscriptionController::class, 'getProducts'])
            ->name('Ussdproducts'); // Example route name

    Route::POST("Ussdunsub",[USSDSubscriptionController::class,'unsubscribeactiveplan'])
    ->name('Ussdunsub');

        // Other routes related to ussd can be added here
    });
});


Route::prefix('v1')->group(function () {
    Route::prefix('auto-debit')->group(function () {
        Route::post("/auto-subscription", [AutoDebitSubscriptionController::class, 'AutoDebitSubscription'])
            ->name('AutoDebitSubscription'); // Example route name
        // Other routes related to IVR can be added here
    });
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post("v1/login",[UserController::class,'index']);

Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::get("v1/takafulplus",[UserController::class,'getProducts']);
    Route::POST("v1/pushSubscription",[UserController::class,'Subscription']);
	Route::POST("v1/listactiveSubcriptions",[UserController::class,'activesubscriptions']);
//Route::POST("v1/UnsubscribePackage",[UserController::class,'unsubscribeactiveplan']);

Route::POST("v1/closeRefundCase",[UserController::class,'Update_refund_status']);
});



Route::prefix('v1')->group(function () {
    Route::prefix('landing-page')->group(function () {

        Route::post("/subscription-lp", [LandingPageSubscription::class, 'landing_page_subscription'])
    ->name('subscription_lp'); // Example route name

	Route::get("/getPlans", [LandingPageSubscription::class, 'getPlans'])
    ->name('get_plans_lp'); // Example route name

	Route::post("/getProducts", [LandingPageSubscription::class, 'getProducts'])
    ->name('get_products_lp');
        // Other routes related to IVR can be added here
    });
});

   // Status Update Auto Debit Button Super Agent L Pannel
   Route::post('/InterestedCustomerStatusUpdate', [CustomApiController::class, 'status_update'])->name('InterestedCustomerStatusUpdate');

   //  Products Fatch Through Plan Id
   Route::post('/GetProductsData', [ProductApiController::class, 'fatch_products'])->name('GetProductsData');

   //  Api NetEnrollment Report
   Route::post('/NetEnrollment', [NetEntrollmentApiController::class, 'NetEnrollment'])->name('NetEnrollment');

