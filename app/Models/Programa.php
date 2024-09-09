<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Programa extends Model
{
    protected $fillable = [
        'Código_SNIES', 'Nombre_del_programa', 'Descripción', 'Logo', 'Correo', 'Teléfono',
        'Lineas_de_trabajo', 'Coordinador_asignado', 'Fecha_generación_del_registro_calificado',
        'Número_de_resolución', 'Resolución_de_registro_calificado'
    ];

    public function coordinador()
    {
        return $this->belongsTo(Coordinador::class, 'Coordinador_asignado');
    }

    public function cohorte()
    {
        return $this->hasMany(Cohorte::class, 'programa_id');
    }

    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class);
    }
    
    public function docentes()
    {
        return $this->belongsToMany(Docente::class, 'docente_programa', 'programa_id', 'docente_id');
    }

}
