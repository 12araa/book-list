<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthorStat extends Model
{
    protected $fillable = [
        'author_id',
        'total_books',
        'avg_rating',
        'last_published_year'
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }
}
