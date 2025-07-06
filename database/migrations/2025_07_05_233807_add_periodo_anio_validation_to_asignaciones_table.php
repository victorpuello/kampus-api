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
        Schema::table('asignaciones', function (Blueprint $table) {
            // Agregar un índice compuesto para mejorar la validación de período por año
            $table->index(['anio_academico_id', 'periodo_id'], 'asignaciones_anio_periodo_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asignaciones', function (Blueprint $table) {
            $table->dropIndex('asignaciones_anio_periodo_index');
        });
    }
};
