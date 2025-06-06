<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tickets';

    protected $fillable = [
        'area_id',
        'numero',
        'es_adulto_mayor',
        'estado',
        'created_at'
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function llamadas()
    {
        return $this->hasMany(Llamada::class, 'ticket_id');
    }
}
