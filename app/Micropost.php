<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Micropost extends Model
{
    protected $fillable = ['content', 'user_id'];
    
    public function user()
    {
        // Userを$micropost->user()->first() もしくは $micropost->user で取得できる
        return $this->belongsTo(User::class);
    }
}
