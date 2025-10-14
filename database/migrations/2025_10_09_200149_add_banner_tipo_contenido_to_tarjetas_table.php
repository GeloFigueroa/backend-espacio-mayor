<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tarjetas', function (Blueprint $table) {
            // Si tipo_contenido ya existe y es string, no es necesario cambiar tipo
            // Solo asegurarse de que pueda almacenar el nuevo valor
            $table->string('tipo_contenido')->change();
        });
    }

    public function down(): void
    {
        Schema::table('tarjetas', function (Blueprint $table) {
            // Revertir cambios si fuera necesario
            $table->string('tipo_contenido')->change();
        });
    }
};
