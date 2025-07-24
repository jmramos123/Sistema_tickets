<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class Usuario extends Authenticatable
{
    use HasFactory, HasRoles;

    protected $guard_name = 'web'; // or your actual guard name
    protected $table = 'usuarios';
    public $timestamps = false;
    


    protected $fillable = [
        'persona_id',
        'username',
        'password',
        'area_id',
        'status'
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
        public function initials()
    {
        return strtoupper(substr($this->persona->nombre, 0, 1) . substr($this->persona->apellido, 0, 1));
    }
    
}
