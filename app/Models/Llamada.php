<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Llamada extends Model
{
    use HasFactory;

    protected $table = 'llamadas';

    protected $fillable = [
        'ticket_id',
        'es_adulto_mayor',
        'escritorio_id',
        'usuario_id',
        'llamado_en',
        'atendido_en',
        'intentos'
    ];

    protected $casts = [
        'llamado_en'  => 'datetime',
        'atendido_en' => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    public $timestamps = true;

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function escritorio()
    {
        return $this->belongsTo(Escritorio::class, 'escritorio_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
