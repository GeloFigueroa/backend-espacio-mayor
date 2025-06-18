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
            // Leemos la lista MÁS RECIENTE de diseños desde el modelo Tarjeta
            $tiposPermitidos = Tarjeta::$disenoTarjetasPermitidos;

            // Usamos el método ->change() para modificar la columna existente
            // y actualizar su lista de valores ENUM.
            $table->enum('diseno_tarjeta', $tiposPermitidos)->default(Tarjeta::TIPO_BASICA)->change();
        });
    }

    // El método down() puede quedar vacío o puedes adaptarlo para revertir al estado anterior
    public function down(): void
    {
        Schema::table('tarjetas', function (Blueprint $table) {
            $tiposAntiguos = ['tarjetaBasica', 'tarjetaMediana', 'tarjetaGrande', 'infoAyuda'];
            $table->enum('diseno_tarjeta', $tiposAntiguos)->default('tarjetaBasica')->change();
        });
    }
};
