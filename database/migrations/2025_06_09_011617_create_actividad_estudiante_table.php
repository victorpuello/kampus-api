<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividad_estudiante', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('actividad_id');
            $table->unsignedBigInteger('estudiante_id');
            $table->decimal('calificacion', 5, 2)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['actividad_id', 'estudiante_id']);
            
            $table->foreign('actividad_id')
                  ->references('id')
                  ->on('actividades')
                  ->onDelete('cascade');
                  
            $table->foreign('estudiante_id')
                  ->references('id')
                  ->on('estudiantes')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividad_estudiante');
    }
}; 