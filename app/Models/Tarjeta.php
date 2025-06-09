<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarjeta extends Model
{
    protected $table = 'tarjetas';

    const TIPO_BASICA = 'tarjetaBasica';
    const TIPO_MEDIANA = 'tarjetaMediana';
    const TIPO_GRANDE = 'tarjetaGrande';

    public static $disenoTarjetasPermitidos = [
        self::TIPO_BASICA,
        self::TIPO_MEDIANA,
        self::TIPO_GRANDE,
    ];

    const WEB_VIEW = 'webView';
    const LISTADO_TARJETAS = 'listadoTarjetas';
    const VIDEOS_YOUTUBE = 'videosYoutube';
    const PDF = 'pdf';
    const SUB_TITULO = 'subTitulo';

    public static $tiposContenidoPermitidos = [
        self::WEB_VIEW,
        self::LISTADO_TARJETAS,
        self::VIDEOS_YOUTUBE,
        self::PDF,
        self::SUB_TITULO,
    ];

    protected $fillable = [
        'titulo',
        'subtitulo',
        'color',
        'imagenURL',
        'firma',
        'georeferenciacion',
        'fecha_expiracion',
        'diseno_tarjeta',
        'nuevoTicket',
        'id_padre',
        'tipo_contenido',
        'contenido',
    ];

    protected $casts = [
        'contenido' => 'array',
        'fecha_expiracion' => 'datetime',
        'nuevoTicket' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($tarjeta) {
            $tarjeta->contenido = array_merge([
                'id_lista' => null,
                'tipo_lista' => null,
                'url' => null
            ], (array) $tarjeta->contenido);
        });
    }


    public function lista()
    {
        return $this->belongsTo(Lista::class);
    }
}
