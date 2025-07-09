<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
     {
        Schema::table('tarjetas', function (Blueprint $table) {
            $table->json('contenido_puntos')->nullable()->after('contenido');

            $table->boolean('boton_accion')->default(false)->after('contenido_puntos');
        });
    }

    public function down(): void
    {
        Schema::table('tarjetas', function (Blueprint $table) {
            $table->dropColumn(['contenido_puntos', 'boton_accion']);
        });
    }
};
