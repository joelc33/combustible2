<?php

namespace gobela\Models\Administracion;
//*******agregar esta linea******//
use Carbon\Carbon;
//*******************************//
use Illuminate\Database\Eloquent\Model;

class tab_proceso_retencion_factura extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_proceso_retencion_factura';

    public function setFeEmisionAttribute( $value ) {
        $this->attributes['fe_emision'] = (new Carbon($value))->format('Y-m-d');
    }

    public static $validarCrear = array(
        "numero_factura" => "required|numeric",
        "numero_control" => "required|numeric",
        "fecha_emision" => "required|date_format:d-m-Y",
        "producto" => "required|numeric",
        "cant_requerida" => "required|numeric",
        "valor_unitario" => "required|numeric",
        "cant_factura" => "required|numeric",
        "monto_factura" => "required|numeric",
        "base_imponible" => "required|numeric",
        "iva" => "required|numeric",
        "iva_monto" => "required|numeric",
        "iva_retencion" => "required|numeric",
        "iva_retencion_monto" => "required|numeric",
        "total_pagar" => "required|numeric",
        "concepto" => "required",
    );
}
