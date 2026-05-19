<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@jakpurwokerto.or.id'],
            [
                'name' => 'the Jakmania Purwokerto',
                'password' => Hash::make('20est08!'),
            ],
        );
    }
}
