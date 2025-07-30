<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
   public $fillable = [
    'user_id',
        'title',
        'description',
        'colors',
        'repeat_type', // daily, weekly, monthly
        'repeat_interval', // for daily: every N days
        'repeat_days_moth', // for weekly: ['monday', 'friday'], for monthly: [1, 15, 30]
        'start_date', // event times
        'end_date', // if needed
        'status', // active, inactive, etc.
    ];
        
    public function times()
    {
        return $this->hasMany(Time::class);
    }
}
