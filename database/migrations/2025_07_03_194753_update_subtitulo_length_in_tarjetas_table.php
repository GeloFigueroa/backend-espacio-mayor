<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tarjetas', function (Blueprint $table) {
            $table->string('subtitulo', 700)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tarjetas', function (Blueprint $table) {
            $table->string('subtitulo', 255)->nullable()->change();
        });
    }
};