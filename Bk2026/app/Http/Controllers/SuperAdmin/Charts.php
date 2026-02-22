<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Subscription\CustomerSubscription;
use Illuminate\Support\Facades\DB; // Add this line at the beginning of your file
use App\Models\TeleSalesAgent;
use App\Models\AgentCount;
use App\Models\RecusiveChargingData;
use App\Models\ConsentData;
use Carbon\CarbonInterval;


class Charts extends Controller
{

    public function getSubscriptionChartData(Request $request)
{
    $timeRange = $request->input('time_range');
    $labels = [];
    $values = [];
    $interval = 'day';

    switch ($timeRange) {
        case 'today':
            $start = Carbon::today();
            $end = Carbon::today()->endOfDay();
            $interval = 'hour';
            break;

        case 'yesterday':
            $start = Carbon::yesterday()->startOfDay();
            $end = Carbon::yesterday()->endOfDay();
            $interval = 'hour';
            break;

        case 'this_year':
            $year = Carbon::now()->year;
            $data = CustomerSubscription::whereYear('subscription_time', $year)
                ->selectRaw("DATE_FORMAT(subscription_time, '%m-%Y') as label, COUNT(*) as count")
                ->groupBy('label')
                ->get();

            // Generate monthly labels
            for ($month = 1; $month <= 12; $month++) {
                $monthLabel = str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . $year;
                $labels[] = $monthLabel;
                $values[$monthLabel] = 0;
            }

            // Fill in counts
            foreach ($data as $subscription) {
                $values[$subscription->label] = $subscription->count;
            }

            return response()->json([
                'labels' => array_keys($values),
                'values' => array_values($values)
            ]);

        case 'last_7_days':
            $start = Carbon::now()->subDays(6)->startOfDay(); // include today
            $end = Carbon::now()->endOfDay();
            break;

        case 'last_30_days':
            $start = Carbon::now()->subDays(29)->startOfDay(); // include today
            $end = Carbon::now()->endOfDay();
            break;

        case 'current_month':
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
            break;

        case 'last_month':
            $start = Carbon::now()->subMonth()->startOfMonth();
            $end = Carbon::now()->subMonth()->endOfMonth();
            break;

        default:
            return response()->json(['error' => 'Invalid time range']);
    }

    // Fetch counts from DB
    $dateFormat = $interval === 'hour' ? '%Y-%m-%d %H:00:00' : '%Y-%m-%d';
    $data = CustomerSubscription::whereBetween('subscription_time', [$start, $end])
        ->selectRaw("DATE_FORMAT(subscription_time, '$dateFormat') as label, COUNT(*) as count")
        ->groupBy('label')
        ->get();

    // Initialize full range
    $current = $start->copy();
    while ($current <= $end) {
        $label = $current->format($interval === 'hour' ? 'Y-m-d H:00:00' : 'Y-m-d');
        $labels[] = $label;
        $values[$label] = 0;
        $current->add($interval === 'hour' ? CarbonInterval::hour() : CarbonInterval::day());
    }

    // Fill values
    foreach ($data as $item) {
        $values[$item->label] = $item->count;
    }

    return response()->json([
        'labels' => array_keys($values),
        'values' => array_values($values)
    ]);
}








 public function getMonthlyActiveSubscriptionChartData()
{
    // Fetch month-wise subscription counts for the current year
    $monthlyData = CustomerSubscription::where('policy_status', "1")
        ->whereBetween('subscription_time', [
            now()->startOfYear(),
            now()->endOfYear()
        ])
        ->selectRaw('MONTH(subscription_time) as month, COUNT(*) as count')
        ->groupBy('month')
        ->pluck('count', 'month'); // pluck into [month => count] format

    $labels = [];
    $values = [];

    // Loop through all months (1 to 12)
    foreach (range(1, 12) as $month) {
        $labels[] = Carbon::create()->month($month)->format('F'); // Month name
        $values[] = $monthlyData->get($month, 0); // Get count or default 0
    }

    return response()->json([
        'labels' => $labels,
        'values' => $values
    ]);
}



public function getMonthlySubscriptionUnsubscriptionChartData()
{
    $currentYear = now()->year;

    // Fetch monthly grouped subscription and unsubscription counts in one query
    $data = CustomerSubscription::selectRaw('
            MONTH(subscription_time) as month,
            COUNT(CASE WHEN policy_status = "1" THEN 1 END) as subscriptions,
            COUNT(CASE WHEN policy_status = "0" THEN 1 END) as unsubscriptions
        ')
        ->whereBetween('subscription_time', [
            Carbon::create($currentYear, 1, 1)->startOfDay(),
            Carbon::create($currentYear, 12, 31)->endOfDay()
        ])
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->keyBy('month');

    $labels = [];
    $subscriptionValues = [];
    $unsubscriptionValues = [];

    // Fill chart data for all 12 months (even if there's no record for that month)
    for ($month = 1; $month <= 12; $month++) {
        $labels[] = Carbon::create()->month($month)->format('F');
        $subscriptionValues[] = $data->has($month) ? $data[$month]->subscriptions : 0;
        $unsubscriptionValues[] = $data->has($month) ? $data[$month]->unsubscriptions : 0;
    }

    return response()->json([
        'labels' => $labels,
        'subscriptions' => $subscriptionValues,
        'unsubscriptions' => $unsubscriptionValues,
    ]);
}


public function getChartData(Request $request)
{
    $companyId = $request->input('company_id');
    $timePeriod = $request->input('time_period', 'daily');

    $query = CustomerSubscription::where('policy_status', "1");

    if ($companyId) {
        $query->where('company_id', $companyId);
    }

    // Determine the time grouping and data filtering based on the time period
    switch ($timePeriod) {
        case 'daily':
            // Filter data for the current year and group by date
            $query->whereYear('created_at', now()->year)
                  ->selectRaw('DATE(created_at) as period, COUNT(*) as count');
            break;

        case 'monthly':
            // Filter data for the current year and group by month
            $query->whereYear('created_at', now()->year)
                  ->selectRaw('MONTH(created_at) as period, COUNT(*) as count');
            break;

        case 'last7days':
            $query->where('created_at', '>=', now()->subDays(7))
                  ->selectRaw('DATE(created_at) as period, COUNT(*) as count');
            break;

        case 'yearly':
            $query->selectRaw('YEAR(created_at) as period, COUNT(*) as count');
            break;

        case 'hourly':
            $query->selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d %H:00") as period, COUNT(*) as count');
            break;
    }

    $data = $query->groupBy('period')
                  ->orderBy('period')
                  ->get();

    // Map labels based on time period
    $labels = $data->map(function ($item) use ($timePeriod) {
        switch ($timePeriod) {
            case 'daily':
            case 'last7days':
                return Carbon::parse($item->period)->format('Y-m-d'); // Format as YYYY-MM-DD

            case 'monthly':
                return Carbon::create()->month($item->period)->format('F'); // Full month name (e.g., January)

            case 'yearly':
                return $item->period; // Year

            case 'hourly':
                return Carbon::parse($item->period)->format('Y-m-d H:00'); // Format as YYYY-MM-DD HH:00
        }
    });

    $counts = $data->pluck('count');

    return response()->json([
        'data' => [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Net Enrollment',
                    'data' => $counts,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1
                ]
            ]
        ]
    ]);
}



public function getLineChartData(Request $request)
{
    $companyId = $request->input('company_id');
    $currentTime = now(); // Get current time

    // Query for hourly MSISDN (sales) data
    $query = CustomerSubscription::selectRaw("
        DATE_FORMAT(DATE_ADD(created_at, INTERVAL 1 HOUR), '%Y-%m-%d %H:00:00') as hour, -- Shift to next hour
        COUNT(subscriber_msisdn) as total_msisdn -- Count sales (MSISDN) for each hour
    ")
    ->whereDate('created_at', now()->format('Y-m-d')) // Filter for today's date
    ->where('created_at', '<', $currentTime) // Only include data for completed hours
    ->where('policy_status', "1");

    if ($companyId) {
        $query->where('company_id', $companyId);
    }

    $query->groupBy('hour')
          ->orderBy('hour');

    $results = $query->get();

    // Prepare the data
    $data = [
        'labels' => [],
        'total_msisdn' => [], // Total MSISDN per hour
        'total_avg' => [],    // Total average per hour
        'total_present_agent' => [], // Live agent count per hour
        'gross_productivity' => 0 // Initialize gross productivity
    ];

    $cumulativeMsisdn = 0; // To hold the cumulative MSISDN count
    $totalAvgSum = 0;      // To sum the total averages

    foreach ($results as $row) {
        // Fetch the latest agent count for the hour
        $agentCount = AgentCount::selectRaw("
            DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') as hour,
            count
        ")
        ->where('company_id', $companyId)
        ->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') = ?", [$row->hour])
        ->first();

        // Total present agents for the hour
        $totalPresentAgent = $agentCount ? $agentCount->count : 0;

        // Only proceed if total present agents is greater than 0
        if ($totalPresentAgent > 0) {
            $data['labels'][] = $row->hour;
            $data['total_msisdn'][] = $row->total_msisdn;
            $data['total_present_agent'][] = $totalPresentAgent;

            // Calculate total average (MSISDN per present agent)
            $totalAvg = round(($row->total_msisdn / $totalPresentAgent), 2);
            $data['total_avg'][] = $totalAvg;

            // Sum up total averages for Gross Productivity
            $totalAvgSum += $totalAvg;
        }
    }

    // Set Gross Productivity (sum of averages)
    $data['gross_productivity'] = $totalAvgSum;

    return response()->json($data);
}


public function RecusiveChargingChart(Request $request)
{
    $query = RecusiveChargingData::query();

    // Apply filter for cps_response (Cause)
    if ($request->has('cause') && $request->cause != '') {
        $query->where('cps_response', $request->cause);
    }

    // Initialize an empty array to store chart data
    $chartData = [];

    // Apply time period filter
    if ($request->has('time_period')) {
        $timePeriod = $request->time_period;

        // Handle different time periods
        if ($timePeriod == 'today') {
            $totalCount = $query->whereDate('created_at', now()->toDateString())
                                ->count();
            $chartData[] = ['count' => $totalCount, 'label' => 'Today']; // Set label as 'Today'
        } elseif ($timePeriod == 'monthly') {
            $chartData = $query->whereYear('created_at', now()->year) // Filter for the current year
                               ->selectRaw('COUNT(*) as count, MONTHNAME(created_at) as label')
                               ->groupBy('label')
                               ->orderByRaw('MONTH(created_at)')
                               ->get();
        } elseif ($timePeriod == 'last7days') {
            $chartData = $query->whereBetween('created_at', [now()->subDays(7), now()])
                               ->selectRaw('COUNT(*) as count, DAYNAME(created_at) as label')
                               ->groupBy('label')
                               ->get();
        } elseif ($timePeriod == 'yearly') {
            $chartData = $query->selectRaw('COUNT(*) as count, YEAR(created_at) as label')
                               ->groupBy('label')
                               ->orderBy('label')
                               ->get();
        }
    } else {
        // Default to return data for today if no time filter is provided
        $totalCount = $query->whereDate('created_at', now()->toDateString())
                            ->count();
        $chartData[] = ['count' => $totalCount, 'label' => 'Today']; // Set label as 'Today'
    }

    // Return the data as JSON for chart rendering
    return response()->json($chartData);
}



public function LowBalaceChart(Request $request)
{
    $query = ConsentData::query();

    // Filter by company
    if ($request->filled('company_id')) {
        $query->where('company_id', $request->company_id);
    }

    // Filter by cause/status
    if ($request->filled('cause')) {
        $query->where('status', $request->cause);
    }

    $chartData = [];

    $timePeriod = $request->input('time_period', 'today');

    switch ($timePeriod) {
        case 'today':
            $count = $query->whereDate('created_at', now())->count();
            $chartData[] = ['label' => 'Today', 'count' => $count];
            break;

        case 'monthly':
            $data = $query->whereYear('created_at', now()->year)
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month');

            foreach (range(1, 12) as $month) {
                $chartData[] = [
                    'label' => Carbon::create()->month($month)->format('F'),
                    'count' => $data->has($month) ? $data[$month]->count : 0,
                ];
            }
            break;

        case 'last7days':
            $start = now()->subDays(6)->startOfDay();
            $end = now()->endOfDay();

            $data = $query->whereBetween('created_at', [$start, $end])
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');

            $days = [];
            for ($i = 0; $i < 7; $i++) {
                $date = $start->copy()->addDays($i)->toDateString();
                $label = Carbon::parse($date)->format('D'); // Mon, Tue, etc.
                $chartData[] = [
                    'label' => $label,
                    'count' => $data->has($date) ? $data[$date]->count : 0,
                ];
            }
            break;

        case 'yearly':
            $data = $query->selectRaw('YEAR(created_at) as year, COUNT(*) as count')
                ->groupBy('year')
                ->orderBy('year')
                ->get();

            foreach ($data as $item) {
                $chartData[] = [
                    'label' => (string)$item->year,
                    'count' => $item->count,
                ];
            }
            break;

        default:
            $count = $query->whereDate('created_at', now())->count();
            $chartData[] = ['label' => 'Today', 'count' => $count];
            break;
    }

    return response()->json($chartData);
}








    /////////////////////
    // public function getSubscriptionChartData($timeFrame)
    // {
    //     $data = [];

    //     switch ($timeFrame) {
    //         case 'Today':
    //             $data = $this->getChartDataForToday();
    //             break;
    //         case 'Yesterday':
    //             $data = $this->getChartDataForYesterday();
    //             break;
    //         case 'Last 7 Days':
    //             $data = $this->getChartDataForLast7Days();
    //             break;
    //         case 'Last 30 Days':
    //             $data = $this->getChartDataForLast30Days();
    //             break;
    //         case 'Current Month':
    //             $data = $this->getChartDataForCurrentMonth();
    //             break;
    //         case 'Last Month':
    //             $data = $this->getChartDataForLastMonth();
    //             break;
    //         default:
    //             break;
    //     }

    //     return response()->json($data);
    // }

    // // Function to get chart data for Today
    // private function getChartDataForToday()
    // {
    //     $startDate = Carbon::today();
    //     $endDate = Carbon::tomorrow();

    //     return $this->fetchChartData($startDate, $endDate);
    // }

    // // Function to get chart data for Yesterday
    // private function getChartDataForYesterday()
    // {
    //     $startDate = Carbon::yesterday();
    //     $endDate = Carbon::today();

    //     return $this->fetchChartData($startDate, $endDate);
    // }

    // // Function to get chart data for Last 7 Days
    // private function getChartDataForLast7Days()
    // {
    //     $startDate = Carbon::today()->subDays(6);
    //     $endDate = Carbon::tomorrow();

    //     return $this->fetchChartData($startDate, $endDate);
    // }

    // // Function to get chart data for Last 30 Days
    // private function getChartDataForLast30Days()
    // {
    //     $startDate = Carbon::today()->subDays(29);
    //     $endDate = Carbon::tomorrow();

    //     return $this->fetchChartData($startDate, $endDate);
    // }

    // // Function to get chart data for Current Month
    // private function getChartDataForCurrentMonth()
    // {
    //     $startDate = Carbon::now()->startOfMonth();
    //     $endDate = Carbon::now()->endOfMonth();

    //     return $this->fetchChartData($startDate, $endDate);
    // }

    // // Function to get chart data for Last Month
    // private function getChartDataForLastMonth()
    // {
    //     $startDate = Carbon::now()->subMonth()->startOfMonth();
    //     $endDate = Carbon::now()->subMonth()->endOfMonth();

    //     return $this->fetchChartData($startDate, $endDate);
    // }

    // // Function to fetch chart data based on given date range
    // private function fetchChartData($startDate, $endDate)
    // {
    //     $data = CustomerSubscription::whereBetween('subscription_time', [$startDate, $endDate])
    //         ->selectRaw('MONTH(subscription_time) as month, COUNT(*) as count')
    //         ->groupBy('month')
    //         ->get();

    //     $labels = [];
    //     $values = [];

    //     for ($month = 1; $month <= 12; $month++) {
    //         $monthData = $data->where('month', $month)->first();
    //         $labels[] = Carbon::create()->month($month)->format('F');
    //         $values[] = $monthData ? $monthData->count : 0;
    //     }

    //     return ['labels' => $labels, 'values' => $values];
    // }
}
