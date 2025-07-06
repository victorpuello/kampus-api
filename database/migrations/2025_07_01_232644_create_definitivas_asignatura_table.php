<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('definitivas_asignatura', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->decimal('calificacion', 5, 2);
            $table->unsignedBigInteger('estudiante_id');
            $table->unsignedBigInteger('asignatura_id');
            $table->unsignedBigInteger('periodo_id');
            $table->timestamps();

            $table->unique(['estudiante_id', 'asignatura_id', 'periodo_id'], 'definitivas_unique');

            $table->foreign('estudiante_id')
                ->references('id')
                ->on('estudiantes')
                ->onDelete('cascade');

            $table->foreign('asignatura_id')
                ->references('id')
                ->on('asignaturas')
                ->onDelete('cascade');

            $table->foreign('periodo_id')
                ->references('id')
                ->on('periodos')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('definitivas_asignatura');
    }
};
