<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archivos', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->string('nombre', 100);
            $table->string('ruta', 255);
            $table->string('tipo', 50);
            $table->unsignedBigInteger('tamano');
            $table->unsignedBigInteger('actividad_id')->nullable();
            $table->unsignedBigInteger('observador_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('actividad_id')
                  ->references('id')
                  ->on('actividades')
                  ->onDelete('cascade');
                  
            $table->foreign('observador_id')
                  ->references('id')
                  ->on('observadores')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archivos');
    }
}; 