<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyStat extends Model
{
    use HasFactory;

       protected $table= 'monthly_stats';

    protected $fillable = ['year', 'data'];
    protected $casts = ['data' => 'array'];


}
