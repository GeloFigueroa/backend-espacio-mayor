<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateTipoContenidoPermitidoInTuTabla extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE tarjetas MODIFY tipo_contenido ENUM(
            'webView',
            'listadoTarjetas',
            'videosYoutube',
            'pdf',
            'subTitulo',
            'infoAyudaContenido',
            'pagoDeServicio',
            'centroDeSalud',
            'mediosDeTransporte',
            'botonVerGuia'
        )");
    }

    public function down()
    {
        DB::statement("ALTER TABLE tarjetas MODIFY tipo_contenido ENUM(
            'webView',
            'listadoTarjetas',
            'videosYoutube',
            'pdf',
            'subTitulo',
            'infoAyudaContenido',
            'pagoDeServicio',
            'centroDeSalud',
            'mediosDeTransporte'
        )");
    }
}


