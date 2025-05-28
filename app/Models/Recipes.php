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
        'updated_at',
        'users_id'
    ];

    public function ingredients()
    {
        return $this->hasMany(RecipeIngredients::class, 'recipes_id');
    }

    public function user()
    {
        return $this->belongsTo(Users::class, 'users_id')->getColums();
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'recipes_id');
    }

    public function comments()
    {
        return $this->hasMany(Rating::class, 'recipes_id')->whereNotNull('comment');
    }

    public function labels()
    {
        return $this->belongsToMany(Labels::class, 'recipe_label', 'recipes_id', 'labels_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Categories::class, 'recipe_category', 'recipe_id', 'categories_id');
    }
}
