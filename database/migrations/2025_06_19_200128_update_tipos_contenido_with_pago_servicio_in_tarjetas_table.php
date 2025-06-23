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
            $tiposPermitidos = Tarjeta::$tiposContenidoPermitidos;
            $table->enum('tipo_contenido', $tiposPermitidos)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tarjetas', function (Blueprint $table) {
            $tiposAntiguos = [
                'webView', 'listadoTarjetas', 'videosYoutube', 'pdf', 
                'subTitulo', 'infoAyudaContenido'
            ];
            $table->enum('tipo_contenido', $tiposAntiguos)->nullable()->change();
        });
    }
};
