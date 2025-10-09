<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tarjeta;
use Carbon\Carbon;

class EliminarTarjetasExpiradas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tarjetas:eliminar-expiradas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina automáticamente las tarjetas que ya expiraron';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hoy = Carbon::now();

        $eliminadas = Tarjeta::where('fecha_expiracion', '<', $hoy)->delete();

        $this->info("Se eliminaron {$eliminadas} tarjetas expiradas.");
    }
}
