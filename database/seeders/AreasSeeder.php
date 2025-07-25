<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Area;

class AreasSeeder extends Seeder
{
    public function run(): void
    {
        Area::updateOrCreate(
            [
                'nombre_area' => 'Administración',
                'codigo_area' => 'ADM',
                'descripcion' => 'Área administrativa del sistema'
            ]
        );
    }
}
