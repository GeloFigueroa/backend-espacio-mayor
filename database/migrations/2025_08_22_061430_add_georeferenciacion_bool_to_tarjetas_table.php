<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tarjetas', function (Blueprint $table) {
            $table->string('georeferenciacion')->nullable()->change();

            $table->boolean('georeferenciacion_bool')->nullable()->after('georeferenciacion');
        });
    }

    public function down(): void
    {
        Schema::table('tarjetas', function (Blueprint $table) {
            $table->string('georeferenciacion')->nullable()->change();
            $table->dropColumn('georeferenciacion_bool');
        });
    }
};
