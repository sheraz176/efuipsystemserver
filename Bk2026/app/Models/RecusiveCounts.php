<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecusiveCounts extends Model
{
    use HasFactory;
     protected $table = 'recusive_counts';

      protected $fillable = [
        'date',
        'total_recursive_today',
        'success_total',
        'success_family_health',
        'success_term_life',
        'failed_total',
        'created_at',
        'updated_at'
    ];
}
