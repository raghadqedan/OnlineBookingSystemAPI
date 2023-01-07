<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            CategoriesSeeder::class,
            AddressSeeder::class,
            CompanySeeder::class,
            UserSeeder::class,
            QueueSeeder::class,
            ServiceSeeder::class



            //

        ]);

    }
}
