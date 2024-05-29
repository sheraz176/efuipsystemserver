<?php

namespace App\Models\Subscription;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Company\CompanyProfile;
use App\Models\Plans\ProductModel;
use App\Models\Plans\PlanModel;
use App\Models\TeleSalesAgent;

class FailedSubscription extends Model
{
    use HasFactory;
    protected $table= 'insufficient_balance_customers';
    protected $fillable = ['request_id',
    'transactionId',
    'timeStamp',
    'resultCode',
    'resultDesc',
    'failedReason',
    'amount',
    'referenceId',
    'accountNumber',
    'type',
    'remark',
    'planId',
    'product_id',
    'agent_id',
    'company_id',
    'source',
    'sale_request_time',
        ];

        public function plan()
        {
            return $this->belongsTo(PlanModel::class, 'planId');
        }

        public function product()
        {
            return $this->belongsTo(ProductModel::class, 'product_id');
        }

        public function companyProfile()
        {
            return $this->belongsTo(CompanyProfile::class, 'id');
        }

        public function teleSalesAgent()
        {
            return $this->belongsTo(TeleSalesAgent::class, 'agent_id');
        }
        public function company()
        {
            return $this->belongsTo(CompanyProfile::class);
        }


}
