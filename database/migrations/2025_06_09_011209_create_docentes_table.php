<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('docentes', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('telefono', 20)->nullable();
            $table->string('especialidad', 255)->nullable();
            $table->date('fecha_contratacion')->nullable();
            $table->decimal('salario', 10, 2)->nullable();
            $table->string('horario_trabajo', 255)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('docentes');
    }
};
