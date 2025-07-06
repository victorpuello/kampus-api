<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('destinatario_user_id');
            $table->string('titulo', 255);
            $table->text('mensaje')->nullable();
            $table->timestamp('leido_at')->nullable();
            $table->enum('canal', ['sistema', 'email', 'push']);
            $table->timestamps();

            $table->foreign('destinatario_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
