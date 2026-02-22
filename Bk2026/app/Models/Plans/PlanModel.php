<?php

namespace App\Models\Plans;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Plans\ProductModel;

class PlanModel extends Model
{
    use HasFactory;

    use HasFactory;
    protected $table= 'plans';
    protected $primaryKey = 'plan_id';
    protected $fillable = ['plan_name','status'];


    
    public function products()
    {
        return $this->hasMany(ProductModel::class, 'plan_id');
    }
}
