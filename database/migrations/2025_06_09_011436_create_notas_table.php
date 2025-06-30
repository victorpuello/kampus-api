<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notas', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->decimal('calificacion', 5, 2);
            $table->unsignedBigInteger('estudiante_id');
            $table->unsignedBigInteger('asignacion_id');
            $table->unsignedBigInteger('periodo_id');
            $table->unsignedBigInteger('competencia_id')->nullable();
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
                  
            $table->foreign('competencia_id')
                  ->references('id')
                  ->on('competencias')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas');
    }
}; 