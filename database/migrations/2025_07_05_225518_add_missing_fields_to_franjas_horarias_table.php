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
        Schema::table('franjas_horarias', function (Blueprint $table) {
            $table->string('nombre')->after('institucion_id');
            $table->text('descripcion')->nullable()->after('nombre');
            $table->integer('duracion_minutos')->default(45)->after('hora_fin');
            $table->enum('estado', ['activo', 'inactivo', 'pendiente'])->default('activo')->after('duracion_minutos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('franjas_horarias', function (Blueprint $table) {
            $table->dropColumn(['nombre', 'descripcion', 'duracion_minutos', 'estado']);
        });
    }
};
