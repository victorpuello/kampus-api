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
        Schema::table('grupos', function (Blueprint $table) {
            // Agregar campo sede_id después de nombre
            $table->unsignedBigInteger('sede_id')->after('nombre');
            
            // Agregar foreign key constraint
            $table->foreign('sede_id')
                  ->references('id')
                  ->on('sedes')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            // Eliminar foreign key constraint
            $table->dropForeign(['sede_id']);
            
            // Eliminar columna
            $table->dropColumn('sede_id');
        });
    }
};
