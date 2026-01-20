<?php

namespace App\Http\Controllers\SuperAgentInterested;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Claim;
use DB;
use Illuminate\Support\Facades\Http;

class SuperAgentDashboardControllerInterested extends Controller
{



public function index()
{
    $totalClaims = Claim::count();

    $approved = Claim::where('status', 'Approved')->count();
    $rejected = Claim::where('status', 'Reject')->count();
    $pending  = Claim::where('status', 'In Process')->count();

    // Average Upload to Decision (Days)
    $avgTat = Claim::whereIn('status', ['Approved', 'Reject'])
        ->select(DB::raw('AVG(DATEDIFF(updated_at, created_at)) as avg_days'))
        ->value('avg_days');

    // Status Donut
    $statusData = Claim::select('status', DB::raw('count(*) as total'))
        ->groupBy('status')
        ->pluck('total', 'status');

    // Line chart (daily avg TAT)
    $tatChart = Claim::whereIn('status', ['Approved', 'Reject'])
        ->select(
            DB::raw('DATE(created_at) as day'),
            DB::raw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
        )
        ->groupBy('day')
        ->orderBy('day')
        ->get();

    // Rejection reasons
    $rejectionReasons = Claim::where('status', 'Reject')
        ->select('rejection_reason', DB::raw('count(*) as total'))
        ->groupBy('rejection_reason')
        ->get();

    return view('super_agent_Interested.dashboard', compact(
        'totalClaims',
        'approved',
        'rejected',
        'pending',
        'avgTat',
        'statusData',
        'tatChart',
        'rejectionReasons'
    ));
}


}
