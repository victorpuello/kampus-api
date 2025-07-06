<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('criterios_promocion', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('anio_id');
            $table->decimal('nota_minima_aprobacion', 5, 2);
            $table->integer('max_areas_reprobadas');
            $table->decimal('asistencia_minima', 5, 2)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('anio_id')
                ->references('id')
                ->on('anios')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criterios_promocion');
    }
};
