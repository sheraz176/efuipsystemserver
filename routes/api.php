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
use App\Http\Controllers\API\GenericApiController;
use App\Http\Controllers\API\MobileApiController;
use App\Http\Controllers\API\ClaimController;
use App\Http\Controllers\API\FamilyHealthController;
use App\Http\Controllers\API\PolicyController;
use App\Http\Controllers\API\IVRTsmController;
use App\Http\Controllers\API\AiBotsController;
use App\Http\Controllers\API\NajoomiApiController;


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




Route::post("family/plan/new", [PolicyController::class, 'family_plan_new'])
            ->name('family.plan.new');

Route::post("term/life/new", [PolicyController::class, 'term_life_new'])
            ->name('term.life.new');



Route::post("/2025/medical", [PolicyController::class, 'medical_2025'])
            ->name('2025.mediacl');
Route::post("/2025/term", [PolicyController::class, 'termlife_2025'])
            ->name('2025.term');
Route::post('/upload-medical', [PolicyController::class, 'uploadMedicalCSV']);



 Route::post("family/policy/sub/api/lftd", [PolicyController::class, 'family_policy_sub_api_lftd'])
            ->name('family.policy.sub.api.lftd');


 Route::post("/family/health/behbud", [PolicyController::class, 'family_policy_sub_api_behbud'])
            ->name('family.health.behbud');


 Route::post("/family/health", [PolicyController::class, 'family_policy_sub_api'])
            ->name('family.health');
  Route::post("/JazzIVR/data", [PolicyController::class, 'JazzIVR'])
            ->name('JazzIVR.data');



  Route::post("IVR/TSM/login",[IVRTsmController::class,'login']);
 Route::group(['middleware' => 'auth:sanctum'], function(){
Route::prefix('Tsm')->group(function () {
    Route::prefix('IVR')->group(function () {

	Route::get("/getPlans", [IVRTsmController::class, 'getPlans'])
    ->name('get_plans_lp');

    	Route::post("/getProducts", [IVRTsmController::class, 'getProducts'])
    ->name('get_products_lp');

     Route::post("/subscription-tsm-ivr", [IVRTsmController::class, 'tsm_ivr_subscription'])
    ->name('subscription_tsm_ivr');

});
 });
});




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
     
     Route::POST("v1/UnsubscribePackage",[UserController::class,'unsubscribeactiveplan']);
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


   Route::post('/sub', [NetEntrollmentApiController::class, 'sub'])->name('sub');

   Route::post('/NetEnrollment', [NetEntrollmentApiController::class, 'NetEnrollment'])->name('NetEnrollment');
   Route::post('/TotalActiveSubscription', [NetEntrollmentApiController::class, 'ActiveSubscription'])->name('TotalActiveSubscription');
   Route::post('/RefundedTransaction', [NetEntrollmentApiController::class, 'RefundedTransaction'])->name('RefundedTransaction');
     Route::post('/recusiveCharging', [NetEntrollmentApiController::class, 'recusiveCharging'])->name('recusiveCharging');

     Route::post('/DailyNetEnrollment', [NetEntrollmentApiController::class, 'DailyNetEnrollment'])->name('DailyNetEnrollment');
     Route::post('/todayCancelled', [NetEntrollmentApiController::class, 'todayCancelled'])->name('todayCancelled');

   Route::get('/dailyCount', [NetEntrollmentApiController::class, 'dailyCount'])->name('dailyCount');






    //Start routes related to Generic Api Controller API

Route::post("v24/login",[GenericApiController::class,'login']);
Route::group(['middleware' => 'auth:sanctum'], function(){
Route::prefix('v24')->group(function () {
    Route::prefix('GenericApi')->group(function () {

        Route::post("generic/get/plan", [GenericApiController::class, 'getPlans'])
            ->name('genericgetplan');
        Route::post("generic/get/products", [GenericApiController::class, 'getProducts'])
            ->name('genericgetproducts');
        Route::post("generic/Subscription", [GenericApiController::class, 'jazz_app_subscription'])
            ->name('genericSubscription');

            Route::POST("generic/UnSubscription",[GenericApiController::class,'unsubscribePackage'])
            ->name('generic/UnSubscription');
        Route::post("check/active/plan", [GenericApiController::class, 'activesubscriptions'])
            ->name('checkactiveplan');

    });
});
});

//End routes related to Generic Api Controller API


// Other routes related to Mobile Api can be added here

 Route::post("v3/login",[MobileApiController::class,'login']);
Route::group(['middleware' => 'auth:sanctum'], function(){
Route::prefix('v3')->group(function () {
    Route::prefix('mobileApi')->group(function () {
        Route::post("sub", [MobileApiController::class, 'jazz_app_subscription'])
            ->name('sub'); // Example route name

        Route::get("plan", [MobileApiController::class, 'getPlans'])
            ->name('plan'); // Example route name

        Route::post("products", [MobileApiController::class, 'getProducts'])
            ->name('products'); // Example route name

     Route::POST("listactiveSubcriptions",[MobileApiController::class,'activesubscriptions']);

    Route::POST("unsub",[MobileApiController::class,'unsubscribePackage'])
    ->name('unsub');

    Route::POST("updaterefund",[MobileApiController::class,'updaterefund'])
    ->name('updaterefund');


    });
});
});

//Start routes related to Claim Api Controller API
Route::post("v25/login",[ClaimController::class,'login']);
Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::prefix('v25')->group(function () {
        Route::prefix('ClaimApi')->group(function () {

            Route::post("submit/claim", [ClaimController::class, 'SubmitClaim'])
                ->name('submit.claim');
            Route::post("claim/details", [ClaimController::class, 'ClaimDetails'])
                ->name('claim.details');
            Route::post("claim/history", [ClaimController::class, 'ClaimHistory'])
                ->name('claim.history');
                
                    Route::post("claim/amounts", [ClaimController::class, 'Claimamounts'])
                ->name('claim.amounts');
            Route::post("claim/status", [ClaimController::class, 'Claimstatus'])
                ->name('claim.status');

        });
      });
});
//End routes related to CallDoctor Api Controller API

Route::prefix('vv23')->group(function () {
    Route::prefix('Aibots')->group(function () {

           Route::get("/getPlans", [AiBotsController::class, 'getPlans'])
            ->name('get_plans');
           Route::post("/getProducts", [AiBotsController::class, 'getProducts'])
            ->name('get_products');
            Route::post("/subscription", [AiBotsController::class, 'AI_bots_subscription'])
             ->name('subscription');

    });
});


Route::post("familyhealth/login",[FamilyHealthController::class,'login']);
Route::prefix('v22')->group(function () {
    Route::prefix('familyhealth')->group(function () {

	Route::get("/getPlans", [FamilyHealthController::class, 'getPlans'])
    ->name('get_plans_lp');

    	Route::post("/getProducts", [FamilyHealthController::class, 'getProducts'])
    ->name('get_products_lp');

     Route::post("/subscription-family-ivr", [FamilyHealthController::class, 'family_ivr_subscription'])
    ->name('subscription_family_ivr');

});

 });


Route::post("najoomi/login",[FamilyHealthController::class,'login']);
Route::group(['middleware' => 'auth:sanctum'], function(){
Route::prefix('c24')->group(function () {
    Route::prefix('najoomi')->group(function () {

	Route::get("/getPlans", [NajoomiApiController::class, 'getPlans'])
    ->name('get_plans_lp');

    	Route::post("/getProducts", [NajoomiApiController::class, 'getProducts'])
    ->name('get_products_lp');

     Route::post("/najoomi/subscription", [NajoomiApiController::class, 'najoomi_subscription'])
    ->name('najoomi_subscription');

});
});
 });




//End Other routes related to Mobile Api can be added here





