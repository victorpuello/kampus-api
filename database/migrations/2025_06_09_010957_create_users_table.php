<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->string('nombre', 255);
            $table->string('apellido', 255);
            $table->string('username', 255)->unique();
            $table->string('email', 255)->unique();
            $table->string('password', 255);
            $table->string('tipo_documento', 10)->nullable();
            $table->string('numero_documento', 20)->nullable();
            $table->unsignedBigInteger('institucion_id');
            $table->string('estado', 50)->default('activo');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('institucion_id')
                ->references('id')
                ->on('instituciones')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
