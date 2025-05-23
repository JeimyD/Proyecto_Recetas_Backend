<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe_Label extends Model
{
    protected $table = 'recipe_label';

    public function recipes(){
        return $this->belongsTo(Recipes::class);
    }

    public function labels(){
        return $this->belongsTo(Labels::class);
    }
}
