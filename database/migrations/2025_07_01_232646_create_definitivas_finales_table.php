<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('definitivas_finales', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->decimal('calificacion', 5, 2);
            $table->unsignedBigInteger('estudiante_id');
            $table->unsignedBigInteger('anio_id');
            $table->timestamps();

            $table->unique(['estudiante_id', 'anio_id']);

            $table->foreign('estudiante_id')
                ->references('id')
                ->on('estudiantes')
                ->onDelete('cascade');

            $table->foreign('anio_id')
                ->references('id')
                ->on('anios')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('definitivas_finales');
    }
};
