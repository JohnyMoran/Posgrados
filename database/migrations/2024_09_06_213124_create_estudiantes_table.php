<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstudiantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('Nombre')->nullable();
            $table->string('Identificación')->nullable();
            $table->string('Código_estudiantil')->nullable();
            $table->string('Fotografía')->nullable();
            $table->string('Dirección_de_residencia')->nullable();
            $table->string('Teléfono')->nullable();
            $table->string('Correo')->nullable();
            $table->string('Género')->nullable();
            $table->date('Fecha_de_nacimiento')->nullable();
            $table->string('Estado_civil')->nullable();
            $table->string('Semestre')->nullable();
            $table->date('Fecha_de_ingreso')->nullable();
            $table->date('Fecha_de_egreso')->nullable();
            $table->string('cohorte_id')->nullable();
            $table->string('programa_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estudiantes');
    }
}
