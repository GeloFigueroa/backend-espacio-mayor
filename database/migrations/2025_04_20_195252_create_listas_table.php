<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Tarjeta;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listas', function (Blueprint $table) {
            $table->id();
            $table->string('tituloTarjeta');
            $table->enum('tipoLista', Tarjeta::$disenoTarjetasPermitidos)
                  ->default(Tarjeta::TIPO_BASICA);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listas');
    }
};
