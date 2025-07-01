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
        Schema::table('docentes', function (Blueprint $table) {
            $table->string('telefono', 20)->nullable()->after('user_id');
            $table->string('especialidad', 255)->nullable()->after('telefono');
            $table->date('fecha_contratacion')->nullable()->after('especialidad');
            $table->decimal('salario', 10, 2)->nullable()->after('fecha_contratacion');
            $table->string('horario_trabajo', 255)->nullable()->after('salario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('docentes', function (Blueprint $table) {
            $table->dropColumn(['telefono', 'especialidad', 'fecha_contratacion', 'salario', 'horario_trabajo']);
        });
    }
};
