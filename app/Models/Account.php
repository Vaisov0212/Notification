<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';

    use HasFactory;
    public $fillable=[
        'chat_id'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


}
