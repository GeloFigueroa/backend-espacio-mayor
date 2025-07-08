<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Tarjeta;
use Illuminate\Validation\Rule;


class StoreTarjetaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        $isCreate = $this->isMethod('post');

        return [
            'titulo' => 'nullable|string|max:255',
            'subtitulo' => 'nullable|string|max:700',
            'color' => 'nullable|string|max:255',
            'imagenURL' => 'nullable|string|max:3000',
            'firma' => 'nullable|string|max:255',
            'georeferenciacion' => 'nullable|string',
            'titulo_bajada_uno' => 'nullable|string|max:255',
            'fecha_expiracion' => 'nullable|date',
            'contenido_puntos' => 'nullable|array',
            'contenido_puntos.*' => 'string|max:255',
            'titulo_bajada_dos' => 'nullable|string|max:255',
            'contenido_bajada_dos' => 'nullable|string|max:3000',

            'boton_accion' => 'nullable|boolean',

            'diseno_tarjeta' => [
                $isCreate ? 'required' : 'sometimes',
                Rule::in(Tarjeta::$disenoTarjetasPermitidos),
            ],

            'nuevoTicket' => 'nullable|boolean',
            'id_padre' => 'nullable|integer|exists:listas,id',

            'tipo_contenido' => [
                $isCreate ? 'required' : 'sometimes',
                Rule::in(Tarjeta::$tiposContenidoPermitidos),
            ],

            'contenido' => 'nullable|array',
            'contenido.id_lista' => 'nullable|integer|exists:listas,id',
            'contenido.tipo_lista' => [
                'nullable',
                Rule::requiredIf(fn() => in_array($this->input('tipo_contenido'), Tarjeta::$listBasedContentTypes)),
                Rule::in(Tarjeta::$disenoTarjetasPermitidos),
            ],
            'contenido.url' => 'nullable|url',
        ];
    }
}
