<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tarjeta;
use Illuminate\Support\Facades\Mail;
use App\Mail\TarjetaPorVencerMail;

class NotificarTarjetasPorVencer extends Command
{
    protected $signature = 'notificar:notificar-por-vencer';
    protected $description = 'Notifica por correo las tarjetas que están próximas a expirar';

    public function handle()
    {
        $diasAntes = 1; 
        $fechaObjetivo = now()->addDays($diasAntes)->toDateString();

        $tarjetas = Tarjeta::whereDate('fecha_expiracion', '=', $fechaObjetivo)->get();

        foreach ($tarjetas as $tarjeta) {
            Mail::to(['gfigueroa@conectamayor.cl', 'gelofiguer@gmail.com',])
                ->send(new TarjetaPorVencerMail($tarjeta));

            $this->info("Correo enviado para tarjeta ID {$tarjeta->id}");
        }
    }
}
