<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->forceDelete();

        $user = new User();
        $user->name = 'admin';
        $user->email = 'admin@mail.com';
        $user->password = Hash::make('admin');
        $user->role = 'admin';
        $user->save();
    }
}
