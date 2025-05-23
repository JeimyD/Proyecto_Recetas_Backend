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

    public function recipes(){
        return $this->hasMany(Recipes::class);
    }
}
