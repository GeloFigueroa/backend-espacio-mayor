<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tarjetas', function (Blueprint $table) {
            $table->string('titulo_bajada_uno')->nullable();
            $table->string('titulo_bajada_dos')->nullable();
            $table->text('contenido_bajada_dos')->nullable();
        });
    }

    public function down()
    {
        Schema::table('tarjetas', function (Blueprint $table) {
            $table->dropColumn([
                'titulo_bajada_uno',
                'titulo_bajada_dos',
                'contenido_bajada_dos',
            ]);
        });
    }
};
