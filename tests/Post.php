<?php

namespace ProtoneMedia\LaravelContent\Tests;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
