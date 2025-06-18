<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Tarjeta;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listas', function (Blueprint $table) {
            $tiposPermitidos = Tarjeta::$disenoTarjetasPermitidos;
            $table->enum('tipoLista', $tiposPermitidos)->change();
        });
    }

    public function down(): void
    {
        Schema::table('listas', function (Blueprint $table) {
            // Lógica para revertir si es necesario
            $tiposAntiguos = ['tarjetaBasica', 'tarjetaMediana', 'tarjetaGrande'];
            $table->enum('tipoLista', $tiposAntiguos)->change();
        });
    }
};
