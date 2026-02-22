<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentSalesStat extends Model
{
    use HasFactory;
   protected $table= 'agent_sales_stats';
     protected $fillable = [
        'agent_id',
        'today_sales',
        'month_sales',
        'year_sales',
        'stat_date',
        'stat_month',
        'stat_year',
    ];


}
