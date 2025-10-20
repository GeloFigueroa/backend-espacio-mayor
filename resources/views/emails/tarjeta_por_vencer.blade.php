<x-mail::message>
# Aviso de vencimiento de tarjeta

La tarjeta **{{ $tarjeta->titulo }}** vencerá el **{{ \Carbon\Carbon::parse($tarjeta->fecha_expiracion)->format('d/m/Y') }}**.  

Por favor, asegúrate de tomar las medidas necesarias antes de su fecha de vencimiento para mantener todo actualizado.

<x-mail::button :url="url('/tarjetas')">
Ver Tarjetas
</x-mail::button>

**El equipo de Espacio Mayor**
</x-mail::message>
