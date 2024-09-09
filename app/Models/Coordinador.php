<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coordinador extends Model
{
    protected $table = 'coordinadores';
    protected $fillable = [
        'Nombre', 'Identificación', 'Dirección', 'Teléfono', 'Correo', 'Género', 'Fecha_de_nacimiento',
        'Fecha_de_vinculación', 'Acuerdo_de_nombramiento_pdf'
    ];

    public function programa()
    {
        return $this->hasMany(Programa::class, 'Coordinador_asignado');
    }
    
    public function asistente()
    {
        return $this->belongsTo(User::class, 'asistente_id');
    }

}
