<?php

namespace App\Http\Controllers\claims;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Claim;
use App\Models\Subscription\CustomerSubscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ClaimsController extends Controller
{

    public function searchPlans(Request $request)
    {
        $msisdn = $request->msisdn;

        // Normalize MSISDN → 0300 format
        $msisdn = $this->formatMsisdn($msisdn);

        $subscriptions = CustomerSubscription::with(['plan'])
            ->where('subscriber_msisdn', $msisdn)
             ->where('policy_status', 1)
            ->get();

        $plans = $subscriptions->map(function ($sub) {
            return [
                'plan_id' => $sub->plan->plan_id ?? null,
                'plan_name' => $sub->plan->plan_name ?? 'N/A'
            ];
        })->filter();

        return response()->json([
            'plans' => $plans->unique('plan_id')->values()
        ]);
    }

    private function formatMsisdn($msisdn)
    {
        $msisdn = preg_replace('/\D/', '', $msisdn); // remove non-digits

        // 92300xxxxxxx → 0300xxxxxxx
        if (str_starts_with($msisdn, '92')) {
            $msisdn = '0' . substr($msisdn, 2);
        }

        // 300xxxxxxx → 0300xxxxxxx
        if (strlen($msisdn) == 10) {
            $msisdn = '0' . $msisdn;
        }

        return $msisdn;
    }

    public function sendSmsSehat(Request $request)
    {
        $request->validate([
            'msisdn' => 'required|string',
            'message' => 'required|string',
        ]);

        $msisdn = $request->msisdn;
        $message = $request->message;

        $key    = 'mYjC!nc3dibleY3k';  // 16 chars
        $iv     = 'Myin!tv3ctorjCM@';  // 16 chars
        $cipher = 'AES-128-CBC';

        $payload = [
            'msisdn'      => $msisdn,
            'content'     => $message,
            'referenceId' => uniqid(),
        ];

        $jsonData = json_encode($payload);

        $encryptedBinary = openssl_encrypt($jsonData, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        if (!$encryptedBinary) {
            return response()->json(['message' => 'Encryption failed'], 500);
        }

        $encryptedHex = bin2hex($encryptedBinary);

        $requestBody = json_encode(['data' => $encryptedHex]);

        $ch = curl_init('https://gateway.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/notification');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-CLIENT-ID: 946658113e89d870aad2e47f715c2b72',
            'X-CLIENT-SECRET: e5a0279efbd7bd797e472d0ce9eebb69',
            'X-PARTNER-ID: 946658113e89d870aad2e47f715c2b72',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode != 200) {
            return response()->json(['message' => 'Failed to send SMS', 'api_response' => $response], 500);
        }

        return response()->json(['message' => 'SMS sent successfully', 'api_response' => $response]);
    }






    public function showSehatPlus()
    {
        return view('super_agent_Interested.sehatplus');
    }


    public function getSehatPlusClaims(Request $request)
    {
        $msisdn = $request->input('customer_msisdn', '');
        $plan_id = $request->input('plan_id', '');

        $page = $request->input('page', 1); // optional, can be used if API supports paging

        // Call external API
        $response = Http::get('https://jazzcash-health.efulife.com/api/getallCustomerClaims', [
            'customer_msisdn' => $msisdn,
            'plan_id' => $plan_id,
            'page' => $page,
        ]);

        $claims = collect($response->json('data') ?? []);

        return DataTables::of($claims)
            ->addIndexColumn()
            ->addColumn('update_amount', function ($row) {
                return '<button class="btn btn-sm btn-success" onclick="updateClaimAmount(\'' . $row['claim_id'] . '\', \'' . $row['claim_amount'] . '\')">Update</button>';
            })
            ->addColumn('update_status', function ($row) {
                return '<button class="btn btn-sm btn-warning updateStatusBtn" data-claim-id="' . $row['claim_id'] . '">Update</button>';
            })
            ->addColumn('images', function ($row) {
                if (isset($row['images']) && is_array($row['images']) && count($row['images']) > 0) {
                    return '<button class="btn btn-sm btn-info" onclick="openImageModal(\'' . $row['claim_id'] . '\')">View (' . count($row['images']) . ')</button>';
                }
                return '-';
            })
            ->rawColumns(['update_amount', 'update_status', 'images'])
            ->make(true);
    }


    public function showClaimIndex()
    {
        return view('super_agent_Interested.claimindex');
    }


    public function sendSms(Request $request)
    {
        $request->validate([
            'claim_id' => 'required|exists:claims,id',
            'msisdn'   => 'required',
            'message'  => 'required|string'
        ]);

        $claim = Claim::findOrFail($request->claim_id);

        // ✅ Format MSISDN
        $msisdn = ltrim($request->msisdn, '+');

        if (substr($msisdn, 0, 2) !== '92') {
            if (substr($msisdn, 0, 1) === '0') {
                $msisdn = '92' . substr($msisdn, 1);
            } elseif (strlen($msisdn) === 10) {
                $msisdn = '92' . $msisdn;
            }
        }

        $message = $request->message;

        // ✅ Encryption Setup
        $key    = 'mYjC!nc3dibleY3k';  // 16 chars
        $iv     = 'Myin!tv3ctorjCM@';  // 16 chars
        $cipher = 'AES-128-CBC';

        $payload = [
            'msisdn'      => $msisdn,
            'content'     => $message,
            'referenceId' => uniqid(),
        ];

        $jsonData = json_encode($payload);

        // ✅ Correct OpenSSL usage
        $encryptedBinary = openssl_encrypt(
            $jsonData,
            $cipher,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if (!$encryptedBinary) {
            return response()->json([
                'message' => 'Encryption failed.'
            ], 500);
        }

        $encryptedHex = bin2hex($encryptedBinary);

        $requestBody = json_encode([
            'data' => $encryptedHex
        ]);

        // ✅ JazzCash API Call
        $ch = curl_init('https://gateway.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/notification');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-CLIENT-ID: 946658113e89d870aad2e47f715c2b72',
            'X-CLIENT-SECRET: e5a0279efbd7bd797e472d0ce9eebb69',
            'X-PARTNER-ID: 946658113e89d870aad2e47f715c2b72',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // ✅ Save SMS in DB
        $claim->update([
            'last_sms' => $message,
            'last_sms_sent_at' => now(),
        ]);

        //dd($response);
        // ✅ Log API Response


        return response()->json([
            'message' => 'SMS sent successfully.',
            'api_response' => $response
        ]);
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
        if ($request->filled('dateFilter')) {
            $dates = explode(' to ', $request->dateFilter);
            if (count($dates) === 2) {
                $query->whereBetween('claims.date', [$dates[0], $dates[1]]);
            }
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('claims.status', $request->status);
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('claims.type', 'LIKE', '%' . $request->type . '%');
        }

        return DataTables::of($query)
            ->addColumn('id', fn($row) => isset($row->id) ? "CLM{$row->id}" : '-')
            ->addColumn('plan_name', fn($row) => $row->plan_name ?? '-')
            ->addColumn('product_name', fn($row) => $row->product_name ?? '-')
            ->addColumn('doctor_prescription', fn($row) =>
            $row->doctor_prescription
                ? '<a href="' . asset('/storage/' . $row->doctor_prescription) . '" target="_blank" class="btn btn-sm btn-primary">View</a>'
                : '-')
            ->addColumn('medical_bill', fn($row) =>
            $row->medical_bill
                ? '<a href="' . asset('/storage/' . $row->medical_bill) . '" target="_blank" class="btn btn-sm btn-info">View</a>'
                : '-')
            ->addColumn('lab_bill', fn($row) =>
            $row->lab_bill
                ? '<a href="' . asset('/storage/' . $row->lab_bill) . '" target="_blank" class="btn btn-sm btn-warning">View</a>'
                : '-')
            ->addColumn('other', fn($row) =>
            $row->other
                ? '<a href="' . asset('/storage/' . $row->other) . '" target="_blank" class="btn btn-sm btn-warning">View</a>'
                : '-')
            ->addColumn('status_action', function ($row) {

                if ($row->status === 'In Process') {
                    return '
            <button class="btn btn-success btn-sm approve-btn" data-id="' . $row->id . '">Approve</button>
            <button class="btn btn-danger btn-sm reject-btn" data-id="' . $row->id . '">Reject</button>
        ';
                }

                // Agar Approved ho
                if ($row->status === 'Approved') {
                    return '<span class="badge bg-success">Approved</span>';
                }

                // Agar Rejected ho to reason bhi show karo
                if ($row->status === 'Reject') {
                    return '
            <span class="badge bg-danger">Rejected</span>
            <br>
            <small class="text-muted">Reason: ' . ($row->rejection_reason ?? 'N/A') . '</small>
        ';
                }

                return '';
            })

            ->addColumn('send_sms', function ($row) {
                return '<button class="btn btn-info btn-sm send-sms-btn"
                data-id="' . $row->id . '"
                data-msisdn="' . $row->msisdn . '">
                Send SMS
            </button>';
            })
            ->addColumn('edit_amount', function ($row) {
                return $row->status !== 'Reject'
                    ? '<button class="btn btn-primary btn-sm edit-amount-btn" data-id="' . $row->id . '" data-amount="' . $row->claim_amount . '">Update Claim Amount</button>'
                    : '';
            })

            ->addColumn('pending_case_remarks', function ($row) {
                return $row->pending_case_remarks
                    ? $row->pending_case_remarks
                    : '-';
            })

            ->addColumn('pending_case_action', function ($row) {
                return '<button class="btn btn-warning btn-sm edit-pending-btn"
                data-id="' . $row->id . '"
                data-remarks="' . $row->pending_case_remarks . '">
                Update Remarks
            </button>';
            })


            ->rawColumns(['id', 'doctor_prescription', 'medical_bill', 'lab_bill', 'status_action', 'edit_amount', 'other', 'send_sms', 'pending_case_action'])
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
            'Claim_id',
            'MSISDN',
            'Plan Name',
            'Product Name',
            'Status',
            'Date',
            'Amount',
            'Type',
            'History Name',
            'Doctor Prescription',
            'Medical Bill',
            'Lab Bill',
            'Other',
            'Claim Amount',
            'Existing Amount',
            'Remaining Amount',
            'send_sms',
        ];

        // Build rows
        $rows = [];
        $rows[] = $headers;

        foreach ($data as $item) {
            $rows[] = [
                $item->id,
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
                $item->last_sms,
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

    public function AgentshowClaimIndex()
    {
        return view('super_agent_Interested.agentclaimindex');
    }

    public function AgentgetClaimsData(Request $request)
    {
        $query = \App\Models\Claim::select([
            'claims.*',
            'plans.plan_name',
            'products.product_name',
        ])
            ->where('claims.agent_upload', '1')
            ->leftJoin('plans', 'claims.plan_id', '=', 'plans.plan_id')
            ->leftJoin('products', 'claims.product_id', '=', 'products.product_id');

        // Date filter
        if ($request->filled('dateFilter')) {
            $dates = explode(' to ', $request->dateFilter);
            if (count($dates) === 2) {
                $query->whereBetween('claims.date', [$dates[0], $dates[1]]);
            }
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('claims.status', $request->status);
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('claims.type', 'LIKE', '%' . $request->type . '%');
        }

        return DataTables::of($query)
            ->addColumn('id', fn($row) => isset($row->id) ? "CLM{$row->id}" : '-')
            ->addColumn('plan_name', fn($row) => $row->plan_name ?? '-')
            ->addColumn('product_name', fn($row) => $row->product_name ?? '-')
            ->addColumn('doctor_prescription', fn($row) =>
            $row->doctor_prescription
                ? '<a href="' . asset('/storage/' . $row->doctor_prescription) . '" target="_blank" class="btn btn-sm btn-primary">View</a>'
                : '-')
            ->addColumn('medical_bill', fn($row) =>
            $row->medical_bill
                ? '<a href="' . asset('/storage/' . $row->medical_bill) . '" target="_blank" class="btn btn-sm btn-info">View</a>'
                : '-')
            ->addColumn('lab_bill', fn($row) =>
            $row->lab_bill
                ? '<a href="' . asset('/storage/' . $row->lab_bill) . '" target="_blank" class="btn btn-sm btn-warning">View</a>'
                : '-')
            ->addColumn('other', fn($row) =>
            $row->other
                ? '<a href="' . asset('/storage/' . $row->other) . '" target="_blank" class="btn btn-sm btn-warning">View</a>'
                : '-')
            ->addColumn('status_action', function ($row) {

                if ($row->status === 'In Process') {
                    return '
            <button class="btn btn-success btn-sm approve-btn" data-id="' . $row->id . '">Approve</button>
            <button class="btn btn-danger btn-sm reject-btn" data-id="' . $row->id . '">Reject</button>
        ';
                }

                // Agar Approved ho
                if ($row->status === 'Approved') {
                    return '<span class="badge bg-success">Approved</span>';
                }

                // Agar Rejected ho to reason bhi show karo
                if ($row->status === 'Reject') {
                    return '
            <span class="badge bg-danger">Rejected</span>
            <br>
            <small class="text-muted">Reason: ' . ($row->rejection_reason ?? 'N/A') . '</small>
        ';
                }

                return '';
            })

            ->addColumn('send_sms', function ($row) {
                return '<button class="btn btn-info btn-sm send-sms-btn"
                data-id="' . $row->id . '"
                data-msisdn="' . $row->msisdn . '">
                Send SMS
            </button>';
            })
            ->addColumn('edit_amount', function ($row) {
                return $row->status !== 'Reject'
                    ? '<button class="btn btn-primary btn-sm edit-amount-btn" data-id="' . $row->id . '" data-amount="' . $row->claim_amount . '">Update Claim Amount</button>'
                    : '';
            })

            ->addColumn('pending_case_remarks', function ($row) {
                return $row->pending_case_remarks
                    ? $row->pending_case_remarks
                    : '-';
            })

            ->addColumn('pending_case_action', function ($row) {
                return '<button class="btn btn-warning btn-sm edit-pending-btn"
                data-id="' . $row->id . '"
                data-remarks="' . $row->pending_case_remarks . '">
                Update Remarks
            </button>';
            })


            ->rawColumns(['id', 'doctor_prescription', 'medical_bill', 'lab_bill', 'status_action', 'edit_amount', 'other', 'send_sms', 'pending_case_action'])
            ->make(true);
    }

  public function Agentexport(Request $request)
    {
        $query = Claim::select([
            'claims.*',
            'plans.plan_name',
            'products.product_name',
        ])
         ->where('claims.agent_upload', '1')
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
            'Claim_id',
            'MSISDN',
            'Plan Name',
            'Product Name',
            'Status',
            'Date',
            'Amount',
            'Type',
            'History Name',
            'Doctor Prescription',
            'Medical Bill',
            'Lab Bill',
            'Other',
            'Claim Amount',
            'Existing Amount',
            'Remaining Amount',
            'send_sms',
        ];

        // Build rows
        $rows = [];
        $rows[] = $headers;

        foreach ($data as $item) {
            $rows[] = [
                $item->id,
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
                $item->last_sms,
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

        //dd($request->all());
        $request->validate([
            'claim_id' => 'nullable|string',
            'status' => 'required|in:Approved,Reject',
            'rejection_reason' => 'required_if:status,Reject',
            'other_reason' => 'nullable|string',
        ]);

        $claim = Claim::find($request->claim_id);
        $claim->status = $request->status;

        //dd($claim);

        if ($request->status === 'Reject') {
            $reason = $request->rejection_reason === 'Other'
                ? $request->other_reason
                : $request->rejection_reason;

            $claim->rejection_reason = $reason;
        }

        $claim->save();

        // Build SMS message
        $claimRef = "CLM{$claim->id}";

        //    dd($claim);
        $msisdn = $claim->msisdn;
        $message = '';

        if ($claim->status === 'Approved') {
            $message = "Claim Approved:\nYour claim (Ref: {$claimRef}) has been approved. Thank you!";
            $settlementMessage = "Claim Settled:\nYour claim (Ref: {$claimRef}) has been settled. Thank you!";

            Http::withHeaders([
                'Authorization' => 'Bearer XXXXAAA489SMSTOKEFU',
                'Channelcode' => 'ITS',
            ])->post('http://api.efulife.com/itssr/its_sendsms', [
                'MobileNo' => $msisdn,
                'sender' => '98902',
                'SMS' => $message,
            ]);

            Http::withHeaders([
                'Authorization' => 'Bearer XXXXAAA489SMSTOKEFU',
                'Channelcode' => 'ITS',
            ])->post('http://api.efulife.com/itssr/its_sendsms', [
                'MobileNo' => $msisdn,
                'sender' => '98902',
                'SMS' => $settlementMessage,
            ]);
        } elseif ($claim->status === 'Reject') {
            $reasonText = $claim->rejection_reason;
            $message = "Claim Rejected:\nYour claim (Ref: {$claimRef}) has been declined.\nReason: {$reasonText}\nFor assistance, contact 042-111-333-033.";

            Http::withHeaders([
                'Authorization' => 'Bearer XXXXAAA489SMSTOKEFU',
                'Channelcode' => 'ITS',
            ])->post('http://api.efulife.com/itssr/its_sendsms', [
                'MobileNo' => $msisdn,
                'sender' => '98902',
                'SMS' => $message,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status updated and SMS sent successfully.'
        ]);
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


    public function updatePendingcase(Request $request)
    {
        $request->validate([
            'claim_id' => 'required|exists:claims,id',
            'pending_case_remarks' => 'required',
        ]);

        $claim = Claim::find($request->claim_id);
        $claim->pending_case_remarks = $request->pending_case_remarks;
        $claim->save();

        return response()->json(['message' => 'pending case remarks updated successfully.']);
    }




    public function index()
    {

        return view('agent.customerInformation.index');
    }

    public function search(Request $request)
    {
        $msisdn = $request->input('msisdn');

        $customers = CustomerSubscription::with(['companyProfiles', 'products', 'plan', 'teleSalesAgent'])
            ->where('policy_status', 1)
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

            // ? Send SMS notification
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

    public function UploadClaim(Request $request)
    {

        // dd($request->all());
        try {
            // Validate incoming request data
            $request->validate([
                'msisdn' => 'required',
                'claim_amount' => 'required',
                'agent_id' => 'required',
                'type' => 'required|in:hospitalization,medical_and_lab_expense',
                'doctor_prescription' => 'nullable|array',
                'medical_bill' => 'nullable|array',
                'lab_bill' => 'nullable|array',
                'other' => 'nullable|array',
                'plan_id' => 'required',

            ]);

            // Check if the claim msisdn exists in the CustomerSubscription table
            $claim_msisdn = CustomerSubscription::where('plan_id', $request->plan_id)
                ->where('subscriber_msisdn', $request->msisdn)
                ->where('policy_status', 1)
                ->first();

            if (!$claim_msisdn) {
                return response()->json(['message' => 'Claim msisdn not found'], 404);
            }

            $amount = $claim_msisdn->transaction_amount;
            $plan_id = $claim_msisdn->plan_id;
            $product_id = $claim_msisdn->productId;
            $subscription_id = $claim_msisdn->subscription_id;

//dd($amount);
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
                'agent_id' => $request->agent_id,
                'sub_id' => $subscription_id,
                'agent_upload' => 1,
            ], $claimData));



            return response()->json(['message' => 'Claim submitted successfully', 'data' => $claim], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation Error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }


    public function indexclaim(Request $request)
    {
        return view('super_agent_Interested.uploadclaim');
    }




    public function indexclaimcsv(Request $request)
    {
        return view('super_agent_Interested.uploadclaimsfile');
    }

    public function downloadDummyCsv()
    {
        $filename = "claim_dummy.csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, ['msisdn', 'channel_name']);

            // Dummy rows
            for ($i = 1; $i <= 5; $i++) {
                fputcsv($file, ['0300123456' . $i, 'Telesales']);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function bulkUpload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        $file = fopen($request->file('csv_file')->getRealPath(), 'r');

        $header = fgetcsv($file); // skip header

        $total = 0;
        $success = 0;
        $failed = 0;
        $errors = [];

        while (($row = fgetcsv($file)) !== false) {
            $total++;

            $msisdn = trim($row[0]);
            $channel = trim($row[1]);

            if (!$msisdn || !$channel) {
                $failed++;
                $errors[] = [
                    'msisdn' => $msisdn,
                    'reason' => 'MSISDN or channel missing'
                ];
                continue;
            }

            $subscription = CustomerSubscription::whereIn('plan_id', [1, 4, 5])
                ->where('subscriber_msisdn', $msisdn)
                ->where('policy_status', 1)
                ->first();

            if (!$subscription) {
                $failed++;
                $errors[] = [
                    'msisdn' => $msisdn,
                    'reason' => 'Active subscription not found'
                ];
                continue;
            }

            try {
                Claim::create([
                    'msisdn' => $msisdn,
                    'plan_id' => $subscription->plan_id,
                    'product_id' => $subscription->productId,
                    'amount' => $subscription->transaction_amount,
                    'status' => 'In Process',
                    'type' => 'hospitalization',
                    'chanel_name' => $channel,
                    'history_name' => 'Hospital',
                    'date' => now(),
                ]);

                $success++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = [
                    'msisdn' => $msisdn,
                    'reason' => $e->getMessage()
                ];
            }
        }

        fclose($file);

        return response()->json([
            'total' => $total,
            'success' => $success,
            'failed' => $failed,
            'errors' => $errors
        ]);
    }


    public function indexstatus(Request $request)
    {
        return view('super_agent_Interested.claimstatusindexclaimcsv');
    }
    public function downloadStatusDummyCsv()
    {
        $filename = "claim_status_dummy.csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // CSV header
            fputcsv($file, ['claim_id', 'amount']);

            // Dummy IDs
            fputcsv($file, [101, 500]);
            fputcsv($file, [102, 1000]);
            fputcsv($file, [103, 2000]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function bulkStatusUpdate(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        $file = fopen($request->file('csv_file')->getRealPath(), 'r');

        $header = fgetcsv($file); // skip header

        $total = 0;
        $success = 0;
        $failed = 0;
        $errors = [];

        while (($row = fgetcsv($file)) !== false) {
            $total++;

            $claimId = trim($row[0]);
            $amount = trim($row[1]);

            if (!$claimId) {
                $failed++;
                $errors[] = [
                    'claim_id' => null,
                    'reason' => 'Claim ID missing'
                ];
                continue;
            }

            $claim = Claim::where('id', $claimId)->first();

            if (!$claim) {
                $failed++;
                $errors[] = [
                    'claim_id' => $claimId,
                    'reason' => 'Claim not found'
                ];
                continue;
            }

            if ($claim->status === 'Approved') {
                $failed++;
                $errors[] = [
                    'claim_id' => $claimId,
                    'reason' => 'Already approved'
                ];
                continue;
            }

            try {
                $claim->update([
                    'status' => 'Approved',
                    'claim_amount' => $amount,

                ]);

                $success++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = [
                    'claim_id' => $claimId,
                    'reason' => $e->getMessage()
                ];
            }
        }

        fclose($file);

        return response()->json([
            'total' => $total,
            'success' => $success,
            'failed' => $failed,
            'errors' => $errors
        ]);
    }




      public function sendcustomersms()
    {
        return view('super_agent_Interested.sendcustomersms');
    }

  public function message(Request $request)
{

// dd($request->all());
    try {

        // ✅ Validate input
        $request->validate([
            'msisdn' => 'required|string',
            'plan_name' => 'required|string',
        ]);

        $msisdn = $request->msisdn;
        $planName = $request->plan_name;

        // ✅ Message
        $message = "Please fill the claim upload form. Your plan name is {$planName}. Use this link: " . route('customer.claims.upload.index');

        // ✅ Encryption config
        $key    = 'mYjC!nc3dibleY3k';
        $iv     = 'Myin!tv3ctorjCM@';
        $cipher = 'AES-128-CBC';

        $payload = [
            'msisdn'      => $msisdn,
            'content'     => $message,
            'referenceId' => uniqid(),
        ];

        $jsonData = json_encode($payload);

        // ✅ Encrypt
        $encryptedBinary = openssl_encrypt(
            $jsonData,
            $cipher,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if (!$encryptedBinary) {
            return response()->json([
                'status' => false,
                'message' => 'Encryption failed',
            ], 500);
        }

        $encryptedHex = bin2hex($encryptedBinary);

        $requestBody = json_encode([
            'data' => $encryptedHex
        ]);

        // ✅ CURL REQUEST
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://gateway.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/notification');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-CLIENT-ID: 946658113e89d870aad2e47f715c2b72',
            'X-CLIENT-SECRET: e5a0279efbd7bd797e472d0ce9eebb69',
            'X-PARTNER-ID: 946658113e89d870aad2e47f715c2b72',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // ✅ CURL ERROR HANDLE
        if (curl_errno($ch)) {
            return response()->json([
                'status' => false,
                'message' => 'CURL Error',
                'error' => curl_error($ch),
            ], 500);
        }

        curl_close($ch);

        $decodedResponse = json_decode($response, true);

        // ❌ API FAIL
        if ($httpCode != 200) {
            return response()->json([
                'status' => false,
                'message' => 'SMS API Failed',
                'http_code' => $httpCode,
                'api_response' => $decodedResponse ?? $response,
            ], 500);
        }

        // ✅ SUCCESS
        return response()->json([
            'status' => true,
            'message' => 'SMS sent successfully',
            'http_code' => $httpCode,
            'api_response' => $decodedResponse ?? $response,
        ]);

    } catch (\Exception $e) {

        // ❌ GENERAL ERROR
        return response()->json([
            'status' => false,
            'message' => 'Server Exception',
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
        ], 500);
    }
}

     public function customerclaimsuplaodindex()
    {
        return view('super_agent_Interested.customeruploadform');
    }

    public function customerclaimsuplaodstore(Request $request)
    {

       // dd($request->all());
        try {
            // Validate incoming request data
            $request->validate([
                'msisdn' => 'required',
                'claim_amount' => 'required',
                'agent_id' => 'required',
                'type' => 'required|in:hospitalization,medical_and_lab_expense',
                'doctor_prescription' => 'nullable|array',
                'medical_bill' => 'nullable|array',
                'lab_bill' => 'nullable|array',
                'other' => 'nullable|array',
                'plan_id' => 'required',

            ]);

            // Check if the claim msisdn exists in the CustomerSubscription table
            $claim_msisdn = CustomerSubscription::where('plan_id', $request->plan_id)
                ->where('subscriber_msisdn', $request->msisdn)
                ->where('policy_status', 1)
                ->first();

            if (!$claim_msisdn) {
                return response()->json(['message' => 'Claim msisdn not found'], 404);
            }

            $amount = $claim_msisdn->transaction_amount;
            $plan_id = $claim_msisdn->plan_id;
            $product_id = $claim_msisdn->productId;
            $subscription_id = $claim_msisdn->subscription_id;


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
                'agent_id' => $request->agent_id,
                'sub_id' => $subscription_id,
                'agent_upload' => 2,
            ], $claimData));



            return response()->json(['message' => 'Claim submitted successfully', 'data' => $claim], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation Error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

   public function CustomershowClaimIndex()
    {
        return view('super_agent_Interested.customerclaimindex');
    }


    public function CustomergetClaimsData(Request $request)
    {
        $query = \App\Models\Claim::select([
            'claims.*',
            'plans.plan_name',
            'products.product_name',
        ])
            ->where('claims.agent_upload', '2')
            ->leftJoin('plans', 'claims.plan_id', '=', 'plans.plan_id')
            ->leftJoin('products', 'claims.product_id', '=', 'products.product_id');

        // Date filter
        if ($request->filled('dateFilter')) {
            $dates = explode(' to ', $request->dateFilter);
            if (count($dates) === 2) {
                $query->whereBetween('claims.date', [$dates[0], $dates[1]]);
            }
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('claims.status', $request->status);
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('claims.type', 'LIKE', '%' . $request->type . '%');
        }

        return DataTables::of($query)
            ->addColumn('id', fn($row) => isset($row->id) ? "CLM{$row->id}" : '-')
            ->addColumn('plan_name', fn($row) => $row->plan_name ?? '-')
            ->addColumn('product_name', fn($row) => $row->product_name ?? '-')
            ->addColumn('doctor_prescription', fn($row) =>
            $row->doctor_prescription
                ? '<a href="' . asset('/storage/' . $row->doctor_prescription) . '" target="_blank" class="btn btn-sm btn-primary">View</a>'
                : '-')
            ->addColumn('medical_bill', fn($row) =>
            $row->medical_bill
                ? '<a href="' . asset('/storage/' . $row->medical_bill) . '" target="_blank" class="btn btn-sm btn-info">View</a>'
                : '-')
            ->addColumn('lab_bill', fn($row) =>
            $row->lab_bill
                ? '<a href="' . asset('/storage/' . $row->lab_bill) . '" target="_blank" class="btn btn-sm btn-warning">View</a>'
                : '-')
            ->addColumn('other', fn($row) =>
            $row->other
                ? '<a href="' . asset('/storage/' . $row->other) . '" target="_blank" class="btn btn-sm btn-warning">View</a>'
                : '-')
            ->addColumn('status_action', function ($row) {

                if ($row->status === 'In Process') {
                    return '
            <button class="btn btn-success btn-sm approve-btn" data-id="' . $row->id . '">Approve</button>
            <button class="btn btn-danger btn-sm reject-btn" data-id="' . $row->id . '">Reject</button>
        ';
                }

                // Agar Approved ho
                if ($row->status === 'Approved') {
                    return '<span class="badge bg-success">Approved</span>';
                }

                // Agar Rejected ho to reason bhi show karo
                if ($row->status === 'Reject') {
                    return '
            <span class="badge bg-danger">Rejected</span>
            <br>
            <small class="text-muted">Reason: ' . ($row->rejection_reason ?? 'N/A') . '</small>
        ';
                }

                return '';
            })

            ->addColumn('send_sms', function ($row) {
                return '<button class="btn btn-info btn-sm send-sms-btn"
                data-id="' . $row->id . '"
                data-msisdn="' . $row->msisdn . '">
                Send SMS
            </button>';
            })
            ->addColumn('edit_amount', function ($row) {
                return $row->status !== 'Reject'
                    ? '<button class="btn btn-primary btn-sm edit-amount-btn" data-id="' . $row->id . '" data-amount="' . $row->claim_amount . '">Update Claim Amount</button>'
                    : '';
            })

            ->addColumn('pending_case_remarks', function ($row) {
                return $row->pending_case_remarks
                    ? $row->pending_case_remarks
                    : '-';
            })

            ->addColumn('pending_case_action', function ($row) {
                return '<button class="btn btn-warning btn-sm edit-pending-btn"
                data-id="' . $row->id . '"
                data-remarks="' . $row->pending_case_remarks . '">
                Update Remarks
            </button>';
            })


            ->rawColumns(['id', 'doctor_prescription', 'medical_bill', 'lab_bill', 'status_action', 'edit_amount', 'other', 'send_sms', 'pending_case_action'])
            ->make(true);
    }





}
