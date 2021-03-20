<?php

namespace Database\Seeders;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('users')->insert([
         	[
 	            'email' => 'user1@email.com',
 	            'name' => 'User 1',
 	            'password' => Hash::make('Uc4nt.Gues$'),
 	            'created_at' => Carbon::now(),
 	            'updated_at' => Carbon::now(),
 	        ],
 	        [
                'email' => 'user2@email.com',
                'name' => 'User 2',
                'password' => Hash::make('Uc4nt.Gues$'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'email' => 'user3@email.com',
                'name' => 'User 3',
                'password' => Hash::make('Uc4nt.Gues$'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'email' => 'user4@email.com',
                'name' => 'User 4',
                'password' => Hash::make('Uc4nt.Gues$'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'email' => 'user5@email.com',
                'name' => 'User 5',
                'password' => Hash::make('Uc4nt.Gues$'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
         ]);
    }
}
