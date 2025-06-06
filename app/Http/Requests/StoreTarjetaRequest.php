<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Tarjeta;
use Illuminate\Validation\Rule;


class StoreTarjetaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
{
    $isCreate = $this->isMethod('post');

    return [
        'titulo' => 'nullable|string|max:255',
        'subtitulo' => 'nullable|string|max:255',
        'color' => 'nullable|string|max:255',
        'imagenURL' => 'nullable|string',
        'firma' => 'nullable|string|max:255',
        'georeferenciacion' => 'nullable|string',
        'fecha_expiracion' => 'nullable|date',

        'diseno_tarjeta' => [
            $isCreate ? 'required' : 'sometimes',
            Rule::in(Tarjeta::$disenoTarjetasPermitidos),
        ],

        'nuevoTicket' => 'nullable|boolean',
        'id_padre' => 'nullable|integer|exists:tarjetas,id',

        'tipo_contenido' => [
            $isCreate ? 'required' : 'sometimes',
            Rule::in(Tarjeta::$tiposContenidoPermitidos),
        ],

        'contenido' => 'nullable|array',
        'contenido.id_lista' => 'nullable|integer|exists:listas,id',
        'contenido.tipo_lista' => [
            Rule::requiredIf(function () {
                return $this->input('tipo_contenido') === Tarjeta::LISTADO_TARJETAS;
            }),
            'nullable',
        ],
        'contenido.url' => 'nullable|url',
    ];
}

}
