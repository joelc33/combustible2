<?php

namespace gobela\Models\Administracion;
//*******agregar esta linea******//
use Carbon\Carbon;
//*******************************//
use Illuminate\Database\Eloquent\Model;

class tab_iva_factura extends Model
{
    //
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_iva_factura';

    public function setFeDesdeAttribute( $value ) {
        $this->attributes['fe_desde'] = (new Carbon($value))->format('Y-m-d');
    }

    public function setFeHastaAttribute( $value ) {
        $this->attributes['fe_hasta'] = (new Carbon($value))->format('Y-m-d');
    }
    
    public static $validarCrear = array(
        "denominacion" => "required|numeric",
        "fecha_desde" => "required|date_format:d-m-Y|before:fecha_hasta",
        "fecha_hasta" => "required|date_format:d-m-Y|after:fecha_desde",
    );

    public static $validarEditar = array(
        "denominacion" => "required|numeric",
        "fecha_desde" => "required|date_format:d-m-Y|before:fecha_hasta",
        "fecha_hasta" => "required|date_format:d-m-Y|after:fecha_desde",
    );

    public function scopeSearch($query, $q, $sortBy)
    {
      switch ($sortBy) {
            case 'id':
                return $query->where('nu_iva_factura', '=', $q);
            break;
                default:
                return $query;
            break;
      }
    }
}
