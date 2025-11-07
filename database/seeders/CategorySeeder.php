<?php
// database/seeders/CategorySeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory;

class CategorySeeder extends Seeder
{
    public function run()
    {
        // $faker = Factory::create();
        // DB::disableQueryLog();
        // $total = 3000;
        // $batch = [];
        // $now = now();

        // for ($i = 0; $i < $total; $i++) {
        //     $batch[] = [
        //         'name' => rtrim($faker->words(2, true) . " " . $i),
        //         'description' => $faker->sentence(),
        //         'created_at' => $now,
        //         'updated_at' => $now,
        //     ];
        // }

        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // DB::table('categories')->insert($batch);
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = Factory::create();
        $categories = [];
        for ($i = 0; $i < 3000; $i++) {
            $categories[] = [
                'name' => ucfirst($faker->randomElement([
                    'Romance',
                    'Fantasy',
                    'Adventure',
                    'Mystery',
                    'Horror',
                    'Thriller',
                    'Science Fiction',
                    'Historical Fiction',
                    'Young Adult',
                    'Classic Literature',
                    'Business & Economics',
                    'Self-Development',
                    'Psychology',
                    'Philosophy',
                    'Technology',
                    'Programming',
                    'Artificial Intelligence',
                    'Design & UX',
                    'Marketing',
                    'Finance & Investment',
                    'Biography',
                    'Memoir',
                    'Poetry',
                    'Children',
                    'Comics',
                    'Manga',
                    'Light Novel',
                    'Religion',
                    'Education',
                    'Travel',
                    'Cooking',
                    'Lifestyle',
                    'Health & Fitness',
                    'Art',
                    'Photography',
                    'Nature',
                    'Sports',
                    'Political Science',
                    'Law',
                ])) . ' #' . $faker->numberBetween(1, 9999),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('categories')->insert($categories);
    }
}
