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
        if ($request->ajax()) {
            // Start building the query
            $query = logs::select('*')
            ->where('source', 'AutoDebitApi')
            ->where('resultCode','0')
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
        return view('superadmin.logs.super_agent_name');
    }


}
