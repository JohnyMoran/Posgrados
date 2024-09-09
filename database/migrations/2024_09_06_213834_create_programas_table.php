<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgramasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('Código_SNIES')->nullable();
            $table->string('Nombre_del_programa')->nullable();
            $table->text('Descripción')->nullable();
            $table->string('Logo')->nullable();
            $table->string('Correo')->nullable();
            $table->string('Teléfono')->nullable();
            $table->string('Lineas_de_trabajo')->nullable();
            $table->string('Coordinador_asignado')->nullable();
            $table->string('Fecha_generación_del_registro_calificado')->nullable();
            $table->string('Número_de_resolución')->nullable();
            $table->string('Resolución_de_registro_calificado')->nullable();
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
        Schema::dropIfExists('programas');
    }
}
