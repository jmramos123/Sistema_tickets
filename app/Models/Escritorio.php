<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Escritorio extends Model
{
    use HasFactory;

    protected $table = 'escritorios';

    protected $fillable = [
        'nombre_escritorio',
        'area_id'
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function llamadas()
    {
        return $this->hasMany(Llamada::class, 'escritorio_id');
    }
}
