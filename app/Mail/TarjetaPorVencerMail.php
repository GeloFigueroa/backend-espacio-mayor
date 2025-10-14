<?php

namespace App\Mail;

use App\Models\Tarjeta;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TarjetaPorVencerMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tarjeta;

    public function __construct(Tarjeta $tarjeta)
    {
        $this->tarjeta = $tarjeta;
    }

    public function build()
    {
        return $this->subject('Aviso: Tarjeta por vencer')
                    ->markdown('emails.tarjeta_por_vencer')
                    ->with([
                        'tarjeta' => $this->tarjeta,
                    ]);
    }
}
