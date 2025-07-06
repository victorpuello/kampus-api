<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('docente_id');
            $table->unsignedBigInteger('asignatura_id');
            $table->unsignedBigInteger('grupo_id');
            $table->unsignedBigInteger('franja_horaria_id');
            $table->enum('dia_semana', ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado']);
            $table->unsignedBigInteger('anio_academico_id');
            $table->unsignedBigInteger('periodo_id')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->softDeletes();
            $table->timestamps();

            // Índices para mejorar performance
            $table->index(['docente_id', 'dia_semana', 'franja_horaria_id']);
            $table->index(['grupo_id', 'dia_semana', 'franja_horaria_id']);
            $table->index(['anio_academico_id', 'periodo_id']);

            // Constraints de integridad
            $table->foreign('docente_id')
                ->references('id')
                ->on('docentes')
                ->onDelete('restrict');

            $table->foreign('asignatura_id')
                ->references('id')
                ->on('asignaturas')
                ->onDelete('restrict');

            $table->foreign('grupo_id')
                ->references('id')
                ->on('grupos')
                ->onDelete('restrict');

            $table->foreign('franja_horaria_id')
                ->references('id')
                ->on('franjas_horarias')
                ->onDelete('restrict');

            $table->foreign('anio_academico_id')
                ->references('id')
                ->on('anios')
                ->onDelete('restrict');

            $table->foreign('periodo_id')
                ->references('id')
                ->on('periodos')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignaciones');
    }
};
