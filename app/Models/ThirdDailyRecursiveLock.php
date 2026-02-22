<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThirdDailyRecursiveLock extends Model
{
    use HasFactory;


      protected $table = 'third_daily_recusive_locks';
     protected $fillable = ['subscription_id', 'process_date'];


}
