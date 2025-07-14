<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tickets';
    public $timestamps = false;

    protected $fillable = [
        'area_id',
        'numero',
        'numero_adulto_mayor',
        'es_adulto_mayor',
        'estado',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
    
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function llamadas()
    {
        return $this->hasMany(Llamada::class, 'ticket_id');
    }
    public function latestLlamada()
    {
        return $this->hasOne(Llamada::class, 'ticket_id')->latestOfMany();
    }

}
