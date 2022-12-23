<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QueueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('queues')->insert([

            'name'=>'Q1',
            'repeats'=>'1',
            'user_id'=>'2',
            'start_regesteration'=>'2022-12-25'
        ] );
    }
}
