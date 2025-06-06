<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Area extends Model
{
    use HasFactory;

    protected $table = 'areas';

    protected $fillable = [
        'nombre_area',
        'codigo_area',
        'descripcion'
    ];

    public function escritorios()
    {
        return this->hasMany(Escritorio::class, 'area_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'area_id');
    }
}
