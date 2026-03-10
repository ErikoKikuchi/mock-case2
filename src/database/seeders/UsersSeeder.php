<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'=>'admin user',
            'email'=>'admin@example.com',
            'password'=>Hash::make('adminpass'),
            'role'=>'admin'
        ]);
        User::create([
            'name'=>'test user',
            'email'=>'user@example.com',
            'password'=>Hash::make('password'),
            'role'=>'user'
        ]);
        User::factory()->count(4)->create();
    }
}
