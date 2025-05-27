<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeIngredients extends Model
{
    protected $fillable = [
        'recipes_id',
        'quantity_2',
        'quantity_4',
        'quantity_8'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipes::class, 'recipes_id');
    }
}
