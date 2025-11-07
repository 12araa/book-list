<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory;

class BookSeeder extends Seeder
{
    public function run()
    {
        $faker = Factory::create();
        DB::disableQueryLog();
        $now = now();

        $authorIds = DB::table('authors')->pluck('id')->toArray();
        $categoryIds = DB::table('categories')->pluck('id')->toArray();

        $total = 100000;
        $batchSize = 1000;
        for ($i = 0; $i < $total; $i += $batchSize) {
            set_time_limit(0);
            $lastIdBeforeBatch = DB::table('books')->max('id') ?? 0;
            $batch = [];
            $pivotBatch = [];

            $limit = min($batchSize, $total - $i);
            for ($k = 0; $k < $limit; $k++) {
                $authorId = $authorIds[array_rand($authorIds)];
                $title = $faker->sentence(3);
                $isbn = $faker->isbn13;
                $publicationYear = $faker->year();
                $availability = ['available','rented','reserved'][array_rand(['available','rented','reserved'])];
                $storeLocation = $faker->city;

                $batch[] = [
                    'title' => $title,
                    'isbn' => $isbn,
                    'publisher' => $faker->company,
                    'publication_year' => $publicationYear,
                    'availability' => $availability,
                    'store_location' => $storeLocation,
                    'author_id' => $authorId,
                    'avg_rating' => 0,
                    'total_votes' => 0,
                    'weighted_rating' => 0,
                    'rating_trend_flag' => $faker->boolean(25),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('books')->insert($batch);
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $insertedBooks = DB::table('books')
                                ->where('id', '>', $lastIdBeforeBatch)
                                ->pluck('id')
                                ->toArray();

            foreach ($insertedBooks as $bookId) {
                $numCats = rand(1, 3);
                $chosen = [];
                for ($c = 0; $c < $numCats; $c++) {
                    $cid = $categoryIds[array_rand($categoryIds)];
                    if (in_array($cid, $chosen)) continue;
                    $chosen[] = $cid;
                    $pivotBatch[] = [
                        'book_id' => $bookId,
                        'category_id' => $cid,
                    ];
                }
            }

            if (!empty($pivotBatch)) {
                DB::table('book_category')->insert($pivotBatch);
            }

            unset($batch, $pivotBatch, $insertedBooks);
            gc_collect_cycles();
        }
    }
}
