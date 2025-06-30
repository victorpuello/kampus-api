<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignatura_prerequisitos', function (Blueprint $table) {
            $table->unsignedBigInteger('asignatura_id');
            $table->unsignedBigInteger('prerequisito_id');
            
            $table->primary(['asignatura_id', 'prerequisito_id']);
            
            $table->foreign('asignatura_id')
                  ->references('id')
                  ->on('asignaturas')
                  ->onDelete('cascade');
                  
            $table->foreign('prerequisito_id')
                  ->references('id')
                  ->on('asignaturas')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignatura_prerequisitos');
    }
}; 