<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = [
        'book_id',
        'user_identifier',
        'rating',
        'review'
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

}
