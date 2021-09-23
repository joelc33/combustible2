<?php

namespace gobela\Models\Administracion;
//*******agregar esta linea******//
use gobela\Models\Administracion\tab_iva_factura;
use DB;
//*******************************//
use Illuminate\Database\Eloquent\Model;

class tab_compra_detalle extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_compra_detalle';

    public static $validarCrear = array(
        "solicitud" => "required|numeric",
        "producto" => "required|numeric",
        "cantidad" => "required|numeric",
        "unidad" => "required|numeric",
        "especificacion" => "required|min:1|max:1200"
    );

    public static $validarEditar = array(
        "solicitud" => "required|numeric",
        "producto" => "required|numeric",
        "cantidad" => "required|numeric",
        "unidad" => "required|numeric",
        "especificacion" => "required|min:1|max:1200"
    );

    protected function moTotal($codigo){

        $tab_compra_detalle = tab_compra_detalle::select( DB::raw("coalesce( SUM(mo_precio_total), 0) as mo_total"))
        ->where('id_tab_compra', '=', $codigo)
        ->first();
    
        return $tab_compra_detalle->mo_total;
    }

    protected function moTotalIva( $codigo, $iva){

        $tab_iva_factura = tab_iva_factura::select( 'nu_iva_factura')
        ->where('id', '=', $iva)
        ->first();

        $tab_compra_detalle = tab_compra_detalle::select( DB::raw("coalesce( SUM(mo_precio_total), 0) as mo_total"))
        ->where('id_tab_compra', '=', $codigo)
        ->first();

        $mo_total = $tab_compra_detalle->mo_total * $tab_iva_factura->nu_iva_factura / 100;
    
        return $mo_total;
    }
}
