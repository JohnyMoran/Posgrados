<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    protected $fillable = [
        'Nombre', 'Identificación', 'Código_estudiantil', 'Fotografía', 'Dirección_de_residencia',
        'Teléfono', 'Correo', 'Género', 'Fecha_de_nacimiento', 'Estado_civil', 'Semestre',
        'Fecha_de_ingreso', 'Fecha_de_egreso', 'cohorte_id', 'programa_id'
    ];

    public function cohorte()
    {
        return $this->belongsTo(Cohorte::class, 'cohorte_id');
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class, 'programa_id');
    }
}
