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
