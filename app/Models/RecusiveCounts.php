<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecusiveCounts extends Model
{
    use HasFactory;
     protected $table = 'recusive_counts';

      protected $fillable = [
          'date',
         'total_recursive_today',
         'success_total',
         'success_family_health',
         'success_term_life',
         'failed_total',
         'created_at',
         'updated_at',
         'success_amount_total',
         'term_life_amount',
         'family_health_amount',
          'term_life_daily_count',
           'term_life_monthly_count',
          'term_life_daily_amount',
          'term_life_monthly_amount',

                'family_health_daily_count',
           'family_health_monthly_count',
          'family_health_daily_amount',
          'family_health_monthly_amount',


    ];
}
