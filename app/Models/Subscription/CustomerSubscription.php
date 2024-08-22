<?php

namespace App\Models\Subscription;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Company\CompanyProfile;
use App\Models\InterestedCustomers\InterestedCustomer;
use App\Models\Plans\ProductModel;
use App\Models\Plans\PlanModel;
use App\Models\TeleSalesAgent;

class CustomerSubscription extends Model
{
    use HasFactory;
    protected $primaryKey = 'subscription_id';
    protected $table= 'customer_subscriptions';
    protected $fillable = ['subscription_id',
                            'customer_id',
                            'payer_cnic',
                            'payer_msisdn',
                            'subscriber_cnic',
                            'subscriber_msisdn',
                            'beneficiary_name',
                            'beneficiary_msisdn',
                            'transaction_amount',
                            'transaction_status',
                            'referenceId',
                            'cps_transaction_id',
                            'cps_response_text',
                            'product_duration',
                            'plan_id',
                            'productId',
                            'policy_status',
                            'pulse',
                            'api_source',
                            'recursive_charging_date',
                            'subscription_time',
                            'grace_period_time',
                            'sales_agent',
                            'company_id',
                            'consent'
                        ];


    public function plan()
    {
        return $this->belongsTo(PlanModel::class, 'plan_id');
    }
    public function tele_sales_agents()
    {
        return $this->belongsTo(TeleSalesAgent::class, 'agent_id');
    }

     // Relationship to TeleSalesAgent
     public function teleSalesAgent()
     {
         return $this->belongsTo(TeleSalesAgent::class, 'sales_agent');
     }

    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id');
    }

    public function companyProfile()
    {
        return $this->belongsTo(CompanyProfile::class, 'id');
    }
    public function companyProfiles()
    {
        return $this->belongsTo(CompanyProfile::class, 'company_id');
    }

    public function company()
    {
        return $this->belongsTo(CompanyProfile::class);
    }
    public function products()
    {
        return $this->belongsTo(ProductModel::class ,'productId');
    }

    // public function interested_customers()
    // {
    //     return $this->hasMany(InterestedCustomer::class, "customer_msisdn", "subscriber_msisdn");
    // }

}
