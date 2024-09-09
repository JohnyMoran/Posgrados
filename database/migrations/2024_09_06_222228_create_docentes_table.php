<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docentes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('Nombre')->nullable();
            $table->string('Identificación')->nullable();
            $table->string('Fotografía')->nullable();
            $table->string('Dirección')->nullable();
            $table->string('Teléfono')->nullable();
            $table->string('Correo')->nullable();
            $table->string('Género')->nullable();
            $table->date('Fecha_de_nacimiento')->nullable();
            $table->string('Formación_académica')->nullable();
            $table->string('Áreas_de_conocimiento')->nullable();
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
        Schema::dropIfExists('docentes');
    }
}
