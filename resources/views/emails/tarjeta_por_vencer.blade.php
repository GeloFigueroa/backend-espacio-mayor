<x-mail::message>
# Aviso de vencimiento de tarjeta

La tarjeta **{{ $tarjeta->titulo }}** vencerá el **{{ \Carbon\Carbon::parse($tarjeta->fecha_expiracion)->format('d/m/Y') }}**.  
Por favor, tomen las medidas necesarias.

<x-mail::button :url="url('/tarjetas')">
Ver Tarjetas
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
