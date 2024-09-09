<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cohorte extends Model
{
    protected $fillable = [
        'Código', 'Nombre', 'Fecha_de_inicio', 'Fecha_de_finalización', 'numero_estudiantes_matriculados', 'Programa_id'
    ];

    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class, 'cohorte_id');
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class, 'programa_id');
    }

}
