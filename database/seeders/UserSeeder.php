<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create(
            [
                'name' => 'Thiago Pinto Dias',
                'email' => 'thiagopinto.lx@gmail.com',
                'phone' => '86988310563',
                'password' => Hash::make('secret')

            ]
        );
    }
}
