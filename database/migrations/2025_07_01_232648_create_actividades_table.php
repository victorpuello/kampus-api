<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividades', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('asignacion_id');
            $table->unsignedBigInteger('periodo_id');
            $table->string('titulo', 100);
            $table->text('descripcion');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->decimal('porcentaje', 5, 2);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('asignacion_id')
                ->references('id')
                ->on('asignaciones')
                ->onDelete('cascade');

            $table->foreign('periodo_id')
                ->references('id')
                ->on('periodos')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};
