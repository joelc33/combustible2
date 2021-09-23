<?php

namespace gobela\Models\Administracion;

use Illuminate\Database\Eloquent\Model;
use DB;

class tab_fondo_tercero_detalle extends Model
{
    //Nombre de la conexion que utitlizara este modelo
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'administracion.tab_fondo_tercero_detalle';
    
    public static $validarCrear = array(
                "retencion" => "required|numeric",
                "fecha_inicio" => "required",
                "fecha_fin" => "required",
                "monto" => "required|numeric|min:1",
	);  
    
    protected function moPago($codigo){

        $tab_fondo_tercero_detalle = tab_fondo_tercero_detalle::select( DB::raw("coalesce( SUM(monto), 0) as mo_total"))
        ->where('id_tab_solicitud', '=', $codigo)
        ->first();
    
        return $tab_fondo_tercero_detalle->mo_total;
    }    

}
