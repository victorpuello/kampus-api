<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('franjas_horarias', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('institucion_id');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->integer('duracion_minutos')->default(45);
            $table->enum('estado', ['activo', 'inactivo', 'pendiente'])->default('activo');
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['institucion_id', 'hora_inicio', 'hora_fin']);

            $table->foreign('institucion_id')
                ->references('id')
                ->on('instituciones')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('franjas_horarias');
    }
};
