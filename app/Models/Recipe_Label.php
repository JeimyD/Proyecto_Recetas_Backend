<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe_Label extends Model
{
    protected $table = 'recipe_label';

    protected $fillable = [
        'recipes_id',
        'labels_id'
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipes::class, 'recipes_id');
    }

    public function label()
    {
        return $this->belongsTo(Labels::class, 'labels_id');
    }
}
