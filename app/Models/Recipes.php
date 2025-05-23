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

    public function user(){
        return $this->belongsTo(Users::class);
    }

    public function recipe_laber(){
        return $this->hasMany(Recipe_Label::class);
    }
}
