<?php

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::create([
            'name' => 'Manhua',
        ]);
        Category::create([
            'name' => 'webtoon',
        ]);
        Category::create([
            'name' => 'light novel',
        ]);
        Category::create([
            'name' => 'hentai',
        ]);
        Category::create([
            'name' => 'doujin',
        ]);
    }
}
