<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observadores', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('estudiante_id');
            $table->unsignedBigInteger('docente_id');
            $table->unsignedBigInteger('periodo_id');
            $table->date('fecha');
            $table->text('descripcion');
            $table->text('recomendaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('estudiante_id')
                ->references('id')
                ->on('estudiantes')
                ->onDelete('cascade');

            $table->foreign('docente_id')
                ->references('id')
                ->on('docentes')
                ->onDelete('cascade');

            $table->foreign('periodo_id')
                ->references('id')
                ->on('periodos')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observadores');
    }
};
