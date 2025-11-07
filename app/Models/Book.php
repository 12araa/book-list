<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Book extends Model
{
    protected $fillable = [
        'title',
        'isbn',
        'publisher',
        'publication_year',
        'availability',
        'store_location',
        'author_id'
    ];

    // Tambahkan appends untuk auto-calculate trending saat book di-load
    protected $appends = ['is_trending'];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'book_category', 'book_id', 'category_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Calculate average rating for last 7 days
     */
    public function getAverageRatingLast7Days()
    {
        $sevenDaysAgo = now()->subDays(7);

        return $this->ratings()
            ->where('created_at', '>=', $sevenDaysAgo)
            ->avg('rating');
    }

    /**
     * Calculate average rating for previous 7 days (8-14 days ago)
     */
    public function getAverageRatingPrevious7Days()
    {
        $fourteenDaysAgo = now()->subDays(14);
        $sevenDaysAgo = now()->subDays(7);

        return $this->ratings()
            ->whereBetween('created_at', [$fourteenDaysAgo, $sevenDaysAgo])
            ->avg('rating');
    }

    /**
     * Accessor untuk check apakah book trending
     * Trending jika: avg rating 7 hari terakhir > avg rating 7 hari sebelumnya
     */
    public function getIsTrendingAttribute()
    {
        if (isset($this->attributes['trending_calculated'])) {
            return (bool) $this->attributes['trending_calculated'];
        }

        $last7DaysAvg = $this->getAverageRatingLast7Days();
        $previous7DaysAvg = $this->getAverageRatingPrevious7Days();

        if (is_null($last7DaysAvg) || is_null($previous7DaysAvg)) {
            return false;
        }

        return $last7DaysAvg > $previous7DaysAvg;
    }

    public function scopeWithTrending($query)
    {
        $sevenDaysAgo = now()->subDays(7)->toDateTimeString();
        $fourteenDaysAgo = now()->subDays(14)->toDateTimeString();

        return $query->addSelect([
            'books.*',
            // Average rating 7 hari terakhir
            'avg_rating_last_7_days' => Rating::selectRaw('AVG(rating)')
                ->whereColumn('book_id', 'books.id')
                ->where('created_at', '>=', $sevenDaysAgo),

            // Average rating 8-14 hari lalu
            'avg_rating_previous_7_days' => Rating::selectRaw('AVG(rating)')
                ->whereColumn('book_id', 'books.id')
                ->whereBetween('created_at', [$fourteenDaysAgo, $sevenDaysAgo]),
        ])->addSelect([
            // Flag trending: 1 jika meningkat, 0 jika tidak
            'trending_calculated' => DB::raw("
                CASE
                    WHEN (
                        SELECT AVG(rating)
                        FROM ratings
                        WHERE ratings.book_id = books.id
                        AND ratings.created_at >= '{$sevenDaysAgo}'
                    ) > (
                        SELECT AVG(rating)
                        FROM ratings
                        WHERE ratings.book_id = books.id
                        AND ratings.created_at BETWEEN '{$fourteenDaysAgo}' AND '{$sevenDaysAgo}'
                    )
                    THEN 1
                    ELSE 0
                END
            ")
        ]);
    }
}
