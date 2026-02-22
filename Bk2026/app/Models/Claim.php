<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Plans\ProductModel;
use App\Models\Plans\PlanModel;

class Claim extends Model
{
    protected $table = 'claims';
    use HasFactory;

    protected $fillable = [
        'msisdn',
        'plan_id',
        'product_id',
        'status',
        'date',
        'amount',
        'type',
        'history_name',
        'doctor_prescription',
        'medical_bill',
        'lab_bill',
        'other',
        'claim_amount',
        'existingamount',
        'remaining_amount',
        'agent_id',
        'chanel_name',
    ];


    public function plan()
    {
        return $this->belongsTo(PlanModel::class, 'plan_id');
    }



    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id');
    }



}
