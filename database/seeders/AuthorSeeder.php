<?php
// database/seeders/AuthorSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory;

class AuthorSeeder extends Seeder
{
    public function run()
    {
        $faker = Factory::create();
        DB::disableQueryLog();

        $total = 1000;
        $batchSize = 1000;
        $now = now();

        $batch = [];
        for ($i = 0; $i < $total; $i++) {
            $batch[] = [
                'name' => $faker->name,
                'bio' => $faker->paragraph(2),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // disable FK checks not necessary for authors but fine
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('authors')->insert($batch);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
