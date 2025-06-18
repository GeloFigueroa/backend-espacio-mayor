<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Tarjeta;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tarjetas', function (Blueprint $table) {
            $tiposPermitidos = Tarjeta::$disenoTarjetasPermitidos;
            $table->enum('diseno_tarjeta', $tiposPermitidos)->default(Tarjeta::TIPO_BASICA)->change();
        });
    }

    public function down(): void
    {
        Schema::table('tarjetas', function (Blueprint $table) {
            $tiposAntiguos = ['tarjetaBasica', 'tarjetaMediana', 'tarjetaGrande'];
            $table->enum('diseno_tarjeta', $tiposAntiguos)->default('tarjetaBasica')->change();
        });
    }
};
