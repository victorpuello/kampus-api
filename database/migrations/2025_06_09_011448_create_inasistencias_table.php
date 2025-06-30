<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inasistencias', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('estudiante_id');
            $table->unsignedBigInteger('asignacion_id');
            $table->unsignedBigInteger('periodo_id');
            $table->date('fecha');
            $table->integer('cantidad_horas')->default(1);
            $table->boolean('justificada')->default(false);
            $table->text('observacion')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('estudiante_id')
                  ->references('id')
                  ->on('estudiantes')
                  ->onDelete('cascade');
                  
            $table->foreign('asignacion_id')
                  ->references('id')
                  ->on('asignaciones')
                  ->onDelete('cascade');
                  
            $table->foreign('periodo_id')
                  ->references('id')
                  ->on('periodos')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inasistencias');
    }
}; 