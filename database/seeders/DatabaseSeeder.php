<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Création des utilisateurs de test
        User::create([
            'name' => 'Administrateur',
            'email' => 'admin@stock.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Manager Stock',
            'email' => 'manager@stock.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
        ]);

        User::create([
            'name' => 'Observateur',
            'email' => 'obs@stock.com',
            'password' => Hash::make('password'),
            'role' => 'observer',
        ]);

        // 2. Appel des seeders de données métier
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}