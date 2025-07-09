<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarjeta extends Model
{
    protected $table = 'tarjetas';

    const TIPO_BASICA = 'tarjetaBasica';
    const TIPO_MEDIANA = 'tarjetaMediana';
    const TIPO_GRANDE = 'tarjetaGrande';
    const INFO_AYUDA = 'infoAyuda';
    const TIPO_YOUTUBE = 'tarjetasYoutube';

    public static $disenoTarjetasPermitidos = [
        self::TIPO_BASICA,
        self::TIPO_MEDIANA,
        self::TIPO_GRANDE,
        self::INFO_AYUDA,
        self::TIPO_YOUTUBE
    ];

    const WEB_VIEW = 'webView';
    const LISTADO_TARJETAS = 'listadoTarjetas';
    const VIDEOS_YOUTUBE = 'videosYoutube';
    const PDF = 'pdf';
    const SUB_TITULO = 'subTitulo';
    const INFO_AYUDA_CONTENIDO = 'infoAyudaContenido';
    const PAGO_DE_SERVICIO = 'pagoDeServicio';
    const CENTRO_DE_SALUD = 'centroDeSalud';
    const MEDIOS_DE_TRANSPORTE = 'mediosDeTransporte';


    public static $tiposContenidoPermitidos = [
        self::WEB_VIEW,
        self::LISTADO_TARJETAS,
        self::VIDEOS_YOUTUBE,
        self::PDF,
        self::SUB_TITULO,
        self::INFO_AYUDA_CONTENIDO,
        self::PAGO_DE_SERVICIO,
        self::CENTRO_DE_SALUD,
        self::MEDIOS_DE_TRANSPORTE,

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
        'titulo_bajada_uno',
        'contenido_puntos',
        'titulo_bajada_dos',
        'contenido_bajada_dos',
        'boton_accion',
        'position',
    ];

    protected $casts = [
        'contenido' => 'array',
        'contenido_puntos' => 'array',
        'fecha_expiracion' => 'datetime',
        'nuevoTicket' => 'boolean',
        'boton_accion' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Tarjeta $tarjeta) {
            if ($tarjeta->isDirty('id_padre') && !is_null($tarjeta->id_padre)) {
                $maxPosition = self::where('id_padre', $tarjeta->id_padre)->max('position');

                $tarjeta->position = ($maxPosition === null ? -1 : $maxPosition) + 1;
            } elseif ($tarjeta->isDirty('id_padre') && is_null($tarjeta->id_padre)) {
                $tarjeta->position = null;
            }
        });

        static::creating(function (Tarjeta $tarjeta) {
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

    public static array $listBasedContentTypes = [
        self::LISTADO_TARJETAS,
        self::PAGO_DE_SERVICIO,
        self::MEDIOS_DE_TRANSPORTE,
        self::CENTRO_DE_SALUD
    ];
}
