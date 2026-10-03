<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    
    public function run()
    {
        DB::table("users")->delete();
        DB::table('users')->insert([
            'name' => 'Ahmed',
            'email' =>   'ahmed14015320@gmail.com',
            'password' => Hash::make('123456'),
        ]);
    }
}
