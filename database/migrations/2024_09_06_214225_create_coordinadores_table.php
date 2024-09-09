<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoordinadoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coordinadores', function (Blueprint $table) {
            $table->increments('id');
            $table->string('Nombre')->nullable();
            $table->string('Identificación')->nullable();
            $table->string('Dirección')->nullable();
            $table->string('Teléfono')->nullable();
            $table->string('Correo')->nullable();
            $table->string('Género')->nullable();
            $table->date('Fecha_de_nacimiento')->nullable();
            $table->date('Fecha_de_vinculación')->nullable();
            $table->string('Acuerdo_de_nombramiento_pdf')->nullable();
            $table->string('asistente_id')->nullable();
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
        Schema::dropIfExists('coordinadores');
    }
}
