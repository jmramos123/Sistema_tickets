<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Video extends Model
{
    use HasFactory;

    protected $table = 'videos';

    protected $fillable = [
        'ruta_archivo',
        'nombre',
        'uploaded_at',
        'is_active' // ✅ added
    ];

    public $timestamps = false;
}
