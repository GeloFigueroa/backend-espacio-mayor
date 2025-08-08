<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateEnumTipoContenidoTarjetasTable extends Migration
{
    public function up()
    {
        // Solo funciona con MySQL (no con SQLite/PostgreSQL)
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
        // Revertir la adición si fuera necesario
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

