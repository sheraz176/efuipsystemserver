<?php

namespace App\Models\Unsubscription;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Company\CompanyProfile;
use App\Models\InterestedCustomers\InterestedCustomer;
use App\Models\Subscription\CustomerSubscription;
use App\Models\Plans\ProductModel;
use App\Models\Plans\PlanModel;

class CustomerUnSubscription extends Model
{
    use HasFactory;

    protected $table = 'unsubscriptions'; // Set the table name if it's different from the model's plural name

    protected $primaryKey = 'unsubscription_id'; // Set the primary key if it's different from 'id'

    protected $fillable = [
        'unsubscription_datetime',
        'medium',
        'subscription_id',
        'refunded_id',
    ];


    public function plan()
    {
        return $this->belongsTo(PlanModel::class, 'plan_id');
    }

    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id');
    }

    public function companyProfile()
    {
        return $this->belongsTo(CompanyProfile::class, 'id');
    }

    public function company()
    {
        return $this->belongsTo(CompanyProfile::class);
    }
    public function products()
    {
        return $this->belongsTo(ProductModel::class ,'productId');
    }
    public function customer_subscription()
    {
        return $this->belongsTo(CustomerSubscription::class ,'subscription_id');
    }


}
