<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coordinador_asistente', function (Blueprint $table) {
            // Crear columnas para el ID del coordinador y del asistente
            $table->unsignedInteger('coordinador_id');
            $table->unsignedInteger('asistente_id');

            // Definir claves foráneas
            $table->foreign('coordinador_id')->references('id')->on('coordinadores')->onDelete('cascade');
            $table->foreign('asistente_id')->references('id')->on('users')->onDelete('cascade');

            // Agregar timestamps si es necesario
            $table->timestamps();

            // Agregar clave primaria compuesta (opcional)
            $table->primary(['coordinador_id', 'asistente_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coordinador_asistente');
    }
};
