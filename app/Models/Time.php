<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    use HasFactory;
    public $fillable = [
        'event_id',
        'start_time',
        'end_time',
    ];
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
