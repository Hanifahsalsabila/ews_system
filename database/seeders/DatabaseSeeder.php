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
                'username' => 'admin.utama',
                'password' => bcrypt('password'),
            ]
        );

        $admin->assignRole('Super_Admin');

        $bidan = User::firstOrCreate(
            ['email' => 'bidan@eposyandu.id'],
            [
                'name' => 'Bidan Desa',
                'username' => 'bidan.desa',
                'password' => bcrypt('password'),
            ]
        );
        $bidan->assignRole('Puskesmas');

        $kader = User::firstOrCreate(
            ['email' => 'kader@eposyandu.id'],
            [
                'name' => 'Kader Posyandu',
                'username' => 'kader.melati',
                'password' => bcrypt('password'),
            ]
        );
        $kader->assignRole('Kader_Posyandu');

        $orangtua = User::firstOrCreate(
            ['username' => 'mbahsiti01'],
            [
                'name' => 'orang tua',
                'password' => bcrypt('password'),
            ]
        );
        $orangtua->assignRole('orang_tua');
    }
}
