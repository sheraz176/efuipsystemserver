<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuperDash extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table= 'super_dash';
    protected $fillable = ['id',
                            'totalTsm',
                            'activeTsm',
                            'totalTsmWfh',
                            'activeTsmWfh',
                            'totalIbex',
                            'activeIbex',
                            'totalAbacus',
                            'activeAbacus',
                            'totalSybrid',
                            'activeSybrid',
                            'totalJazzIVR',
                            'activeJazzIVR',
                            'totalactive',
                            'totallive',
                            'netentrollmentrevinus',
                            'todaySubscriptionCount',
                            'currentMonthSubscriptionCount',
                            'currentYearSubscriptionCount',
                            'NetEnrollmentCount',
                            'dailyTransactionSum',
                            'monthlyTransactionSum',
                            'yearlyTransactionSum',
                            'TotalRecusiveChargingCount',
                            'TodayRecusiveChargingCount',
                            'LastMonthRecusiveChargingCount',
                            'TodaySubscriptionsCount',
                            'TotalSubscriptionCount',
                            'TotalCount',
                            'totalWaada',
                            'activeWaadaIVR',
                        ];
}
