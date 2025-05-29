<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorites extends Model
{
    protected $fillable = [
        'users_id',
        'recipes_id'
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'users_id');
    }

    public function recipe()
    {
        return $this->belongsTo(Recipes::class, 'recipes_id');
    }
}
