<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Model;

class tab_ejercicio_fiscal extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'configuracion.tab_ejercicio_fiscal';

    public static $validar = array(
        "ejercicio" => "required|integer"
    );

    public static $validarCrear = array(
        "ejercicio" => "required|integer",
        "fecha_desde" => "required|date_format:d-m-Y|before:fecha_hasta",
        "fecha_hasta" => "required|date_format:d-m-Y|after:fecha_desde",
    );

    public static $validarEditar = array(
        "ejercicio" => "required|integer",
        "fecha_desde" => "required|date_format:d-m-Y|before:fecha_hasta",
        "fecha_hasta" => "required|date_format:d-m-Y|after:fecha_desde",
    );

    public function scopeSearch($query, $q, $sortBy)
    {
      switch ($sortBy) {
            case 'id':
                return $query->where('id', '=', $q);
            break;
                default:
                return $query;
            break;
      }
    }
}
