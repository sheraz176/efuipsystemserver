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
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

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
            'claim_amount' => 'required',
            'type' => 'required|in:hospitalization,medical_and_lab_expense',
            'doctor_prescription' => 'nullable|array',
            'medical_bill' => 'nullable|array',
            'lab_bill' => 'nullable|array',
            'other' => 'nullable|array',
        ]);

        // Check if the claim msisdn exists in the CustomerSubscription table
          $claim_msisdn = CustomerSubscription::whereIn('plan_id', [4, 5])
           ->where('subscriber_msisdn', $request->msisdn)
          ->where('policy_status', 1)
            ->first();

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


private function detectMimeType($binaryData)
{
    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    return $finfo->buffer($binaryData);
}

private function getExtensionFromMimeType($mimeType)
{
    $mimeToExt = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'application/pdf' => 'pdf',
        'image/bmp' => 'bmp',
        'image/webp' => 'webp',
        'image/tiff' => 'tiff',
    ];

    return $mimeToExt[$mimeType] ?? 'jpg';
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
                    'claim_id' => $claim->id,
                    'history_name' => $claim->history_name,
                    'amount' => 'Rs. ' . number_format($claim->amount, 0), // Format the amount
                    'claim_amount' => 'Rs. ' . number_format($claim->claim_amount, 0),
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

    public function Claimamounts(Request $request)
{
    $request->validate([
        'msisdn' => 'required|digits_between:10,13', // Validate MSISDN is provided and is of valid length
        'subscription_id ' => 'required', // Validate MSISDN is provided and is of valid length
    ]);

    // Check if the claim MSISDN exists in the CustomerSubscription table
    $claim_msisdn = CustomerSubscription::whereIn('plan_id', [4, 5])
    ->where('subscriber_msisdn', $request->msisdn)
    ->where('subscription_id', $request->subscription_id)
    ->where('policy_status', 1)
    ->first();
     //dd($claim_msisdn);

    if (!$claim_msisdn) {
        return response()->json(['message' => 'Claim MSISDN not found'], 404);
    }

    // Retrieve all claims for the same MSISDN, plan_id, and product_id
    $existingClaims = Claim::where('msisdn', $request->msisdn)
        ->where('plan_id', $claim_msisdn->plan_id)
        ->where('status', 'approved')
        ->get();

      //dd($existingClaims);

    // Get the package amount from the ProductModel
    $product = ProductModel::where('product_id', $claim_msisdn->productId)->first();

    //dd($product);

    // Initialize base amounts
    $baseHospitalizationAmount = 20000;

    //dd($baseHospitalizationAmount);
    //$baseMedicalExpenseAmount = 10000;

   if($claim_msisdn->plan_id == "4"){
         $baseMedicalExpenseAmount = 750000;
    }
    else{
         $baseMedicalExpenseAmount = 850000;
    }


    // If no existing claims found, return response with base amounts and zero claims
    if ($existingClaims->isEmpty()) {
      //dd('hi');
        return response()->json([
            'msisdn' => $request->msisdn,
            'plan_id' => $claim_msisdn->plan_id,
            'product_id' => $claim_msisdn->productId,
            'package_amount' => $product->fee ?? 0, // Ensure fee exists
            'Total_Hospitalization_Amount_existing' => $baseHospitalizationAmount,
            'Total_Medical_Bill_Amount_existing' => $baseMedicalExpenseAmount,
            'total_hospitalization_claimed_amount' => 0,
            'remaining_hospitalization_amount' => '20000',
            'total_medical_bill_claimed_amount' => 0,
            'remaining_medical_bill_amount' => $baseMedicalExpenseAmount,
        ]);
    }

    // Calculate total claimed amounts separately
    $totalHospitalizationClaimed = $existingClaims->where('type', 'hospitalization')->sum('claim_amount');
    $totalMedicalExpenseClaimed = $existingClaims->where('type', 'medical_and_lab_expense')->sum('claim_amount');

    // Calculate remaining amounts
    $remainingHospitalizationAmount = max($baseHospitalizationAmount - $totalHospitalizationClaimed, 0);
    //dd($remainingHospitalizationAmount);
    $remainingMedicalExpenseAmount = max($baseMedicalExpenseAmount - $totalMedicalExpenseClaimed, 0);
     //dd($remainingMedicalExpenseAmount);

    // Update all claims with the new remaining amounts
    foreach ($existingClaims as $claim) {
        if ($claim->type == 'hospitalization') {
             //dd('hi');
            $claim->update([
                'existingamount' => $baseHospitalizationAmount,
                'remaining_amount' => $remainingHospitalizationAmount
            ]);
        } elseif ($claim->type == 'medical_and_lab_expense') {
            $claim->update([
                'existingamount' => $baseMedicalExpenseAmount,
                'remaining_amount' => $remainingMedicalExpenseAmount
            ]);
        }
    }

 return response()->json([
    'msisdn' => $request->msisdn ?? 0,
    'plan_id' => $claim_msisdn->plan_id ?? 0,
    'product_id' => $claim_msisdn->productId ?? 0,
    'package_amount' => $product->fee ?? 0, // Ensure fee exists
    'Total_Hospitalization_Amount_existing' => $baseHospitalizationAmount ?? 0,
    'Total_Medical_Bill_Amount_existing' => $baseMedicalExpenseAmount ?? 0,
    'total_hospitalization_claimed_amount' => $totalHospitalizationClaimed ?? 0,
    'remaining_hospitalization_amount' => $remainingHospitalizationAmount ?? 0,
    'total_medical_bill_claimed_amount' => $totalMedicalExpenseClaimed ?? 0,
    'remaining_medical_bill_amount' => $remainingMedicalExpenseAmount ?? 0,
]);

}

public function Claimstatus(Request $request)
{
    try {
        $request->validate([
            'claim_id' => 'required', // Validate claim_id exists in claims table
            'msisdn' => 'required|digits_between:10,13', // Validate MSISDN
            'status' => 'required|in:approved,pending,cancelled' // Ensure status is one of these values
        ]);

        // Find the claim record
        $claim = Claim::where('id', $request->claim_id)
            ->where('msisdn', $request->msisdn)
            ->first();

        if (!$claim) {
            return response()->json(['message' => 'Claim not found'], 404);
        }

        // Update claim status
        $claim->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Claim status updated successfully',
            'claim_id' => $claim->id,
            'msisdn' => $claim->msisdn,
            'status' => $claim->status
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An error occurred',
            'error' => $e->getMessage()
        ], 500);
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
                ->first();

            $product_name = $product ? $product->product_name : 'N/A';

            // Fetch plan details
            $plan = PlanModel::where('plan_id', $claim->plan_id)
                ->where('status', 1)
                ->first();

            $plan_name = $plan ? $plan->plan_name : 'N/A';

            // Map claim details with associated data
            return [
                'claim_id' => $claim->id,
                'history_name' => $claim->history_name,
                'amount' => 'Rs. ' . number_format($claim->amount, 0),
                'claim_amount' => 'Rs. ' . number_format($claim->claim_amount, 0),
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
