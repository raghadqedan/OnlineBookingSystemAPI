<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\models\Category;
class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {  DB::table('categories')->insert([[
        'name' => 'Banks',
        'logo' => '', ],

       ['name' => 'Salons',
        'logo' => '',
     ],

       [
        'name' => 'Ministry Of Health',
        'logo' => '',
       ],

       [
        'name' => 'Barber Salons',
        'logo' => '',
       ],]




);
        
    }
}
