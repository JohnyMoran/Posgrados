<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCohortesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cohortes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('Código')->nullable();
            $table->string('Nombre')->nullable();
            $table->date('Fecha_de_inicio')->nullable();
            $table->date('Fecha_de_finalización')->nullable();
            $table->string('Número_de_estudiantes_matriculados')->nullable();
            $table->string('Programa_id')->nullable();
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
        Schema::dropIfExists('cohortes');
    }
}
