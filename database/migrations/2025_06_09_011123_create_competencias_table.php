<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competencias', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->text('descripcion');
            $table->unsignedBigInteger('area_id');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('area_id')
                ->references('id')
                ->on('areas')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competencias');
    }
};
