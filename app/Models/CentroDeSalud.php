<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class CentroDeSalud extends Model
{
  use HasFactory;

    /**
     *
     * @var string
     */
    protected $table = 'centros_de_salud_csv';

    /**
     *
     * @var array
     */
    protected $guarded = [];

    /**
     *
     * @var bool
     */
    public $timestamps = false;
}
