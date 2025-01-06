<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plans\PlanModel;
use App\Models\Plans\ProductModel;
use App\Models\Claim;
use Illuminate\Support\Facades\Hash;
use App\Models\Subscription\CustomerSubscription;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ClaimController extends Controller
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

    public function SubmitClaim(Request $request)
    {
        try {
            // Validate incoming request data
            $request->validate([
                'msisdn' => 'required',
                'product_id' => 'required',
                'type' => 'required|in:hospitalization,medical_and_lab_expense', // Validate type is either hospitalization or medical_and_lab_expense
                'doctor_prescription' => 'nullable|file|mimes:pdf,jpg,png',
                'medical_bill' => 'nullable|file|mimes:pdf,jpg,png',
                'lab_bill' => 'nullable|file|mimes:pdf,jpg,png',
                'other' => 'nullable|file|mimes:pdf,jpg,png',
            ]);

            // Check if the claim msisdn exists in the CustomerSubscription table
            $claim_msisdn = CustomerSubscription::where('plan_id', '5')
                ->where('productId', $request->product_id)
                ->where('subscriber_msisdn', $request->msisdn)
                ->where('policy_status', 1)
                ->first();

            // If no matching msisdn found, return an error response
            if (!$claim_msisdn) {
                return response()->json(['message' => 'Claim msisdn not found'], 404);
            }

            // Check if a claim already exists for the same msisdn, plan_id, and product_id
            $existingClaim = Claim::where('msisdn', $request->msisdn)
                ->where('plan_id', $claim_msisdn->plan_id)
                ->where('product_id', $request->product_id)
                ->first();

            if ($existingClaim) {
                return response()->json(['message' => 'A claim has already been submitted for this product.'], 409);
            }

            // Get the transaction amount from the found msisdn
            $amount = $claim_msisdn->transaction_amount;
            $plan_id = $claim_msisdn->plan_id;

            // Check if the type is valid and assign appropriate values for `type` and `history_name`
            $type = $history_name = null;

            if ($request->type == 'hospitalization') {
                $type = 'hospitalization';
                $history_name = 'Hospital';
            } elseif ($request->type == 'medical_and_lab_expense') {
                $type = 'medical_and_lab_expense';
                $history_name = 'Medicine';
            }

            // Handle the file uploads and store the file paths
            $doctorPrescriptionPath = $request->file('doctor_prescription') ? $request->file('doctor_prescription')->store('claims_documents') : null;
            $medicalBillPath = $request->file('medical_bill') ? $request->file('medical_bill')->store('claims_documents') : null;
            $labBillPath = $request->file('lab_bill') ? $request->file('lab_bill')->store('claims_documents') : null;
            $otherPath = $request->file('other') ? $request->file('other')->store('claims_documents') : null;

            // Save the claim with the relevant data
            $claim = Claim::create([
                'msisdn' => $request->msisdn,
                'plan_id' => $plan_id,
                'product_id' => $request->product_id,
                'status' => 'In Process', // Default status
                'date' => now(),
                'amount' => $amount,
                'type' => $type,
                'history_name' => $history_name,
                'doctor_prescription' => $doctorPrescriptionPath, // Path for doctor prescription
                'medical_bill' => $medicalBillPath, // Path for medical bill
                'lab_bill' => $labBillPath, // Path for lab bill
                'other' => $otherPath, // Path for other document
            ]);

            // Return a success response
            return response()->json(['message' => 'Claim submitted successfully', 'data' => $claim], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation Error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }




    public function ClaimHistory(Request $request)
    {
        try {
            // Validate the incoming request
            $request->validate([
                'msisdn' => 'required|digits_between:10,13', // Validate MSISDN is provided and is of valid length
            ]);

            // Fetch the claim history for the given MSISDN
            $ClaimHistory = Claim::where('msisdn', $request->msisdn)
                ->orderBy('date', 'desc') // Order by the most recent claims
                ->get();

            // Check if any claims exist for the given MSISDN
            if ($ClaimHistory->isEmpty()) {
                return response()->json(['message' => 'No claim history found for the provided MSISDN.'], 404);
            }

            // Format the response
            $formattedClaims = $ClaimHistory->map(function ($claim) {
                return [
                    'history_name' => $claim->history_name,
                    'amount' => 'Rs. ' . number_format($claim->amount, 0), // Format the amount
                    'date' => \Carbon\Carbon::parse($claim->date)->format('d M, Y'), // Format the date
                    'status' => $claim->status,
                ];
            });

            // Return the formatted claim history
            return response()->json(['message' => 'Claim history retrieved successfully', 'data' => $formattedClaims], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => 'Validation Error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }


    public function ClaimDetails(Request $request)
{
    try {
        // Validate the request
        $request->validate([
            'msisdn' => 'required|digits_between:10,13', // Validate MSISDN
        ]);

        // Fetch claim details for the provided MSISDN
        $ClaimDetails = Claim::where('msisdn', $request->msisdn)
            ->orderBy('date', 'desc')
            ->get();

        // Check if claims exist
        if ($ClaimDetails->isEmpty()) {
            return response()->json(['message' => 'No claim details found for the provided MSISDN.'], 404);
        }

        // Prepare the detailed response
        $detailedClaims = $ClaimDetails->map(function ($claim) {
            // Fetch product details
            $product = ProductModel::where('product_id', $claim->product_id)
                ->where('api_status', 1)
                ->first();

            $product_name = $product ? $product->product_name : 'N/A';

            // Fetch plan details
            $plan = PlanModel::where('plan_id', $claim->plan_id)
                ->where('status', 1)
                ->first();

            $plan_name = $plan ? $plan->plan_name : 'N/A';

            // Map claim details with associated data
            return [
                'history_name' => $claim->history_name,
                'amount' => 'Rs. ' . number_format($claim->amount, 0),
                'date' => \Carbon\Carbon::parse($claim->date)->format('d M, Y'),
                'status' => $claim->status,
                'type' => $claim->type, // Include type
                'product_name' => $product_name,
                'plan_name' => $plan_name,
                'documents' => [
                    'doctor_prescription' => $claim->doctor_prescription ? url('storage/' . $claim->doctor_prescription) : null,
                    'medical_bill' => $claim->medical_bill ? url('storage/' . $claim->medical_bill) : null,
                    'lab_bill' => $claim->lab_bill ? url('storage/' . $claim->lab_bill) : null,
                    'other' => $claim->other ? url('storage/' . $claim->other) : null,
                ],
            ];
        });

        // Return the detailed claims
        return response()->json(['message' => 'Claim details retrieved successfully', 'data' => $detailedClaims], 200);
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Handle validation errors
        return response()->json(['message' => 'Validation Error', 'errors' => $e->errors()], 422);
    } catch (\Exception $e) {
        // Handle other exceptions
        return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
    }
}



public function mHealthDoctors(Request $request)
{
    try {
        // Make a GET request to the external API
        $response = Http::get('https://portal.mhealth.efulife.com/mhealth/api/doctor-profile/list/jazz');

        // Check if the response is successful
        if ($response->successful()) {
            // Return the JSON response
            return response()->json([
                'message' => 'Doctors list retrieved successfully',
                'data' => $response->json(),
            ], 200);
        } else {
            // Handle unsuccessful response
            return response()->json([
                'message' => 'Failed to retrieve doctors list',
                'error' => $response->body(), // Include the error response from the API
            ], $response->status());
        }
    } catch (\Exception $e) {
        // Handle exceptions
        return response()->json([
            'message' => 'An error occurred while fetching the doctors list',
            'error' => $e->getMessage(),
        ], 500);
    }
}


}
