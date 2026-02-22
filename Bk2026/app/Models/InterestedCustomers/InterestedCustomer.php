<?php

namespace App\Models\InterestedCustomers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TeleSalesAgent;

class InterestedCustomer extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'customer_msisdn',
        'customer_cnic',
        'plan_id',
        'product_id',
        'beneficiary_msisdn',
        'beneficiary_cnic',
        'relationship',
        'beneficinary_name',
        'deduction_applied',
        'agent_id',
        'company_id',
        'super_agent_id',

    ];

    public function agent()
    {
        return $this->belongsTo(TeleSalesAgent::class, 'agent_id', 'agent_id');
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company\CompanyProfile::class, 'company_id', 'company_id');
    }

    public function plan()
    {
        return $this->belongsTo(\App\Models\Plans\PlanModel::class, 'plan_id', 'plan_id');
    }
    public function product()
    {
        return $this->belongsTo(\App\Models\Plans\ProductModel::class, 'product_id', 'product_id');
    }
}
