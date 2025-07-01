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
            $table->date('fecha_nacimiento')->nullable()->after('codigo_estudiantil');
            $table->enum('genero', ['M', 'F', 'O'])->default('M')->after('fecha_nacimiento');
            $table->string('direccion', 255)->nullable()->after('genero');
            $table->string('telefono', 20)->nullable()->after('direccion');
            $table->unsignedBigInteger('institucion_id')->nullable()->after('telefono');
            $table->enum('estado', ['activo', 'inactivo'])->default('activo')->after('institucion_id');
            $table->unsignedBigInteger('acudiente_id')->nullable()->after('estado');

            // Agregar foreign keys
            $table->foreign('institucion_id')
                  ->references('id')
                  ->on('instituciones')
                  ->onDelete('set null');

            $table->foreign('acudiente_id')
                  ->references('id')
                  ->on('acudientes')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estudiantes', function (Blueprint $table) {
            // Eliminar foreign keys primero
            $table->dropForeign(['institucion_id']);
            $table->dropForeign(['acudiente_id']);
            
            // Eliminar columnas
            $table->dropColumn([
                'fecha_nacimiento',
                'genero',
                'direccion',
                'telefono',
                'institucion_id',
                'estado',
                'acudiente_id'
            ]);
        });
    }
};
