<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupos', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->string('nombre', 255);
            $table->string('descripcion')->nullable();
            $table->unsignedInteger('capacidad')->default(35);
            $table->unsignedBigInteger('sede_id');
            $table->unsignedBigInteger('anio_id');
            $table->unsignedBigInteger('grado_id');
            $table->unsignedBigInteger('director_docente_id')->nullable();
            $table->unsignedBigInteger('institucion_id')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('sede_id')
                ->references('id')
                ->on('sedes')
                ->onDelete('restrict');

            $table->foreign('anio_id')
                ->references('id')
                ->on('anios')
                ->onDelete('restrict');

            $table->foreign('grado_id')
                ->references('id')
                ->on('grados')
                ->onDelete('restrict');

            $table->foreign('director_docente_id')
                ->references('id')
                ->on('docentes')
                ->onDelete('set null');

            $table->foreign('institucion_id')
                ->references('id')
                ->on('instituciones')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};
