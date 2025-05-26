<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Tarjeta;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tarjetas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 191)->nullable();
            $table->string('subtitulo', 255)->nullable();
            $table->string('color', 25)->nullable();
            $table->string('imagenURL', 400)->nullable();
            $table->string('firma', 191)->nullable();
            $table->string('georeferenciacion', 255)->nullable();
            $table->dateTime('fecha_expiracion')->nullable();

            $table->enum('diseno_tarjeta', Tarjeta::$disenoTarjetasPermitidos)
                  ->default(Tarjeta::TIPO_BASICA);

            $table->boolean('nuevoTicket')->default(false);

            $table->foreignId('id_padre')->nullable()->constrained('tarjetas')->onDelete('set null');


            $table->enum('tipo_contenido', Tarjeta::$tiposContenidoPermitidos)
                  ->nullable();

            $table->json('contenido')->nullable(); // Coincide con el cast 'array' y el booted()
            $table->timestamps(); // Campos created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tarjetas');
    }
};