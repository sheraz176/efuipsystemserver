<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Plans\ProductModel;
use App\Models\Plans\PlanModel;

class RecusiveChargingData extends Model
{
    use HasFactory;
    protected $table= 'recusive_charging_data';
       
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
    ];


    public function plan()
    {
        return $this->belongsTo(PlanModel::class, 'planId');
    }

    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id');
    }

    public function plans()
    {
        return $this->belongsTo(PlanModel::class,'plan_id');
    }

}
