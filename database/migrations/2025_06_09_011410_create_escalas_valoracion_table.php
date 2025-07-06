<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escalas_valoracion', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('institucion_id');
            $table->string('nombre', 100);
            $table->string('abreviatura', 10)->nullable();
            $table->decimal('valor_minimo', 5, 2);
            $table->decimal('valor_maximo', 5, 2);
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
        Schema::dropIfExists('escalas_valoracion');
    }
};
