<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $admin = User::firstOrCreate(
            ['email' => 'admin@eposyandu.id'],
            [
                'name' => 'Admin Utama',
                'password' => bcrypt('password'),
            ]
        );

        $admin->assignRole('Super_Admin');

        $bidan = User::firstOrCreate(
            ['email' => 'bidan@eposyandu.id'],
            [
                'name' => 'Bidan Desa',
                'password' => bcrypt('password'),
            ]
        );
        $bidan->assignRole('Bidan_Desa');

        $kader = User::firstOrCreate(
            ['email' => 'kader@eposyandu.id'],
            [
                'name' => 'Kader Posyandu',
                'password' => bcrypt('password'),
            ]
        );
        $kader->assignRole('Kader_Posyandu');
    }
}
