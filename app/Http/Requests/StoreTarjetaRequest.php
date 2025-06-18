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
            'subtitulo' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'imagenURL' => 'nullable|string|max:3000',
            'firma' => 'nullable|string|max:255',
            'georeferenciacion' => 'nullable|string',
            'fecha_expiracion' => 'nullable|date',

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
                Rule::requiredIf(fn() => $this->input('tipo_contenido') === Tarjeta::LISTADO_TARJETAS),
                Rule::in(Tarjeta::$disenoTarjetasPermitidos),
            ],
            'contenido.url' => 'nullable|url',
        ];
    }
}
