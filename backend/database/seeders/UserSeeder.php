<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin Fix',
            'email' => 'admin@teste.com',
            'password' => Hash::make('123456'),
        ]);

        $userFixo = User::factory()->create([
            'name' => 'Usuário Fixo',
            'email' => 'teste@teste.com',
            'password' => Hash::make('123456'),
        ]);

       $usersAleatorios = User::factory()->count(5)->create();

       Task::factory()->count(3)->create(['user_id' => $userFixo->id]);

       foreach ($usersAleatorios as $user) {
            Task::factory()->count(3)->create(['user_id' => $user->id]);
        }

    }
}
