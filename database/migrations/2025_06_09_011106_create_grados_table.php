<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grados', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->string('nombre', 255);
            $table->enum('nivel', [
                'Preescolar',
                'Básica Primaria',
                'Básica Secundaria',
                'Educación Media',
            ]);
            $table->unsignedBigInteger('institucion_id');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('institucion_id')
                ->references('id')
                ->on('instituciones')
                ->onDelete('cascade');
            $table->unique(['institucion_id', 'nombre'], 'institucion_nombre_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grados');
    }
};
