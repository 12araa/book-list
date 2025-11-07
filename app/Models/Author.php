<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $fillable = [
        'name', 'bio'
    ];

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function stats()
    {
        return $this->hasOne(AuthorStat::class, 'author_id');
    }

    public function ratedBooks()
    {
        return $this->hasManyThrough(Rating::class, Book::class);
    }
}
