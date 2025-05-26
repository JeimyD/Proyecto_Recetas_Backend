<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = [
        'recipes_id',
        'users_id',
        'comment',
        'rate'
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'users_id');
    }
}
