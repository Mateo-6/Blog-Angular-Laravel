<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';
    protected $fillable = ['user_id', 'category_id', 'title', 'content', 'image'];

    // Relacion de uno a muchos (muchos a uno)
    public function users()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    // Relacion de uno a muchos (muchos a uno)
    public function category()
    {
        return $this->belongsTo('App\Category');
    }
}
