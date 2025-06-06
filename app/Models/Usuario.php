<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\user as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Usuario extends Model
{
    use HasFactory, HasRoles;

    protected $table = 'usuarios';

    protected $fillable = [
        'persona_id',
        'username',
        'password',
        'area_id'
    ];

    protected $hidden = [
        'password'
    ];
    
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }
}
