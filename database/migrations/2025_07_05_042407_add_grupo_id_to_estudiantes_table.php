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
        Schema::table('estudiantes', function (Blueprint $table) {
            // Agregar campo grupo_id después de codigo_estudiantil
            $table->unsignedBigInteger('grupo_id')->nullable()->after('codigo_estudiantil');
            
            // Agregar foreign key constraint
            $table->foreign('grupo_id')
                  ->references('id')
                  ->on('grupos')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estudiantes', function (Blueprint $table) {
            // Eliminar foreign key constraint
            $table->dropForeign(['grupo_id']);
            
            // Eliminar columna
            $table->dropColumn('grupo_id');
        });
    }
};
