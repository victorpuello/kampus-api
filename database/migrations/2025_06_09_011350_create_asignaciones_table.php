<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('docente_id');
            $table->unsignedBigInteger('asignatura_id');
            $table->unsignedBigInteger('grupo_id');
            $table->unsignedBigInteger('anio_id');
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['docente_id', 'asignatura_id', 'grupo_id', 'anio_id']);
            
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
                  
            $table->foreign('anio_id')
                  ->references('id')
                  ->on('anios')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignaciones');
    }
}; 