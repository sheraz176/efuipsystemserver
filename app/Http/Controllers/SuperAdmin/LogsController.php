<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\logs;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class LogsController extends Controller
{
    public function SuperAgentindex()
    {
        return view('superadmin.logs.super_agent_logs');
    }
    public function SuperAgentlogsData(Request $request)
    {
        if ($request->ajax()) {
            // Start building the query
            $query = logs::select('*')
            ->where('source', 'AutoDebitApi')
            ->orderBy('created_at', 'desc')
            ->get();

            return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('created_at', function($query){
                // Format the created_at field as needed
                return Carbon::parse($query->created_at)->format('m-d-y h:i A'); // Customize the format
            })
            ->rawColumns(['created_at']) // Only necessary if you are adding HTML content
            ->make(true);

        }
    }

    public function Agentindex()
    {
        return view('superadmin.logs.agent_logs');
    }
    public function AgentlogsData(Request $request)
    {
        if ($request->ajax()) {
            // Start building the query
            $query = logs::select('*')
            ->where('source', 'PaymentController')
            ->orderBy('created_at', 'desc')
            ->get();

            return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('created_at', function($query){
                // Format the created_at field as needed
                return Carbon::parse($query->created_at)->format('m-d-y h:i A'); // Customize the format
            })
            ->rawColumns(['created_at']) // Only necessary if you are adding HTML content
            ->make(true);

        }
    }

    public function bulkmanagerindex()
    {
        return view('superadmin.bulkmanager.logs');
    }
    public function bulkmanagerlogsData(Request $request)
    {
        if ($request->ajax()) {
            // Start building the query
            $query = logs::select('*')
            ->where('source', 'BulkRefundManager')
            ->orderBy('created_at', 'desc')
            ->get();


            return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('created_at', function($query){
                // Format the created_at field as needed
                return Carbon::parse($query->created_at)->format('m-d-y h:i A'); // Customize the format
            })
            ->rawColumns(['created_at']) // Only necessary if you are adding HTML content
            ->make(true);
        }
    }

    public function buttonlogsindex()
    {
       return view('superadmin.refund.logs');
    }
    public function buttonlogsData(Request $request)
    {
       if ($request->ajax()) {
        // Start building the query
          $query = logs::select('*')
          ->where('source', 'ButtonRefundManager')
          ->orderBy('created_at', 'desc')
           ->get();


           return DataTables::of($query)
           ->addIndexColumn()
           ->addColumn('created_at', function($query){
               // Format the created_at field as needed
               return Carbon::parse($query->created_at)->format('m-d-y h:i A'); // Customize the format
           })
           ->rawColumns(['created_at']) // Only necessary if you are adding HTML content
           ->make(true);
      }
    }


    public function downloadSampleCsv()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sample.csv"',
        ];

        $rows = [
            ['923115014142', '4'],
            ['923008758478', '1950'],
            // Add more rows as needed
        ];

        $callback = function () use ($rows) {
            $file = fopen('php://output', 'w');

            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function SuperAgentName(Request $request)
    {

        return view('superadmin.logs.super_agent_name');
    }

    public function SuperAgentNameAjax(Request $request)
    {
        if ($request->ajax()) {
            // Start building the query
            $query = logs::select('*')
                ->where('source', 'AutoDebitApi')
                ->where('resultCode', '0')
                ->orderBy('created_at', 'desc');

            // Apply date filter if provided
            if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
                $dateRange = explode(' to ', $request->input('dateFilter'));
                $startDate = $dateRange[0];
                $endDate = $dateRange[1];

                $query->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);
            }

            // Execute the query and get the results
            $results = $query->get();

            return DataTables::of($results)
                ->addIndexColumn()
                ->addColumn('created_at', function($query) {
                    // Format the created_at field as needed
                    return Carbon::parse($query->created_at)->format('m-d-y h:i A'); // Customize the format
                })
                ->rawColumns(['created_at']) // Only necessary if you are adding HTML content
                ->make(true);
        }
    }

    public function export(Request $request)
    {

         $query = logs::select([
            'logs.*', // Select all columns from customer_subscriptions table

        ])
        ->where('logs.source', '=', 'AutoDebitApi')
        ->where('logs.resultCode', '=', '0'); ; // Filter by policy status


        if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
            $dateRange = explode(' to ', $request->input('dateFilter'));
            $startDate = $dateRange[0];
            $endDate = $dateRange[1];

            $query->whereDate('logs.created_at', '>=', $startDate)
            ->whereDate('logs.created_at', '<=', $endDate);
        }
        $data = $query->get();
        //   dd($data);

      // Define headers
     $headers = ['logs ID', 'Msisdn', 'Super Agent Name','Date']; // Replace with your actual column names
      // Prepare the data with headers
    $rows[] = $headers;
    foreach ($data as $item) {
     $rows[] = [
        $item->id,
        $item->msisdn,
        $item->super_agent_name,
        $item->created_at,

    ];
   }

   // Generate XLS file
   $filePath = storage_path('app/SuperAgentNameReport.xls');
   $file = fopen($filePath, 'w');
   foreach ($rows as $row) {
    fputcsv($file, $row, "\t"); // Tab-delimited for Excel
    }
    fclose($file);

   // Download the file
   return response()->download($filePath)->deleteFileAfterSend(true);

    }


}
