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
        Schema::table('instituciones', function (Blueprint $table) {
            $table->string('slogan')->nullable()->after('siglas');
            $table->string('dane')->nullable()->after('slogan');
            $table->string('resolucion_aprobacion')->nullable()->after('dane');
            $table->text('direccion')->nullable()->after('resolucion_aprobacion');
            $table->string('telefono')->nullable()->after('direccion');
            $table->string('email')->nullable()->after('telefono');
            $table->string('rector')->nullable()->after('email');
            $table->string('escudo')->nullable()->after('rector'); // Ruta de la imagen
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instituciones', function (Blueprint $table) {
            $table->dropColumn([
                'slogan',
                'dane',
                'resolucion_aprobacion',
                'direccion',
                'telefono',
                'email',
                'rector',
                'escudo'
            ]);
        });
    }
};
