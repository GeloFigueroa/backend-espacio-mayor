<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
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
            'chileCultura',
            'abastible',
            'lipigas',
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
            'puntoCargaBip',
            'chileCultura',
            // 👈 sin 'abastible'
            // 👈 sin 'lipigas'
        ];

        $enumList = "'" . implode("','", $tiposOriginales) . "'";
        DB::statement("ALTER TABLE tarjetas MODIFY COLUMN tipo_contenido ENUM($enumList) NOT NULL");
    }
};
