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
        Schema::table('periodos', function (Blueprint $table) {
            $table->date('fecha_inicio')->after('nombre');
            $table->date('fecha_fin')->after('fecha_inicio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('periodos', function (Blueprint $table) {
            $table->dropColumn(['fecha_inicio', 'fecha_fin']);
        });
    }
};
