<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tiposPermitidos = [
            'webView',
            'listadoTarjetas',
            'videosYoutube',
            'pdf',
            'subTitulo',
            'infoAyudaContenido',
            'pagoDeServicio',
            'centroDeSalud',
            'mediosDeTransporte',
            'botonVerGuia',
            'puntoCargaBip',
        ];

        $enumList = "'" . implode("','", $tiposPermitidos) . "'";

        DB::statement("ALTER TABLE tarjetas MODIFY COLUMN tipo_contenido ENUM($enumList) NOT NULL");
    }

    public function down(): void
    {
        $tiposOriginales = [
            'webView',
            'listadoTarjetas',
            'videosYoutube',
            'pdf',
            'subTitulo',
            'infoAyudaContenido',
            'pagoDeServicio',
            'centroDeSalud',
            'mediosDeTransporte',
            'botonVerGuia',
        ];

        $enumList = "'" . implode("','", $tiposOriginales) . "'";

        DB::statement("ALTER TABLE tarjetas MODIFY COLUMN tipo_contenido ENUM($enumList) NOT NULL");
    }
};