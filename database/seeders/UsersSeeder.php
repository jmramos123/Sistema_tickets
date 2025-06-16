<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Persona;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('usuarios')->truncate();
        DB::table('personas')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Crear persona asociada
        $person = Persona::create([
            'nombre'    => 'Admin',
            'apellido'  => 'Principal',
            'email'     => 'admin@sistema.test',
            'telefono'  => '12345678',
        ]);

        // Crear usuario
        $user = Usuario::create([
            'persona_id' => $person->id,
            'username'  => 'admin',
            'password'  => Hash::make('password'),
            'area_id'   => 1,
        ]);

        // Asignar rol de admin
        $user->assignRole('admin');
    }
}
