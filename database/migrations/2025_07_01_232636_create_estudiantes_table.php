<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('user_id')->nullable()->unique();
            $table->string('codigo_estudiantil', 50)->unique();
            $table->unsignedBigInteger('grupo_id')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('genero', ['M', 'F', 'O'])->default('M');
            $table->string('direccion', 255)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->unsignedBigInteger('institucion_id')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->unsignedBigInteger('acudiente_id')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('grupo_id')
                ->references('id')
                ->on('grupos')
                ->onDelete('set null');

            $table->foreign('institucion_id')
                ->references('id')
                ->on('instituciones')
                ->onDelete('set null');

            $table->foreign('acudiente_id')
                ->references('id')
                ->on('acudientes')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
