<?php

namespace App\Http\Controllers\BasicAgent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InterestedCustomers\InterestedCustomer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function saveCustomer(Request $request)
{
    // Validate the incoming request
    $validatedData = $request->validate([
        'customer_msisdn' => 'required|string|max:255',
        'customer_cnic' => 'required|string|max:255',
        'plan_id' => 'required|integer',
        'product_id' => 'required|integer',
        'beneficiary_msisdn' => 'required|string|max:255',
        'beneficiary_cnic' => 'required|string|max:255',
        'relationship' => 'required|string|max:255',
        'beneficinary_name' => 'required|string|max:255',
        'agent_id' => 'required|integer',
        'company_id' => 'required|integer',
    ]);

    $today = Carbon::now('Asia/Karachi')->format('Y-m-d');

    try {
        // Start transaction
        DB::beginTransaction();

        // Check for existing entry
        $customerChecks = InterestedCustomer::where('customer_msisdn', $request->customer_msisdn)
            ->whereDate('created_at', $today)
            ->lockForUpdate()
            ->first();

        if ($customerChecks) {
            // Rollback transaction
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Today Already Number Add Interested Customer'], 500);
          } else {
            // Create a new InterestedCustomer instance
            $customer = InterestedCustomer::create($validatedData);

            if ($customer) {
                // Commit transaction
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Customer saved successfully']);
            } else {
                // Rollback transaction
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Failed to save customer'], 500);
            }
        }
    } catch (\Exception $e) {
        // Rollback transaction in case of an error
        DB::rollBack();
        return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
    }
}
}
