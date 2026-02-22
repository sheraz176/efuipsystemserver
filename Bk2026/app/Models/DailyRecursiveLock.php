<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyRecursiveLock extends Model
{
    use HasFactory;

     protected $table = 'daily_recursive_locks';
     protected $fillable = ['subscription_id', 'process_date'];

}
