<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use Carbon\Carbon;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $now = new \DateTime();
        $categories = [ 'Remessa Parcial', 'Remessa' ];

        $categoriesToCreate = array_map(fn ($name) => [
            'name' => $name, 
            'created_at' => $now
        ], $categories);

        Category::insert($categoriesToCreate);
    }
}
