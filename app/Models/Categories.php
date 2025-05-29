<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'description',
        'icon',
    ];

    protected $hidden = [
        'updated_at',
        'created_at'
    ];

    public function recipe_label(){
        return $this->hasMany(Recipe_Category::class);
    }

    public function recipes()
    {
        return $this->belongsToMany(Recipes::class, 'recipe_category', 'categories_id', 'recipe_id');
    }
}
