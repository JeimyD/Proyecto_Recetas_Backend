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

    public function rating()
    {
        return $this->hasMany(Rating::class, 'recipes_id');
    }

    public function getAverageRatingAttribute()
    {
        return $this->rating()->avg('rate') ?? 0;
    }

    public function getTotalRatingsAttribute()
    {
        return $this->rating()->count();
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

    public function favoritedBy()
    {
        return $this->belongsToMany(Users::class, 'favorites', 'recipes_id', 'users_id')
            ->withTimestamps();
    }

    public function favorites()
    {
        return $this->hasMany(Favorites::class, 'recipes_id');
    }

    public function getTotalFavoritesAttribute()
    {
        return $this->favorites()->count();
    }

    public function isFavoritedBy($userId)
    {
        return $this->favoritedBy()->where('users.id', $userId)->exists();
    }
}
