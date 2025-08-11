<?php

namespace App\Enums;

enum RegionChileEnum: string
{
    case ARICA_Y_PARINACOTA = 'arica_y_parinacota';
    case TARAPACA = 'tarapaca';
    case ANTOFAGASTA = 'antofagasta';
    case ATACAMA = 'atacama';
    case COQUIMBO = 'coquimbo';
    case VALPARAISO = 'valparaiso';
    case METROPOLITANA_DE_SANTIAGO = 'metropolitana_de_santiago';
    case OHIGGINS = 'ohiggins';
    case MAULE = 'maule';
    case NUBLE = 'nuble';
    case BIOBIO = 'biobio';
    case LA_ARAUCANIA = 'la_araucania';
    case LOS_RIOS = 'los_rios';
    case LOS_LAGOS = 'los_lagos';
    case AYSEN = 'aysen';
    case MAGALLANES = 'magallanes';
    
    public function getDisplayName(): string
    {
        return match ($this) {
            self::ARICA_Y_PARINACOTA => 'Arica y Parinacota',
            self::TARAPACA => 'Tarapacá',
            self::ANTOFAGASTA => 'Antofagasta',
            self::ATACAMA => 'Atacama',
            self::COQUIMBO => 'Coquimbo',
            self::VALPARAISO => 'Valparaíso',
            self::METROPOLITANA_DE_SANTIAGO => 'Metropolitana de Santiago',
            self::OHIGGINS => "Libertador General Bernardo O'Higgins",
            self::MAULE => 'Maule',
            self::NUBLE => 'Ñuble',
            self::BIOBIO => 'Biobío',
            self::LA_ARAUCANIA => 'La Araucanía',
            self::LOS_RIOS => 'Los Ríos',
            self::LOS_LAGOS => 'Los Lagos',
            self::AYSEN => 'Aysén del General Carlos Ibáñez del Campo',
            self::MAGALLANES => 'Magallanes y de la Antártica Chilena',
        };
    }
}