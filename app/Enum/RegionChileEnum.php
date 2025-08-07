<?php

namespace App\Enums;

/**
 * Enum para las regiones de Chile.
 * Proporciona un conjunto de casos estandarizados para evitar errores de tipeo
 * y facilitar la validación y el manejo de datos de regiones.
 */
enum RegionChileEnum: string
{
    case ARICA_Y_PARINACOTA = 'Arica y Parinacota';
    case TARAPACA = 'Tarapacá';
    case ANTOFAGASTA = 'Antofagasta';
    case ATACAMA = 'Atacama';
    case COQUIMBO = 'Coquimbo';
    case VALPARAISO = 'Valparaíso';
    case METROPOLITANA = 'Metropolitana de Santiago';
    case OHIGGINS = 'Libertador General Bernardo O\'Higgins';
    case MAULE = 'Maule';
    case NUBLE = 'Ñuble';
    case BIOBIO = 'Biobío';
    case ARAUCANIA = 'La Araucanía';
    case LOS_RIOS = 'Los Ríos';
    case LOS_LAGOS = 'Los Lagos';
    case AYSEN = 'Aysén del General Carlos Ibáñez del Campo';
    case MAGALLANES = 'Magallanes y de la Antártica Chilena';
}
