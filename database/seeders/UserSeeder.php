<?php

namespace Database\Seeders;

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
        // Admin 
        User::create([
            'name' => 'reiza',
            'email' => 'reiza@gmail.com',
            'password' => Hash::make('12345'), 
            'role_id' => 1,
            'position_id' => 2, 
            'phone' => '0812345675',
        ]);

        // Operator 
        User::create([
            'name' => 'hans',
            'email' => 'hans@gmail.com',
            'password' => Hash::make('12345'),
            'role_id' => 2,
            'position_id' => 4, 
            'phone' => '07291318761',
        ]);

        // User
        User::create([
            'name' => 'naufal',
            'email' => 'naufal@gmail.com',
            'password' => Hash::make('12345'),
            'role_id' => 3,
            'position_id' => 1, 
            'phone' => '0823187312',
        ]);
    }
}
