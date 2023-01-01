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
    {
            DB::table('categories')->insert([[
        'name' => 'Banks',
        'logo' => 'categories\Ministry Of Health.jpg', ],

        ['name' => 'Salons',
        'logo' => 'categories\salon.jpg',
        ],

        [
        'name' => 'The Ministry Of Health',
        'logo' => 'categories\Ministry Of Health.jpg',
        ],

        [
        'name' => 'Barber Salons',
        'logo' => 'categories\Barber Salons.jpg',
        ],
        [
        'name' => 'Doctors',
        'logo' => 'categories\doctor.jpg',
        ],

        [
        'name' => 'The ministry of communications',
        'logo' => 'categories\The ministry of communications.jpg',
        ],



        ]




);

    }
}
