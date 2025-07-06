<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignaturas', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->string('nombre', 255);
            $table->string('codigo', 10)->nullable();
            $table->text('descripcion')->nullable();
            $table->decimal('porcentaje_area', 5, 2);
            $table->unsignedBigInteger('area_id');
            $table->unsignedBigInteger('institucion_id')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('area_id')
                ->references('id')
                ->on('areas')
                ->onDelete('cascade');

            $table->foreign('institucion_id')
                ->references('id')
                ->on('instituciones')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignaturas');
    }
};
