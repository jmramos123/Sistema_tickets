<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Persona extends Model
{
    use HasFactory;

    Protected $table = 'personas';

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono'
    ];
    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'persona_id');
    }
}
