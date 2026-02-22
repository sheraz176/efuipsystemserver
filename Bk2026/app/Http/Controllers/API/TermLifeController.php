<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plans\PlanModel;
use App\Models\Plans\ProductModel;
use App\Models\User;
use App\Models\Refund\RefundedCustomer;
use Illuminate\Support\Facades\Hash;
use App\Models\Subscription\CustomerSubscription;
use App\Models\Unsubscription\CustomerUnSubscription;
use App\Http\Controllers\Subscription\FailedSubscriptionsController;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\MarchantModel;
use Illuminate\Support\Facades\Http;
use App\Models\SMSMsisdn;
use App\Models\Claim;
use Illuminate\Support\Facades\Storage;


class TermLifeController extends Controller
{
    public function login(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'messageCode' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Attempt to retrieve the user
        $user = User::where('name', $request->name)->first();

        // Check if user exists and password matches
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => ['These credentials do not match our records.'],
            ], 404);
        }

        // Create token and set expiration time
        $token = $user->createToken('my-app-token')->plainTextToken;
        $tokenExpiration = Carbon::now('Asia/Karachi')->addMinutes(30)->format('Y-m-d H:i:s');

        // Prepare response
        $response = [
            'token' => $token,
            'token_expiration' => $tokenExpiration,
        ];

        return response()->json($response, 201);
    }

    public function getPlans(Request $request)
    {
        $activePlans = PlanModel::select('plan_id', 'plan_name', 'status')
            ->where('status', 0)
            ->where('plan_id', 6)   // ðŸ‘ˆ sirf plan_id 6 ka record
            ->get();

        return response()->json([
            'status' => 'success',
            'statusCode' => '3000',
            'data' => $activePlans,
        ]);
    }


    public function getProducts(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'messageCode' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        $products = ProductModel::where('plan_id', $request->plan_id)
            ->where('api_status', 1)
            ->get();

        // Filter & JSON Decode
        $filteredProducts = $products->map(function ($product) {

            // Convert to array
            $productArray = collect($product)->toArray();

            // Decode mode JSON string
            if (!empty($productArray['mode'])) {
                $productArray['mode'] = json_decode($productArray['mode'], true);
            }

            // Filter out null, 0, and empty strings (except mode)
            return collect($productArray)->filter(function ($value, $key) {
                if ($key === 'mode') return true; // don't filter decoded mode
                return !is_null($value) && $value !== 0 && $value !== '';
            });
        });

        return response()->json([
            'status' => 'success',
            'statusCode' => 3100,
            'data' => $filteredProducts,
        ], 200);
    }


    public function jazz_app_subscription(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|integer|in:6', // Only plan_id = 6
            'product_id' => 'required|integer',
            'customer_msisdn' => 'required|regex:/^\d{11,12}$/',
            'transaction_amount' => 'required|numeric',
            'cpsOriginatorConversationId' => 'required|string',
            'cpsTransactionId' => 'required|string',
            'cpsResponse' => 'required|string',
            'transactionStatus' => 'required',
            'subscriber_cnic' => 'required|numeric',
            'mode' => 'required|array',
            'mode.Duration' => 'required|string',
            'mode.Price' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'statusCode' => 400,
                'message' => $validator->errors()
            ], 400);
        }

        // Normalize MSISDN
        $subscriber_msisdn = $request->customer_msisdn;
        if (preg_match('/^92\d{10}$/', $subscriber_msisdn)) {
            $subscriber_msisdn = '0' . substr($subscriber_msisdn, 2);
        }

        // Validate product under plan 6
        $product = ProductModel::where('plan_id', 6)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$product) {
            return response()->json([
                'error' => true,
                'statusCode' => 3101,
                'message' => 'Invalid product for plan 6'
            ], 404);
        }

        // Decode product mode JSON
        $productModes = json_decode($product->mode, true);

        if (!is_array($productModes)) {
            return response()->json([
                'error' => true,
                'statusCode' => 5001,
                'message' => 'Product mode format invalid'
            ]);
        }

        // Requested mode
        $reqMode = $request->mode;
        $reqDuration = strtolower($reqMode['Duration']);
        $reqPrice = $reqMode['Price'];

        // MATCH mode.Price with product->mode JSON
        $matchFound = false;
        foreach ($productModes as $pm) {
            if (
                strtolower($pm['Duration']) == $reqDuration &&
                (int)$pm['Price'] == (int)$reqPrice
            ) {
                $matchFound = true;
                break;
            }
        }

        if ($matchFound === false) {
            return response()->json([
                'error' => true,
                'statusCode' => 4500,
                'message' => 'Price mismatch in product'
            ]);
        }

        // Convert duration
        if ($reqDuration == "daily") {
            $product_duration = 1;
        } elseif ($reqDuration == "monthly") {
            $product_duration = 30;
        } elseif ($reqDuration == "annual") {
            $product_duration = 365;
        } else {
            return response()->json([
                'error' => true,
                'statusCode' => 4002,
                'message' => 'Invalid duration selected'
            ]);
        }

        // Final amount = Price from mode
        $amount = $reqPrice;

        // Grace period & recursive charging date
        $grace_period_time = date('Y-m-d H:i:s', strtotime("+14 days"));
        $recursive_charging_date = date('Y-m-d H:i:s', strtotime("+$product_duration days"));

        // Already subscribed check
        $subscription = CustomerSubscription::where('subscriber_msisdn', $subscriber_msisdn)
            ->where('plan_id', 6)
            ->where('policy_status', 1)
            ->first();

        if ($subscription) {
            $existingProduct = ProductModel::find($subscription->productId);

            return response()->json([
                'error' => false,
                'statusCode' => 4000,
                'message' => 'Already subscribed to the plan.',
                'Policy Number' => $subscription->subscription_id,
                'planCode' => $existingProduct->product_code,
                'transactionAmount' => $subscription->transaction_amount,
                'Subscriber Number' => $subscription->subscriber_msisdn,
                'Subcription Time' => $subscription->subscription_time
            ]);
        }

        // Create subscription
        $customer_subscription = CustomerSubscription::create([
            'customer_id' => '0011' . $subscriber_msisdn,
            'payer_cnic' => 1,
            'payer_msisdn' => $subscriber_msisdn,
            'subscriber_cnic' => $request->subscriber_cnic,
            'subscriber_msisdn' => $subscriber_msisdn,
            'beneficiary_name' => 'Need to Filled in Future',
            'beneficiary_msisdn' => 0,
            'transaction_amount' => $amount,
            'transaction_status' => $request->transactionStatus,
            'referenceId' => $request->cpsOriginatorConversationId,
            'cps_transaction_id' => $request->cpsTransactionId,
            'cps_response_text' => $request->cpsResponse,
            'product_duration' => $product_duration,
            'plan_id' => 6,
            'productId' => $request->product_id,
            'policy_status' => 1,
            'pulse' => 'Recursive Charging',
            'api_source' => 'Jazz Application',
            'recursive_charging_date' => $recursive_charging_date,
            'subscription_time' => now(),
            'grace_period_time' => $grace_period_time,
            'sales_agent' => 1,
            'company_id' => 15
        ]);

        // SMS log
        SMSMsisdn::create([
            'msisdn' => $subscriber_msisdn,
            'plan_id' => 6,
            'product_id' => $request->product_id,
            'status' => 0
        ]);

        // Final response
        $sub = CustomerSubscription::find($customer_subscription->subscription_id);
        $prod = ProductModel::find($sub->productId);

        return response()->json([
            'error' => false,
            'statusCode' => 2000,
            'message' => 'Customer Subscribed Successfully',
            'policy_subscription_id' => $sub->subscription_id,
            'Information' => [
                'customer_id' => $sub->customer_id,
                'subscriber_msisdn' => $sub->subscriber_msisdn,
                'transaction_amount' => $sub->transaction_amount,
                'transactionStatus' => $sub->transaction_status,
                'planId' => $sub->plan_id,
                'productId' => $sub->productId,
                'planCode' => $prod->product_code,
                'product_duration' => $sub->product_duration,
                'Recursive_charging_date' => $sub->recursive_charging_date,
                'subcription_time' => $sub->subscription_time,
                'grace_period_time' => $sub->grace_period_time
            ]
        ]);
    }



    public function unsubscribePackage(Request $request)
    {
        // Step 1: Validate the request to ensure `subscriber_msisdn` is provided
        $validator = Validator::make($request->all(), [
            'subscriber_msisdn' => 'required|regex:/^\d{11,12}$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        $subscriber_msisdn = $request->input("subscriber_msisdn");
        if (preg_match('/^92\d{10}$/', $subscriber_msisdn)) {
            // Convert '92300XXXXXXX' to '0300XXXXXXX'
            $subscriber_msisdn = '0' . substr($subscriber_msisdn, 2);
        }

        // Step 2: Check for active subscriptions based on `subscriber_msisdn`
        $subscriptions = CustomerSubscription::where('subscriber_msisdn', $subscriber_msisdn)
            ->where('policy_status', 1) // Only active subscriptions
            ->get();

        // If no active subscriptions are found, return a message
        if ($subscriptions->isEmpty()) {
            return response()->json([
                'statusCode' => 4004,
                'message' => 'No active subscriptions found for this subscriber.',
            ], 404);
        }
        // Step 3: If active subscriptions are found, return them to the user
        if (!$request->has('subscription_id')) {
            return response()->json([
                'statusCode' => 4000,
                'message' => 'Active subscriptions found.',
                'subscriptions' => $subscriptions->map(function ($subscription) {
                    return [
                        'subscription_id' => $subscription->subscription_id,
                        'plan_id' => $subscription->plan_id,
                        'transaction_amount' => $subscription->transaction_amount,
                    ];
                }),
            ]);
        }
        // Step 4: If `subscription_id` is provided, proceed to unsubscribe
        $subscriptionId = $request->input('subscription_id');
        $subscription = $subscriptions->where('subscription_id', $subscriptionId)->first();

        if (!$subscription) {
            return response()->json([
                'statusCode' => 4004,
                'message' => 'Subscription with the given ID not found in active subscriptions.',
            ], 404);
        }
        $nonRefundableAmounts = ['4', '9', '133', '199', '163', '5', '10', '200', '2000', '1950', '1600', '5000', '12', '300', '3000', '2950', '299', '2900', '1', '2'];
        if (in_array($subscription->transaction_amount, $nonRefundableAmounts)) {
            // Handle non-refundable unsubscription
            CustomerUnSubscription::create([
                'unsubscription_datetime' => now(),
                'medium' => 'Jazz Application',
                'subscription_id' => $subscription->subscription_id,
                'refunded_id' => '1',
            ]);
            $subscription->update(['policy_status' => 0]);

            return response()->json([
                'statusCode' => 2001,
                'refund' => 'false',
                'medium' => 'Jazz Application',
                'message' => 'Package unsubscribed successfully. You are not eligible for a refund.',
            ]);
        }
    }

    public function activesubscriptions(Request $request)
    {

        $subscriber_msisdn = $request->input("subscriber_msisdn");
        $rules = [
            'subscriber_msisdn' => 'required|regex:/^\d{11,12}$/'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $subscriber_msisdn = $request->input("subscriber_msisdn");
        if (preg_match('/^92\d{10}$/', $subscriber_msisdn)) {
            // Convert '92300XXXXXXX' to '0300XXXXXXX'
            $subscriber_msisdn = '0' . substr($subscriber_msisdn, 2);
        }
        // Retrieve all active subscriptions
        $subscriptions = CustomerSubscription::where('subscriber_msisdn', $subscriber_msisdn)
            ->where('policy_status', 1)
            ->get();

        if ($subscriptions->isNotEmpty()) {
            $activeSubscriptions = [];

            foreach ($subscriptions as $subscription) {

                // fetch all claims for this subscription MSISDN
                $claims = Claim::where('msisdn', $subscription->subscriber_msisdn)->get();

                $activeSubscriptions[] = [
                    'id' => $subscription->subscription_id,
                    'customer_id' => $subscription->customer_id,
                    'Duration' => $subscription->product_duration,
                    'payer_cnic' => $subscription->payer_cnic,
                    'payer_msisdn' => $subscription->payer_msisdn,
                    'subscriber_cnic' => $subscription->subscriber_cnic,
                    'subscriber_msisdn' => $subscription->subscriber_msisdn,
                    'beneficinary_name' => $subscription->beneficinary_name,
                    'benficinary_msisdn' => $subscription->benficinary_msisdn,
                    'transaction_amount' => $subscription->transaction_amount,
                    'transactionStatus' => $subscription->transaction_status,
                    'cpsOriginatorConversationId' => $subscription->referenceId,
                    'cpsTransactionId' => $subscription->cps_transaction_id,
                    'cpsRefundTransactionId' => -1,
                    'cpsResponse' => $subscription->cps_response_text,
                    'planId' => $subscription->plan_id,
                    'plan_status' => 1,
                    'pulse' => $subscription->pulse,
                    'APIsource' => $subscription->api_source,
                    'Recusive_charing_date' => $subscription->recursive_charging_date,
                    'subcription_time' => $subscription->subscription_time,
                    'grace_period_time' => $subscription->grace_period_time,
                    'Sales_agent' => $subscription->sales_agent,
                    'created_at' => $subscription->created_at,
                    'updated_at' => $subscription->updated_at,
                    'product_id' => $subscription->product_id,

                    // ➕ Add All Claims Here
                    'claims' => $claims
                ];
            }


            return response()->json([
                'error' => false,
                'is_policy_data' => true,
                'statusCode' => 4000,
                'message' => 'Active Policies',
                'Active Subscriptions' => $activeSubscriptions
            ]);
        } else {
            return response()->json([
                'error' => true,
                'is_policy_data' => false,
                'statusCode' => 4004,
                'message' => 'Customer Didnt Subscribed to any Policy',
                'Active Subscriptions' => []
            ]);
        }
    }

    public function SubmitClaim(Request $request)
{

     dd("hi");
    try {
        // Validate incoming request data
        $request->validate([
            'msisdn' => 'required',
            'claim_amount' => 'required',
            'type' => 'required|in:hospitalization,medical_and_lab_expense',
            'doctor_prescription' => 'nullable|array',
            'medical_bill' => 'nullable|array',
            'lab_bill' => 'nullable|array',
            'other' => 'nullable|array',
        ]);

        // Check if the claim msisdn exists in the CustomerSubscription table
          $claim_msisdn = CustomerSubscription::whereIn('plan_id', [6])
           ->where('subscriber_msisdn', $request->msisdn)
          ->where('policy_status', 1)
            ->first();

            dd($claim_msisdn);

        if (!$claim_msisdn) {
            return response()->json(['message' => 'Claim msisdn not found'], 404);
        }

        $amount = $claim_msisdn->transaction_amount;
        $plan_id = $claim_msisdn->plan_id;
        $product_id = $claim_msisdn->productId;


        $type = ($request->type == 'hospitalization') ? 'hospitalization' : 'medical_and_lab_expense';
        $history_name = ($type == 'hospitalization') ? 'Hospital' : 'Medicine';

        // Handle base64 image uploads
        $fileFields = ['doctor_prescription', 'medical_bill', 'lab_bill', 'other'];
        $claimData = [];

        foreach ($fileFields as $field) {
            if ($request->has($field) && is_array($request->{$field})) {
                $fileData = $request->{$field};

                if (!empty($fileData['base64']) && !empty($fileData['type'])) {
                    $extension = strtolower($fileData['type']); // e.g., "png"
                    $filename = time() . '_' . $field . '.' . $extension;
                    $path = 'claims/' . $field . '/' . $filename;

                    Storage::disk('public')->put($path, base64_decode($fileData['base64']));
                    $claimData[$field] = $path;
                }
            }
        }

        // Save the claim
        $claim = Claim::create(array_merge([
            'msisdn' => $request->msisdn,
            'plan_id' => $plan_id,
            'product_id' => $product_id,
            'status' => 'In Process',
            'date' => now(),
            'amount' => $amount,
            'claim_amount' => $request->claim_amount,
            'type' => $type,
            'history_name' => $history_name,
        ], $claimData));

        return response()->json(['message' => 'Claim submitted successfully', 'data' => $claim], 200);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json(['message' => 'Validation Error', 'errors' => $e->errors()], 422);
    } catch (\Exception $e) {
        return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
    }
}




}
