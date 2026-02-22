<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Plans\ProductModel;
use App\Models\Plans\PlanModel;
use App\Models\Subscription\CustomerSubscription;

class RecusiveCharging extends Model
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
    ];


    public function products()
    {
        return $this->belongsTo(ProductModel::class ,'productId');
    }
    public function planing()
    {
        return $this->belongsTo(PlanModel::class,'plan_id');
    }
    public function plan()
    {
        return $this->belongsTo(PlanModel::class, 'plan_id');
    }
    public function product()
    {
        return $this->belongsTo(ProductModel::class,'product_id');
    }
    public function customer_subscription()
    {
        return $this->belongsTo(CustomerSubscription::class ,'subscription_id');
    }
}
