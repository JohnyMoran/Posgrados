<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    protected $fillable = [
        'Nombre', 'Identificación', 'Fotografía', 'Dirección', 'Teléfono', 'Correo', 'Fecha_de_nacimiento', 'Formación_académica','Áreas_de_conocimiento', 'programa_id'
    ];

    public function programa()
    {
        return $this->belongsToMany(Programa::class, 'docente_programa', 'docente_id', 'programa_id');
    }
}
