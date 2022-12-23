<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([[

                'name'=>'raghad',
                'role_id'=>'1',
                'email'=>'raghad@yahoo.com',
                'password'=>'1234567890',
                'phone_number'=>'0599938123',
                'company_id'=>'1'
        ],
            [

                'name'=>'rama',
                'role_id'=>'2',
                'email'=>'rama@yahoo.com',
                'password'=>'12345',
                'phone_number'=>'05999675',
                'company_id'=>'1'
            ]


        ]);

    }
}
