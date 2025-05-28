<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe_Category extends Model
{
    protected $table = 'recipe_category';

    protected $fillable = [
        'recipes_id',
        'categories_id'
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipes::class, 'recipes_id');
    }

    public function label()
    {
        return $this->belongsTo(Categories::class, 'categories_id');
    }
}
