<?php

namespace App\Http\Controllers\claims;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Claim;
use App\Models\Subscription\CustomerSubscription;
use Illuminate\Support\Facades\Http;


class ClaimsController extends Controller
{


  public function showClaimIndex()
{
    return view('super_agent_Interested.claimindex');
}

public function getClaimsData(Request $request)
{
    $query = \App\Models\Claim::select([
        'claims.*',
        'plans.plan_name',
        'products.product_name',
    ])
    ->leftJoin('plans', 'claims.plan_id', '=', 'plans.plan_id')
    ->leftJoin('products', 'claims.product_id', '=', 'products.product_id');

    // Date filter
    if ($request->has('dateFilter') && $request->dateFilter) {
        $dates = explode(' to ', $request->dateFilter);
        if (count($dates) === 2) {
            $query->whereBetween('claims.date', [$dates[0], $dates[1]]);
        }
    }

    return DataTables::of($query)
        ->addColumn('id', function ($row) {
            return isset($row->id) ? "CLM{$row->id}" : '-';
           })
        ->addColumn('plan_name', function ($row) {
            return $row->plan_name ?? '-';
        })
        ->addColumn('product_name', function ($row) {
            return $row->product_name ?? '-';
        })
        ->addColumn('doctor_prescription', function ($row) {
            return $row->doctor_prescription
                ? '<a href="' . asset('storage/app/public/' . $row->doctor_prescription) . '" target="_blank" class="btn btn-sm btn-primary">View</a>'
                : '-';
        })
        ->addColumn('medical_bill', function ($row) {
            return $row->medical_bill
                ? '<a href="' . asset('storage/app/public/' . $row->medical_bill) . '" target="_blank" class="btn btn-sm btn-info">View</a>'
                : '-';
        })
        ->addColumn('lab_bill', function ($row) {
            return $row->lab_bill
                ? '<a href="' . asset('storage/app/public/' . $row->lab_bill) . '" target="_blank" class="btn btn-sm btn-warning">View</a>'
                : '-';
        })
        ->addColumn('status_action', function ($row) {
        if ($row->status === 'In Process') {
           return '
              <button class="btn btn-success btn-sm approve-btn" data-id="'.$row->id.'">Approve</button>
               <button class="btn btn-danger btn-sm reject-btn" data-id="'.$row->id.'">Reject</button>
        ';
        }
            return '<span class="badge bg-'.($row->status === 'Approved' ? 'success' : 'danger').'">'.$row->status.'</span>';
        })
       ->addColumn('edit_amount', function ($row) {
    if ($row->status === 'Reject') {
        return ''; // No button for rejected claims
    }

    return '<button class="btn btn-primary btn-sm edit-amount-btn" data-id="' . $row->id . '" data-amount="' . $row->claim_amount . '">Update Claim Amount</button>';
})
        ->rawColumns(['id','doctor_prescription', 'medical_bill', 'lab_bill', 'status_action','edit_amount'])

        ->make(true);
}


   public function export(Request $request)
{
    $query = Claim::select([
        'claims.*',
        'plans.plan_name',
        'products.product_name',
    ])
    ->leftJoin('plans', 'claims.plan_id', '=', 'plans.plan_id')
    ->leftJoin('products', 'claims.product_id', '=', 'products.product_id');

    // Optional date filter
    if ($request->filled('dateFilter')) {
        $dateRange = explode(' to ', $request->input('dateFilter'));
        if (count($dateRange) === 2) {
            $startDate = $dateRange[0];
            $endDate = $dateRange[1];

            $query->whereBetween('claims.date', [$startDate, $endDate]);
        }
    }

    $data = $query->get();

    // Define column headers
    $headers = [
        'MSISDN', 'Plan Name', 'Product Name', 'Status', 'Date', 'Amount', 'Type',
        'History Name', 'Doctor Prescription', 'Medical Bill', 'Lab Bill', 'Other',
        'Claim Amount', 'Existing Amount', 'Remaining Amount',
    ];

    // Build rows
    $rows = [];
    $rows[] = $headers;

    foreach ($data as $item) {
        $rows[] = [
            $item->msisdn,
            $item->plan_name,
            $item->product_name,
            $item->status,
            $item->date,
            $item->amount,
            $item->type,
            $item->history_name,
            $item->doctor_prescription,
            $item->medical_bill,
            $item->lab_bill,
            $item->other,
            $item->claim_amount,
            $item->existingamount,
            $item->remaining_amount,
        ];
    }

    // Export as tab-delimited .xls
    $filePath = storage_path('app/claims_export.xls');
    $file = fopen($filePath, 'w');

    foreach ($rows as $row) {
        fputcsv($file, $row, "\t");
    }

    fclose($file);

    return response()->download($filePath)->deleteFileAfterSend(true);
}


public function updateClaimStatus(Request $request)
{
    $request->validate([
        'id' => 'required|exists:claims,id',
        'status' => 'required|in:Approved,Reject',
    ]);

    $claim = Claim::find($request->id);
    $claim->status = $request->status;
    $claim->save();

    // Build SMS message based on status
    $claimRef = "CLM{$claim->id}";
    $msisdn = $claim->msisdn;
    $message = '';

    if ($claim->status === 'Approved') {
        $message = "Claim Approved:\nGood news! Your claim (Ref: {$claimRef}) has been approved. We will notify you once the settlement is processed. Thank you for your patience.";

        // Optionally, immediately send the settlement message too:
        $settlementMessage = "Claim Settled:\nYour claim (Ref: {$claimRef}) has been successfully settled. The payment has been processed. Thank you for choosing us.";

        // Send both messages (approval + settlement)
        Http::withHeaders([
            'Authorization' => 'Bearer XXXXAAA489SMSTOKEFU',
            'Channelcode' => 'ITS',
        ])->post('http://api.efulife.com/itssr/its_sendsms', [
            'MobileNo' => $msisdn,
            'sender' => '98902',
            'SMS' => $message,
            'telco' => '',
        ]);

        Http::withHeaders([
            'Authorization' => 'Bearer XXXXAAA489SMSTOKEFU',
            'Channelcode' => 'ITS',
        ])->post('http://api.efulife.com/itssr/its_sendsms', [
            'MobileNo' => $msisdn,
            'sender' => '98902',
            'SMS' => $settlementMessage,
            'telco' => '',
        ]);
    } elseif ($claim->status === 'Reject') {
        $message = "Claim Rejected:\nYour claim (Ref: {$claimRef}) has been declined. For details or assistance, please contact us at 042-111-333-033.";

        Http::withHeaders([
            'Authorization' => 'Bearer XXXXAAA489SMSTOKEFU',
            'Channelcode' => 'ITS',
        ])->post('http://api.efulife.com/itssr/its_sendsms', [
            'MobileNo' => $msisdn,
            'sender' => '98902',
            'SMS' => $message,
            'telco' => '',
        ]);
    }

    return response()->json(['success' => true, 'message' => 'Status updated and SMS sent successfully.']);
}

public function updateAmount(Request $request)
{
    $request->validate([
        'claim_id' => 'required|exists:claims,id',
        'new_amount' => 'required|numeric|min:0',
    ]);

    $claim = Claim::find($request->claim_id);
    $claim->claim_amount = $request->new_amount;
    $claim->save();

    return response()->json(['message' => 'claim_amount updated successfully.']);
}


 public function index()
    {

        return view('agent.customerInformation.index');
    }

    public function search(Request $request)
    {
        $msisdn = $request->input('msisdn');

        $customers = CustomerSubscription::with(['companyProfiles', 'products', 'plan', 'teleSalesAgent'])
                                         ->where('policy_status',1)
                                         ->where('productId', '11')
                                         ->where('subscriber_msisdn', $msisdn)
                                         ->get();

        if ($customers->isEmpty()) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        return view('agent.customerInformation.partials.customer_info', compact('customers'));
    }

 public function SubmitClaim(Request $request)
{
    try {
        $request->validate([
            'msisdn' => 'required',
            'type' => 'required|in:hospitalization,medical_and_lab_expense',
            'plan_id' => 'required',
            'product_id' => 'required',
        ]);

        $claim_msisdn = CustomerSubscription::where('productId', '11')
            ->where('subscriber_msisdn', $request->msisdn)
            ->where('policy_status', 1)
            ->first();

        if (!$claim_msisdn) {
            return back()->with('error', 'Claim MSISDN not found.');
        }

        $amount = $claim_msisdn->transaction_amount;
        $type = $request->type;
        $history_name = ($type == 'hospitalization') ? 'Hospital' : 'Medicine';

        $claim = Claim::create([
            'msisdn' => $request->msisdn,
            'plan_id' => $request->plan_id,
            'product_id' => $request->product_id,
            'status' => 'In Process',
            'date' => now(),
            'amount' => $amount,
            'claim_amount' => '0',
            'type' => $type,
            'history_name' => $history_name,
        ]);

        // ✅ Send SMS notification
        $smsMessage = "Claim Submission (In Process):\nYour claim (Ref: CLM{$claim->id}) has been received and is under review. We will update you on the status shortly. For queries, call 042-111-333-033.";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer XXXXAAA489SMSTOKEFU',
            'Channelcode' => 'ITS',
        ])->post('http://api.efulife.com/itssr/its_sendsms', [
            'MobileNo' => $request->msisdn,
            'sender' => '98902',
            'SMS' => $smsMessage,
            'telco' => '',
        ]);

        // Optional: handle SMS response (log if needed)
        // if ($response->failed()) {
        //     Log::error('SMS API failed: ' . $response->body());
        // }

        return back()->with('success', 'Claim submitted successfully! SMS sent.');
    } catch (\Illuminate\Validation\ValidationException $e) {
        return back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        return back()->with('error', 'An error occurred: ' . $e->getMessage());
    }
}

}
