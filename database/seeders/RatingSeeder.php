<?php
// database/seeders/RatingSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory;

class RatingSeeder extends Seeder
{
    public function run()
    {
        $faker = Factory::create();
        DB::disableQueryLog();
        $now = now();

        $total = 500000;
        $batchSize = 5000;

        $bookIds = DB::table('books')->pluck('id')->toArray();
        $bookCount = count($bookIds);
        if ($bookCount === 0) {
            throw new \Exception('No books found. Run BookSeeder first.');
        }

        $fakeUserIdentifiers = [];
        for ($k = 1; $k <= 1000; $k++) {
            $paddedId = str_pad($k, 4, "0", STR_PAD_LEFT);
            $fakeUserIdentifiers[] = "user_" . $paddedId;
        }

        for ($i = 0; $i < $total; $i += $batchSize) {
            set_time_limit(0);
            $batch = [];
            $limit = min($batchSize, $total - $i);
            for ($j = 0; $j < $limit; $j++) {
                $bookId = $bookIds[array_rand($bookIds)];
                $userIdentifier = $fakeUserIdentifiers[array_rand($fakeUserIdentifiers)];
                $score = rand(1, 10); // rating scale 1..10

                $batch[] = [
                    'book_id' => $bookId,
                    'user_identifier' => $userIdentifier,
                    'rating' => $score,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }


            DB::table('ratings')->insertOrIgnore($batch);
            unset($batch);
        }

        DB::statement("
            UPDATE books b
            JOIN (
                SELECT book_id, COUNT(*) as cnt, AVG(rating) as avg_rating
                FROM ratings
                GROUP BY book_id
            ) r ON b.id = r.book_id
            SET b.total_votes = r.cnt,
                b.avg_rating = ROUND(r.avg_rating,2)
        ");

        $globalAvg = DB::table('ratings')->avg('rating') ?: 0;
        $m = 50;

        DB::statement("
            UPDATE books
            SET weighted_rating = ROUND(((total_votes / (total_votes + {$m})) * avg_rating) + (({$m} / (total_votes + {$m})) * {$globalAvg}), 4)
        ");

        DB::statement("
            INSERT INTO author_stats (author_id, total_ratings, avg_rating, created_at, updated_at)
            SELECT b.author_id, SUM(r.cnt) AS total_ratings, AVG(b.avg_rating) AS avg_rating, NOW(), NOW()
            FROM (
                SELECT book_id, COUNT(*) as cnt
                FROM ratings
                GROUP BY book_id
            ) r
            JOIN books b ON b.id = r.book_id
            GROUP BY b.author_id
            ON DUPLICATE KEY UPDATE total_ratings = VALUES(total_ratings), avg_rating = VALUES(avg_rating), updated_at = VALUES(updated_at)
        ");
    }
}
