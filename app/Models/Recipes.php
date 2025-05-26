<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipes extends Model
{
    protected $table = 'recipes';

    protected $fillable = [
        'tittle',
        'image',
        'video',
        'description',
        'instructions',
        'preparation_time',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'users_id')->getColums();
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredient')
            ->withPivot('quantity_2', 'quantity_4', 'quantity_8');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'recipes_id');
    }

    public function comments()
    {
        return $this->hasMany(Rating::class, 'recipes_id')->whereNotNull('comment');
    }
}
