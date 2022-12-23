<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('companies')->insert([
            'name' => 'Moon Salon',
            'address_id'=>'1',
            'category_id'=>'1',
            'logo'=>'image',
            'description'=>'Ladies beauty salon',
            'type'=>'0',
             // 'street'=>''
            ], );
    }
}
