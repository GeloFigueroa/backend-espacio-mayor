<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Lista COMPLETA de todos los valores permitidos, incluyendo el nuevo.
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
            'chileCultura', // <-- El nuevo valor que agregaste
        ];

        $enumList = "'" . implode("','", $tiposPermitidos) . "'";
        DB::statement("ALTER TABLE tarjetas MODIFY COLUMN tipo_contenido ENUM($enumList) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // La lista sin el nuevo valor, para poder revertir la migración
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
        ];

        $enumList = "'" . implode("','", $tiposOriginales) . "'";
        DB::statement("ALTER TABLE tarjetas MODIFY COLUMN tipo_contenido ENUM($enumList) NOT NULL");
    }
};