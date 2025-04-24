<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tarjetas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo',30);
            $table->string('subtitulo',30);
            $table->string('categoria',30);
            $table->string('color',15);
            $table->string('imagenURL',100);
            $table->string('firma',30);
            $table->string('georeferenciacion',30);
            $table->string('tiempoExpiracion',30);
            $table->string('tipo',15);
            $table->boolean('favorito', false);
            $table->boolean('nuevoTicket',false);
            $table->string('path',100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarjetas');
    }
};
