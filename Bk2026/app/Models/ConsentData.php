<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Plans\ProductModel;
use App\Models\Plans\PlanModel;
use App\Models\Company\CompanyProfile;

class ConsentData extends Model
{
    use HasFactory;

    protected $table = 'consent_data';

    protected $fillable = [
        'id',
        'msisdn',
        'resultCode',
        'response',
        'agent_id',
        'company_id',
        'planId',
        'productId',
        'amount',
        'status',
    ];

    public function plan()
    {
        return $this->belongsTo(PlanModel::class, 'planId');
    }
    public function product()
    {
        return $this->belongsTo(ProductModel::class,'productId');
    }
    public function company()
    {
        return $this->belongsTo(CompanyProfile::class, 'company_id');
    }
    public function plans()
    {
        return $this->belongsTo(PlanModel::class,'plan_id');
    }

}
