<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institution_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('institucion_id');
            $table->string('clave', 100);
            $table->text('valor');
            $table->timestamps();

            $table->unique(['institucion_id', 'clave']);

            $table->foreign('institucion_id')
                ->references('id')
                ->on('instituciones')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_settings');
    }
};
