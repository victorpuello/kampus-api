<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aulas', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('institucion_id');
            $table->string('nombre', 100);
            $table->enum('tipo', ['Salón', 'Laboratorio', 'Auditorio', 'Deportivo']);
            $table->integer('capacidad')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('institucion_id')
                  ->references('id')
                  ->on('instituciones')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aulas');
    }
}; 