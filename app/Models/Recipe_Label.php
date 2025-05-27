<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe_Label extends Model
{
    protected $table = 'recipe_label';

    public function recipes(){
        return $this->belongsToMany(Recipes::class, 'recipe_label');
    }

    public function labels(){
        return $this->belongsToMany(Labels::class, 'recipe_label');
    }
}
