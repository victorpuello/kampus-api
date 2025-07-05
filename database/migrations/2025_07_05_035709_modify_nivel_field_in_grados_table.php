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
        Schema::table('grados', function (Blueprint $table) {
            // Modificar el campo nivel para usar enum con las opciones especificadas
            $table->enum('nivel', [
                'Preescolar',
                'Básica Primaria', 
                'Básica Secundaria',
                'Educación Media'
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grados', function (Blueprint $table) {
            // Revertir a string para mantener compatibilidad
            $table->string('nivel', 50)->change();
        });
    }
};
