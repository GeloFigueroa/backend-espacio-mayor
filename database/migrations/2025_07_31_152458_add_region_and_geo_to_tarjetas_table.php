<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('tarjetas', 'etiqueta_regiones_visualizacion')) {
            Schema::table('tarjetas', function (Blueprint $table) {
                $table->json('etiqueta_regiones_visualizacion')->nullable()->after('contenido_bajada_dos');
            });
        }
        DB::table('tarjetas')->update(['georeferenciacion' => false]);
        Schema::table('tarjetas', function (Blueprint $table) {
            $table->boolean('georeferenciacion')->default(false)->change();
        });
    }
    public function down()
    {
        if (Schema::hasColumn('tarjetas', 'etiqueta_regiones_visualizacion')) {
            Schema::table('tarjetas', function (Blueprint $table) {
                $table->dropColumn('etiqueta_regiones_visualizacion');
            });
        }

        if (Schema::hasColumn('tarjetas', 'georeferenciacion')) {
            Schema::table('tarjetas', function (Blueprint $table) {
                $table->string('georeferenciacion')->nullable()->change();
            });
        }
    }
};
