<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Tarjeta;

class UpdateTarjetaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => 'nullable|string|max:255',
            'subtitulo' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'imagenURL' => 'nullable|string',
            'firma' => 'nullable|string|max:255',
            'georeferenciacion' => 'nullable|string',
            'fecha_expiracion' => 'nullable|date',

            'diseno_tarjeta' => ['sometimes', Rule::in(Tarjeta::$disenoTarjetasPermitidos)],
            'nuevoTicket' => 'nullable|boolean',
            'id_padre' => 'nullable|integer|exists:tarjetas,id',

            'tipo_contenido' => ['sometimes', Rule::in(Tarjeta::$tiposContenidoPermitidos)],
            'contenido' => 'nullable|array',
            'contenido.id_lista' => 'nullable|integer|exists:listas,id',
            'contenido.tipo_lista' => [
                Rule::requiredIf($this->input('tipo_contenido') === Tarjeta::LISTADO_TARJETAS),
                Rule::in(Tarjeta::$disenoTarjetasPermitidos),
            ],
            'contenido.url' => 'nullable|url',
        ];
    }
}
