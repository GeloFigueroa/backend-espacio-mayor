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
            $disenosPermitidos = Tarjeta::$disenoTarjetasPermitidos;
            $contenidosPermitidos = Tarjeta::$tiposContenidoPermitidos;
            
            $table->enum('diseno_tarjeta', $disenosPermitidos)
                  ->default(Tarjeta::TIPO_BASICA)
                  ->change();

            $table->enum('tipo_contenido', $contenidosPermitidos)
                  ->nullable()
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('tarjetas', function (Blueprint $table) {
            $disenosAntiguos = ['tarjetaBasica', 'tarjetaMediana', 'tarjetaGrande', 'infoAyuda', 'tarjetasYoutube']; // O el estado anterior que corresponda
            $contenidosAntiguos = ['webView', 'listadoTarjetas', 'videosYoutube', 'pdf', 'subTitulo', 'infoAyudaContenido']; // O el estado anterior
            
            $table->enum('diseno_tarjeta', $disenosAntiguos)->default('tarjetaBasica')->change();
            $table->enum('tipo_contenido', $contenidosAntiguos)->nullable()->change();
        });
    }
};
