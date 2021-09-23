<?php

namespace gobela\Models\Administracion;
//*******agregar esta linea******//
use Carbon\Carbon;
//*******************************//
use Illuminate\Database\Eloquent\Model;

class tab_compra extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_compra';

    public function setFeIniAttribute( $value ) {
        $this->attributes['fe_ini'] = (new Carbon($value))->format('Y-m-d');
    }

    public function setFeFinAttribute( $value ) {
        $this->attributes['fe_fin'] = (new Carbon($value))->format('Y-m-d');
    }

    public function setFeEntregaAttribute( $value ) {
        $this->attributes['fe_entrega'] = (new Carbon($value))->format('Y-m-d');
    }

    public function setFeCompraAttribute( $value ) {
        $this->attributes['fe_compra'] = (new Carbon($value))->format('Y-m-d');
    }

    public static $validarCrear = array(
        "solicitud" => "required|numeric",
        "tipo_documento" => "required|numeric",
        "proveedor" => "required",
        "documento" => "required",
        "razon_social" => "required|min:1|max:1200",
        "direccion" => "required",
        "tipo_contrato" => "required|numeric",
        "fecha_inicio" => "required|date_format:d-m-Y",
        "fecha_fin" => "required|date_format:d-m-Y",
        "fecha_entrega" => "required|date_format:d-m-Y",
        "monto_contrato" => "required|numeric",
        "observacion" => "required",
        "fecha_compra" => "required|date_format:d-m-Y",
        "iva" => "required|numeric",
        "ejecutor_entrega" => "required|numeric",
    );

    public static $validarEditar = array(
        "solicitud" => "required|numeric",
        "producto" => "required|numeric",
        "cantidad" => "required|numeric",
        "unidad" => "required|numeric",
        "especificacion" => "required|min:1|max:1200"
    );

    public static $validarOrdenCompra = array(
        "solicitud" => "required|numeric",
        "tipo_documento" => "required",
        "proveedor" => "required",
        "documento" => "required",
        "razon_social" => "required|min:1|max:1200",
        "direccion" => "required",
        "tipo_contrato" => "required",
        "fecha_inicio" => "required|date_format:d-m-Y",
        "fecha_fin" => "required|date_format:d-m-Y",
        "fecha_entrega" => "required|date_format:d-m-Y",
        "monto_contrato" => "required",
        "observacion" => "required",
        "fecha_compra" => "required|date_format:d-m-Y",
        "iva" => "required",
        "ejecutor_entrega" => "required",
    );
}
