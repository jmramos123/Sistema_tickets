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

        switch ($driver) {
            case 'mysql':
                // Temporarily disable FKs for MySQL
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                Persona::truncate();
                Usuario::truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                break;

            case 'sqlite':
                // Temporarily disable FKs for SQLite
                DB::statement('PRAGMA foreign_keys = OFF;');
                Persona::truncate();
                Usuario::truncate();
                DB::statement('PRAGMA foreign_keys = ON;');
                break;

            case 'pgsql':
                // In Postgres, use TRUNCATE ... CASCADE and reset identities
                DB::statement('TRUNCATE TABLE personas, usuarios RESTART IDENTITY CASCADE;');
                break;

            default:
                // Fallback for other drivers
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
