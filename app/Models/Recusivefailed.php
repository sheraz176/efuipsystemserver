<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recusivefailed extends Model
{


       use HasFactory;

       protected $table= 'Recusive_failed';


   protected $fillable = [
        'subscription_id',
        'tid',
        'reference_id',
        'amount',
        'plan_id',
        'product_id',
        'cps_response',
        'charging_date',
        'customer_msisdn',
        'duration',
        'daily_unique_date',
        'looping',
         'status',
    ];
}
