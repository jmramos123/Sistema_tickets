<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $driver = DB::getDriverName();

        // **Skip seeding on SQLite** so it won't error out there:
        if ($driver === 'sqlite') {
            return;
        }

        switch ($driver) {
            case 'mysql':
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                Persona::truncate();
                Usuario::truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                break;

            case 'pgsql':
                DB::statement('TRUNCATE TABLE personas, usuarios RESTART IDENTITY CASCADE;');
                break;

            default:
                Persona::truncate();
                Usuario::truncate();
                break;
        }

        // Now insert your seed data
        $person = Persona::create([
            'nombre'    => 'Admin',
            'apellido'  => 'Principal',
            'email'     => 'admin@sistema.test',
            'telefono'  => '12345678',
        ]);

        $user = Usuario::create([
            'persona_id' => $person->id,
            'username'   => 'admin',
            'password'   => Hash::make('password'),
            'area_id'    => 1,
        ]);

        $user->assignRole('admin');
    }
}
