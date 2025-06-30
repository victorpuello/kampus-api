<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudiante_acudiente', function (Blueprint $table) {
            $table->unsignedBigInteger('estudiante_id');
            $table->unsignedBigInteger('acudiente_id');
            $table->string('parentesco', 50)->nullable();
            
            $table->primary(['estudiante_id', 'acudiente_id']);
            
            $table->foreign('estudiante_id')
                  ->references('id')
                  ->on('estudiantes')
                  ->onDelete('cascade');
                  
            $table->foreign('acudiente_id')
                  ->references('id')
                  ->on('acudientes')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiante_acudiente');
    }
}; 