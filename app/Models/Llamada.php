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
        'escritorio_id',
        'llamado_en',
        'atendido_en',
        'intentos'
    ];

    public $timestamps = false;

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function escritorio()
    {
        return $this->belongsTo(Ticket::class, 'escritorio_id');
    }
}
