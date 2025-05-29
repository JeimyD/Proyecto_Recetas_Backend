<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Users extends Model
{
    use HasApiTokens;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    protected $hidden = [
        'password',
        'created_at',
        'updated_at'
    ];

    public function recipes()
    {
        return $this->hasMany(Recipes::class);
    }

    public function favoriteRecipes()
    {
        return $this->belongsToMany(Recipes::class, 'favorites', 'users_id', 'recipes_id')
            ->withTimestamps();
    }

    public function favorites()
    {
        return $this->hasMany(Favorites::class, 'users_id');
    }

    public function hasFavorite($recipeId)
    {
        return $this->favoriteRecipes()->where('recipes.id', $recipeId)->exists();
    }
}
