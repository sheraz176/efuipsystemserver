<?php

namespace App\Models\Plans;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Plans\PlanModel;

class ProductModel extends Model
{
    use HasFactory;
    protected $table= 'products';
    protected $primaryKey = 'product_id'; 
    
    protected $fillable = ['plan_id','product_name','term_takaful',
    'natural_death_benefit',
    'accidental_death_benefit',
    'accidental_medicial_reimbursement',
    'annual_contribution',
    'plan_code',
    'fee',
    'autoRenewal',
    'duration',
    'status',
    'scope_of_cover',
    'eligibility',
    'other_key_details',
    'exclusions'];

    

    public function plan()
    {
        return $this->belongsTo(PlanModel::class, 'plan_id');
    }
}
