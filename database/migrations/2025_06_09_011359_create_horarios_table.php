<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('asignacion_id');
            $table->unsignedBigInteger('aula_id');
            $table->unsignedBigInteger('franja_id');
            $table->tinyInteger('dia_semana')->comment('1:Lunes, 2:Martes, ..., 7:Domingo');
            $table->unsignedBigInteger('anio_id');
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['aula_id', 'franja_id', 'dia_semana', 'anio_id']);
            $table->unique(['asignacion_id', 'franja_id', 'dia_semana', 'anio_id']);
            
            $table->foreign('asignacion_id')
                  ->references('id')
                  ->on('asignaciones')
                  ->onDelete('cascade');
                  
            $table->foreign('aula_id')
                  ->references('id')
                  ->on('aulas')
                  ->onDelete('restrict');
                  
            $table->foreign('franja_id')
                  ->references('id')
                  ->on('franjas_horarias')
                  ->onDelete('restrict');
                  
            $table->foreign('anio_id')
                  ->references('id')
                  ->on('anios')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
}; 