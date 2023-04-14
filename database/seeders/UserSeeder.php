<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $listFakeUser = [
            [
                'username' => 'user1',
                'password' => Hash::make('password1'),
            ],
            [
                'username' => 'user2',
                'password' => Hash::make('password2'),
            ],
        ];
        foreach ($listFakeUser as $user) {
            User::factory()->create($user);
        }
        echo 'Generated user';
    }
}
