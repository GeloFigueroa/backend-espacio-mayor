<?php

namespace App\Models;

use App\Enums\ComunaChileEnum;
use App\Enums\RegionChileEnum;
use Illuminate\Database\Eloquent\Casts\AsEnumArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tarjeta extends Model
{
    use HasFactory;
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
        self::TIPO_YOUTUBE,
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
    const BOTON_VER_GUIA = 'botonVerGuia';
    const PUNTO_CARGA_BIP = 'puntoCargaBip';
    const CHILE_CULTURA = 'chileCultura';
    const ABASTIBLE = 'abastible';
    const LIPIGAS = 'lipigas';
    const TIPO_BANNER = 'banner';



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
        self::BOTON_VER_GUIA,
        self::PUNTO_CARGA_BIP,
        self::CHILE_CULTURA,
        self::ABASTIBLE,
        self::LIPIGAS,
        self::TIPO_BANNER,
    ];

    protected $fillable = [
        'titulo',
        'subtitulo',
        'color',
        'imagenURL',
        'firma',
        'georeferenciacion',
        'georeferenciacion_bool',
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
        'etiqueta_regiones_visualizacion',
        'etiqueta_comunas_visualizacion',
    ];

    protected $casts = [
        'contenido' => 'array',
        'contenido_puntos' => 'array',
        'fecha_expiracion' => 'datetime',
        'nuevoTicket' => 'boolean',
        'boton_accion' => 'boolean',
        'georeferenciacion_bool' => 'boolean',
        'etiqueta_regiones_visualizacion' => AsEnumArrayObject::class . ':' . RegionChileEnum::class,
        'etiqueta_comunas_visualizacion' => AsEnumArrayObject::class . ':' . ComunaChileEnum::class,
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
