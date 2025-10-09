<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use App\Mail\TarjetaPorVencerMail;
use App\Console\Commands\NotificarTarjetasPorVencer;
use App\Models\Tarjeta;

class NotificarTarjetasPorVencerTest extends TestCase
{
    /** @test */
    public function prueba_envio_de_correo_tarjetas_por_vencer()
    {
        // Simula que se envían correos
        Mail::fake();

        // Crea una tarjeta de prueba que vence mañana
        $tarjeta = Tarjeta::factory()->create([
            'fecha_expiracion' => now()->addDay()->toDateString(),
        ]);

        // Ejecuta tu comando
        $this->artisan('notificar:notificar-por-vencer')
             ->assertExitCode(0);

        // Verifica que se intentó enviar un correo
        Mail::assertSent(TarjetaPorVencerMail::class, function ($mail) use ($tarjeta) {
            return $mail->tarjeta->id === $tarjeta->id;
        });
    }
}
